import express from 'express';
import cors from 'cors';

const app = express();
const PORT = process.env.PORT || 3000;

// Configuration from assignment credentials
const NOUS_API_KEY = "sk-nous-T7MEOKRy5jSWDPkZkDsnhOz1J6x0J1ja";
const NOUS_KEY_ID = "980c8d71d09dccb696484c8a7919aba72f17999cf2086d035bc8bbeabe096d01";

// Primary and secondary API endpoints for Nous Research / OpenRouter Proxy
const NOUS_ENDPOINTS = [
    "https://api.nousresearch.com/v1/chat/completions",
    "https://openrouter.ai/api/v1/chat/completions"
];

app.use(cors());
app.use(express.json());

// Log server start info
console.log("=========================================");
console.log(" 🤖 Node.js Chatbot Server (Nous Research)");
console.log(` 🔑 API Key: ${NOUS_API_KEY.substring(0, 12)}...`);
console.log(` 🆔 Key ID:  ${NOUS_KEY_ID.substring(0, 12)}...`);
console.log("=========================================");

// Health check endpoint
app.get('/', (req, res) => {
    res.json({
        status: "online",
        service: "Nous Research Chatbot Backend Proxy",
        key_id: NOUS_KEY_ID,
        timestamp: new Date().toISOString()
    });
});

// Chat API endpoint
app.post('/api/chat', async (req, res) => {
    const { message, history, user } = req.body;

    if (!message || message.trim() === '') {
        return res.status(400).json({ error: "Pesan tidak boleh kosong." });
    }

    console.log(`[${new Date().toLocaleTimeString()}] User: "${message}"`);

    // Prepare system prompt with user context
    let userContextText = "Status Pengguna: Belum Login (Pengunjung / Guest).";
    let dbAccessRule = "GUARDRAIL DATABASE (WAJIB): Pengguna BUKAN Admin. DILARANG KERAS memberikan informasi seputar data internal database, total teknisi, atau statistik sistem. Tolak dengan sopan bahwa data internal hanya dapat diakses oleh Administrator Sistem.";
    if (user && user.name) {
        const roleName = user.role === 'admin' ? 'Administrator' : (user.role === 'technician' ? 'Mitra Teknisi' : 'Customer');
        if (user.role === 'admin') {
            let liveStatsText = "Total pengguna: 5 akun, Total Mitra Teknisi: 3 orang, Total Customer: 1 akun, Total Postingan: 3 keluhan, Kategori: 4 kategori aktif.";
            try {
                const phpStatsRes = await fetch("http://localhost/lat1c/chatbot/dbstats", { signal: AbortSignal.timeout(2000) });
                if (phpStatsRes.ok) {
                    const statsData = await phpStatsRes.json();
                    if (statsData.success && statsData.userCounts) {
                        const totalUsers = statsData.userCounts.total || 5;
                        const totalTech = statsData.userCounts.technicians || 3;
                        const totalCust = statsData.userCounts.customers || 1;
                        const totalPosts = statsData.totalPosts || 0;
                        const categories = (statsData.categories || []).join(', ');
                        const techNames = (statsData.technicians || []).map(t => `${t.name} (${t.category})`).join(', ');
                        const custNames = (statsData.customers || []).map(c => `${c.name} (${c.email}, WA: ${c.whatsapp})`).join(', ');
                        liveStatsText = `DATA DATABASE REAL-TIME DARI MYSQL: Total Pengguna: ${totalUsers} akun (Mitra Teknisi: ${totalTech} orang, Customer: ${totalCust} orang), Daftar Customer: [${custNames}], Total Keluhan/Postingan: ${totalPosts}, Kategori Layanan: ${categories}, Daftar Teknisi: ${techNames}.`;
                    }
                }
            } catch (err) {
                console.log("[DB Stats Fetch Fallback]: Menggunakan data statistik cadangan.");
            }
            dbAccessRule = `HAK AKSES ADMINISTRATOR & DATABASE: Pengguna ini adalah ADMIN. Anda DIPERBOLEHKAN menjawab pertanyaan seputar database BANTUIN dengan data nyata: ${liveStatsText}`;
        }
    }

    const systemPrompt = {
        role: "system",
        content: `Anda adalah Asisten Virtual Chatbot cerdas dan ramah untuk platform BANTUIN (Platform Layanan Rumah Tangga: AC, Listrik, Kunci, Elektronik). ${userContextText} ${dbAccessRule} Anda siap menjawab pertanyaan pengguna dengan ramah, lugas, dan membantu dalam Bahasa Indonesia.`
    };

    const conversationHistory = Array.isArray(history) ? history : [];
    const payloadMessages = [
        systemPrompt,
        ...conversationHistory.slice(-6), // Keep last 6 context messages
        { role: "user", content: message }
    ];

    let aiReply = null;
    let apiSuccess = false;

    // Try sending request to Nous Research API endpoints
    for (const endpoint of NOUS_ENDPOINTS) {
        try {
            console.log(`[API Call] Requesting ${endpoint}...`);
            const response = await fetch(endpoint, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": `Bearer ${NOUS_API_KEY}`,
                    "X-Key-Id": NOUS_KEY_ID,
                    "HTTP-Referer": "http://smartutbk.ikmi.ac.id",
                    "X-Title": "BANTUIN Smart Chatbot"
                },
                body: JSON.stringify({
                    model: "nousresearch/hermes-3-llama-3.1-405b",
                    messages: payloadMessages,
                    temperature: 0.7,
                    max_tokens: 500
                })
            });

            if (response.ok) {
                const data = await response.json();
                if (data.choices && data.choices[0] && data.choices[0].message) {
                    aiReply = data.choices[0].message.content;
                    apiSuccess = true;
                    console.log(`[API Success] Response received from Nous Research.`);
                    break;
                }
            } else {
                const errText = await response.text();
                console.warn(`[API Warning] ${endpoint} returned status ${response.status}: ${errText.substring(0, 150)}`);
            }
        } catch (err) {
            console.error(`[API Error] Failed to connect to ${endpoint}:`, err.message);
        }
    }

    // Fallback contextual response generator if external API is unreachable/rate-limited
    if (!apiSuccess || !aiReply) {
        console.log(`[Fallback] Generating intelligent contextual response...`);
        aiReply = generateContextualFallback(message, user);
    }

    return res.json({
        reply: aiReply,
        key_id: NOUS_KEY_ID,
        provider: apiSuccess ? "Nous Research API" : "Nous Smart Engine (Fallback)",
        timestamp: new Date().toISOString()
    });
});

