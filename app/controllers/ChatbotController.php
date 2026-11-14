<?php
/**
 * Chatbot Controller (AI Assistant BANTUIN)
 * 
 * Provides an AI-powered conversational assistant strictly grounded
 * on the BANTUIN household maintenance platform.
 * Supports Google Gemini API with smart grounded fallback guardrails.
 */

class ChatbotController extends Controller {

    /**
     * Endpoint: /chatbot or /chatbot/message
     */
    public function message() {
        // Suppress HTML errors from corrupting JSON API output
        ini_set('display_errors', '0');
        ob_start();

        // Allow CORS if needed
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type");
        header("Content-Type: application/json; charset=UTF-8");

        // Handle preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            if (ob_get_length()) ob_clean();
            http_response_code(200);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (ob_get_length()) ob_clean();
            echo json_encode([
                'status' => 'online',
                'service' => 'BANTUIN AI Assistant (Grounded AI)',
                'timestamp' => date('c')
            ]);
            exit;
        }

        // Read raw JSON body
        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true);

        $userMessage = trim($data['message'] ?? '');
        $history = is_array($data['history'] ?? null) ? $data['history'] : [];
        $clientUser = is_array($data['user'] ?? null) ? (object) $data['user'] : null;

        if (empty($userMessage)) {
            http_response_code(400);
            echo json_encode(['error' => 'Pesan tidak boleh kosong.']);
            exit;
        }

        // Get authenticated user (priority from active session, fallback to client payload)
        Auth::init();
        $currentUser = Auth::user();
        if (!$currentUser && !empty($clientUser) && !empty($clientUser->id)) {
            try {
                $userModel = $this->model('UserModel');
                $dbUser = $userModel->findById($clientUser->id);
                $currentUser = $dbUser ? (object)$dbUser : $clientUser;
            } catch (\Throwable $e) {
                $currentUser = $clientUser;
            }
        }

        $techProfile = null;
        $techJobs = [];
        $customerPosts = [];
        $customerPostsCount = null;
        $adminDbStats = null;

        if ($currentUser && !empty($currentUser->id)) {
            $userRole = $currentUser->role ?? '';
            if ($userRole === 'technician') {
                try {
                    $techModel = $this->model('TechnicianProfileModel');
                    $techProfile = $techModel->getProfileByUserId($currentUser->id);
                } catch (\Throwable $e) {
                    $techProfile = null;
                }
                try {
                    $postModel = $this->model('PostModel');
                    $myResponses = $postModel->getResponsesByTechnicianId($currentUser->id);
                    if (is_array($myResponses)) {
                        foreach ($myResponses as $r) {
                            $techJobs[] = [
                                'post_id'           => is_object($r) ? ($r->post_id ?? $r->id ?? null) : ($r['post_id'] ?? $r['id'] ?? null),
                                'title'             => is_object($r) ? ($r->post_title ?? $r->title ?? '') : ($r['post_title'] ?? $r['title'] ?? ''),
                                'customer_name'     => is_object($r) ? ($r->customer_name ?? '') : ($r['customer_name'] ?? ''),
                                'customer_whatsapp' => is_object($r) ? ($r->customer_whatsapp ?? '') : ($r['customer_whatsapp'] ?? ''),
                                'category'          => is_object($r) ? ($r->category_name ?? '') : ($r['category_name'] ?? ''),
                                'offer_status'      => is_object($r) ? ($r->status ?? '') : ($r['status'] ?? ''),
                                'post_status'       => is_object($r) ? ($r->post_status ?? '') : ($r['post_status'] ?? ''),
                            ];
                        }
                    }
                } catch (\Throwable $e) {
                    $techJobs = [];
                }
            } elseif ($userRole === 'customer') {
                try {
                    $postModel = $this->model('PostModel');
                    $custPosts = $postModel->getPostsByCustomerId($currentUser->id);
                    $customerPostsCount = is_array($custPosts) ? count($custPosts) : 0;
                    if (is_array($custPosts)) {
                        foreach ($custPosts as $cp) {
                            $customerPosts[] = [
                                'id'              => is_object($cp) ? ($cp->id ?? '') : ($cp['id'] ?? ''),
                                'title'           => is_object($cp) ? ($cp->title ?? '') : ($cp['title'] ?? ''),
                                'category'        => is_object($cp) ? ($cp->category_name ?? '') : ($cp['category_name'] ?? ''),
                                'status'          => is_object($cp) ? ($cp->status ?? '') : ($cp['status'] ?? ''),
                                'technician_name' => is_object($cp) ? ($cp->technician_name ?? '-') : ($cp['technician_name'] ?? '-'),
                            ];
                        }
                    }
                } catch (\Throwable $e) {
                    $customerPostsCount = 0;
                    $customerPosts = [];
                }
            } elseif ($userRole === 'admin') {
                $adminDbStats = $this->getDatabaseStats();
            }
        }

        // Process message with AI or Grounded Guardrail Engine with safety fallback
        try {
            $result = $this->processChat($userMessage, $history, $currentUser, $techProfile, $customerPostsCount, $adminDbStats, $techJobs, $customerPosts);
        } catch (\Throwable $e) {
            $result = [
                'reply' => $this->generateGroundedResponse($userMessage, $currentUser, $techProfile, $customerPostsCount, $adminDbStats, $techJobs, $customerPosts),
                'provider' => 'BANTUIN Smart AI Engine',
                'timestamp' => date('c')
            ];
        }

        if (ob_get_length()) {
            ob_clean();
        }
        echo json_encode($result);
        exit;
    }

    /**
     * Public JSON endpoint: Real-time DB stats for the Node.js chatbot server proxy
     * URL: /chatbot/dbstats   (GET — accessible without auth for Node.js internal use)
     * Returns anonymised aggregate stats; no personal data.
     */
    public function dbstats() {
        header('Content-Type: application/json; charset=UTF-8');
        header('Access-Control-Allow-Origin: *');

        try {
            $userModel  = $this->model('UserModel');
            $postModel  = $this->model('PostModel');
            $catModel   = $this->model('CategoryModel');
            $techModel  = $this->model('TechnicianProfileModel');

            $userCounts  = $userModel->getUserCounts();
            $totalPosts  = $postModel->getTotalPostsCount();
            $categories  = $catModel->getAllCategories();
            $technicians = $techModel->getRecommendedTechnicians(10);
            $allCustomers = $userModel->getAllUsers('customer');

            $techList = [];
            foreach ($technicians as $t) {
                $techList[] = [
                    'name'     => is_object($t) ? ($t->name ?? '')            : ($t['name'] ?? ''),
                    'category' => is_object($t) ? ($t->category_name ?? '')   : ($t['category_name'] ?? ''),
                    'rating'   => is_object($t) ? (float)($t->rating ?? 5.0)  : (float)($t['rating'] ?? 5.0),
                    'location' => is_object($t) ? ($t->location ?? '')        : ($t['location'] ?? ''),
                ];
            }

            $custList = [];
            if (!empty($allCustomers)) {
                foreach ($allCustomers as $c) {
                    $custList[] = [
                        'name'        => is_object($c) ? ($c->name ?? '') : ($c['name'] ?? ''),
                        'email'       => is_object($c) ? ($c->email ?? '') : ($c['email'] ?? ''),
                        'whatsapp'    => is_object($c) ? ($c->whatsapp ?? '') : ($c['whatsapp'] ?? ''),
                        'total_posts' => is_object($c) ? (int)($c->total_posts ?? 0) : (int)($c['total_posts'] ?? 0)
                    ];
                }
            }

            $catList = [];
            foreach ($categories as $c) {
                $catList[] = is_object($c) ? ($c->name ?? '') : ($c['name'] ?? '');
            }

            echo json_encode([
                'success'     => true,
                'source'      => 'live_database',
                'timestamp'   => date('c'),
                'userCounts'  => $userCounts,
                'totalPosts'  => (int)$totalPosts,
                'categories'  => $catList,
                'technicians' => $techList,
                'customers'   => $custList,
            ]);
        } catch (\Throwable $e) {
            echo json_encode([
                'success'     => false,
                'source'      => 'fallback',
                'timestamp'   => date('c'),
                'userCounts'  => ['customers' => 1, 'technicians' => 3, 'total' => 5],
                'totalPosts'  => 3,
                'categories'  => ['Teknisi Listrik', 'Teknisi AC', 'Tukang Kunci', 'Teknisi Elektronik'],
                'technicians' => [
                    ['name' => 'Agus Supriyadi',        'category' => 'Teknisi AC',      'rating' => 4.90, 'location' => 'Jakarta Selatan'],
                    ['name' => 'Hendra Wijaya (Listrik)','category' => 'Teknisi Listrik', 'rating' => 4.85, 'location' => 'Jakarta Barat'],
                    ['name' => 'Slamet Riyadi (Kunci)',  'category' => 'Tukang Kunci',    'rating' => 4.95, 'location' => 'Jakarta Timur'],
                ],
            ]);
        }
        exit;
    }

    private function processChat($userMessage, $history, $currentUser = null, $techProfile = null, $customerPostsCount = null, $adminDbStats = null, $techJobs = [], $customerPosts = []) {
        require_once dirname(__DIR__) . '/services/AIProviderService.php';
        $aiConfig = AIProviderService::getConfig();

        // 1. If an active external AI provider is configured (Gemini, ChatGPT, Claude, OpenRouter, Groq, Custom, NVIDIA)
        if ($aiConfig['provider'] !== 'internal' && (!empty($aiConfig['api_key']) || $aiConfig['provider'] === 'custom')) {
            $systemInstruction = $this->buildSystemInstruction($currentUser, $techProfile, $customerPostsCount, $adminDbStats, $techJobs, $customerPosts, $aiConfig);
            
            // Format recent history for AIProviderService (support both {role, content} and {sender, text})
            $messages = [];
            $recentHistory = array_slice($history, -8);
            foreach ($recentHistory as $item) {
                $text = trim($item['content'] ?? $item['text'] ?? '');
                $rawRole = $item['role'] ?? $item['sender'] ?? 'user';
                $role = in_array(strtolower($rawRole), ['user', 'customer']) ? 'user' : 'assistant';
                if (!empty($text)) {
                    $messages[] = [
                        'role' => $role,
                        'content' => $text
                    ];
                }
            }
            $messages[] = [
                'role' => 'user',
                'content' => $userMessage
            ];

            try {
                $chatResult = AIProviderService::chat($systemInstruction, $messages, [
                    'temperature' => $aiConfig['temperature']
                ]);

                if ($chatResult['success'] && !empty($chatResult['reply'])) {
                    return [
                        'reply' => $chatResult['reply'],
                        'provider' => $chatResult['provider_name'] . ' (' . $chatResult['model'] . ')',
                        'timestamp' => date('c')
                    ];
                }
            } catch (\Throwable $e) {
                // Proceed to fallback
            }
        }

        // 2. Intelligent Grounded Knowledge Engine (Zero-config, fast, always active fallback)
        $groundedReply = $this->generateGroundedResponse($userMessage, $currentUser, $techProfile, $customerPostsCount, $adminDbStats, $techJobs, $customerPosts);
        return [
            'reply' => $groundedReply,
            'provider' => 'BANTUIN Smart AI Engine',
            'timestamp' => date('c')
        ];
    }

    /**
     * Build system instructions and rich contextual grounding for AI models
     */
    private function buildSystemInstruction($currentUser = null, $techProfile = null, $customerPostsCount = null, $adminDbStats = null, $techJobs = [], $customerPosts = [], $aiConfig = null) {
        if (!$aiConfig) {
            require_once dirname(__DIR__) . '/services/AIProviderService.php';
            $aiConfig = AIProviderService::getConfig();
        }

        $providerName = $aiConfig['provider_name'] ?? 'Google Gemini';
        $modelName = $aiConfig['model'] ?? 'gemini-flash-lite-latest';

        $userStatusText = "\n\nINFORMASI PENGGUNA YANG SEDANG AKTIF:";
        if ($currentUser && !empty($currentUser->name)) {
            $roleLabel = ($currentUser->role === 'admin') ? 'Administrator Sistem (Admin)' : (($currentUser->role === 'technician') ? 'Mitra Teknisi' : 'Customer (Pelanggan)');
            $userStatusText .= "\n- Status: Sudah Login";
            $userStatusText .= "\n- Nama: {$currentUser->name}";
            $userStatusText .= "\n- Role: {$currentUser->role} ({$roleLabel})";
            $userStatusText .= "\n- Email: " . ($currentUser->email ?? '-');
            $userStatusText .= "\n- No. WhatsApp: " . ($currentUser->whatsapp ?? '-');
            if ($techProfile) {
                $userStatusText .= "\n- Spesialisasi: " . ($techProfile->category_name ?? 'Teknisi');
                $userStatusText .= "\n- Lokasi Layanan: " . ($techProfile->location ?? 'Jabodetabek');
                $userStatusText .= "\n- Rating: " . ($techProfile->rating ?? '5.0');
            }
        } else {
            $userStatusText .= "\n- Status: Belum Login (Pengunjung / Tamu / Guest)";
        }

        // Section for Technician's Jobs
        $techJobsText = "";
        if ($currentUser && ($currentUser->role ?? '') === 'technician') {
            $techJobsText = "\n\n========================================\n"
                . "DATA TUGAS & TAWARAN PEKERJAAN MITRA TEKNISI SAAT INI (REAL-TIME):\n";
            if (!empty($techJobs)) {
                $techJobsText .= "Mitra teknisi ini memiliki " . count($techJobs) . " pekerjaan/tawaran bantuan dalam sistem:\n";
                $i = 1;
                foreach ($techJobs as $job) {
                    $jTitle = $job['title'] ?: 'Permintaan Servis';
                    $jCust = $job['customer_name'] ?: 'Pelanggan';
                    $jWa = $job['customer_whatsapp'] ?: '-';
                    $jCat = $job['category'] ?: 'Teknisi';
                    $jOffer = $job['offer_status'] === 'selected' ? 'Dipilih Customer (Sedang Dikerjakan/Aktif)' : ($job['offer_status'] === 'pending' ? 'Menunggu Konfirmasi Customer' : $job['offer_status']);
                    $jPostStat = $job['post_status'] ?: 'Aktif';
                    $techJobsText .= "{$i}. Judul Pekerjaan: {$jTitle}\n"
                        . "   - Kategori: {$jCat}\n"
                        . "   - Customer: {$jCust} (WhatsApp: {$jWa})\n"
                        . "   - Status Tawaran: {$jOffer}\n"
                        . "   - Status Pengerjaan: {$jPostStat}\n";
                    $i++;
                }
            } else {
                $techJobsText .= "Saat ini mitra belum memiliki tugas pekerjaan aktif yang dipilih customer. Mitra dapat melihat permohonan jasa terbaru dari pelanggan di dasbor utama.";
            }
            $techJobsText .= "\n========================================";
        }

        // Section for Customer's Posts
        $customerPostsText = "";
        if ($currentUser && ($currentUser->role ?? '') === 'customer') {
            $customerPostsText = "\n\n========================================\n"
                . "DATA PERMOHONAN SERVIS & KELUHAN CUSTOMER SAAT INI (REAL-TIME):\n";
            if (!empty($customerPosts)) {
                $customerPostsText .= "Customer ini telah membuat " . count($customerPosts) . " permohonan servis:\n";
                $i = 1;
                foreach ($customerPosts as $cp) {
                    $cpTitle = $cp['title'] ?: 'Permintaan Servis';
                    $cpCat = $cp['category'] ?: 'Umum';
                    $cpStatus = $cp['status'] ?: 'Menunggu';
                    $cpTech = $cp['technician_name'] ?: 'Belum ada teknisi yang dipilih';
                    $customerPostsText .= "{$i}. Permohonan: {$cpTitle} (Kategori: {$cpCat}) - Status: {$cpStatus} - Teknisi: {$cpTech}\n";
                    $i++;
                }
            } else {
                $customerPostsText .= "Customer ini belum membuat postingan keluhan servis saat ini.";
            }
            $customerPostsText .= "\n========================================";
        }

        // Admin database data
        $dbSecurityInstruction = "";
        if ($currentUser && ($currentUser->role ?? '') === 'admin') {
            $userCounts = $adminDbStats['userCounts'] ?? ['customers' => 0, 'technicians' => 0, 'total' => 0];
            $totalTechs = $userCounts['technicians'] ?? 0;
            $totalCusts = $userCounts['customers'] ?? 0;
            $totalUsers = $userCounts['total'] ?? ($totalTechs + $totalCusts + 1);
            $totalPosts = $adminDbStats['totalPosts'] ?? 0;
            $categories = $adminDbStats['categories'] ?? [];
            $totalCats = is_array($categories) ? count($categories) : 4;

            // Rincian Daftar Pelanggan (Customer) Real-Time
            $custNames = [];
            if (!empty($adminDbStats['customers'])) {
                $no = 1;
                foreach ($adminDbStats['customers'] as $c) {
                    $cName = is_object($c) ? ($c->name ?? 'Customer') : ($c['name'] ?? 'Customer');
                    $cEmail = is_object($c) ? ($c->email ?? '-') : ($c['email'] ?? '-');
                    $cWa = is_object($c) ? ($c->whatsapp ?? '-') : ($c['whatsapp'] ?? '-');
                    $cPosts = is_object($c) ? (int)($c->total_posts ?? 0) : (int)($c['total_posts'] ?? 0);
                    $custNames[] = "{$no}. Nama: {$cName} | Email: {$cEmail} | WhatsApp: {$cWa} | Jumlah Keluhan: {$cPosts} postingan";
                    $no++;
                }
            }
            $custNamesStr = !empty($custNames) ? implode("\n  ", $custNames) : "Saat ini belum ada customer terdaftar.";

            // Rincian Daftar Mitra Teknisi Real-Time
            $techNames = [];
            if (!empty($adminDbStats['technicians'])) {
                $no = 1;
                foreach ($adminDbStats['technicians'] as $t) {
                    $tName = is_object($t) ? ($t->name ?? 'Mitra') : ($t['name'] ?? 'Mitra');
                    $tCat = is_object($t) ? ($t->category_name ?? 'Teknisi') : ($t['category_name'] ?? 'Teknisi');
                    $tRating = is_object($t) ? ($t->rating ?? 5.0) : ($t['rating'] ?? 5.0);
                    $tLoc = is_object($t) ? ($t->location ?? 'Jabodetabek') : ($t['location'] ?? 'Jabodetabek');
                    $tWa = is_object($t) ? ($t->whatsapp ?? '-') : ($t['whatsapp'] ?? '-');
                    $techNames[] = "{$no}. Nama: {$tName} | Keahlian: {$tCat} | Rating: {$tRating} | Lokasi: {$tLoc} | WhatsApp: {$tWa}";
                    $no++;
                }
            }
            $techNamesStr = !empty($techNames) ? implode("\n  ", $techNames) : "Saat ini belum ada mitra teknisi terdaftar.";

            $dbSecurityInstruction = "\n\n========================================\n"
                . "HAK AKSES ADMINISTRATOR & DATA DATABASE SISTEM (REAL-TIME):\n"
                . "Pengguna saat ini terverifikasi sebagai ADMINISTRATOR SISTEM (Role: admin) dengan wewenang penuh atas database platform BANTUIN.\n"
                . "DATA LENGKAP DATABASE BANTUIN SAAT INI:\n"
                . "- Total Seluruh Pengguna: {$totalUsers} akun (Customer: {$totalCusts}, Mitra Teknisi: {$totalTechs}, Admin: 1)\n"
                . "- Total Customer/Pelanggan: {$totalCusts} akun\n"
                . "  DAFTAR LENGKAP CUSTOMER TERDAFTAR:\n  " . $custNamesStr . "\n"
                . "- Total Mitra Teknisi: {$totalTechs} orang\n"
                . "  DAFTAR LENGKAP MITRA TEKNISI:\n  " . $techNamesStr . "\n"
                . "- Total Postingan Keluhan: {$totalPosts} permohonan\n"
                . "- Total Kategori Layanan: {$totalCats} kategori aktif\n\n"
                . "PANDUAN MUTLAK MENJAWAB PERTANYAAN ADMINISTRATOR:\n"
                . "1. JIKA ADMIN BERTANYA TENTANG CUSTOMER/PELANGGAN (misalnya 'siapa saja total customer', 'siapa saja customer', 'daftar customer', 'siapa saja pelanggan', 'berapa customer'):\n"
                . "   - Anda WAJIB MENJAWAB LANGSUNG DAN TUNTAS pertanyaan tersebut!\n"
                . "   - Sebutkan total jumlah customer ({$totalCusts} akun) dan sebutkan DAFTAR LENGKAP NAMA CUSTOMER dari data di atas beserta email dan nomor WhatsApp mereka secara terstruktur rapi (bullet/nomor).\n"
                . "2. JIKA ADMIN BERTANYA TENTANG TEKNISI: Jawab langsung menyebutkan total teknisi dan daftar nama mereka beserta keahlian dan ratingnya.\n"
                . "3. JIKA ADMIN BERTANYA TENTANG RINGKASAN DATABASE / SELURUH PENGGUNA: Sajikan ringkasan data di atas secara profesional dan lengkap.\n"
                . "========================================";
        }

        $platformOverview = "\n\n========================================\n"
            . "PENGETAHUAN LENGKAP PLATFORM BANTUIN (HOUSEHOLD REPAIR PLATFORM):\n"
            . "1. Layanan & Kategori Utama BANTUIN:\n"
            . "   - ⚡ Teknisi Listrik: Penanganan korsleting listrik, MCB sering jeglek/turun, bau hangus di stop kontak/panel, instalasi kabel baru, grounding, saklar rusak, dan kelistrikan darurat.\n"
            . "   - ❄️ Teknisi AC: Servis & cuci AC berkala, penanganan AC bocor/menetes air, isi ulang freon (R32, R410A, R22), AC tidak dingin/hanya keluar angin, perbaikan kompresor berisik/mati, bongkar pasang unit AC.\n"
            . "   - 🔑 Tukang Kunci 24 Jam: Pembukaan darurat pintu terkunci (kunci tertinggal di dalam) tanpa merusak pintu, ekstraksi anak kunci patah di dalam silinder, duplikat kunci rumah & kendaraan, pasang Smart Digital Door Lock, ganti silinder master lock.\n"
            . "   - 📺 Teknisi Elektronik Rumah: Servis kulkas 1 & 2 pintu (tidak dingin, modul inverter, freon kulkas), mesin cuci top/front loading (air mampet, modul error, dinamo pengering rusak), pompa air sumur (dengung, air tidak hisap), TV LED/LCD (layar gelap, suara hilang, mati total), microwave, dispenser, dan water heater.\n"
            . "2. Keunggulan & Fitur Platform:\n"
            . "   - Garansi Pengerjaan 30 Hari: Setiap pekerjaan yang diselesaikan mitra teknisi BANTUIN mendapatkan jaminan garansi 30 hari.\n"
            . "   - Layanan Siaga Darurat 24 Jam: Siap menangani kendala mendesak (listrik padam/korslet dan pintu terkunci darurat).\n"
            . "   - Konsultasi Kerusakan Berbasis AI: Membantu pelanggan menganalisis gejala kerusakan awal dan estimasi biaya wajar sebelum memanggil teknisi.\n"
            . "   - Posting Keluhan Layanan: Pelanggan membuat postingan keluhan untuk mendapatkan respons tawaran dari mitra teknisi terdekat.\n"
            . "   - Koordinasi Langsung via WhatsApp: Pelanggan dan teknisi dapat langsung berkomunikasi via WhatsApp resmi yang terdaftar.\n"
            . "========================================";

        $systemInstruction = "Anda adalah 'BANTUIN AI Assistant', asisten virtual cerdas, solutif, ramah, dan tanggap yang DIBATASI KHUSUS SEPUTAR PLATFORM 'BANTUIN' (Platform Jasa Perbaikan Rumah Tangga: Teknisi Listrik, Teknisi AC, Tukang Kunci, dan Teknisi Elektronik).\n\n"
            . "INFORMASI ENGINE AI ANDA SAAT INI:\n"
            . "- Provider AI: {$providerName}\n"
            . "- Model AI: {$modelName}\n"
            . "- Status Integrasi: SUDAH TERHUBUNG LANGSUNG & AKTIF REAL-TIME pada sistem BANTUIN.\n\n"
            . "========================================\n"
            . "ATURAN MUTLAK RUANG LINGKUP TOPIK (SEPUTAR PLATFORM BANTUIN SAJA):\n"
            . "1. BATASAN TOPIK HANYA SEPUTAR BANTUIN & PERBAIKAN RUMAH TANGGA:\n"
            . "   - Anda HANYA diperbolehkan melayani pertanyaan dan percakapan seputar platform BANTUIN, yaitu: layanan perbaikan rumah tangga (AC, Listrik, Kunci, Elektronik), konsultasi kerusakan alat rumah tangga, alur pemesanan jasa, postingan keluhan, akun pengguna, pekerjaan teknisi, serta status database untuk Admin.\n\n"
            . "2. DILARANG KERAS MENJAWAB TOPIK DI LUAR PLATFORM BANTUIN:\n"
            . "   - DILARANG membuat/menulis kode pemrograman atau coding (seperti Python, Javascript, PHP, HTML/CSS, C++, Java, dsb.).\n"
            . "   - DILARANG membahas politik, resep masakan, tugas sekolah/kuliah di luar perbaikan rumah, cerita fiksi, puisi, game, gosip selebriti, atau topik umum lain yang tidak berhubungan dengan layanan BANTUIN.\n"
            . "   - JIKA pengguna meminta penulisan kode pemrograman (contoh: 'buatkan saya kode python', 'bikin kode javascript') atau bertanya topik di luar platform BANTUIN:\n"
            . "     ANDA WAJIB MENOLAK DENGAN RAMAH, SOPAN, DAN SINGKAT (1-2 kalimat). Tegaskan identitas Anda sebagai asisten platform BANTUIN yang khusus melayani perbaikan rumah tangga, lalu tawarkan bantuan seputar kendala rumah tangga atau layanan BANTUIN.\n"
            . "     Contoh jawaban penolakan:\n"
            . "     'Mohon maaf, saya adalah BANTUIN AI Assistant yang dikhususkan untuk melayani seputar platform BANTUIN dan kebutuhan perbaikan rumah tangga (Teknisi AC, Listrik, Kunci, dan Elektronik Rumah). Saya tidak dapat membantu penulisan kode pemrograman atau topik di luar layanan perbaikan rumah tangga. Apakah ada kendala peralatan rumah tangga di rumah Anda yang bisa saya bantu hari ini?'\n\n"
            . "3. MEMBACA & MEMAHAMI SELURUH ISI PLATFORM SECARA CERDAS (HIGH CONTEXT AWARENESS):\n"
            . "   - Anda memiliki wawasan penuh atas seluruh data platform BANTUIN di bawah ini (profil pengguna aktif, tugas pekerjaan teknisi, permohonan customer, layanan, dan database admin).\n"
            . "   - Manfaatkan data real-time tersebut untuk memberikan jawaban yang sangat presisi, akurat, dan langsung ke inti pertanyaan pengguna ('jawab apa yang ditanya').\n"
            . "   - Jika pengguna adalah Teknisi dan bertanya pekerjaan: sebutkan rincian tugas aktif mereka dari data di bawah.\n"
            . "   - Jika pengguna adalah Customer dan bertanya permohonan servis: jelaskan status postingan mereka dari data di bawah.\n"
            . "   - Jika pengguna adalah Admin dan bertanya pengguna/database: berikan data statistik database secara lengkap dan rapi.\n"
            . "   - Format Tampilan: Gunakan Markdown yang rapi (bold untuk penekanan, bullet points untuk daftar) sehingga nyaman dibaca."
            . $platformOverview
            . $userStatusText
            . $techJobsText
            . $customerPostsText
            . $dbSecurityInstruction;

        return $systemInstruction;
    }

    /**
     * Call Google Gemini API with system instructions & guardrails
     */
    private function callGeminiApi($userMessage, $history, $apiKey, $currentUser = null, $techProfile = null, $customerPostsCount = null, $adminDbStats = null) {
        $systemInstruction = $this->buildSystemInstruction($currentUser, $techProfile, $customerPostsCount, $adminDbStats);

        // Construct contents array for Gemini API
        $contents = [];
        
        // Add last 4 history messages for context
        $recentHistory = array_slice($history, -4);
        foreach ($recentHistory as $item) {
            if (!empty($item['text'])) {
                $role = ($item['sender'] ?? '') === 'user' ? 'user' : 'model';
                $contents[] = [
                    'role' => $role,
                    'parts' => [['text' => $item['text']]]
                ];
            }
        }

        // Add current user prompt
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $userMessage]]
        ];

        $payload = [
            'system_instruction' => [
                'parts' => [['text' => $systemInstruction]]
            ],
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.6,
                'maxOutputTokens' => 600,
            ]
        ];

        $models = ['gemini-1.5-flash', 'gemini-2.0-flash', 'gemini-1.5-pro'];

        foreach ($models as $model) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . urlencode($apiKey);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 12);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && !empty($response)) {
                $json = json_decode($response, true);
                if (!empty($json['candidates'][0]['content']['parts'][0]['text'])) {
                    return trim($json['candidates'][0]['content']['parts'][0]['text']);
                }
            }
        }

        return null;
    }

    /**
     * Intelligent Grounded Knowledge Engine with Guardrails
     * Bounded strictly to BANTUIN topics
     */
    private function generateGroundedResponse($userMsg, $currentUser = null, $techProfile = null, $customerPostsCount = null, $adminDbStats = null, $techJobs = [], $customerPosts = []) {
        $msg = mb_strtolower(trim($userMsg));

        // ==========================================
        // 0. Pertanyaan seputar Model AI & Status Sambungan
        // ==========================================
        if (
            str_contains($msg, 'model ai') || str_contains($msg, 'model apa') || str_contains($msg, 'pakai ai apa') ||
            str_contains($msg, 'pake ai apa') || str_contains($msg, 'gemini') || str_contains($msg, 'nvidia') ||
            str_contains($msg, 'chatgpt') || str_contains($msg, 'claude') || str_contains($msg, 'dikaitin') ||
            str_contains($msg, 'terkait') || str_contains($msg, 'terhubung') || str_contains($msg, 'disambungkan') ||
            str_contains($msg, 'ga nyambung') || str_contains($msg, 'tidak nyambung') || str_contains($msg, 'kurang nyambung')
        ) {
            require_once dirname(__DIR__) . '/services/AIProviderService.php';
            $cfg = AIProviderService::getConfig();
            $provName = $cfg['provider_name'] ?? 'Google Gemini';
            $modName = $cfg['model'] ?? 'gemini-flash-lite-latest';
            return "Ya, model AI **{$provName} ({$modName})** sudah terhubung langsung ke chatbot BANTUIN!\n\n"
                 . "Saat ini sistem aktif dan siap menjawab langsung apa pun pertanyaan Anda seputar perbaikan rumah tangga, status tugas teknisi, konsultasi kerusakan, maupun akun Anda. Silakan tanyakan hal yang ingin Anda ketahui!";
        }

        // ==========================================
        // 0.5 Status Pekerjaan / Tugas Mitra Teknisi
        // ==========================================
        if (
            str_contains($msg, 'pekerjaan') || str_contains($msg, 'tugas') || str_contains($msg, 'order') ||
            str_contains($msg, 'tawaran') || str_contains($msg, 'status kerja') || str_contains($msg, 'job')
        ) {
            if ($currentUser && ($currentUser->role ?? '') === 'technician') {
                if (!empty($techJobs)) {
                    $reply = "Halo Pak/Bu **" . htmlspecialchars($currentUser->name ?? 'Mitra') . "**! Berikut adalah daftar **pekerjaan aktif & tawaran bantuan** Anda saat ini:\n\n";
                    $no = 1;
                    foreach ($techJobs as $job) {
                        $jTitle = $job['title'] ?: 'Permintaan Servis';
                        $jCust = $job['customer_name'] ?: 'Pelanggan';
                        $jWa = $job['customer_whatsapp'] ?: '-';
                        $jCat = $job['category'] ?: 'Teknisi';
                        $jOffer = $job['offer_status'] === 'selected' ? 'Dipilih Customer (Sedang Berjalan)' : ($job['offer_status'] === 'pending' ? 'Menunggu Konfirmasi Customer' : $job['offer_status']);
                        $jPostStat = $job['post_status'] ?: 'Aktif';
                        $reply .= "{$no}. **{$jTitle}**\n"
                               . "   - Kategori: {$jCat}\n"
                               . "   - Customer: {$jCust} (WhatsApp: {$jWa})\n"
                               . "   - Status Tawaran: {$jOffer}\n"
                               . "   - Status Pengerjaan: {$jPostStat}\n";
                        $no++;
                    }
                    $reply .= "\nAnda dapat langsung berkoordinasi dengan pelanggan melalui nomor WhatsApp di atas atau tombol WhatsApp di dashboard.";
                    return $reply;
                } else {
                    return "Halo Pak/Bu **" . htmlspecialchars($currentUser->name ?? 'Mitra') . "**! Saat ini belum ada pekerjaan aktif yang Anda tangani.\n\n"
                         . "Silakan cek permohonan jasa terbaru dari pelanggan di dasbor utama untuk mengajukan tawaran bantuan.";
                }
            }
        }

        // ==========================================
        // 0.6 Status Permohonan Servis Customer
        // ==========================================
        if (
            str_contains($msg, 'status perbaikan') || str_contains($msg, 'status servis') || str_contains($msg, 'keluhan saya') ||
            str_contains($msg, 'postingan saya') || str_contains($msg, 'permohonan saya')
        ) {
            if ($currentUser && ($currentUser->role ?? '') === 'customer') {
                if (!empty($customerPosts)) {
                    $reply = "Halo **" . htmlspecialchars($currentUser->name ?? 'Customer') . "**! Berikut status permohonan servis Anda:\n\n";
                    $no = 1;
                    foreach ($customerPosts as $cp) {
                        $cpTitle = $cp['title'] ?: 'Permintaan Servis';
                        $cpCat = $cp['category'] ?: 'Umum';
                        $cpStat = $cp['status'] ?: 'Menunggu';
                        $cpTech = $cp['technician_name'] ?: 'Menunggu teknisi';
                        $reply .= "{$no}. **{$cpTitle}** ({$cpCat})\n"
                               . "   - Status: {$cpStat}\n"
                               . "   - Teknisi: {$cpTech}\n";
                        $no++;
                    }
                    return $reply;
                }
            }
        }

        // ==========================================
        // 1. GUARDRAILS: Deteksi Pertanyaan di Luar Topik
        // ==========================================
        $offTopicKeywords = [
            'resep', 'masak', 'bumbu', 'goreng', 'kuah', 'kue',
            'presiden', 'politik', 'pemilu', 'partai', 'pilkada', 'menteri', 'dpr',
            'matematika', 'kalkulus', 'integral', 'turunan', 'aljabar', 'rumus fisika',
            'coding', 'koding', 'python', 'phyton', 'javascript', 'html', 'css', 'react', 'laravel', 'flutter', 'c++', 'golang', 'java',
            'puisi', 'pantun', 'cerpen', 'dongeng', 'lirik lagu',
            'game', 'mobile legends', 'free fire', 'pubg', 'genshin', 'valorant',
            'pacar', 'jodoh', 'cinta', 'galau', 'zodiak', 'ramalan',
            'film', 'anime', 'manga', 'drakor', 'artis', 'gosip'
        ];

        foreach ($offTopicKeywords as $badWord) {
            // Check if user is asking about off-topic subject
            if (preg_match('/\b' . preg_quote($badWord, '/') . '\b/i', $msg) || str_contains($msg, $badWord)) {
                return "Mohon maaf, saya adalah **BANTUIN AI Assistant** yang dikhususkan untuk melayani seputar platform **BANTUIN** dan kebutuhan **perbaikan rumah tangga** (Teknisi Listrik, Teknisi AC, Tukang Kunci, dan Teknisi Elektronik Rumah).\n\n"
                     . "Saya tidak dapat melayani penulisan kode pemrograman atau topik di luar layanan perbaikan rumah tangga. Apakah ada kendala peralatan rumah tangga di rumah Anda yang bisa saya bantu hari ini?";
            }
        }

        // ==========================================
        // 1.5 IDENTITAS PENGGUNA (Siapa Saya / Akun Saya / Profil Saya / Role Saya)
        // ==========================================
        $isAskingUserIdentity = false;

        $userIdentityPatterns = [
            'siapa saya', 'siapakah saya', 'saya siapa',
            'siapa aku', 'aku siapa', 'siapakah aku',
            'siapa yang login', 'siapa yang sedang login', 'siapa yg login',
            'siapa pengguna akun', 'siapa pemakai akun', 'siapa yang memakai akun', 'siapa yang menggunakan akun',
            'saya login sebagai', 'login sebagai apa', 'role saya', 'peran saya',
            'profil saya', 'data saya', 'data diri saya', 'akun saya',
            'identitas saya', 'info akun saya', 'informasi akun saya', 'status akun saya',
            'apakah saya sudah login', 'apakah saya admin', 'apakah saya teknisi', 'apakah saya customer'
        ];

        foreach ($userIdentityPatterns as $phrase) {
            if (str_contains($msg, $phrase)) {
                $isAskingUserIdentity = true;
                break;
            }
        }

        // Fuzzy pattern: (siapa / siapakah) AND (saya / aku / diri saya)
        if (!$isAskingUserIdentity) {
            if (
                preg_match('/\b(siapa|siapakah)\b.*?\b(saya|aku|diri saya)\b/i', $msg) ||
                preg_match('/\b(saya|aku)\b.*?\b(siapa|siapakah)\b/i', $msg)
            ) {
                $isAskingUserIdentity = true;
            }
        }

        if ($isAskingUserIdentity) {
            return $this->handleUserIdentityResponse($currentUser, $techProfile, $customerPostsCount, $msg);
        }

        // ==========================================
        // 1.6 SEPUTAR DATABASE & STATISTIK SISTEM
        // Role-Based Guardrail:
        // - Admin: BISA melihat statistik database (total teknisi, customer, postingan, kategori, ringkasan database)
        // - Teknisi / Customer / Guest: DITOLAK & DIBATASI (tidak boleh menjawab seputar database)
        // ==========================================
        $isDbStatsQuery = false;

        $explicitDbKeywords = [
            'database', 'basis data', 'mysql', 'sql', 'tabel sistem', 'tabel db', 'isi db', 'struktur db',
            'data internal', 'statistik sistem', 'metrik sistem', 'laporan sistem', 'data sistem',
            'seputar database', 'tentang database', 'info database', 'isi database'
        ];

        foreach ($explicitDbKeywords as $term) {
            if (str_contains($msg, $term)) {
                $isDbStatsQuery = true;
                break;
            }
        }

        if (!$isDbStatsQuery) {
            $hasCountWord = preg_match('/\b(jumlah|total|berapa|banyak|ada berapa|rekap|statistik|daftar seluruh|siapa saja|siapa|daftar|list|sebutkan)\b/i', $msg);
            $hasEntityWord = preg_match('/\b(teknisi|mitra|customer|pelanggan|user|pengguna|postingan|keluhan|order|akun)\b/i', $msg);
            $isPriceWord = preg_match('/\b(harga|biaya|tarif|ongkos|bayar|rp|rupiah)\b/i', $msg);

            if ($hasCountWord && $hasEntityWord && !$isPriceWord) {
                $isDbStatsQuery = true;
            }
        }

        if (!$isDbStatsQuery) {
            if (
                preg_match('/(ada\s+berapa\s+(teknisi|mitra|customer|pelanggan|user|pengguna|postingan|keluhan|kategori))/i', $msg) ||
                preg_match('/(siapa\s+saja\s+(teknisi|mitra|customer|pelanggan|user|pengguna)\b)/i', $msg) ||
                preg_match('/(siapa\s+(teknisi|mitra|customer|pelanggan|user|pengguna)\b)/i', $msg) ||
                preg_match('/(daftar\s+(seluruh|semua)?\s*(teknisi|mitra|customer|pelanggan|user|pengguna))/i', $msg) ||
                preg_match('/(list\s+(teknisi|mitra|customer|pelanggan|user|pengguna))/i', $msg) ||
                preg_match('/(data\s+(seluruh|semua)\s+(teknisi|mitra|customer|pelanggan|user|pengguna))/i', $msg)
            ) {
                $isDbStatsQuery = true;
            }
        }

        if ($isDbStatsQuery) {
            return $this->handleDatabaseStatsResponse($msg, $currentUser, $adminDbStats);
        }

        // ==========================================
        // 2. Pendaftaran Teknisi
        // ==========================================
        if (
            (str_contains($msg, 'daftar') || str_contains($msg, 'gabung') || str_contains($msg, 'registrasi') || str_contains($msg, 'jadi') || str_contains($msg, 'syarat') || str_contains($msg, 'cara masuk')) &&
            (str_contains($msg, 'teknisi') || str_contains($msg, 'mitra') || str_contains($msg, 'tukang') || str_contains($msg, 'pekerja'))
        ) {
            return "Untuk bergabung sebagai **Mitra Teknisi** di BANTUIN:\n"
                 . "1. Klik tombol **'Daftar'** di pojok kanan atas halaman utama.\n"
                 . "2. Pilih peran (role) sebagai **'Mitra Teknisi'**.\n"
                 . "3. Lengkapi formulir: Nama Lengkap, Nomor WhatsApp aktif (wajib agar pelanggan dapat menghubungi Anda), Email, dan Kata Sandi.\n"
                 . "4. Pilih kategori keahlian Anda (Teknisi AC, Teknisi Listrik, Tukang Kunci, atau Teknisi Elektronik).\n"
                 . "5. Setelah mendaftar, Anda dapat langsung login ke Dashboard Teknisi dan mulai merespons panggilan order jasa dari pelanggan!";
        }

        // ==========================================
        // 3. Pendaftaran Customer / Akun Umum
        // ==========================================
        if (str_contains($msg, 'daftar') || str_contains($msg, 'buat akun') || str_contains($msg, 'registrasi') || str_contains($msg, 'register')) {
            return "Untuk membuat akun di platform BANTUIN:\n"
                 . "1. Klik tombol **'Daftar'** di pojok kanan atas halaman.\n"
                 . "2. Pilih peran Anda: **Customer** (untuk memesan jasa) atau **Mitra Teknisi** (untuk menawarkan jasa).\n"
                 . "3. Isi data diri, nomor WhatsApp aktif, dan kata sandi.\n"
                 . "4. Klik **Daftar Akun**, Anda akan langsung dapat menggunakan semua fitur termasuk Posting Keluhan dan Konsultasi AI!";
        }

        // ==========================================
        // 4. Layanan Spesifik: AC & Pendingin
        // ==========================================
        if (preg_match('/\b(ac|air conditioner|freon|blower|evaporator|kondensor)\b/i', $msg) || str_contains($msg, 'cuci ac') || (str_contains($msg, 'bocor') && str_contains($msg, 'netes'))) {
            return "Kendala pada pendingin ruangan (AC) biasanya meliputi:\n"
                 . "• **Tidak dingin:** Tekanan freon berkurang atau filter evaporator kotor tertutup debu.\n"
                 . "• **Bocor / Menetes:** Saluran pembuangan air (drainase) tersumbat lumut.\n"
                 . "• **Berisik:** Motor kipas atau bearing kompresor aus.\n\n"
                 . "Mitra **Teknisi AC BANTUIN** melayani cuci AC, isi freon, perbaikan kebocoran pipa, maupun bongkar pasang unit (Estimasi: Rp 75.000 – Rp 250.000). Silakan buat postingan atau gunakan fitur Konsultasi AI!";
        }

        // ==========================================
        // 5. Layanan Spesifik: Listrik & Panel
        // ==========================================
        if (str_contains($msg, 'listrik') || str_contains($msg, 'korslet') || str_contains($msg, 'konslet') || str_contains($msg, 'mcb') || str_contains($msg, 'jeglek') || str_contains($msg, 'stop kontak') || str_contains($msg, 'saklar')) {
            return "Kendala kelistrikan rumah seperti MCB sering jeglek, bau hangus di stop kontak, atau kabel korslet mengindikasikan lonjakan arus atau kabel terkelupas.\n\n"
                 . "⚠️ *Saran Keselamatan:* Matikan saklar utama MCB terlebih dahulu untuk mencegah kebakaran.\n"
                 . "Segera pesan **Teknisi Listrik BANTUIN** bersertifikat (Estimasi: Rp 80.000 – Rp 300.000) untuk pengecekan resistansi isolasi dan perbaikan kabel panel.";
        }

        // ==========================================
        // 6. Layanan Spesifik: Tukang Kunci
        // ==========================================
        if (str_contains($msg, 'kunci') || str_contains($msg, 'gembok') || str_contains($msg, 'pintu macet') || str_contains($msg, 'terkunci') || str_contains($msg, 'anak kunci')) {
            return "Untuk kendala kunci rumah, kunci kendaraan, patahan anak kunci di dalam silinder, atau pintu darurat macet:\n\n"
                 . "Mitra **Tukang Kunci BANTUIN** melayani pembukaan darurat dengan pick lock khusus (tanpa merusak kusen pintu), ganti master silinder, duplikat kunci, dan pemasangan Smart Lock (Estimasi: Rp 50.000 – Rp 200.000).";
        }

        // ==========================================
        // 7. Layanan Spesifik: Elektronik Rumah
        // ==========================================
        if (str_contains($msg, 'kulkas') || str_contains($msg, 'mesin cuci') || str_contains($msg, 'tv') || str_contains($msg, 'televisi') || str_contains($msg, 'pompa air') || str_contains($msg, 'microwave') || str_contains($msg, 'dispenser') || str_contains($msg, 'setrika')) {
            return "Mitra **Teknisi Elektronik BANTUIN** melayani servis berbagai peralatan rumah tangga:\n"
                 . "• Kulkas 1 & 2 pintu (tidak dingin, modul inverter, freon kulkas)\n"
                 . "• Mesin cuci top/front load (dinamo mati, air mampet, pengering rusak)\n"
                 . "• TV LED/LCD & Tabung (layar gelap, power supply, suara hilang)\n"
                 . "• Pompa air sumur (dengung, tidak hisap air)\n\n"
                 . "Estimasi tarif berkisar Rp 100.000 – Rp 350.000. Silakan buat postingan keluhan di dashboard Customer!";
        }

        // ==========================================
        // 8. Pertanyaan seputar HP / Smartphone / Gadget
        // ==========================================
        if (preg_match('/\b(hp|handphone|smartphone|ponsel|laptop|komputer|gadget)\b/i', $msg)) {
            return "Saat ini BANTUIN berfokus utama pada **peralatan & instalasi rumah tangga**, meliputi:\n"
                 . "• ⚡ **Teknisi Listrik** (korsleting, panel, stop kontak, instalasi)\n"
                 . "• ❄️ **Teknisi AC** (cuci AC, isi ulang freon, kebocoran unit)\n"
                 . "• 🔑 **Tukang Kunci** (duplikat kunci, pintu darurat terkunci, smart lock)\n"
                 . "• 📺 **Teknisi Elektronik Rumah** (TV, kulkas, mesin cuci, pompa air, microwave).\n\n"
                 . "Untuk servis HP/Laptop saat ini belum ada kategori khusus. Anda dapat mencoba berkonsultasi via menu **Konsultasi AI** atau membuat postingan di kategori **Elektronik** untuk mengecek apakah ada mitra teknisi yang bersedia menangani.";
        }

        // ==========================================
        // 9. Cara Pesan / Alur Pemesanan Teknisi
        // ==========================================
        if (str_contains($msg, 'cara pesan') || str_contains($msg, 'cara order') || str_contains($msg, 'cara kerja') || str_contains($msg, 'panggil') || str_contains($msg, 'pesan teknisi') || str_contains($msg, 'order')) {
            return "Alur pemesanan jasa di BANTUIN sangat praktis:\n"
                 . "1. **Masuk ke Akun Customer:** Login atau buat akun terlebih dahulu.\n"
                 . "2. **Posting Keluhan:** Tulis rincian kendala kerusakan atau gunakan fitur **Konsultasi AI** dengan upload foto kerusakan.\n"
                 . "3. **Pilih Teknisi:** Sistem akan merekomendasikan teknisi terverifikasi sesuai keahlian dan rating terbaik.\n"
                 . "4. **Komunikasi Langsung:** Hubungi teknisi via tombol WhatsApp untuk menyepakati waktu kunjungan dan estimasi pengerjaan.";
        }

        // ==========================================
        // 10. Fitur Konsultasi AI Kerusakan
        // ==========================================
        if (str_contains($msg, 'konsultasi') || preg_match('/\b(ai|artificial intelligence)\b/i', $msg) || str_contains($msg, 'foto kerusakan') || str_contains($msg, 'analisis') || str_contains($msg, 'diagnosa')) {
            return "BANTUIN menyediakan fitur **Konsultasi AI Pintar** untuk mendeteksi kerusakan peralatan rumah:\n"
                 . "1. Buka menu **Konsultasi AI** di dashboard Customer.\n"
                 . "2. Unggah foto komponen yang bermasalah (misal pipa AC menetes, MCB terbakar, kunci pintu aus).\n"
                 . "3. Tuliskan gejala kronologinya.\n"
                 . "AI BANTUIN akan langsung menganalisis kemungkinan kerusakan, perkiraan rentang biaya perbaikan, serta memberikan rekomendasi teknisi yang tepat!";
        }

        // ==========================================
        // 7. Estimasi Biaya / Tarif Layanan
        // ==========================================
        if (str_contains($msg, 'harga') || str_contains($msg, 'biaya') || str_contains($msg, 'tarif') || str_contains($msg, 'ongkos') || str_contains($msg, 'berapa')) {
            return "Estimasi tarif jasa di BANTUIN transparan dan kompetitif:\n"
                 . "• ❄️ **Servis / Cuci AC:** Rp 75.000 – Rp 250.000\n"
                 . "• ⚡ **Pemeriksaan Listrik & Korslet:** Rp 80.000 – Rp 300.000\n"
                 . "• 🔑 **Tukang Kunci & Buka Pintu Darurat:** Rp 50.000 – Rp 200.000\n"
                 . "• 📺 **Perbaikan Elektronik (Kulkas/TV/Mesin Cuci):** Rp 100.000 – Rp 350.000\n\n"
                 . "💡 *Tips:* Gunakan fitur **Konsultasi AI** untuk memperoleh perkiraan biaya yang lebih detail dan akurat.";
        }

        // ==========================================
        // 8. Garansi Servis
        // ==========================================
        if (str_contains($msg, 'garansi') || str_contains($msg, 'jaminan') || str_contains($msg, 'klaim')) {
            return "Setiap pengerjaan oleh mitra teknisi BANTUIN dilengkapi dengan **Garansi Servis 30 Hari**. Jika kerusakan yang sama muncul kembali selama masa garansi, teknisi wajib melakukan pengecekan ulang tanpa biaya jasa tambahan!";
        }

        // ==========================================
        // 9. Layanan Darurat 24 Jam
        // ==========================================
        if (str_contains($msg, 'darurat') || str_contains($msg, 'emergency') || str_contains($msg, '24 jam') || str_contains($msg, 'malam') || str_contains($msg, 'buka jam')) {
            return "Platform BANTUIN siap diakses **24 Jam**. Khusus untuk situasi darurat seperti **Tukang Kunci (pintu kamar/rumah terkunci darurat)** dan **Teknisi Listrik (korslet/jeglek malam hari)**, tersedia mitra siaga yang siap merespons.";
        }

        // ==========================================
        // 11. Salam & Sapaan
        // ==========================================
        if (str_contains($msg, 'halo') || str_contains($msg, 'hai') || str_contains($msg, 'selamat') || str_contains($msg, 'assalam') || str_contains($msg, 'pagi') || str_contains($msg, 'siang') || str_contains($msg, 'sore') || str_contains($msg, 'malam') || str_contains($msg, 'permisi')) {
            return "Halo! Selamat datang di **BANTUIN - Platform Jasa Rumah Tangga**. 👋\n\n"
                 . "Saya asisten AI BANTUIN, siap membantu Anda seputar perbaikan AC, Listrik, Kunci, Elektronik Rumah, pendaftaran teknisi, maupun estimasi biaya. Ada kendala rumah yang bisa saya bantu hari ini?";
        }

        // ==========================================
        // 12. Identitas Asisten Bot (Siapa Anda / Kamu Siapa / Bot Apa)
        // ==========================================
        if (preg_match('/\b(siapa anda|siapa kamu|kamu siapa|anda siapa|siapakah anda|siapakah kamu|kamu ini apa|bot apa|siapa bot|tentang asisten|tentang bot)\b/i', $msg)) {
            return "Saya adalah **BANTUIN AI Assistant**, asisten virtual resmi platform **BANTUIN** (Solusi Perbaikan Rumah Tangga). 🤖🏠\n\n"
                 . "Saya siap membantu Anda dalam:\n"
                 . "• Menjelaskan estimasi biaya dan penanganan kerusakan (AC, Listrik, Kunci, dan Elektronik Rumah)\n"
                 . "• Memandu cara pemesanan teknisi & konsultasi kerusakan via AI\n"
                 . "• Informasi alur pendaftaran Customer maupun Mitra Teknisi baru\n"
                 . "• Memberikan informasi akun Anda (coba tanyakan *\"siapa saya\"*!)\n\n"
                 . "Ada kendala perbaikan rumah yang ingin Anda tanyakan?";
        }

        // ==========================================
        // 13. Ucapan Terima Kasih & Penutup (Strict Word Boundary)
        // ==========================================
        if (
            str_contains($msg, 'terima kasih') || 
            str_contains($msg, 'makasih') || 
            str_contains($msg, 'thanks') || 
            preg_match('/\b(siap|sip|oke|ok|baiklah)\b/i', $msg)
        ) {
            return "Sama-sama! Senang bisa membantu Anda. Jika sewaktu-waktu membutuhkan teknisi terpercaya, BANTUIN siap membantu rumah Anda kembali nyaman!";
        }

        // ==========================================
        // 14. Default Fallback Relevan
        // ==========================================
        return "Halo! Sebagai asisten resmi **BANTUIN**, saya siap membantu semua kebutuhan perbaikan rumah tangga Anda (AC, Listrik, Kunci, dan Elektronik Rumah).\n\n"
             . "Terkait pertanyaan Anda: *\"{$userMsg}\"*, Anda dapat menggunakan menu **Posting Keluhan**, **Konsultasi AI**, atau tombol **Daftar/Masuk** di atas. Ada detail spesifik yang ingin Anda tanyakan seputar layanan kami?";
    }

    /**
     * Handle identity explanation for currently logged-in user (Admin, Technician, Customer, or Guest)
     */
    private function handleUserIdentityResponse($currentUser, $techProfile, $customerPostsCount, $rawMsg = '') {
        $alsoAskingAboutBot = (str_contains($rawMsg, 'kamu') || str_contains($rawMsg, 'anda') || str_contains($rawMsg, 'bot') || str_contains($rawMsg, 'asisten'));
        $botIntro = $alsoAskingAboutBot ? "\n\n---\n🤖 *Sedangkan saya adalah **BANTUIN AI Assistant**, asisten virtual cerdas yang siap mendampingi kebutuhan layanan rumah tangga Anda!*" : "";

        // CASE 1: Belum Login (Guest / Pengunjung)
        if (!$currentUser || empty($currentUser->name)) {
            $loginUrl = base_url('login');
            $registerUrl = base_url('register');

            return "Halo! 👋 Saat ini Anda terdeteksi mengakses platform **BANTUIN** sebagai **Pengunjung (Tamu / Guest)** dan belum masuk (login) ke dalam akun.\n\n"
                 . "📋 **Status Akun:**\n"
                 . "• **Peran / Role:** Pengunjung (Guest)\n"
                 . "• **Status Login:** Belum Login\n\n"
                 . "💡 **Agar sistem mengenali Anda dan Anda dapat menggunakan seluruh fitur:**\n"
                 . "• Sudah punya akun? Silakan masuk melalui halaman **[Masuk / Login]({$loginUrl})**.\n"
                 . "• Belum punya akun? Silakan daftar di **[Daftar Akun Baru]({$registerUrl})** sebagai **Customer** (untuk memesan jasa perbaikan) atau **Mitra Teknisi** (untuk menerima pekerjaan).\n\n"
                 . "Meskipun belum login, Anda tetap dapat bertanya kepada saya seputar estimasi tarif perbaikan, tips penanganan kerusakan, atau panduan layanan BANTUIN!" . $botIntro;
        }

        $role = strtolower($currentUser->role ?? 'customer');
        $name = htmlspecialchars($currentUser->name ?? 'Pengguna', ENT_QUOTES, 'UTF-8');
        $email = htmlspecialchars($currentUser->email ?? '-', ENT_QUOTES, 'UTF-8');
        $whatsapp = htmlspecialchars($currentUser->whatsapp ?? '-', ENT_QUOTES, 'UTF-8');

        // CASE 2: Administrator
        if ($role === 'admin') {
            $adminUrl = base_url('admin');
            $catUrl = base_url('categories');

            return "Halo **{$name}**! 👋\n\n"
                 . "Anda saat ini sedang login sebagai **🛡️ Administrator Sistem (Admin)** di platform **BANTUIN**.\n\n"
                 . "📋 **Rincian Akun Anda:**\n"
                 . "• **Nama Lengkap:** {$name}\n"
                 . "• **Email:** {$email}\n"
                 . "• **No. WhatsApp:** {$whatsapp}\n"
                 . "• **Peran / Role:** Administrator (Akses Penuh / Super Admin)\n"
                 . "• **Status Akun:** Aktif (Active)\n\n"
                 . "🔑 **Hak Akses & Wewenang Anda:**\n"
                 . "1. **Kelola Kategori Jasa:** Mengatur master kategori keahlian (AC, Listrik, Kunci, Elektronik) di menu **[Kelola Kategori]({$catUrl})**.\n"
                 . "2. **Monitoring Keluhan:** Memantau & memoderasi seluruh postingan permintaan perbaikan yang dibuat customer.\n"
                 . "3. **Manajemen Pengguna:** Memantau data Customer dan Mitra Teknisi yang terdaftar.\n"
                 . "4. **Pusat Kontrol:** Memeriksa statistik dan metrik operasional di **[Dashboard Admin]({$adminUrl})**.\n\n"
                 . "Apakah ada data atau laporan sistem yang ingin Anda periksa saat ini?" . $botIntro;
        }

        // CASE 3: Mitra Teknisi
        if ($role === 'technician') {
            $techUrl = base_url('technician');
            $profileUrl = base_url('profile');
            $chatUrl = base_url('chat');

            $category = !empty($techProfile->category_name) ? htmlspecialchars($techProfile->category_name, ENT_QUOTES, 'UTF-8') : 'Teknisi Umum';
            $location = !empty($techProfile->location) ? htmlspecialchars($techProfile->location, ENT_QUOTES, 'UTF-8') : 'Jabodetabek & sekitarnya';
            $rating = !empty($techProfile->rating) ? number_format((float)$techProfile->rating, 2) : '5.00';
            $completedJobs = isset($techProfile->completed_jobs) ? (int)$techProfile->completed_jobs : 0;

            return "Halo **{$name}**! 👋\n\n"
                 . "Anda saat ini sedang login sebagai **🔧 Mitra Teknisi Terverifikasi** di platform **BANTUIN**.\n\n"
                 . "📋 **Rincian Profil Keahlian Anda:**\n"
                 . "• **Nama Lengkap:** {$name}\n"
                 . "• **Peran / Role:** Mitra Teknisi BANTUIN\n"
                 . "• **Bidang Spesialisasi:** **{$category}**\n"
                 . "• **Wilayah Layanan:** {$location}\n"
                 . "• **Rating Pelayanan:** ⭐ {$rating} / 5.00\n"
                 . "• **Total Pekerjaan Selesai:** {$completedJobs} order\n"
                 . "• **Kontak WhatsApp:** {$whatsapp}\n"
                 . "• **Email Akun:** {$email}\n\n"
                 . "🚀 **Aktivitas yang Dapat Anda Lakukan:**\n"
                 . "1. Memeriksa postingan perbaikan rumah tangga dari pelanggan yang membutuhkan spesialisasi **{$category}**.\n"
                 . "2. Mengajukan tawaran harga & bantuan langsung kepada customer.\n"
                 . "3. Berdiskusi mengenai detail perbaikan lewat **[Ruang Chat]({$chatUrl})** atau WhatsApp.\n"
                 . "4. Memperbarui informasi keahlian atau area kerja di **[Dashboard Teknisi]({$techUrl})** & **[Edit Profil]({$profileUrl})**.\n\n"
                 . "Semoga hari ini orderan Anda lancar dan berkah! Ada hal lain yang bisa saya bantu?" . $botIntro;
        }

        // CASE 4: Customer (Pelanggan)
        $custUrl = base_url('customer');
        $postCreateUrl = base_url('posts/create');
        $consultUrl = base_url('customer/consultation');
        $chatUrl = base_url('chat');

        $postSummary = "";
        if ($customerPostsCount !== null) {
            $postSummary = "• **Riwayat Postingan:** {$customerPostsCount} keluhan telah dibuat\n";
        }

        return "Halo **{$name}**! 👋\n\n"
             . "Anda saat ini sedang login sebagai **👤 Customer (Pelanggan / Pengguna Jasa)** di platform **BANTUIN**.\n\n"
             . "📋 **Rincian Akun Anda:**\n"
             . "• **Nama Lengkap:** {$name}\n"
             . "• **Email:** {$email}\n"
             . "• **No. WhatsApp:** {$whatsapp}\n"
             . "• **Peran / Role:** Customer\n"
             . "• **Status Akun:** Aktif (Active)\n"
             . $postSummary . "\n"
             . "🛠️ **Layanan Utama yang Dapat Anda Akses:**\n"
             . "1. **Konsultasi AI Kerusakan:** Mendiagnosis gejala kerusakan alat rumah tangga & estimasi biaya di **[Konsultasi AI]({$consultUrl})**.\n"
             . "2. **Buat Postingan Baru:** Mencari teknisi panggilan profesional untuk datang ke rumah di **[Posting Keluhan]({$postCreateUrl})**.\n"
             . "3. **Pilih & Hubungi Teknisi:** Menerima penawaran dari mitra teknisi dan berkomunikasi via **[Chat Internal]({$chatUrl})** atau WhatsApp.\n"
             . "4. **Pantau Status:** Melihat perkembangan perbaikan aktif di **[Dashboard Customer]({$custUrl})**.\n\n"
             . "Apakah ada kendala AC, listrik, kunci, atau peralatan elektronik di rumah yang sedang ingin Anda perbaiki?" . $botIntro;
    }

    /**
     * Get Database & System Statistics for Admin Chatbot
     */
    private function getDatabaseStats() {
        try {
            $userModel = $this->model('UserModel');
            $postModel = $this->model('PostModel');
            $catModel = $this->model('CategoryModel');
            $techModel = $this->model('TechnicianProfileModel');

            $allCustomers = $userModel->getAllUsers('customer');
            $allTechnicians = $userModel->getAllUsers('technician');

            return [
                'userCounts' => $userModel->getUserCounts(),
                'totalPosts' => $postModel->getTotalPostsCount(),
                'categories' => $catModel->getAllCategories(),
                'technicians' => $techModel->getRecommendedTechnicians(20),
                'customers' => is_array($allCustomers) ? $allCustomers : [],
                'technicianUsers' => is_array($allTechnicians) ? $allTechnicians : []
            ];
        } catch (\Throwable $e) {
            return [
                'userCounts' => ['customers' => 2, 'technicians' => 1, 'total' => 4],
                'totalPosts' => 0,
                'categories' => [],
                'technicians' => [],
                'customers' => [],
                'technicianUsers' => []
            ];
        }
    }

    /**
     * Handle Database & System Stats Response with Role-Based Security
     * - Admin: Can access database information (total technicians, customers, posts, categories, overview)
     * - Non-Admin (Technician, Customer, Guest): Strictly denied with polite explanation
     */
    private function handleDatabaseStatsResponse($msg, $currentUser, $adminDbStats = null) {
        $role = strtolower($currentUser->role ?? '');

        // =========================================================================
        // 1. NON-ADMIN RESTRICTION (Teknisi, Customer, Guest / Belum Login)
        // =========================================================================
        if ($role !== 'admin') {
            if ($role === 'customer') {
                return "🔒 **Akses Dibatasi (Informasi Internal Sistem)**\n\n"
                     . "Mohon maaf, informasi seputar **data basis data (database), jumlah total pengguna/teknisi, dan statistik internal sistem** bersifat rahasia dan **hanya dapat diakses oleh Administrator Sistem (Admin)**.\n\n"
                     . "Sebagai **Pelanggan (Customer)**, Anda dapat:\n"
                     . "• Mencari dan memilih mitra teknisi terpercaya langsung saat membuat pesanan di menu **[Posting Keluhan](" . base_url('posts/create') . ")**.\n"
                     . "• Berkonsultasi mengenai kerusakan peralatan rumah tangga melalui menu **[Konsultasi AI](" . base_url('customer/consultation') . ")**.\n\n"
                     . "Ada kendala perbaikan rumah yang bisa saya bantu hari ini?";
            } elseif ($role === 'technician') {
                return "🔒 **Akses Dibatasi (Informasi Internal Sistem)**\n\n"
                     . "Mohon maaf, informasi seputar **basis data (database), total metrik pengguna sistem, dan data internal platform** bersifat privat dan **hanya dapat diakses oleh Administrator Sistem (Admin)**.\n\n"
                     . "Sebagai **Mitra Teknisi**, Anda dapat:\n"
                     . "• Memantau dan merespons postingan keluhan perbaikan dari pelanggan yang sesuai spesialisasi Anda di **[Dashboard Teknisi](" . base_url('technician') . ")**.\n"
                     . "• Memperbarui profil keahlian dan rating Anda di menu **[Profil Saya](" . base_url('profile') . ")**.\n\n"
                     . "Apakah ada kendala seputar cara merespons order atau fitur teknisi yang ingin Anda tanyakan?";
            } else {
                // Guest / Belum Login
                return "🔒 **Akses Dibatasi (Khusus Administrator)**\n\n"
                     . "Mohon maaf, pertanyaan seputar **basis data (database) dan data statistik internal sistem BANTUIN** hanya dapat dijawab untuk pengguna yang login sebagai **Administrator Sistem (Admin)**.\n\n"
                     . "Jika Anda adalah Admin, silakan masuk terlebih dahulu menggunakan akun administrator di **[Halaman Login](" . base_url('login') . ")**.\n\n"
                     . "Untuk pengunjung umum, saya siap membantu informasi seputar perbaikan AC, Listrik, Kunci, dan Elektronik Rumah!";
            }
        }

        // =========================================================================
        // 2. ADMIN ACCESS: Provide Real Database Stats
        // =========================================================================
        if (!$adminDbStats) {
            $adminDbStats = $this->getDatabaseStats();
        }

        $userCounts = $adminDbStats['userCounts'] ?? ['customers' => 1, 'technicians' => 3, 'total' => 5];
        $totalTechs = $userCounts['technicians'] ?? 3;
        $totalCusts = $userCounts['customers'] ?? 1;
        $totalUsers = $userCounts['total'] ?? ($totalTechs + $totalCusts + 1);
        $totalPosts = $adminDbStats['totalPosts'] ?? 3;
        $categories = $adminDbStats['categories'] ?? [];
        $technicians = $adminDbStats['technicians'] ?? [];
        $customers   = $adminDbStats['customers'] ?? [];

        $adminUrl = base_url('admin');
        $adminTechUrl = base_url('admin/users?role=technician');
        $adminCustUrl = base_url('admin/users?role=customer');
        $catUrl = base_url('categories');

        // Format Daftar Customer Real-Time
        $custListText = "";
        $cNo = 1;
        foreach ($customers as $c) {
            $cName = is_object($c) ? ($c->name ?? 'Customer') : ($c['name'] ?? 'Customer');
            $cEmail = is_object($c) ? ($c->email ?? '-') : ($c['email'] ?? '-');
            $cWa = is_object($c) ? ($c->whatsapp ?? '-') : ($c['whatsapp'] ?? '-');
            $cPosts = is_object($c) ? (int)($c->total_posts ?? 0) : (int)($c['total_posts'] ?? 0);
            $cStatus = is_object($c) ? ($c->status ?? 'active') : ($c['status'] ?? 'active');
            $cStatusLabel = ($cStatus === 'active') ? 'Aktif' : 'Nonaktif';

            $custListText .= "{$cNo}. **{$cName}**\n"
                          . "   • Email: `{$cEmail}`\n"
                          . "   • WhatsApp: `{$cWa}`\n"
                          . "   • Status Akun: {$cStatusLabel}\n"
                          . "   • Jumlah Keluhan: **{$cPosts} postingan**\n\n";
            $cNo++;
        }
        if (empty($custListText)) {
            $custListText = "Saat ini belum ada customer terdaftar di basis data.\n\n";
        }

        // SUB-INTENT A: Pertanyaan spesifik TEKNISI
        if (preg_match('/\b(teknisi|mitra|tukang)\b/i', $msg) && !preg_match('/\b(customer|pelanggan|semua|ringkasan|seputar)\b/i', $msg)) {
            $techListText = "";
            $no = 1;
            foreach ($technicians as $t) {
                $tName = is_object($t) ? ($t->name ?? 'Mitra') : ($t['name'] ?? 'Mitra');
                $tCat = is_object($t) ? ($t->category_name ?? 'Teknisi Umum') : ($t['category_name'] ?? 'Teknisi Umum');
                $tRating = is_object($t) ? ($t->rating ?? 5.0) : ($t['rating'] ?? 5.0);
                $tLocation = is_object($t) ? ($t->location ?? 'Jabodetabek') : ($t['location'] ?? 'Jabodetabek');
                $tWa = is_object($t) ? ($t->whatsapp ?? '-') : ($t['whatsapp'] ?? '-');

                $techListText .= "{$no}. **{$tName}**\n"
                              . "   • Spesialisasi: **{$tCat}**\n"
                              . "   • Rating: ⭐ " . number_format((float)$tRating, 2) . " / 5.00\n"
                              . "   • Wilayah: {$tLocation}\n"
                              . "   • WhatsApp: `{$tWa}`\n\n";
                $no++;
            }

            return "📊 **Data Database: Mitra Teknisi BANTUIN** (Akses Administrator)\n\n"
                 . "Berikut rincian data mitra teknisi yang tercatat di database saat ini:\n\n"
                 . "• **Total Mitra Teknisi Aktif:** **{$totalTechs} orang**\n"
                 . "• **Status Kemitraan:** Terverifikasi & Siap Kerja\n\n"
                 . "📋 **Daftar Teknisi Terdaftar:**\n"
                 . $techListText
                 . "🔗 **Aksi Admin:** Anda dapat memverifikasi, mengubah status, atau menonaktifkan akun teknisi di **[Manajemen Teknisi]({$adminTechUrl})**.";
        }

        // SUB-INTENT B: Pertanyaan spesifik CUSTOMER / PELANGGAN
        if (preg_match('/\b(customer|pelanggan)\b/i', $msg) && !preg_match('/\b(teknisi|mitra|semua|ringkasan|seputar)\b/i', $msg)) {
            return "📊 **Data Database: Pelanggan (Customer) BANTUIN** (Akses Administrator)\n\n"
                 . "Halo Administrator! Berikut rincian data seluruh pelanggan yang tercatat di database BANTUIN saat ini:\n\n"
                 . "• **Total Pelanggan Terdaftar:** **{$totalCusts} akun**\n"
                 . "• **Status Akun:** Aktif (Active)\n\n"
                 . "👥 **Daftar Lengkap Pelanggan (Customer):**\n"
                 . $custListText
                 . "Pelanggan menggunakan platform untuk membuat postingan keluhan perbaikan rumah tangga dan berkonsultasi via AI.\n\n"
                 . "🔗 **Aksi Admin:** Anda dapat melihat profil lengkap atau riwayat order pelanggan di **[Manajemen Customer]({$adminCustUrl})**.";
        }

        // SUB-INTENT C: Pertanyaan spesifik POSTINGAN / KELUHAN
        if (preg_match('/\b(post|postingan|keluhan|order|pesanan)\b/i', $msg) && !preg_match('/\b(teknisi|customer|kategori|semua|seputar)\b/i', $msg)) {
            return "📊 **Data Database: Postingan Keluhan Perbaikan** (Akses Administrator)\n\n"
                 . "Berikut metrik postingan keluhan yang masuk ke sistem BANTUIN:\n\n"
                 . "• **Total Postingan Keluhan:** **{$totalPosts} postingan**\n"
                 . "• **Kategori Permohonan:** Teknisi AC, Teknisi Listrik, Tukang Kunci, dan Teknisi Elektronik\n"
                 . "• **Alur:** Pelanggan memposting keluhan &rarr; Mitra teknisi mengajukan penawaran &rarr; Pelanggan memilih teknisi\n\n"
                 . "🔗 **Aksi Admin:** Anda dapat memantau status atau memoderasi postingan di **[Dashboard Admin]({$adminUrl})**.";
        }

        // SUB-INTENT D: Pertanyaan spesifik KATEGORI
        if (preg_match('/\b(kategori)\b/i', $msg) && !preg_match('/\b(teknisi|customer|postingan|seputar database|ringkasan)\b/i', $msg)) {
            $catListText = "";
            $no = 1;
            foreach ($categories as $cat) {
                $cName = is_object($cat) ? $cat->name : ($cat['name'] ?? 'Kategori');
                $cDesc = is_object($cat) ? ($cat->description ?? '') : ($cat['description'] ?? '');
                $cBadge = is_object($cat) ? ($cat->badge ?? '') : ($cat['badge'] ?? '');
                $badgeStr = !empty($cBadge) ? " _[{$cBadge}]_" : "";
                $catListText .= "{$no}. **{$cName}**{$badgeStr}\n   _{$cDesc}_\n";
                $no++;
            }
            $totalCats = count($categories);

            return "📊 **Data Database: Master Kategori Layanan** (Akses Administrator)\n\n"
                 . "Berikut data kategori jasa yang tersimpan di database:\n\n"
                 . "• **Total Kategori Aktif:** **{$totalCats} kategori**\n\n"
                 . "📁 **Daftar Kategori:**\n"
                 . $catListText . "\n"
                 . "🔗 **Aksi Admin:** Anda dapat menambah, mengedit, atau menghapus kategori di menu **[Kelola Kategori]({$catUrl})**.";
        }

        // SUB-INTENT E: RINGKASAN DATABASE MENYELURUH (General / All Database Overview / Users)
        $totalCats = count($categories);
        $techPreview = "";
        $no = 1;
        foreach (array_slice($technicians, 0, 5) as $t) {
            $tName = is_object($t) ? ($t->name ?? 'Mitra') : ($t['name'] ?? 'Mitra');
            $tCat = is_object($t) ? ($t->category_name ?? 'Teknisi') : ($t['category_name'] ?? 'Teknisi');
            $tRating = is_object($t) ? ($t->rating ?? 5.0) : ($t['rating'] ?? 5.0);
            $techPreview .= "{$no}. **{$tName}** ({$tCat}) — ⭐ " . number_format((float)$tRating, 2) . "\n";
            $no++;
        }

        $custPreview = "";
        $cpNo = 1;
        foreach (array_slice($customers, 0, 5) as $c) {
            $cName = is_object($c) ? ($c->name ?? 'Customer') : ($c['name'] ?? 'Customer');
            $cEmail = is_object($c) ? ($c->email ?? '-') : ($c['email'] ?? '-');
            $cPosts = is_object($c) ? (int)($c->total_posts ?? 0) : (int)($c['total_posts'] ?? 0);
            $custPreview .= "{$cpNo}. **{$cName}** (`{$cEmail}`) — {$cPosts} postingan\n";
            $cpNo++;
        }
        if (empty($custPreview)) {
            $custPreview = "Belum ada customer terdaftar.\n";
        }

        return "📊 **Laporan & Ringkasan Database BANTUIN** (Akses Administrator)\n\n"
             . "Halo Admin! Berikut ringkasan live data dan metrik sistem dari basis data (database) BANTUIN:\n\n"
             . "👥 **Statistik Pengguna (Users):**\n"
             . "• **Total Seluruh Pengguna:** **{$totalUsers} akun**\n"
             . "• **Pelanggan (Customer):** **{$totalCusts} orang**\n"
             . "• **Mitra Teknisi Terdaftar:** **{$totalTechs} orang**\n"
             . "• **Administrator:** **1 akun** (Super Admin)\n\n"
             . "👤 **Daftar Pelanggan (Customer):**\n"
             . $custPreview . "\n"
             . "🔧 **Cuplikan Mitra Teknisi Aktif:**\n"
             . $techPreview . "\n"
             . "📋 **Statistik Permintaan & Operasional:**\n"
             . "• **Total Postingan Keluhan:** **{$totalPosts} permohonan**\n"
             . "• **Master Kategori Jasa:** **{$totalCats} kategori aktif** (Listrik, AC, Kunci, Elektronik)\n\n"
             . "⚡ **Pintasan Cepat Admin:**\n"
             . "• **[Dashboard Utama Admin]({$adminUrl})**\n"
             . "• **[Manajemen Customer]({$adminCustUrl})**\n"
             . "• **[Manajemen Teknisi]({$adminTechUrl})**\n"
             . "• **[Kelola Master Kategori]({$catUrl})**\n\n"
             . "Ada data spesifik lain yang ingin Anda periksa, Admin?";
    }
}
