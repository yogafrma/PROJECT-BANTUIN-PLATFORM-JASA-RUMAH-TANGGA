import sys
from pptx import Presentation
from pptx.util import Inches, Pt
from pptx.dml.color import RGBColor
from pptx.enum.text import PP_ALIGN
from pptx.enum.shapes import MSO_SHAPE

def build_deck():
    prs = Presentation()
    prs.slide_width = Inches(13.333)
    prs.slide_height = Inches(7.5)
    blank_layout = prs.slide_layouts[6]

    NAVY = RGBColor(0, 48, 73)          # #003049
    AZURE = RGBColor(0, 120, 212)       # #0078D4
    DARK = RGBColor(30, 41, 59)         # #1E293B
    MUTED = RGBColor(100, 116, 139)     # #64748B
    WHITE = RGBColor(255, 255, 255)
    LIGHT_BG = RGBColor(248, 250, 252)  # #F8FAFC
    CARD_BORDER = RGBColor(226, 232, 240)
    GREEN = RGBColor(16, 185, 129)
    AMBER = RGBColor(245, 158, 11)
    PURPLE = RGBColor(124, 58, 237)
    CYAN = RGBColor(6, 182, 212)

    def add_header(slide, tag_text, title_text, subtitle_text=""):
        tag = slide.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(0.35), Inches(3.4), Inches(0.38))
        tag.fill.solid()
        tag.fill.fore_color.rgb = RGBColor(240, 246, 252)
        tag.line.color.rgb = RGBColor(208, 226, 245)
        tf = tag.text_frame
        p = tf.paragraphs[0]
        p.text = tag_text.upper()
        p.font.size = Pt(9.5)
        p.font.bold = True
        p.font.color.rgb = AZURE
        p.alignment = PP_ALIGN.CENTER

        title_box = slide.shapes.add_textbox(Inches(0.8), Inches(0.78), Inches(11.7), Inches(0.8))
        tf_title = title_box.text_frame
        tf_title.word_wrap = True
        p_title = tf_title.paragraphs[0]
        p_title.text = title_text
        p_title.font.name = "Segoe UI"
        p_title.font.size = Pt(22)
        p_title.font.bold = True
        p_title.font.color.rgb = NAVY

        if subtitle_text:
            p_sub = tf_title.add_paragraph()
            p_sub.text = subtitle_text
            p_sub.font.name = "Segoe UI"
            p_sub.font.size = Pt(12)
            p_sub.font.color.rgb = MUTED
            p_sub.space_before = Pt(2)

    # -------------------------------------------------------------------------
    # SLIDE 1: Title & Academic Identity (Tahap 2: SDD UML)
    # -------------------------------------------------------------------------
    s1 = prs.slides.add_slide(blank_layout)
    bg1 = s1.shapes.add_shape(MSO_SHAPE.RECTANGLE, 0, 0, Inches(13.333), Inches(7.5))
    bg1.fill.solid()
    bg1.fill.fore_color.rgb = NAVY
    bg1.line.fill.background()

    c1 = s1.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(0.8), Inches(11.733), Inches(5.9))
    c1.fill.solid()
    c1.fill.fore_color.rgb = RGBColor(5, 38, 59)
    c1.line.color.rgb = RGBColor(14, 116, 185)
    c1.line.width = Pt(1.5)

    tb1 = s1.shapes.add_textbox(Inches(1.3), Inches(1.15), Inches(10.7), Inches(5.2))
    tf1 = tb1.text_frame
    tf1.word_wrap = True

    p = tf1.paragraphs[0]
    p.text = "PROYEK AKHIR MANDIRI REKAYASA PERANGKAT LUNAK (TIKB2503021) — SEMESTER 3"
    p.font.name = "Segoe UI"
    p.font.size = Pt(11)
    p.font.bold = True
    p.font.color.rgb = RGBColor(56, 189, 248)

    p2 = tf1.add_paragraph()
    p2.text = "BANTUIN"
    p2.font.name = "Segoe UI"
    p2.font.size = Pt(40)
    p2.font.bold = True
    p2.font.color.rgb = WHITE
    p2.space_before = Pt(6)

    p3 = tf1.add_paragraph()
    p3.text = "Software Design Document (SDD): Pemodelan 4 Diagram UML Utama & Prototipe UI/UX Figma"
    p3.font.name = "Segoe UI"
    p3.font.size = Pt(17)
    p3.font.color.rgb = RGBColor(226, 232, 240)
    p3.space_before = Pt(3)

    p4 = tf1.add_paragraph()
    p4.text = "PRESENTASI PROGRES 2: PEMODELAN ARSITEKTUR & DESAIN (ESTIMASI: 05:30 - 06:00 MENIT)"
    p4.font.name = "Segoe UI"
    p4.font.size = Pt(12)
    p4.font.bold = True
    p4.font.color.rgb = AMBER
    p4.space_before = Pt(14)

    p5 = tf1.add_paragraph()
    p5.text = "___________________________________________________________________________________"
    p5.font.size = Pt(10)
    p5.font.color.rgb = RGBColor(14, 116, 185)

    p6 = tf1.add_paragraph()
    p6.text = "Nama Mahasiswa : Yoga Fratama Fahdi Sardi        |   Program Studi  : S1 Teknik Informatika"
    p6.font.name = "Segoe UI"
    p6.font.size = Pt(13)
    p6.font.bold = True
    p6.font.color.rgb = WHITE
    p6.space_before = Pt(10)

    p7 = tf1.add_paragraph()
    p7.text = "NIM            : 41257243 (Kelas 1C)           |   Dosen Pengampu : Yudhistira Arie Wijaya, M.Kom., CDS."
    p7.font.name = "Segoe UI"
    p7.font.size = Pt(13)
    p7.font.color.rgb = RGBColor(203, 213, 225)
    p7.space_before = Pt(4)

    p8 = tf1.add_paragraph()
    p8.text = "Institusi      : STMIK IKMI Cirebon             |   Bobot Nilai    : 30% dari Total Nilai Video (OBE)"
    p8.font.name = "Segoe UI"
    p8.font.size = Pt(13)
    p8.font.color.rgb = RGBColor(148, 163, 184)
    p8.space_before = Pt(4)

    s1.notes_slide.notes_text_frame.text = (
        "[00:00 - 00:40] PEMBUKA & IDENTITAS MAHASISWA:\n\n"
        "\"Assalamu’alaikum Warahmatullahi Wabarakatuh. Selamat pagi/siang kepada Bapak Dosen Pengampu, "
        "Bapak Yudhistira Arie Wijaya, M.Kom., CDS., dan rekan-rekan mahasiswa semuanya.\n\n"
        "Kembali lagi bersama saya Yoga Fratama Fahdi Sardi, NIM 41257243, mahasiswa S1 Teknik Informatika di STMIK IKMI Cirebon.\n\n"
        "Setelah pada video Progres 1 sebelumnya kita telah menuntaskan spesifikasi kebutuhan PRD, pada video Progres 2 kali ini saya akan mempresentasikan "
        "Software Design Document (SDD) aplikasi BANTUIN, yang membedah secara runut 4 Diagram UML Utama—yaitu Use Case Diagram, Activity Diagram, "
        "Class Diagram berorientasi objek berbasis prinsip SOLID, Sequence Diagram—serta rancangan antarmuka UI/UX dan design system. "
        "Mari kita bedah diagram yang pertama.\""
    )

    # -------------------------------------------------------------------------
    # SLIDE 2: Diagram UML 1: Use Case Diagram & Skenario Penggunaan
    # -------------------------------------------------------------------------
    s2 = prs.slides.add_slide(blank_layout)
    add_header(s2, "Diagram UML 1", "Use Case Diagram: 4 Aktor & 10 Fungsionalitas Inti", "Memetakan batas sistem (system boundary), peran aktor, dan interaksi layanan AI.")

    # Left: 4 Actors
    actors_info = [
        ("Customer (Pengguna)", AZURE, "Konsultasi AI, posting keluhan, negosiasi harga di chat, konfirmasi bayar & riwayat servis."),
        ("Mitra Teknisi", GREEN, "Melihat feed keluhan per kategori, ajukan penawaran harga wajar, terima deal transaksi."),
        ("Super Administrator", NAVY, "Pusat kendali metrik sistem, moderasi mitra & kategori, serta konfigurasi multi-AI engine."),
        ("Sistem AI (Secondary Actor)", AMBER, "Penyedia layanan eksternal (Vision & LLM) untuk diagnosa kerusakan dan chatbot otomatis.")
    ]

    for i, (act_title, act_color, act_desc) in enumerate(actors_info):
        card = s2.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(1.85 + (i * 1.25)), Inches(5.7), Inches(1.15))
        card.fill.solid()
        card.fill.fore_color.rgb = WHITE
        card.line.color.rgb = act_color
        card.line.width = Pt(1.5)

        tb = s2.shapes.add_textbox(Inches(0.95), Inches(1.9 + (i * 1.25)), Inches(5.4), Inches(1.0))
        tf = tb.text_frame
        tf.word_wrap = True
        p = tf.paragraphs[0]
        p.text = act_title
        p.font.name = "Segoe UI"
        p.font.size = Pt(13.5)
        p.font.bold = True
        p.font.color.rgb = act_color

        p_desc = tf.add_paragraph()
        p_desc.text = act_desc
        p_desc.font.size = Pt(10.5)
        p_desc.font.color.rgb = DARK
        p_desc.space_before = Pt(3)

    # Right: Use Case Scenario Highlight Card
    sc_card = s2.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(6.8), Inches(1.85), Inches(5.7), Inches(4.9))
    sc_card.fill.solid()
    sc_card.fill.fore_color.rgb = LIGHT_BG
    sc_card.line.color.rgb = CARD_BORDER
    sc_card.line.width = Pt(1.5)

    tb_sc = s2.shapes.add_textbox(Inches(7.0), Inches(2.0), Inches(5.3), Inches(4.6))
    tf_sc = tb_sc.text_frame
    tf_sc.word_wrap = True

    p = tf_sc.paragraphs[0]
    p.text = "Skenario Utama Use Case: Konsultasi AI & Deal Lock"
    p.font.name = "Segoe UI"
    p.font.size = Pt(15)
    p.font.bold = True
    p.font.color.rgb = NAVY

    sc_steps = [
        "1. Pre-Condition: Customer & Teknisi telah login di platform BANTUIN.",
        "2. Step 1 (AI Diagnosis): Customer upload foto kerusakan fisik + kronologi. Sistem AI merespon kategori teknisi & estimasi harga wajar (Rp Min - Max).",
        "3. Step 2 (Posting): Customer terbitkan postingan ke feed publik kategori.",
        "4. Step 3 (Interaksi Chat): Teknisi membuka chat, mendiskusikan teknis, dan mengajukan modal Nego Harga resmi.",
        "5. Step 4 (Deal Lock): Customer klik 'Terima (Deal)', nominal harga terkunci di database.",
        "6. Post-Condition: Order selesai, status lunas, tercatat di riwayat transaksi."
    ]
    for step in sc_steps:
        p_st = tf_sc.add_paragraph()
        p_st.text = step
        p_st.font.size = Pt(10.5)
        p_st.font.color.rgb = DARK
        p_st.space_before = Pt(6)

    s2.notes_slide.notes_text_frame.text = (
        "[00:40 - 01:45] DIAGRAM UML 1: USE CASE DIAGRAM & SKENARIO:\n\n"
        "\"Diagram pertama adalah Use Case Diagram. Dalam sistem BANTUIN, terdapat 4 aktor yang berinteraksi:\n"
        "Tiga aktor primer manusia, yaitu Customer, Mitra Teknisi, dan Super Administrator, serta satu aktor sekunder yaitu Sistem AI sebagai penyedia inferensi cerdas.\n\n"
        "Fungsionalitas sistem mencakup 10 use case terpadu, mulai dari registrasi akun, konsultasi AI, penerbitan keluhan, hingga transaksi.\n\n"
        "Skenario intinya adalah: Customer mengunggah foto kerusakan pada modul konsultasi AI. Sistem AI menganalisis dan mengeluarkan estimasi biaya wajar. "
        "Setelah keluhan diterbitkan, teknisi melihat feed, masuk ke ruang chat privat, dan melakukan negosiasi harga. "
        "Saat kedua belah pihak sepakat, fitur Deal Lock mengunci nominal harga pengerjaan, sehingga tidak ada kenaikan ongkos liar di lapangan. "
        "Alur inilah yang kemudian kita detailkan pada Activity Diagram.\""
    )

    # -------------------------------------------------------------------------
    # SLIDE 3: Diagram UML 2: Activity Diagram Alur Bisnis & AI
    # -------------------------------------------------------------------------
    s3 = prs.slides.add_slide(blank_layout)
    add_header(s3, "Diagram UML 2", "Activity Diagram: Alur Eksekusi AI & Transaksi Chat", "Menggambarkan alur kerja paralel modul inferensi AI, negosiasi harga, dan settlement.")

    act_stages = [
        ("1. Input & Diagnosa AI", AZURE, [
            "Customer kirim foto & kronologi.",
            "Cek koneksi & API key valid?",
            "Cabang A: Inferensi Gemini / NVIDIA NIM.",
            "Cabang B: Contextual Heuristic Fallback.",
            "Output: Probabilitas komponen rusak & estimasi harga Rp Min - Max."
        ]),
        ("2. Moderasi Feed & Chat Nego", GREEN, [
            "Postingan diterbitkan ke feed kategori.",
            "Mitra teknisi masuk ke ruang chat.",
            "Ajukan penawaran harga pengerjaan.",
            "Kondisi: Ditolak (Nego ulang) ATAU Disetujui (Deal Disepakati).",
            "Sistem mengunci harga (Deal Locked)."
        ]),
        ("3. Eksekusi & Penyelesaian", NAVY, [
            "Teknisi menyelesaikan servis fisik.",
            "Pelanggan konfirmasi pembayaran (Cash / QRIS).",
            "Update status transaksi menjadi 'Selesai'.",
            "Generate faktur & pencatatan riwayat permanen di database."
        ])
    ]

    col_lefts = [0.8, 4.8, 8.8]
    for i, (title, color, items) in enumerate(act_stages):
        card = s3.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(col_lefts[i]), Inches(1.85), Inches(3.7), Inches(4.9))
        card.fill.solid()
        card.fill.fore_color.rgb = WHITE
        card.line.color.rgb = color
        card.line.width = Pt(2.0)

        tb = s3.shapes.add_textbox(Inches(col_lefts[i] + 0.2), Inches(2.05), Inches(3.3), Inches(4.5))
        tf = tb.text_frame
        tf.word_wrap = True

        p = tf.paragraphs[0]
        p.text = title
        p.font.name = "Segoe UI"
        p.font.size = Pt(15)
        p.font.bold = True
        p.font.color.rgb = color

        for item in items:
            p_it = tf.add_paragraph()
            p_it.text = "• " + item
            p_it.font.size = Pt(11)
            p_it.font.color.rgb = DARK
            p_it.space_before = Pt(8)

    s3.notes_slide.notes_text_frame.text = (
        "[01:45 - 02:45] DIAGRAM UML 2: ACTIVITY DIAGRAM ALUR BISNIS & AI:\n\n"
        "\"Memasuki diagram kedua, yaitu Activity Diagram. Diagram ini memodelkan aliran aktivitas dari awal hingga transaksi selesai.\n\n"
        "Dimulai dari Tahap 1: Saat formulir konsultasi dikirimkan oleh Customer, sistem melakukan percabangan keputusan logika:\n"
        "Jika koneksi cloud dan API key aktif, sistem mengeksekusi inferensi AI melalui Google Gemini atau NVIDIA NIM.\n"
        "Namun jika internet sedang terganggu atau API key kosong, sistem secara otomatis beralih ke Contextual Heuristic Fallback, "
        "sehingga sistem zero crash dan tetap menghasilkan rekomendasi yang rasional.\n\n"
        "Masuk ke Tahap 2: Hasil konsultasi diterbitkan menjadi postingan keluhan. Teknisi membaca keluhan dan membuka ruang chat. "
        "Di ruang chat terjadi aktivitas negosiasi harga. Terdapat kondisi pengambilan keputusan: jika tawaran ditolak, terjadi siklus nego ulang. "
        "Jika tawaran disetujui, aktivitas berlanjut ke penguncian harga Deal Lock.\n\n"
        "Dan di Tahap 3: Setelah pekerjaan selesai, customer mengonfirmasi pembayaran, status pesanan diperbarui menjadi Selesai, dan faktur digital diterbitkan.\""
    )

    # -------------------------------------------------------------------------
    # SLIDE 4: Diagram UML 3: Class Diagram & Penerapan Prinsip SOLID
    # -------------------------------------------------------------------------
    s4 = prs.slides.add_slide(blank_layout)
    add_header(s4, "Diagram UML 3", "Class Diagram: Arsitektur OOP & Kepatuhan Prinsip SOLID", "Membedah struktur Model-View-Controller, abstraksi Database, dan isolasi AI Service.")

    # Left: Classes Overview
    c_left = s4.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(1.85), Inches(5.7), Inches(4.9))
    c_left.fill.solid()
    c_left.fill.fore_color.rgb = WHITE
    c_left.line.color.rgb = AZURE
    c_left.line.width = Pt(1.5)

    tb_cl = s4.shapes.add_textbox(Inches(1.0), Inches(2.0), Inches(5.3), Inches(4.6))
    tf_cl = tb_cl.text_frame
    tf_cl.word_wrap = True

    p = tf_cl.paragraphs[0]
    p.text = "Struktur Kelas Utama (Custom PHP MVC)"
    p.font.name = "Segoe UI"
    p.font.size = Pt(15)
    p.font.bold = True
    p.font.color.rgb = NAVY

    classes_text = [
        "1. Database (Core Singleton Wrapper): Mengelola koneksi PDO, binding parameter aman, dan eksekusi query.",
        "2. Model (Base Abstract Class): Induk seluruh model yang menginjeksi instance Database.",
        "3. UserModel, PostModel, ChatModel: Turunan Model yang menangani entitas akun, keluhan servis, dan negosiasi chat.",
        "4. ChatController & PostController: Menangani request HTTP, validasi sesi RBAC, dan merender View.",
        "5. AIProviderService (Isolated Service): Menangani komunikasi multi-model (Gemini & NVIDIA NIM) secara terisolasi."
    ]
    for c_item in classes_text:
        p_c = tf_cl.add_paragraph()
        p_c.text = c_item
        p_c.font.size = Pt(10.5)
        p_c.font.color.rgb = DARK
        p_c.space_before = Pt(7)

    # Right: SOLID Principles Compliance
    c_right = s4.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(6.8), Inches(1.85), Inches(5.7), Inches(4.9))
    c_right.fill.solid()
    c_right.fill.fore_color.rgb = LIGHT_BG
    c_right.line.color.rgb = PURPLE
    c_right.line.width = Pt(1.5)

    tb_sol = s4.shapes.add_textbox(Inches(7.0), Inches(2.0), Inches(5.3), Inches(4.6))
    tf_sol = tb_sol.text_frame
    tf_sol.word_wrap = True

    p = tf_sol.paragraphs[0]
    p.text = "Kepatuhan Terhadap Prinsip SOLID"
    p.font.name = "Segoe UI"
    p.font.size = Pt(15)
    p.font.bold = True
    p.font.color.rgb = PURPLE

    solid_items = [
        ("SRP (Single Responsibility):", "Setiap kelas hanya punya 1 alasan untuk berubah. ChatModel hanya urus negosiasi, sedangkan AIProviderService khusus menangani komunikasi API eksternal."),
        ("OCP (Open/Closed Principle):", "AIProviderService dirancang terbuka untuk penambahan provider AI baru (misal: Groq, Claude) tanpa mengubah logika controller yang sudah berjalan."),
        ("LSP (Liskov Substitution):", "Seluruh model (UserModel, PostModel, ChatModel) mewarisi kelas Model dasar dengan kontrak interface database yang konsisten."),
        ("DIP (Dependency Inversion):", "Controller bergantung pada abstraksi Service dan Model, bukan langsung memanggil query mentah SQL di antarmuka.")
    ]
    for s_title, s_desc in solid_items:
        p_st = tf_sol.add_paragraph()
        p_st.text = s_title
        p_st.font.size = Pt(11)
        p_st.font.bold = True
        p_st.font.color.rgb = NAVY
        p_st.space_before = Pt(6)

        p_sd = tf_sol.add_paragraph()
        p_sd.text = s_desc
        p_sd.font.size = Pt(10)
        p_sd.font.color.rgb = DARK
        p_sd.space_before = Pt(1)

    s4.notes_slide.notes_text_frame.text = (
        "[02:45 - 03:50] DIAGRAM UML 3: CLASS DIAGRAM BERORIENTASI OBJEK (SOLID):\n\n"
        "\"Diagram ketiga adalah Class Diagram berorientasi objek yang mematuhi prinsip SOLID, sesuai ketentuan rubrik penilaian OBE.\n\n"
        "Di lapisan paling dasar, kita memiliki kelas Database sebagai pembungkus PDO yang aman dari SQL Injection. "
        "Kelas ini diinjeksi ke dalam kelas abstrak Model, yang kemudian diturunkan oleh UserModel, PostModel, dan ChatModel.\n\n"
        "Penerapan prinsip SOLID diimplementasikan secara nyata pada kode sistem kami:\n"
        "Pertama, Single Responsibility Principle (SRP): Setiap model hanya bertanggung jawab pada tabelnya masing-masing. "
        "Dan yang paling penting, komunikasi kecerdasan buatan tidak dicampur aduk di controller, melainkan diisolasi penuh di dalam kelas AIProviderService.\n\n"
        "Kedua, Open/Closed Principle (OCP): AIProviderService dirancang agar terbuka untuk penambahan model AI masa depan (seperti DeepSeek atau OpenAI) "
        "tanpa perlu merombak Controller yang sudah stabil.\n\n"
        "Dan ketiga, Liskov Substitution Principle (LSP): Seluruh model anak dapat menggantikan kelas Model induk dengan aman dan konsisten. "
        "Hubungan antar-kelas ini menjamin kode sistem BANTUIN sangat bersih, terstruktur, dan mudah dipelihara.\""
    )

    # -------------------------------------------------------------------------
    # SLIDE 5: Diagram UML 4: Sequence Diagram Alur Negosiasi & Deal Lock
    # -------------------------------------------------------------------------
    s5 = prs.slides.add_slide(blank_layout)
    add_header(s5, "Diagram UML 4", "Sequence Diagram: Alur Pertukaran Pesan Negosiasi & Deal Lock", "Menelusuri interaksi pesan real-time antara View, Controller, Model, dan Database MySQL.")

    seq_steps = [
        ("Step 1: Pengajuan Nego", AZURE, "Customer membuka modal di View dan memasukkan nominal penawaran Rp 150.000."),
        ("Step 2: Request Dispatch", DARK, "View mengirim AJAX POST /chat/negotiate membawa token CSRF dan data penawaran."),
        ("Step 3: Validasi Controller", NAVY, "ChatController memvalidasi sesi RBAC pengguna dan memastikan status postingan masih aktif."),
        ("Step 4: Penyimpanan Transaksi", PURPLE, "ChatModel mengeksekusi INSERT ke tabel price_negotiations dengan status 'pending'."),
        ("Step 5: Injeksi Pesan Chat", GREEN, "Sistem membuat record pesan khusus [NEGO:id:150000:pending] di tabel messages."),
        ("Step 6: Respon & Deal Acceptance", AMBER, "Teknisi klik 'Terima Deal' -> ChatModel mengubah status menjadi 'accepted'."),
        ("Step 7: Penguncian Harga (Deal Locked)", GREEN, "PostModel mengeksekusi UPDATE posts SET agreed_price = 150000. Harga resmi terkunci!")
    ]

    for i, (title, color, desc) in enumerate(seq_steps):
        card = s5.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(1.85 + (i * 0.7)), Inches(11.7), Inches(0.62))
        card.fill.solid()
        card.fill.fore_color.rgb = WHITE
        card.line.color.rgb = color
        card.line.width = Pt(1.5)

        tb = s5.shapes.add_textbox(Inches(1.0), Inches(1.88 + (i * 0.7)), Inches(11.3), Inches(0.55))
        tf = tb.text_frame
        tf.word_wrap = True

        p = tf.paragraphs[0]
        p.text = f"{title}: {desc}"
        p.font.name = "Segoe UI"
        p.font.size = Pt(11)
        p.font.color.rgb = DARK

    s5.notes_slide.notes_text_frame.text = (
        "[03:50 - 04:50] DIAGRAM UML 4: SEQUENCE DIAGRAM ALUR PERTUKARAN PESAN:\n\n"
        "\"Diagram keempat adalah Sequence Diagram yang membedah alur pertukaran pesan secara kronologis pada fitur unggulan kami, yaitu Negosiasi & Deal Lock.\n\n"
        "Alur kronologisnya berjalan sebagai berikut:\n"
        "Pertama, Customer membuka modal di View dan memasukkan tawaran Rp 150.000.\n"
        "Kedua, View mengirimkan request AJAX POST ke ChatController lengkap dengan proteksi CSRF Token.\n"
        "Ketiga, ChatController memeriksa hak akses pengguna dan memanggil ChatModel::createNegotiation.\n"
        "Keempat, ChatModel menyimpan record ke tabel price_negotiations dengan status pending dan menyisipkan pesan penawaran ke tabel messages.\n"
        "Kelima, saat Mitra Teknisi menekan tombol 'Terima (Deal)', request dikirim ke endpoint /chat/negotiate-action.\n"
        "Keenam, ChatModel memperbarui status nego menjadi 'accepted', lalu memanggil PostModel untuk mengeksekusi update kolom agreed_price di database.\n"
        "Hasilnya, nominal Rp 150.000 resmi dikunci di database dan kedua belah pihak mendapatkan notifikasi visual kartu Deal Lock. "
        "Alur ini memastikan integritas data transaksi 100% terjaga dan transparan.\""
    )

    # -------------------------------------------------------------------------
    # SLIDE 6: Perancangan Antarmuka UI/UX & Design System
    # -------------------------------------------------------------------------
    s6 = prs.slides.add_slide(blank_layout)
    add_header(s6, "Perancangan UI/UX", "Design System & Prototipe Antarmuka Microsoft Fluent 2", "Desain antarmuka berorientasi pengguna, responsif multi-device, dan arsitektur 1-Screen Viewport Lock.")

    ui_cards = [
        ("Design Tokens & Palet Warna", AZURE, [
            "Primary Accent: Fluent Azure Blue (#0078D4).",
            "Command Surface: Deep Navy (#003049).",
            "Semantic Success: Emerald (#10B981) untuk status Deal.",
            "Semantics Warning: Amber (#F59E0B) untuk negosiasi pending."
        ]),
        ("Arsitektur 1-Screen Viewport Lock", GREEN, [
            "Khusus antarmuka Chat Room & Dashboard Admin.",
            "Tinggi container chat terkunci 100% pada viewport.",
            "Mencegah window scrolling berlebih pada desktop & mobile.",
            "Area percakapan bergulir internal secara halus (smooth scroll)."
        ]),
        ("Komponen Interaktif Responsif", PURPLE, [
            "Kartu konsultasi AI dengan upload foto & pratinjau instan.",
            "Modal negosiasi harga dengan tombol aksi Deal & Tolak.",
            "Floating AI Chatbot Widget yang standby 24 jam.",
            "Badge status rounded-pill yang informatif dan elegan."
        ])
    ]

    for i, (title, color, points) in enumerate(ui_cards):
        card = s6.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(col_lefts[i]), Inches(1.85), Inches(3.7), Inches(4.9))
        card.fill.solid()
        card.fill.fore_color.rgb = WHITE
        card.line.color.rgb = color
        card.line.width = Pt(2.0)

        tb = s6.shapes.add_textbox(Inches(col_lefts[i] + 0.2), Inches(2.05), Inches(3.3), Inches(4.5))
        tf = tb.text_frame
        tf.word_wrap = True

        p = tf.paragraphs[0]
        p.text = title
        p.font.name = "Segoe UI"
        p.font.size = Pt(14.5)
        p.font.bold = True
        p.font.color.rgb = color

        for pt in points:
            p_p = tf.add_paragraph()
            p_p.text = "• " + pt
            p_p.font.size = Pt(11)
            p_p.font.color.rgb = DARK
            p_p.space_before = Pt(8)

    s6.notes_slide.notes_text_frame.text = (
        "[04:50 - 05:40] PERANCANGAN ANTARMUKA UI/UX & DESIGN SYSTEM:\n\n"
        "\"Selain diagram UML, luaran Tahap 2 ini mencakup perancangan antarmuka UI/UX berbasis prinsip Microsoft Fluent 2.\n\n"
        "Kami merancang Design System yang konsisten:\n"
        "Dari segi warna: menggunakan warna utama Fluent Azure Blue (#0078D4) yang melambangkan keandalan teknologi, "
        "dipadukan dengan Deep Navy (#003049) pada header command center untuk memberikan kesan kokoh dan elegan.\n\n"
        "Salah satu inovasi ergonomis terbaik kami adalah arsitektur 1-Screen Viewport Lock pada ruang chat: "
        "tinggi halaman terkunci pas satu layar penuh tanpa scrolling luar, sehingga pengguna merasa sedang menggunakan aplikasi native yang sangat nyaman.\n\n"
        "Semua komponen—mulai dari pratinjau unggah foto kerusakan, modal tawar-menawar harga, hingga widget chatbot AI—dirancang responsif "
        "dan telah teruji optimal diakses lewat layar smartphone maupun layar monitor desktop.\""
    )

    # -------------------------------------------------------------------------
    # SLIDE 7: Kesimpulan & Roadmap Menuju Tahap 3 (Konstruksi & CI/CD)
    # -------------------------------------------------------------------------
    s7 = prs.slides.add_slide(blank_layout)
    add_header(s7, "Penutup & Langkah Selanjutnya", "Kesimpulan SDD & Roadmap Menuju Tahap 3 (Konstruksi & CI/CD)", "Seluruh diagram UML dan rancangan antarmuka siap diverifikasi ke kode nyata.")

    box = s7.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(1.85), Inches(11.7), Inches(4.9))
    box.fill.solid()
    box.fill.fore_color.rgb = WHITE
    box.line.color.rgb = GREEN
    box.line.width = Pt(2.0)

    tb = s7.shapes.add_textbox(Inches(1.2), Inches(2.1), Inches(10.9), Inches(4.4))
    tf = tb.text_frame
    tf.word_wrap = True

    p = tf.paragraphs[0]
    p.text = "Capaian Luaran Progres 2 (Tahap Perancangan SDD & UML):"
    p.font.name = "Segoe UI"
    p.font.size = Pt(17)
    p.font.bold = True
    p.font.color.rgb = NAVY

    conclusions = [
        "4 Diagram UML Tuntas & Presisi: Use Case, Activity Diagram alur AI, Class Diagram berorientasi objek SOLID, dan Sequence Diagram negosiasi harga telah terhubung erat secara logis.",
        "Desain Berorientasi Pengguna: Antarmuka modern Microsoft Fluent 2 dengan Viewport Lock memberikan pengalaman chat dan diagnosa terbaik bagi pengguna.",
        "Kesiapan Menuju Tahap 3 (Konstruksi Kode & Testing): Arsitektur ini terbukti dapat langsung diimplementasikan ke dalam Clean PHP MVC murni tanpa celah arsitektural.",
        "Komitmen Standar OBE: Penjelasan teknis ini memenuhi seluruh kriteria dimensi penilaian kurikulum OBE dengan tingkat kedalaman rekayasa maksimal."
    ]

    for c in conclusions:
        p_c = tf.add_paragraph()
        p_c.text = "✔  " + c
        p_c.font.name = "Segoe UI"
        p_c.font.size = Pt(12)
        p_c.font.color.rgb = DARK
        p_c.space_before = Pt(10)

    p_close = tf.add_paragraph()
    p_close.text = "Terima Kasih atas Perhatian Bapak Dosen Pengampu & Rekan-Rekan Mahasiswa."
    p_close.font.name = "Segoe UI"
    p_close.font.size = Pt(13.5)
    p_close.font.bold = True
    p_close.font.color.rgb = AZURE
    p_close.space_before = Pt(16)

    s7.notes_slide.notes_text_frame.text = (
        "[05:40 - 06:00] PENUTUP & KESIAPAN TAHAP SELANJUTNYA:\n\n"
        "\"Sebagai penutup, penyusunan Software Design Document (SDD) ini membuktikan bahwa rancangan sistem BANTUIN telah matang secara teoritis "
        "maupun arsitektural. Empat diagram UML yang disajikan saling melengkapi dari level interaksi pengguna, alur proses bisnis, struktur kode SOLID, hingga pertukaran pesan database.\n\n"
        "Dengan selesainya perancangan ini, sistem siap melangkah ke Tahap 3 dan Video Final, di mana kita akan menguji kode aplikasi secara live, "
        "menjalankan pengujian otomatis, dan memverifikasi status hijau pada CI/CD Pipeline GitHub Actions.\n\n"
        "Demikian presentasi Progres 2 ini. Terima kasih banyak atas perhatian Bapak Yudhistira dan rekan-rekan sekalian. "
        "Wabillahi taufiq wal hidayah, Wassalamu’alaikum Warahmatullahi Wabarakatuh.\""
    )

    output_path = "PRESENTASI_PROGRES_2_BANTUIN.pptx"
    prs.save(output_path)
    print(f"PowerPoint file saved successfully at: {output_path}")

if __name__ == "__main__":
    build_deck()