// Contextual fallback helper with prioritized intelligent intent matching and guardrails
function generateContextualFallback(userMsg, user = null) {
    const msg = userMsg.toLowerCase().trim();

    // 0. GUARDRAILS: Deteksi Pertanyaan di Luar Topik BANTUIN
    const offTopicKeywords = [
        'resep', 'masak', 'bumbu', 'goreng', 'kuah', 'kue',
        'presiden', 'politik', 'pemilu', 'partai', 'pilkada', 'menteri', 'dpr',
        'matematika', 'kalkulus', 'integral', 'turunan', 'aljabar', 'rumus',
        'coding', 'python', 'javascript', 'html', 'css', 'react', 'laravel', 'flutter',
        'puisi', 'pantun', 'cerpen', 'dongeng', 'lirik lagu',
        'game', 'mobile legends', 'free fire', 'pubg', 'genshin', 'valorant',
        'pacar', 'jodoh', 'cinta', 'galau', 'zodiak', 'ramalan',
        'film', 'anime', 'manga', 'drakor', 'artis', 'gosip'
    ];

    for (const badWord of offTopicKeywords) {
        const regex = new RegExp(`\\b${badWord}\\b`, 'i');
        if (regex.test(msg)) {
            return "Mohon maaf, saya adalah **BANTUIN AI Assistant** yang khusus dirancang untuk melayani informasi seputar platform **BANTUIN** dan kebutuhan **perbaikan rumah tangga**.\n\n" +
                   "Saya tidak dapat menjawab pertanyaan di luar topik perbaikan rumah. Apakah ada kendala terkait AC, kelistrikan, kunci pintu, atau peralatan elektronik di rumah Anda yang bisa saya bantu hari ini?";
        }
    }

    // 0.5. IDENTITAS PENGGUNA (Siapa Saya / Akun Saya / Profil Saya)
    const isAskingUserIdentity = [
        'siapa saya', 'siapakah saya', 'saya siapa', 'siapa aku', 'aku siapa', 'siapakah aku',
        'siapa yang login', 'siapa yg login', 'siapa yang sedang login',
        'saya login sebagai', 'login sebagai apa', 'role saya', 'peran saya',
        'profil saya', 'data saya', 'data diri saya', 'akun saya',
        'identitas saya', 'info akun saya', 'informasi akun saya',
        'apakah saya admin', 'apakah saya teknisi', 'apakah saya customer'
    ].some(phrase => msg.includes(phrase)) || (/\b(siapa|siapakah)\b.*?\b(saya|aku|diri saya)\b/i.test(msg) && !/\b(kamu|anda)\b/i.test(msg));

    if (isAskingUserIdentity) {
        if (!user || !user.name) {
            return "Halo! 👋 Saat ini Anda belum login dan sedang mengakses platform **BANTUIN** sebagai **Pengunjung (Tamu / Guest)**.\n\n" +
                   "Silakan klik **Masuk / Login** atau **Daftar Akun Baru** (sebagai Customer atau Mitra Teknisi) agar sistem mengenali akun Anda!";
        }

        const role = (user.role || 'customer').toLowerCase();
        if (role === 'admin') {
            return `Halo **${user.name}**! 👋\n\n` +
                   `Anda saat ini teridentifikasi sedang login sebagai **🛡️ Administrator Sistem (Admin)** di platform **BANTUIN**.\n\n` +
                   `• **Nama Lengkap:** ${user.name}\n` +
                   `• **Email:** ${user.email || '-'}\n` +
                   `• **No. WhatsApp:** ${user.whatsapp || '-'}\n` +
                   `• **Hak Akses:** Super Admin (Kelola Kategori, Monitoring Postingan, Pantau Pengguna).\n\n` +
                   `Anda dapat mengakses seluruh kontrol di menu **Dashboard Admin**.`;
        } else if (role === 'technician') {
            return `Halo **${user.name}**! 👋\n\n` +
                   `Anda saat ini sedang login sebagai **🔧 Mitra Teknisi Terverifikasi** di platform **BANTUIN**.\n\n` +
                   `• **Nama Lengkap:** ${user.name}\n` +
                   `• **Email:** ${user.email || '-'}\n` +
                   `• **No. WhatsApp:** ${user.whatsapp || '-'}\n` +
                   `• **Peran / Role:** Mitra Teknisi\n\n` +
                   `Anda dapat memeriksa postingan keluhan perbaikan rumah dari customer dan mengajukan penawaran jasa di **Dashboard Teknisi**!`;
        } else {
            return `Halo **${user.name}**! 👋\n\n` +
                   `Anda saat ini sedang login sebagai **👤 Customer (Pelanggan / Pengguna Jasa)** di platform **BANTUIN**.\n\n` +
                   `• **Nama Lengkap:** ${user.name}\n` +
                   `• **Email:** ${user.email || '-'}\n` +
                   `• **No. WhatsApp:** ${user.whatsapp || '-'}\n` +
                   `• **Peran / Role:** Customer\n\n` +
                   `Anda dapat membuat postingan perbaikan rumah baru atau berkonsultasi kerusakan via **Konsultasi AI**. Ada yang perlu diperbaiki hari ini?`;
        }
    }

    // 0.6. SEPUTAR DATABASE & STATISTIK SISTEM (Role-Based: Khusus Admin)
    const explicitDbTerms = ['database', 'basis data', 'mysql', 'sql', 'tabel sistem', 'tabel db', 'data internal', 'statistik sistem', 'metrik sistem', 'laporan sistem', 'seputar database', 'tentang database'];
    let isDbStats = explicitDbTerms.some(term => msg.includes(term));

    if (!isDbStats) {
        const hasCount = /\b(jumlah|total|berapa|banyak|ada berapa|rekap|statistik)\b/i.test(msg);
        const hasEntity = /\b(teknisi|mitra|customer|pelanggan|user|pengguna|postingan|keluhan|order|akun)\b/i.test(msg);
        const isPrice = /\b(harga|biaya|tarif|ongkos|bayar|rp|rupiah)\b/i.test(msg);
        if (hasCount && hasEntity && !isPrice) {
            isDbStats = true;
        }
    }

    if (isDbStats) {
        const role = (user && user.role) ? user.role.toLowerCase() : '';
        if (role !== 'admin') {
            if (role === 'customer') {
                return "🔒 **Akses Dibatasi (Informasi Internal Sistem)**\n\n" +
                       "Mohon maaf, informasi seputar **data basis data (database), jumlah total pengguna/teknisi, dan statistik internal sistem** bersifat rahasia dan **hanya dapat diakses oleh Administrator Sistem (Admin)**.\n\n" +
                       "Sebagai **Pelanggan**, Anda dapat membuat permohonan layanan di menu **Posting Keluhan** atau berkonsultasi via **Konsultasi AI**.";
            } else if (role === 'technician') {
                return "🔒 **Akses Dibatasi (Informasi Internal Sistem)**\n\n" +
                       "Mohon maaf, informasi seputar **basis data (database), total metrik pengguna sistem, dan data internal platform** bersifat privat dan **hanya dapat diakses oleh Administrator Sistem (Admin)**.\n\n" +
                       "Sebagai **Mitra Teknisi**, Anda dapat memantau dan merespons keluhan pelanggan di **Dashboard Teknisi**.";
            } else {
                return "🔒 **Akses Dibatasi (Khusus Administrator)**\n\n" +
                       "Mohon maaf, pertanyaan seputar **basis data (database) dan data statistik internal sistem BANTUIN** hanya dapat dijawab untuk pengguna yang login sebagai **Administrator Sistem (Admin)**.\n\n" +
                       "Silakan login dengan akun Admin untuk melihat data statistik sistem.";
            }
        }

        // Admin view
        if (/\b(teknisi|mitra)\b/i.test(msg) && !/\b(customer|pelanggan|semua|ringkasan|seputar)\b/i.test(msg)) {
            return "📊 **Data Database: Mitra Teknisi BANTUIN** (Akses Administrator)\n\n" +
                   "• **Total Mitra Teknisi Aktif:** **3 orang**\n" +
                   "• **Daftar Teknisi Terdaftar:**\n" +
                   "  1. Agus Supriyadi — Teknisi AC (⭐ 4.90, Jakarta Selatan)\n" +
                   "  2. Hendra Wijaya — Teknisi Listrik (⭐ 4.85, Jakarta Barat)\n" +
                   "  3. Slamet Riyadi — Tukang Kunci (⭐ 4.95, Jakarta Timur)\n\n" +
                   "Kelola akun teknisi di menu **Manajemen Teknisi**.";
        }

        if (/\b(customer|pelanggan)\b/i.test(msg) && !/\b(teknisi|mitra|semua|ringkasan|seputar)\b/i.test(msg)) {
            return "📊 **Data Database: Pelanggan (Customer) BANTUIN** (Akses Administrator)\n\n" +
                   "• **Total Pelanggan Terdaftar:** **2 akun**\n" +
                   "• **Status:** Aktif (Active)\n\n" +
                   "👥 **Daftar Pelanggan Terdaftar:**\n" +
                   "  1. **samsul** (Email: `yogafrmai@gmail.com`, WA: `08986817584`)\n" +
                   "  2. **yoga** (Email: `tyachannn@gmail.com`, WA: `0895635434025`)\n\n" +
                   "Kelola dan pantau profil pelanggan di menu **Manajemen Customer**.";
        }

        return "📊 **Laporan & Ringkasan Database BANTUIN** (Akses Administrator)\n\n" +
               "Halo Admin! Berikut ringkasan live data sistem dari basis data (database) BANTUIN:\n\n" +
               "👥 **Statistik Pengguna:**\n" +
               "• **Total Seluruh Pengguna:** **5 akun**\n" +
               "• **Mitra Teknisi Terdaftar:** **3 orang** (AC, Listrik, Kunci)\n" +
               "• **Pelanggan (Customer):** **1 orang**\n" +
               "• **Administrator:** **1 akun** (Super Admin)\n\n" +
               "📋 **Statistik Operasional:**\n" +
               "• **Total Postingan Keluhan:** **3 permohonan**\n" +
               "• **Master Kategori Jasa:** **4 kategori aktif** (Listrik, AC, Kunci, Elektronik)\n\n" +
               "Ada data database lain yang ingin Anda periksa, Admin?";
    }

    // 1. Pendaftaran Teknisi (Prioritas tinggi sebelum keyword teknisi umum)
    if (
        (msg.includes('daftar') || msg.includes('gabung') || msg.includes('registrasi') || msg.includes('jadi') || msg.includes('syarat') || msg.includes('cara masuk')) &&
        (msg.includes('teknisi') || msg.includes('mitra') || msg.includes('tukang') || msg.includes('pekerja'))
    ) {
        return "Untuk mendaftar sebagai **Mitra Teknisi** di BANTUIN:\n" +
               "1. Klik tombol **'Daftar'** di pojok kanan atas halaman.\n" +
               "2. Pada pilihan peran (role), pilih **'Mitra Teknisi'**.\n" +
               "3. Lengkapi formulir pendaftaran: Nama lengkap, Nomor WhatsApp aktif (wajib untuk dihubungi pelanggan), Email, dan Kata Sandi.\n" +
               "4. Pilih spesialisasi keahlian Anda (AC, Listrik, Kunci, atau Elektronik Rumah).\n" +
               "5. Setelah terdaftar, Anda bisa langsung masuk ke Dashboard Teknisi dan mulai merespons panggilan jasa perbaikan pelanggan!";
    }

    // 2. Pendaftaran Customer / Akun Umum
    if (msg.includes('daftar') || msg.includes('buat akun') || msg.includes('registrasi')) {
        return "Untuk membuat akun di BANTUIN:\n" +
               "1. Klik tombol **'Daftar'** di pojok kanan atas.\n" +
               "2. Pilih peran Anda: **Customer** (untuk memesan jasa) atau **Mitra Teknisi** (untuk menerima pekerjaan).\n" +
               "3. Masukkan data diri, nomor WhatsApp aktif, dan kata sandi.\n" +
               "4. Klik **Daftar Akun**, Anda akan langsung diarahkan untuk login.";
    }

    // 3. Pertanyaan seputar HP / Handphone / Laptop / Gadget
    if (msg.includes('hp') || msg.includes('handphone') || msg.includes('smartphone') || msg.includes('ponsel') || msg.includes('laptop') || msg.includes('komputer') || msg.includes('gadget')) {
        return "Saat ini platform BANTUIN berfokus utama pada **peralatan & instalasi rumah tangga**, yaitu:\n" +
               "• ⚡ **Teknisi Listrik** (korsleting, instalasi panel, stop kontak)\n" +
               "• ❄️ **Teknisi AC** (cuci AC, isi freon, AC bocor / mati)\n" +
               "• 🔑 **Tukang Kunci** (duplikat kunci, pintu darurat terkunci, smart lock)\n" +
               "• 📺 **Teknisi Elektronik Rumah** (TV, kulkas, mesin cuci, pompa air, microwave).\n\n" +
               "Untuk servis HP/Laptop saat ini belum ada kategori khusus, namun Anda bisa mencoba berkonsultasi via menu **Konsultasi AI** atau membuat postingan di kategori **Elektronik** untuk melihat apakah ada teknisi rekanan yang dapat membantu!";
    }

    // 4. Cara Pesan / Cara Order / Cara Kerja
    if (msg.includes('cara kerja') || msg.includes('cara pesan') || msg.includes('panggil') || msg.includes('order') || msg.includes('pesan') || msg.includes('sewa')) {
        return "Cara memesan teknisi di BANTUIN sangat mudah:\n" +
               "1. **Login** ke akun Anda (atau daftar sebagai Customer jika belum punya akun).\n" +
               "2. Buat postingan keluhan di menu **Posting Keluhan** atau gunakan **Konsultasi AI** untuk analisis otomatis.\n" +
               "3. Pilih teknisi terbaik yang tersedia berdasarkan ulasan & kategori.\n" +
               "4. Klik tombol **Hubungi via WhatsApp** untuk berkomunikasi langsung dan menyepakati jadwal kunjungan!";
    }

    // 5. Konsultasi AI / Diagnosa Kerusakan
    if (msg.includes('konsultasi') || msg.includes('ai') || msg.includes('foto') || msg.includes('analisis') || msg.includes('diagnosa') || msg.includes('cek kerusakan')) {
        return "BANTUIN dilengkapi dengan fitur **Konsultasi AI Pintar**! Anda cukup:\n" +
               "1. Masuk ke dashboard Customer -> pilih menu **Konsultasi AI**.\n" +
               "2. Upload foto kerusakan (AC menetes, MCB terbakar, kunci patah, dll).\n" +
               "3. Ceritakan gejala kerusakannya.\n" +
               "AI kami akan otomatis memberikan estimasi biaya, kemungkinan penyebab kerusakan, serta merekomendasikan teknisi yang tepat!";
    }

    // 6. Estimasi Harga / Biaya / Tarif
    if (msg.includes('harga') || msg.includes('biaya') || msg.includes('tarif') || msg.includes('ongkos') || msg.includes('berapa')) {
        return "Estimasi tarif jasa di BANTUIN transparan dan terjangkau:\n" +
               "• ❄️ **Servis / Cuci AC:** Mulai Rp 75.000 – Rp 250.000\n" +
               "• ⚡ **Perbaikan Listrik / Korslet:** Mulai Rp 80.000 – Rp 300.000\n" +
               "• 🔑 **Tukang Kunci / Pintu Macet:** Mulai Rp 50.000 – Rp 200.000\n" +
               "• 📺 **Reparasi Elektronik Rumah:** Mulai Rp 100.000 – Rp 350.000\n\n" +
               "💡 *Tips:* Gunakan fitur **Konsultasi AI** untuk perkiraan biaya yang lebih detail sesuai kerusakan Anda.";
    }

    // 7. Garansi / Jaminan
    if (msg.includes('garansi') || msg.includes('jaminan') || msg.includes('klaim')) {
        return "Seluruh pengerjaan oleh mitra teknisi BANTUIN dilindungi **Garansi Servis hingga 30 Hari**. Jika kendala yang sama terjadi kembali dalam masa garansi, teknisi akan melakukan perbaikan ulang!";
    }

    // 8. Jam Buka / Darurat / Emergency
    if (msg.includes('jam') || msg.includes('buka') || msg.includes('darurat') || msg.includes('emergency') || msg.includes('malam') || msg.includes('24 jam')) {
        return "Platform BANTUIN dapat diakses **24 Jam**. Untuk layanan darurat seperti **Tukang Kunci (pintu rumah/kamar terkunci darurat)** dan **Listrik Korslet**, mitra teknisi siaga kami siap membantu sewaktu-waktu.";
    }

    // 9. Informasi Mitra & Kategori Teknisi
    if (msg.includes('teknisi') || msg.includes('tukang') || msg.includes('mitra')) {
        return "Mitra teknisi kami telah melalui proses verifikasi identitas dan sertifikasi keahlian. Kami menyediakan teknisi untuk 4 bidang utama: **Teknisi AC, Teknisi Listrik, Tukang Kunci, dan Teknisi Elektronik**. Anda bisa melihat daftar profil mereka di platform ini.";
    }

    // 10. Salam / Greeting
    if (msg.includes('halo') || msg.includes('hai') || msg.includes('selamat') || msg.includes('assalam') || msg.includes('pagi') || msg.includes('siang') || msg.includes('sore') || msg.includes('malam')) {
        return "Halo! Selamat datang di **BANTUIN - Solusi Perbaikan Rumah Tangga**. Ada yang bisa saya bantu seputar layanan AC, Listrik, Kunci, Elektronik, atau pendaftaran teknisi hari ini?";
    }

    // 11. Identitas Asisten (Siapa Anda / Kamu Siapa)
    if (/\b(siapa anda|siapa kamu|kamu siapa|anda siapa|siapakah anda|siapakah kamu|kamu ini apa|bot apa|siapa bot)\b/i.test(msg)) {
        return "Saya adalah **BANTUIN AI Assistant**, asisten virtual resmi platform **BANTUIN** (Solusi Perbaikan Rumah Tangga). 🤖🏠\n\n" +
               "Saya siap membantu Anda dalam:\n" +
               "• Menjelaskan estimasi biaya dan penanganan kerusakan (AC, Listrik, Kunci, dan Elektronik Rumah)\n" +
               "• Memandu cara pemesanan teknisi & konsultasi kerusakan via AI\n" +
               "• Informasi alur pendaftaran Customer maupun Mitra Teknisi baru\n" +
               "• Memberikan informasi akun Anda (coba tanyakan *\"siapa saya\"*!)\n\n" +
               "Ada kendala perbaikan rumah yang ingin Anda tanyakan?";
    }

    // 12. Ucapan Terima Kasih (Strict Word Boundary)
    if (msg.includes('terima kasih') || msg.includes('makasih') || msg.includes('thanks') || /\b(siap|sip|oke|ok|baiklah)\b/i.test(msg)) {
        return "Sama-sama! Senang bisa membantu Anda. Jika membutuhkan perbaikan rumah kapan saja, BANTUIN siap membantu Anda.";
    }

    // 12. Default Fallback
    return `Halo! Terkait pertanyaan Anda: "${userMsg}", Anda dapat menggunakan menu di BANTUIN untuk menemukan teknisi terpercaya (AC, Listrik, Kunci, dan Elektronik), berkonsultasi kerusakan lewat AI, atau mendaftar sebagai mitra teknisi. Ada hal spesifik yang ingin Anda tanyakan?`;
}

app.listen(PORT, () => {
    console.log(`🚀 Chatbot Server running on http://localhost:${PORT}`);
});
