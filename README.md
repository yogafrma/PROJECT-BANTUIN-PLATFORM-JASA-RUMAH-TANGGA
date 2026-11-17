# BANTUIN — Platform Layanan Jasa Perbaikan Rumah Tangga Berbasis Web & AI
### Tugas Proyek Akhir Mandiri Rekayasa Perangkat Lunak (TIKB2503021) — Semester 3
**Program Studi S1 Teknik Informatika — STMIK IKMI Cirebon**

- **Nama Mahasiswa:** Yoga Fratama Fahdi Sardi
- **NIM:** 41257243
- **Kelas:** 1C (2025)
- **Dosen Pengampu:** Yudhistira Arie Wijaya, M.Kom., CDS.
- **Dokumen Lengkap Proyek (PRD + SDD UML + Testing):** [DOKUMEN_PROYEK_AKHIR_BANTUIN.md](DOKUMEN_PROYEK_AKHIR_BANTUIN.md)

---

## 🎬 Tautan Video Presentasi Proyek (OBE Deliverables)
1. **Video Progres 1 (PRD & Konsep Aplikasi - Durasi 3–5 Menit):** [Tautan Video Progres 1 (YouTube/Drive)](#)
2. **Video Progres 2 (Pemodelan UML & UI/UX Figma - Durasi 5–7 Menit):** [Tautan Video Progres 2 (YouTube/Drive)](#)
3. **Video Final (Live Demo Aplikasi Web & Sistem AI - Durasi 8–12 Menit):** [Tautan Video Final Demo (YouTube/Drive)](#)

---

## 🚀 Fitur & Lingkup Implementasi BANTUIN

- **Arsitektur:** Custom PHP MVC murni (tanpa framework eksternal).
- **Frontend:** HTML5, CSS3 kustom modern, Bootstrap 5.3, Bootstrap Icons, Google Fonts.
- **Database Engine:** MySQL dengan PDO Wrapper (Prepared Statements aman dari SQL Injection).
- **Sistem Authentication & RBAC (Role-Based Access Control):**
  - Hashing password dengan `password_hash()` (Bcrypt).
  - Validasi form Login & Register (WhatsApp wajib, Email unik).
  - Session authentication & proteksi role (`admin`, `customer`, `technician`).
- **Fitur Utama: Konsultasi AI (`/customer/consultation`):**
  - **Upload Foto Kerusakan:** Dukungan file gambar (`jpg`, `png`, `webp`) dengan preview otomatis.
  - **Input Detail & Kronologi:** Deskripsi gejala kerusakan dan alur kejadian.
  - **AIService Terpisah (`app/services/AIService.php`):**
    - Diagnosis terstruktur dalam Bahasa Indonesia: Kemungkinan kerusakan, Kategori teknisi rekomendasi, Estimasi rentang harga (Min & Max), Penjelasan analisis, dan Draf postingan.
    - Diksi ramah & berbasis kemungkinan (*"berdasarkan informasi yang diberikan, kemungkinan..."*).
    - Mesin heuristik cerdas / fallback kontekstual sehingga fitur dapat diuji 100% tanpa konfigurasi API key eksternal.
  - **Tampilan Hasil Analisis (Bootstrap 5 Card):**
    - Kartu diagnosis probabilitas kerusakan.
    - Estimasi rentang biaya Rupiah.
    - Rekomendasi teknisi + Tombol Chat WhatsApp.
    - Draf postingan siap salin (Copy Draft).
    - Riwayat konsultasi tersimpan di tabel `consultations`.
- **Fitur Komunikasi WhatsApp Terintegrasi (`WhatsAppService` & Helpers):**
  - **Normalisasi Nomor Otomatis:** Format nomor telepon (`08...`, `+628...`, `0812-3456-789`) secara otomatis divalidasi dan dinormalisasi ke standar internasional `628...` untuk URL `https://wa.me/...`.
  - **Generator Pesan Otomatis:**
    - Template Customer ke Teknisi: *"Halo [Nama Teknisi], saya tertarik dengan bantuan Anda untuk keluhan: [Judul Postingan]. Saya ingin mendiskusikan detail perbaikannya."*
    - Template Konsultasi AI ke Teknisi: *"Halo [Nama Teknisi], saya telah berkonsultasi melalui AI di platform Teknisi Rumah Tangga mengenai: [Topik]. Apakah Anda bisa membantu menjadwalkan kunjungan perbaikan?"*
  - **Tombol "Go to WhatsApp":** Desain responsif Bootstrap 5 pada detail postingan, hasil AI, dan dashboard teknisi yang berfungsi optimal di desktop (WhatsApp Web) maupun perangkat seluler (Aplikasi WhatsApp) tanpa perlu chat internal.

---

## 🔑 Akun Demo Default untuk Pengujian

Semua akun default menggunakan kata sandi: `password123`

| Role | Email | Password | Dashboard URL | Fitur Utama |
| :--- | :--- | :--- | :--- | :--- |
| **Customer** | `customer@gmail.com` | `password123` | `/customer` | **Konsultasi AI (`/customer/consultation`)**, Postingan Saya, Rekomendasi Teknisi |
| **Admin** | `admin@teknisirumah.com` | `password123` | `/admin` | 4 Statistik Utama, **Pusat Konfigurasi AI Multi-Model (Gemini/ChatGPT/Claude/OpenRouter/Groq)**, Kelola Kategori Jasa (CRUD), Monitoring |
| **Technician** | `teknisi@gmail.com` | `password123` | `/technician` | Profil Keahlian, Postingan per Kategori, Respons Saya |


---

## 📂 Struktur Direktori Proyek

```text
lat1c/
├── app/
│   ├── config/
│   │   ├── config.php          # Konfigurasi konstanta (BASE_URL, DB, APP_NAME)
│   │   └── database.sql        # Skrip database (users, categories, technician_profiles, posts, responses, consultations)
│   ├── core/
│   │   ├── App.php             # Core Router & URL Dispatcher
│   │   ├── Auth.php            # Core Auth & Middleware RBAC
│   │   ├── Controller.php      # Base Controller
│   │   ├── Database.php        # PDO Database Wrapper
│   │   ├── Model.php           # Base Model
│   │   └── Helper.php          # Helper functions
│   ├── services/
│   │   └── AIService.php       # AI Consultation Service (Backend diagnosis & structured output)
│   ├── controllers/
│   │   ├── HomeController.php  # Landing page
│   │   ├── AuthController.php  # Login, register, logout
│   │   ├── CategoryController.php # CRUD Manajemen Kategori Jasa (Khusus Admin)
│   │   ├── CustomerController.php # Customer dashboard & Konsultasi AI
│   │   ├── TechnicianController.php # Technician dashboard
│   │   ├── AdminController.php # Admin dashboard
│   │   ├── ProfileController.php # Halaman profil adaptif per role
│   │   └── NotFoundController.php # Handler 404
│   ├── models/
│   │   ├── CategoryModel.php   # Model CRUD kategori layanan
│   │   ├── UserModel.php       # Model autentikasi & user counts
│   │   ├── PostModel.php       # Model postingan permintaan jasa & respons
│   │   ├── TechnicianProfileModel.php # Model profil teknisi & rekomendasi
│   │   └── ConsultationModel.php # Model riwayat konsultasi AI
│   └── views/
│       ├── layouts/
│       │   ├── header.php      # Header & Navbar adaptif (Link Konsultasi AI & Kelola Kategori)
│       │   └── footer.php      # Footer & status sistem
│       ├── home/
│       │   └── index.php       # Landing Page
│       ├── auth/
│       │   ├── login.php       # Halaman Login
│       │   └── register.php    # Halaman Register
│       ├── admin/
│       │   ├── dashboard.php   # Dashboard Admin
│       │   └── categories/     # CRUD Views Kategori
│       ├── customer/
│       │   ├── dashboard.php   # Dashboard Customer
│       │   └── consultation.php # Halaman Formulir & Hasil Analisis Konsultasi AI
│       ├── technician/
│       │   └── dashboard.php   # Dashboard Teknisi
│       ├── profile/
│       │   └── index.php       # Halaman Pengaturan Profil
│       └── errors/
│           └── 404.php         # Halaman 404
├── public/
│   ├── css/
│   │   └── style.css           # Custom styling modern
│   ├── js/
│   │   └── main.js             # Frontend interactive scripts
│   ├── uploads/
│   │   └── consultations/      # Folder upload foto kerusakan
│   ├── .htaccess               # URL Rewriting
│   └── index.php               # Single Entry Point aplikasi
├── .htaccess                   # Root htaccess
└── README.md                   # Dokumentasi
```

---

## ⚙️ Cara Menjalankan Aplikasi

### Opsi 1: Menggunakan XAMPP / Apache
1. Pastikan folder proyek berada di `htdocs` XAMPP (`C:/xampp.new/htdocs/lat1c`).
2. Nyalakan modul **Apache** dan **MySQL** di XAMPP Control Panel.
3. Buka browser: `http://localhost/lat1c/`
4. *(Opsional)* Impor database schema dari [database.sql](file:///c:/xampp.new/htdocs/lat1c/app/config/database.sql).

### Opsi 2: Menggunakan PHP Built-in Server
```bash
cd c:/xampp.new/htdocs/lat1c
php -S localhost:8000 -t public
```
Akses di browser: `http://localhost:8000`.
