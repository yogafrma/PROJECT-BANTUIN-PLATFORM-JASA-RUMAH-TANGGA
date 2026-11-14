<?php
/**
 * Application Master Configuration
 * Project: BANTUIN — Platform Layanan & Teknisi Rumah Tangga
 * Mata Kuliah: Rekayasa Perangkat Lunak (RPL)
 */

// =========================================================================
// 1. IDENTITAS & LINGKUNGAN APLIKASI
// =========================================================================
define('APP_NAME', 'BANTUIN');
define('APP_TAGLINE', 'Platform Jasa Rumah Tangga');
define('APP_VERSION', '2.0.0');
define('APP_ENV', getenv('APP_ENV') ?: 'development'); // 'development' atau 'production'

// Set Zona Waktu Default Indonesia (WIB)
date_default_timezone_set('Asia/Jakarta');

// =========================================================================
// 2. DETEKSI DUKUNGAN URL (BASE URL)
// =========================================================================
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$baseUrl = rtrim($protocol . $host . $scriptName, '/');

define('BASE_URL', $baseUrl);

// =========================================================================
// 3. KONFIGURASI DATABASE MYSQL
// =========================================================================
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'db_teknisi_rumah');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_CHARSET', 'utf8mb4');

// =========================================================================
// 4. KONFIGURASI NOTIFIKASI EMAIL (SMTP GMAIL — REAL OTP)
// =========================================================================
// Gunakan akun Gmail resmi pengirim beserta 16 digit Sandi Aplikasi (Google App Password)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 465); // 465 (SSL) atau 587 (TLS)
define('SMTP_USER', getenv('SMTP_USER') ?: 'yogafrmai@gmail.com'); // Akun Gmail pengirim
define('SMTP_PASS', getenv('SMTP_PASS') ?: 'ippahfeamabefmqn'); // 16 digit Google App Password
define('SMTP_FROM_NAME', APP_NAME . ' - Platform Jasa');
define('SMTP_FROM_EMAIL', SMTP_USER ?: 'no-reply@bantuin.id');

// =========================================================================
// 5. KONFIGURASI CHATBOT AI & GOOGLE GEMINI
// =========================================================================
// Opsional: Isi dengan API Key dari Google AI Studio (https://aistudio.google.com/app/apikey)
// Jika kosong, sistem otomatis menggunakan BANTUIN Smart Grounded Engine lokal (Zero-config).
define('GEMINI_API_KEY', getenv('GEMINI_API_KEY') ?: '');

// Mode Chatbot: 'php' (Internal Grounded Engine / Gemini) atau 'node' (Proxy port 3001)
define('CHATBOT_MODE', getenv('CHATBOT_MODE') ?: 'php');
define('CHATBOT_NODE_URL', getenv('CHATBOT_NODE_URL') ?: 'http://localhost:3001');

// =========================================================================
// 6. KONTAK BANTUAN & LAYANAN PELANGGAN
// =========================================================================
define('ADMIN_SUPPORT_PHONE', '0895635434025');
define('ADMIN_SUPPORT_EMAIL', 'yogafrmai@gmail.com');
define('ADMIN_WHATSAPP_NUMBER', '62895635434025');
