import sys
from pptx import Presentation
from pptx.util import Inches, Pt
from pptx.dml.color import RGBColor
from pptx.enum.text import PP_ALIGN, MSO_ANCHOR
from pptx.enum.shapes import MSO_SHAPE

def build_deck():
    prs = Presentation()
    # 16:9 widescreen layout
    prs.slide_width = Inches(13.333)
    prs.slide_height = Inches(7.5)
    blank_layout = prs.slide_layouts[6]

    # Colors
    NAVY = RGBColor(0, 48, 73)        # #003049
    AZURE = RGBColor(0, 120, 212)     # #0078D4
    DARK = RGBColor(30, 41, 59)       # #1E293B
    MUTED = RGBColor(100, 116, 139)   # #64748B
    WHITE = RGBColor(255, 255, 255)
    LIGHT_BG = RGBColor(248, 250, 252) # #F8FAFC
    CARD_BORDER = RGBColor(226, 232, 240)
    GREEN = RGBColor(16, 185, 129)
    AMBER = RGBColor(245, 158, 11)

    # -------------------------------------------------------------------------
    # Helper: Add Slide Header
    # -------------------------------------------------------------------------
    def add_header(slide, tag_text, title_text, subtitle_text=""):
        # Tag Badge
        tag = slide.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(0.4), Inches(3.2), Inches(0.4))
        tag.fill.solid()
        tag.fill.fore_color.rgb = RGBColor(240, 246, 252)
        tag.line.color.rgb = RGBColor(208, 226, 245)
        tf = tag.text_frame
        tf.word_wrap = True
        p = tf.paragraphs[0]
        p.text = tag_text.upper()
        p.font.size = Pt(10)
        p.font.bold = True
        p.font.color.rgb = AZURE
        p.alignment = PP_ALIGN.CENTER

        # Main Title
        title_box = slide.shapes.add_textbox(Inches(0.8), Inches(0.85), Inches(11.7), Inches(0.8))
        tf_title = title_box.text_frame
        tf_title.word_wrap = True
        p_title = tf_title.paragraphs[0]
        p_title.text = title_text
        p_title.font.name = "Segoe UI"
        p_title.font.size = Pt(24)
        p_title.font.bold = True
        p_title.font.color.rgb = NAVY

        if subtitle_text:
            p_sub = tf_title.add_paragraph()
            p_sub.text = subtitle_text
            p_sub.font.name = "Segoe UI"
            p_sub.font.size = Pt(13)
            p_sub.font.color.rgb = MUTED
            p_sub.space_before = Pt(3)

    # =========================================================================
    # SLIDE 1: Title & Academic Identity
    # =========================================================================
    s1 = prs.slides.add_slide(blank_layout)
    bg1 = s1.shapes.add_shape(MSO_SHAPE.RECTANGLE, 0, 0, Inches(13.333), Inches(7.5))
    bg1.fill.solid()
    bg1.fill.fore_color.rgb = NAVY
    bg1.line.fill.background()

    # Accent glow card on left
    c1 = s1.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(0.8), Inches(11.733), Inches(5.9))
    c1.fill.solid()
    c1.fill.fore_color.rgb = RGBColor(5, 38, 59)
    c1.line.color.rgb = RGBColor(14, 116, 185)
    c1.line.width = Pt(1.5)

    # Title texts
    tb = s1.shapes.add_textbox(Inches(1.3), Inches(1.2), Inches(10.7), Inches(5.0))
    tf = tb.text_frame
    tf.word_wrap = True

    p = tf.paragraphs[0]
    p.text = "PROYEK AKHIR MANDIRI REKAYASA PERANGKAT LUNAK (TIKB2503021) — SEMESTER 3"
    p.font.name = "Segoe UI"
    p.font.size = Pt(11)
    p.font.bold = True
    p.font.color.rgb = RGBColor(56, 189, 248) # Cyan/Azure tint

    p2 = tf.add_paragraph()
    p2.text = "BANTUIN"
    p2.font.name = "Segoe UI"
    p2.font.size = Pt(44)
    p2.font.bold = True
    p2.font.color.rgb = WHITE
    p2.space_before = Pt(8)

    p3 = tf.add_paragraph()
    p3.text = "Platform Layanan Jasa Perbaikan Rumah Tangga Berbasis Web dengan Integrasi Kecerdasan Artifisial (AI)"
    p3.font.name = "Segoe UI"
    p3.font.size = Pt(18)
    p3.font.color.rgb = RGBColor(226, 232, 240)
    p3.space_before = Pt(4)

    p4 = tf.add_paragraph()
    p4.text = "PRESENTASI PROGRES 1: DOKUMEN PRD & KONSEP APLIKASI (ESTIMASI: 03:30 - 04:00 MENIT)"
    p4.font.name = "Segoe UI"
    p4.font.size = Pt(12)
    p4.font.bold = True
    p4.font.color.rgb = AMBER
    p4.space_before = Pt(16)

    # Divider bar
    p5 = tf.add_paragraph()
    p5.text = "___________________________________________________________________________________"
    p5.font.size = Pt(10)
    p5.font.color.rgb = RGBColor(14, 116, 185)

    p6 = tf.add_paragraph()
    p6.text = "Nama Mahasiswa : Yoga Fratama Fahdi Sardi        |   Program Studi  : S1 Teknik Informatika"
    p6.font.name = "Segoe UI"
    p6.font.size = Pt(13)
    p6.font.bold = True
    p6.font.color.rgb = WHITE
    p6.space_before = Pt(12)

    p7 = tf.add_paragraph()
    p7.text = "NIM            : 41257243 (Kelas 1C)           |   Dosen Pengampu : Yudhistira Arie Wijaya, M.Kom., CDS."
    p7.font.name = "Segoe UI"
    p7.font.size = Pt(13)
    p7.font.color.rgb = RGBColor(203, 213, 225)
    p7.space_before = Pt(4)

    p8 = tf.add_paragraph()
    p8.text = "Institusi      : STMIK IKMI Cirebon             |   Metode Uji     : Outcome-Based Education (OBE)"
    p8.font.name = "Segoe UI"
    p8.font.size = Pt(13)
    p8.font.color.rgb = RGBColor(148, 163, 184)
    p8.space_before = Pt(4)

    s1.notes_slide.notes_text_frame.text = (
        "[00:00 - 00:35] PEMBUKA & IDENTITAS MAHASISWA:\n\n"
        "\"Assalamu’alaikum Warahmatullahi Wabarakatuh. Selamat pagi/siang kepada Bapak Dosen Pengampu, "
        "Bapak Yudhistira Arie Wijaya, M.Kom., CDS., dan rekan-rekan semuanya.\n\n"
        "Perkenalkan, saya Yoga Fratama Fahdi Sardi, NIM 41257243, mahasiswa Program Studi S1 Teknik Informatika di STMIK IKMI Cirebon.\n\n"
        "Pada kesempatan kali ini, saya akan mempresentasikan Progres 1 Rekayasa Perangkat Lunak, yaitu penyusunan Dokumen PRD "
        "(Product Requirement Document) dan konsep aplikasi yang saya kembangkan secara mandiri, yaitu BANTUIN: Platform Jasa Perbaikan "
        "Rumah Tangga Berbasis Web dengan Integrasi Kecerdasan Artifisial (AI).\""
    )

    # =========================================================================
    # SLIDE 2: Latar Belakang Masalah Riil
    # =========================================================================
    s2 = prs.slides.add_slide(blank_layout)
    add_header(s2, "Tahap 1: Inisiasi PRD", "Latar Belakang & Identifikasi Masalah Riil", "Tiga titik kritis (pain points) yang dialami masyarakat saat menghadapi kerusakan darurat di rumah.")

    problems = [
        ("1. Asimetri Informasi & Biaya 'Gelap'",
         "Konsumen kerap merasa was-was karena ketiadaan patokan tarif pengerjaan standar. Ongkos perbaikan sering dimainkan atau membengkak sepihak setelah teknisi tiba di tempat."),
        ("2. Ketidaktahuan Jenis Kerusakan",
         "Masyarakat awam bingung mengidentifikasi spare part atau spesifikasi teknis kerusakan. Tidak tahu harus memanggil spesialis apa dan berapa perkiraan biaya normalnya."),
        ("3. Tanpa Sistem Kesepakatan Resmi",
         "Pencarian teknisi panggilan manual lewat media sosial tidak memiliki kontrak kerja transparan, ketiadaan mekanisme negosiasi harga tertulis, dan tanpa jaminan riwayat servis.")
    ]

    lefts = [0.8, 4.8, 8.8]
    for i, (title, desc) in enumerate(problems):
        card = s2.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(lefts[i]), Inches(2.0), Inches(3.7), Inches(4.7))
        card.fill.solid()
        card.fill.fore_color.rgb = LIGHT_BG
        card.line.color.rgb = CARD_BORDER
        card.line.width = Pt(1.2)

        tb = s2.shapes.add_textbox(Inches(lefts[i] + 0.2), Inches(2.2), Inches(3.3), Inches(4.3))
        tf = tb.text_frame
        tf.word_wrap = True

        p = tf.paragraphs[0]
        p.text = title
        p.font.name = "Segoe UI"
        p.font.size = Pt(16)
        p.font.bold = True
        p.font.color.rgb = NAVY

        p_desc = tf.add_paragraph()
        p_desc.text = desc
        p_desc.font.name = "Segoe UI"
        p_desc.font.size = Pt(13)
        p_desc.font.color.rgb = DARK
        p_desc.space_before = Pt(14)

    s2.notes_slide.notes_text_frame.text = (
        "[00:35 - 01:25] LATAR BELAKANG MASALAH RIIL:\n\n"
        "\"Bapak Dosen dan rekan-rekan sekalian, proyek ini dilatarbelakangi oleh permasalahan nyata yang sering dihadapi masyarakat perkotaan "
        "saat mengalami kerusakan darurat di rumah—seperti kunci pintu patah, korsleting listrik, atau AC yang tiba-tiba mati total.\n\n"
        "Ada tiga masalah utama yang terjadi di lapangan:\n"
        "Pertama, sulitnya menemukan teknisi yang terpercaya dan cepat tanggap tanpa harus keliling mencari bengkel fisik.\n"
        "Kedua, ketidakpastian biaya pengerjaan. Konsumen sering merasa was-was karena harga jasa kerap dimainkan atau tidak transparan di awal.\n"
        "Ketiga, ketidaktahuan konsumen mengenai jenis kerusakan, sehingga bingung harus memanggil teknisi spesialis apa dan berapa perkiraan biaya normalnya.\n\n"
        "Dari permasalahan inilah, hadir aplikasi BANTUIN sebagai solusi digital satu pintu.\""
    )

    # =========================================================================
    # SLIDE 3: Solusi & Nilai Guna Aplikasi BANTUIN
    # =========================================================================
    s3 = prs.slides.add_slide(blank_layout)
    add_header(s3, "Solusi & Nilai Guna", "Ekosistem BANTUIN: Menghubungkan 3 Aktor Utama", "Mentransformasikan layanan perbaikan rumah tangga menjadi transparan, aman, dan bergaransi.")

    actors = [
        ("Customer (Pelanggan)", AZURE, [
            "Upload foto & konsultasi kerusakan via AI instan.",
            "Buat postingan keluhan perbaikan ke feed teknisi.",
            "Ajukan dan terima negosiasi harga di ruang chat.",
            "Penyelesaian transaksi & cetak riwayat servis."
        ]),
        ("Mitra Teknisi", GREEN, [
            "Melihat keluhan masuk sesuai kategori keahlian.",
            "Memberikan estimasi ongkos pengerjaan yang adil.",
            "Komunikasi privat & konfirmasi jadwal survei.",
            "Menerima pendapatan langsung dari customer."
        ]),
        ("Super Administrator", NAVY, [
            "Memantau seluruh metrik operasional & transaksi.",
            "Manajemen kategori jasa (AC, Listrik, Kunci, dll).",
            "Pusat kendali multi-AI Engine (Gemini & NVIDIA).",
            "Manajemen akun & verifikasi mitra teknisi."
        ])
    ]

    for i, (act_name, act_color, feats) in enumerate(actors):
        card = s3.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(lefts[i]), Inches(2.0), Inches(3.7), Inches(3.8))
        card.fill.solid()
        card.fill.fore_color.rgb = WHITE
        card.line.color.rgb = act_color
        card.line.width = Pt(2.0)

        tb = s3.shapes.add_textbox(Inches(lefts[i] + 0.2), Inches(2.15), Inches(3.3), Inches(3.5))
        tf = tb.text_frame
        tf.word_wrap = True

        p = tf.paragraphs[0]
        p.text = act_name
        p.font.name = "Segoe UI"
        p.font.size = Pt(16)
        p.font.bold = True
        p.font.color.rgb = act_color

        for f in feats:
            p_f = tf.add_paragraph()
            p_f.text = "• " + f
            p_f.font.name = "Segoe UI"
            p_f.font.size = Pt(12)
            p_f.font.color.rgb = DARK
            p_f.space_before = Pt(8)

    # Deal lock banner below
    deal_card = s3.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(6.05), Inches(11.7), Inches(0.95))
    deal_card.fill.solid()
    deal_card.fill.fore_color.rgb = RGBColor(232, 245, 233)
    deal_card.line.color.rgb = RGBColor(165, 214, 167)
    tb_deal = s3.shapes.add_textbox(Inches(1.0), Inches(6.15), Inches(11.3), Inches(0.75))
    tf_deal = tb_deal.text_frame
    tf_deal.word_wrap = True
    p_dl = tf_deal.paragraphs[0]
    p_dl.text = "FITUR UNGGULAN: DEAL LOCK (KESEPAKATAN HARGA RESMI)"
    p_dl.font.name = "Segoe UI"
    p_dl.font.size = Pt(13)
    p_dl.font.bold = True
    p_dl.font.color.rgb = RGBColor(30, 126, 52)
    p_dl2 = tf_deal.add_paragraph()
    p_dl2.text = "Nominal biaya pengerjaan disepakati di ruang chat dan DIKUNCI secara digital sebelum transaksi ditutup demi keamanan kedua pihak."
    p_dl2.font.size = Pt(11)
    p_dl2.font.color.rgb = DARK

    s3.notes_slide.notes_text_frame.text = (
        "[01:25 - 02:15] SOLUSI & NILAI GUNA APLIKASI BANTUIN:\n\n"
        "\"BANTUIN mentransformasikan pencarian teknisi menjadi ekosistem digital yang adil dan transparan. "
        "Dalam dokumen PRD yang saya susun, sistem melayani tiga aktor utama:\n\n"
        "Pertama, Customer: dapat berkonsultasi kerusakan via AI, membuat postingan keluhan, bernegosiasi harga resmi di ruang chat, hingga konfirmasi pembayaran.\n"
        "Kedua, Mitra Teknisi: menerima notifikasi keluhan sesuai bidang keahliannya (seperti teknisi listrik, AC, atau tukang kunci), memberikan penawaran biaya transparan, dan menyelesaikan pesanan.\n"
        "Ketiga, Super Admin: memantau seluruh metrik operasional, transaksi servis, manajemen akun pengguna, serta mengonfigurasi mesin AI secara terpusat.\n\n"
        "Nilai keunggulan BANTUIN terletak pada fitur kesepakatan harga atau Deal Lock, di mana nominal biaya dikunci sebelum pengerjaan selesai demi keamanan kedua belah pihak.\""
    )

    # =========================================================================
    # SLIDE 4: Kebutuhan Sistem (PRD: Fungsional & Non-Fungsional)
    # =========================================================================
    s4 = prs.slides.add_slide(blank_layout)
    add_header(s4, "Spesifikasi Kebutuhan PRD", "Kebutuhan Fungsional & Non-Fungsional Sistem", "Standar kebutuhan perangkat lunak berbasis kaidah rekayasa IEEE 830 / ISO 29148.")

    # Left: Functional
    col1 = s4.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(2.0), Inches(5.7), Inches(5.0))
    col1.fill.solid()
    col1.fill.fore_color.rgb = WHITE
    col1.line.color.rgb = AZURE
    col1.line.width = Pt(1.5)

    tb1 = s4.shapes.add_textbox(Inches(1.0), Inches(2.15), Inches(5.3), Inches(4.6))
    tf1 = tb1.text_frame
    tf1.word_wrap = True
    p1 = tf1.paragraphs[0]
    p1.text = "Spesifikasi Kebutuhan Fungsional (Functional)"
    p1.font.name = "Segoe UI"
    p1.font.size = Pt(16)
    p1.font.bold = True
    p1.font.color.rgb = NAVY

    f_points = [
        "REQ-F01: Autentikasi RBAC (Customer, Teknisi, Admin) & Session Guard.",
        "REQ-F02: Modul Konsultasi AI Kerusakan (Upload foto + Kronologi).",
        "REQ-F03: Manajemen Permintaan Jasa Servis & Filter Kategori.",
        "REQ-F04: Live Chat privat interaktif (1-Screen Viewport Locked Layout).",
        "REQ-F05: Negosiasi harga resmi di chat (Ajukan, Terima Deal, Tolak).",
        "REQ-F06: Konfirmasi Pembayaran Selesai (Cash/QRIS) & Riwayat Transaksi."
    ]
    for pt in f_points:
        p_item = tf1.add_paragraph()
        p_item.text = pt
        p_item.font.size = Pt(11.5)
        p_item.font.color.rgb = DARK
        p_item.space_before = Pt(8)

    # Right: Non-Functional
    col2 = s4.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(6.8), Inches(2.0), Inches(5.7), Inches(5.0))
    col2.fill.solid()
    col2.fill.fore_color.rgb = WHITE
    col2.line.color.rgb = RGBColor(99, 102, 241) # Indigo
    col2.line.width = Pt(1.5)

    tb2 = s4.shapes.add_textbox(Inches(7.0), Inches(2.15), Inches(5.3), Inches(4.6))
    tf2 = tb2.text_frame
    tf2.word_wrap = True
    p2 = tf2.paragraphs[0]
    p2.text = "Kebutuhan Non-Fungsional (Kualitas Rekayasa)"
    p2.font.name = "Segoe UI"
    p2.font.size = Pt(16)
    p2.font.bold = True
    p2.font.color.rgb = NAVY

    nf_points = [
        "NFR-01 (Arsitektur): Pola Clean PHP MVC murni (Separation of Concerns).",
        "NFR-02 (Keamanan): Anti-SQL Injection via PDO Prepared Statements.",
        "NFR-03 (Proteksi Web): Anti-CSRF Token & Anti-XSS (HTML Sanitization).",
        "NFR-04 (Keandalan): Password Bcrypt Hash & Validasi Unik Email/WA.",
        "NFR-05 (Ketersediaan): Contextual Heuristic AI Fallback (Zero crash).",
        "NFR-06 (Usabilitas): Microsoft Fluent 2 Azure UI responsif desktop & mobile."
    ]
    for pt in nf_points:
        p_item = tf2.add_paragraph()
        p_item.text = pt
        p_item.font.size = Pt(11.5)
        p_item.font.color.rgb = DARK
        p_item.space_before = Pt(8)

    s4.notes_slide.notes_text_frame.text = (
        "[02:15 - 03:05] KEBUTUHAN SISTEM (FUNGSIONAL & NON-FUNGSIONAL):\n\n"
        "\"Secara spesifikasi rekayasa, kebutuhan fungsional utama yang didefinisikan dalam dokumen PRD mencakup:\n"
        "Sistem autentikasi berbasis peran (RBAC) yang aman,\n"
        "Modul konsultasi diagnosis kerusakan otomatis,\n"
        "Sistem posting keluhan dan penawaran teknisi,\n"
        "Modul Live Chat internal dengan fitur negosiasi dan persetujuan harga resmi,\n"
        "Serta pencatatan riwayat transaksi dan invoice digital.\n\n"
        "Sedangkan dari sisi Kebutuhan Non-Fungsional, aplikasi ini dirancang berbasis arsitektur Clean PHP MVC, "
        "menggunakan database MySQL dengan PDO Prepared Statements untuk mencegah celah SQL Injection, "
        "tampilan antarmuka responsif Bootstrap 5 Fluent, serta latensi respons API yang cepat di bawah 2 detik.\""
    )

    # =========================================================================
    # SLIDE 5: Peran Strategis Fitur AI
    # =========================================================================
    s5 = prs.slides.add_slide(blank_layout)
    add_header(s5, "Fitur Kecerdasan Artifisial", "Peran Strategis Fitur AI (Bukan Sekadar Gimmick)", "Mengintegrasikan Computer Vision dan Large Language Model untuk efisiensi operasional.")

    ai_features = [
        ("1. AI Damage Diagnosis & Cost Estimator",
         "Konsumen cukup mengunggah foto kerusakan dan kronologi singkat. Mesin AI secara multimodal menganalisis probabilitas kerusakan fisik, menyarankan kategori teknisi yang tepat, dan menghitung estimasi rentang biaya servis yang wajar (Rp Min - Rp Max)."),
        ("2. Chatbot AI Assistant 24 Jam",
         "Asisten percakapan cerdas yang standby di landing page selama 24 jam untuk menjawab pertanyaan seputar panduan tarif, garansi layanan 30 hari, serta alur pemesanan secara interaktif."),
        ("3. Arsitektur Multi-Model (Gemini & NVIDIA NIM)",
         "Mendukung pergantian fleksibel antara Google Gemini 1.5 Flash dan akselerasi GPU NVIDIA NIM (Mistral Large & Nemotron). Dilengkapi Contextual Expert Heuristic Fallback agar sistem tetap 100% aktif jika internet terganggu.")
    ]

    top_y = [2.0, 3.65, 5.3]
    for i, (title, desc) in enumerate(ai_features):
        card = s5.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(top_y[i]), Inches(11.7), Inches(1.45))
        card.fill.solid()
        card.fill.fore_color.rgb = LIGHT_BG
        card.line.color.rgb = CARD_BORDER
        card.line.width = Pt(1.2)

        tb = s5.shapes.add_textbox(Inches(1.0), Inches(top_y[i] + 0.1), Inches(11.3), Inches(1.25))
        tf = tb.text_frame
        tf.word_wrap = True

        p = tf.paragraphs[0]
        p.text = title
        p.font.name = "Segoe UI"
        p.font.size = Pt(15)
        p.font.bold = True
        p.font.color.rgb = AZURE

        p_desc = tf.add_paragraph()
        p_desc.text = desc
        p_desc.font.name = "Segoe UI"
        p_desc.font.size = Pt(12)
        p_desc.font.color.rgb = DARK
        p_desc.space_before = Pt(4)

    s5.notes_slide.notes_text_frame.text = (
        "[03:05 - 03:40] PERAN STRATEGIS FITUR AI (AI VALUE PROPOSITION):\n\n"
        "\"Sesuai dengan standar kurikulum OBE pada mata kuliah ini, integrasi AI pada BANTUIN bukan sekadar tempelan atau gimmick, "
        "melainkan menjadi fitur inti pemecah masalah, yaitu:\n\n"
        "Pertama, AI Damage Diagnosis & Cost Estimator: Konsumen cukup mengunggah foto dan menceritakan kronologi kerusakan. "
        "Mesin AI secara cerdas menganalisis potensi kerusakan, merekomendasikan kategori teknisi yang tepat, dan memberikan rentang estimasi biaya wajar.\n"
        "Kedua, Chatbot AI 24 Jam: Asisten cerdas di beranda yang siap menjawab panduan layanan secara interaktif.\n"
        "Ketiga, Multi-Model Engine: Sistem BANTUIN dirancang fleksibel mendukung provider AI terkemuka seperti Google Gemini dan akselerasi GPU NVIDIA NIM.\""
    )

    # =========================================================================
    # SLIDE 6: Kesimpulan & Roadmap Menuju SDD UML
    # =========================================================================
    s6 = prs.slides.add_slide(blank_layout)
    add_header(s6, "Penutup & Langkah Selanjutnya", "Kesimpulan PRD & Roadmap Menuju SDD (Tahap 2)", "Validasi spesifikasi kebutuhan mandiri siap dilanjutkan ke perancangan arsitektur UML.")

    box = s6.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(2.0), Inches(11.7), Inches(4.8))
    box.fill.solid()
    box.fill.fore_color.rgb = WHITE
    box.line.color.rgb = GREEN
    box.line.width = Pt(2.0)

    tb = s6.shapes.add_textbox(Inches(1.2), Inches(2.3), Inches(10.9), Inches(4.2))
    tf = tb.text_frame
    tf.word_wrap = True

    p = tf.paragraphs[0]
    p.text = "Capaian Luaran Progres 1 (Tahap Inisiasi PRD):"
    p.font.name = "Segoe UI"
    p.font.size = Pt(18)
    p.font.bold = True
    p.font.color.rgb = NAVY

    conclusions = [
        "Identifikasi Masalah & Solusi Terdefinisi Jelas: BANTUIN memecahkan asimetri informasi dan ketidakpastian biaya servis secara nyata.",
        "Kebutuhan Fungsional & Non-Fungsional Terstandarisasi: Mematuhi kaidah IEEE 830 / ISO 29148 dengan 9 User Stories dan kriteria penerimaan terukur.",
        "Fitur AI Berdampak Tinggi: Diagnosis foto kerusakan dan chatbot 24 jam terbukti memberikan nilai guna langsung bagi konsumen.",
        "Kesiapan Menuju Tahap 2 (SDD): Landasan kebutuhan ini siap dimodelkan ke dalam 4 Diagram UML Utama (Use Case, Activity, Class SOLID, Sequence Diagram) serta Prototipe Interaktif UI/UX Figma."
    ]

    for c in conclusions:
        p_c = tf.add_paragraph()
        p_c.text = "✔  " + c
        p_c.font.name = "Segoe UI"
        p_c.font.size = Pt(13)
        p_c.font.color.rgb = DARK
        p_c.space_before = Pt(14)

    p_close = tf.add_paragraph()
    p_close.text = "Terima Kasih atas Perhatian Bapak Dosen Pengampu & Rekan-Rekan Mahasiswa."
    p_close.font.name = "Segoe UI"
    p_close.font.size = Pt(14)
    p_close.font.bold = True
    p_close.font.color.rgb = AZURE
    p_close.space_before = Pt(20)

    s6.notes_slide.notes_text_frame.text = (
        "[03:40 - 04:00] PENUTUP & ROADMAP TAHAP SELANJUTNYA:\n\n"
        "\"Sebagai kesimpulan, Dokumen PRD ini telah menjadi landasan spesifikasi kebutuhan yang jelas dan terukur untuk pembangunan aplikasi BANTUIN. "
        "Pada tahap selanjutnya, saya akan melanjutkan ke perancangan arsitektur dan pemodelan 4 Diagram UML Utama pada Dokumen SDD.\n\n"
        "Demikian presentasi Progres 1 ini saya sampaikan. Terima kasih banyak atas perhatian Bapak Dosen dan rekan-rekan. "
        "Wassalamu’alaikum Warahmatullahi Wabarakatuh.\""
    )

    output_path = "PRESENTASI_PROGRES_1_BANTUIN.pptx"
    prs.save(output_path)
    print(f"PowerPoint file saved successfully at: {output_path}")

if __name__ == "__main__":
    build_deck()
