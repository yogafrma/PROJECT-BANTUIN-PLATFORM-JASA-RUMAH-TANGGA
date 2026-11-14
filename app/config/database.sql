-- Database: db_teknisi_rumah
-- Proyek: Teknisi Rumah Tangga (MVP)

CREATE DATABASE IF NOT EXISTS `db_teknisi_rumah` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_teknisi_rumah`;

-- 1. Tabel Users (Authentication & Multi-Role)
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `whatsapp` VARCHAR(30) NOT NULL,
    `role` ENUM('admin', 'customer', 'technician') NOT NULL DEFAULT 'customer',
    `status` ENUM('active', 'inactive', 'pending') NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Data Akun Default (Password: password123)
INSERT INTO `users` (`id`, `name`, `email`, `password`, `whatsapp`, `role`, `status`) VALUES
(1, 'Administrator Teknisi', 'admin@teknisirumah.com', '$2y$10$bHhrZIXwDC6me0lh7Lzlk.H9tcLehbb2PP9o0rizffpy6q0cbcM4q', '081234567890', 'admin', 'active'),
(2, 'Budi Santoso', 'customer@gmail.com', '$2y$10$bHhrZIXwDC6me0lh7Lzlk.H9tcLehbb2PP9o0rizffpy6q0cbcM4q', '081987654321', 'customer', 'active'),
(3, 'Agus Supriyadi', 'teknisi@gmail.com', '$2y$10$bHhrZIXwDC6me0lh7Lzlk.H9tcLehbb2PP9o0rizffpy6q0cbcM4q', '085678901234', 'technician', 'active'),
(4, 'Hendra Wijaya (Listrik)', 'hendra.listrik@gmail.com', '$2y$10$bHhrZIXwDC6me0lh7Lzlk.H9tcLehbb2PP9o0rizffpy6q0cbcM4q', '081299887766', 'technician', 'active'),
(5, 'Slamet Riyadi (Kunci)', 'slamet.kunci@gmail.com', '$2y$10$bHhrZIXwDC6me0lh7Lzlk.H9tcLehbb2PP9o0rizffpy6q0cbcM4q', '087811223344', 'technician', 'active'),
(6, 'Yoga', 'customer1@gmail.com', '$2y$10$bHhrZIXwDC6me0lh7Lzlk.H9tcLehbb2PP9o0rizffpy6q0cbcM4q', '0895635434025', 'customer', 'active'),
(7, 'yurdika ac gacor', 'yurdika@gmail.com', '$2y$10$bHhrZIXwDC6me0lh7Lzlk.H9tcLehbb2PP9o0rizffpy6q0cbcM4q', '0882002400745', 'technician', 'active')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `whatsapp`=VALUES(`whatsapp`), `role`=VALUES(`role`);

-- 2. Tabel Kategori Jasa (Category Management)
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `name` VARCHAR(150) NOT NULL,
    `description` TEXT NOT NULL,
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `icon` VARCHAR(100) NOT NULL DEFAULT 'bi-tools',
    `badge` VARCHAR(50) DEFAULT 'Populer',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data Awal Kategori Layanan MVP
INSERT INTO `categories` (`id`, `slug`, `name`, `description`, `status`, `icon`, `badge`) VALUES
(1, 'teknisi-listrik', 'Teknisi Listrik', 'Perbaikan korsleting, instalasi panel listrik, stop kontak, lampu rumah, dan cek daya.', 'active', 'bi-lightning-charge-fill', 'Paling Dicari'),
(2, 'teknisi-ac', 'Teknisi AC', 'Service berkala AC, cuci AC, isi ulang freon, perbaikan AC bocor, dan pasang unit baru.', 'active', 'bi-snow', 'Garansi Dingin'),
(3, 'tukang-kunci', 'Tukang Kunci', 'Duplikat kunci, buka pintu macet/terkunci darurat 24 jam, ganti master lock, dan smart door lock.', 'active', 'bi-key-fill', 'Darurat 24 Jam'),
(4, 'teknisi-elektronik', 'Teknisi Elektronik', 'Reparasi TV, kulkas, mesin cuci, microwave, pompa air, dan peralatan elektronik rumah tangga lainnya.', 'active', 'bi-tv-fill', 'Ahli & Cepat')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `description`=VALUES(`description`), `status`=VALUES(`status`), `icon`=VALUES(`icon`);

-- 3. Tabel Technician Profiles
CREATE TABLE IF NOT EXISTS `technician_profiles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL UNIQUE,
    `category_id` INT NOT NULL,
    `description` TEXT NOT NULL,
    `location` VARCHAR(150) NOT NULL,
    `rating` DECIMAL(3,2) NOT NULL DEFAULT 5.00,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Data Profil Teknisi
INSERT INTO `technician_profiles` (`id`, `user_id`, `category_id`, `description`, `location`, `rating`) VALUES
(1, 3, 2, 'Spesialis perbaikan, cuci AC berkala, freon R32/R410A, dan instalasi AC inverter berbagai merek sejak 2018.', 'Jakarta Selatan & sekitarnya', 4.90),
(2, 4, 1, 'Ahli instalasi kelistrikan rumah tinggal, panel MCB, perbaikan korslet darurat 24 jam bersertifikat.', 'Jakarta Barat & Tangerang', 4.85),
(3, 5, 3, 'Tukang kunci profesional spesialis pintu kayu, gerbang, brankas, dan instalasi smart digital door lock.', 'Jakarta Timur & Bekasi', 4.95)
ON DUPLICATE KEY UPDATE `description`=VALUES(`description`), `location`=VALUES(`location`), `rating`=VALUES(`rating`);

-- 4. Tabel Konsultasi AI (Customer AI Consultation)
CREATE TABLE IF NOT EXISTS `consultations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `customer_id` INT NOT NULL,
    `image` VARCHAR(255) NULL,
    `damage_detail` TEXT NOT NULL,
    `chronology` TEXT NOT NULL,
    `ai_result` LONGTEXT NOT NULL,
    `estimated_min_price` INT NOT NULL DEFAULT 0,
    `estimated_max_price` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`customer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Data Konsultasi AI
INSERT INTO `consultations` (`id`, `customer_id`, `image`, `damage_detail`, `chronology`, `ai_result`, `estimated_min_price`, `estimated_max_price`, `created_at`) VALUES
(1, 2, NULL, 'AC mengeluarkan angin biasa dan terdengar suara desisan pelan di pipa tembaga luar.', 'Sejak 2 hari lalu setelah pemakaian seharian, suhu ruangan tidak turun dan pipa ada embun es.', '{"possible_damage":"Kemungkinan kebocoran halus pada sambungan pipa refrigrant (freon) atau kondensor kotor tersumbat debu.","category_name":"Teknisi AC","category_slug":"teknisi-ac","estimated_min_price":85000,"estimated_max_price":250000,"explanation":"Berdasarkan informasi yang diberikan, gejala angin tidak dingin disertai desisan dan es di pipa, terdapat indikasi tekanan freon menurun akibat kebocoran pada flare nut sambungan atau evaporator unit indoor yang kotor. Disarankan dilakukan pemeriksaan kebocoran, pembersihan cuci steam, dan pengisian ulang freon sesuai kapasitas unit.","draft_post_title":"Perbaikan AC Tidak Dingin & Cek Kebocoran Freon","draft_post_description":"Membutuhkan teknisi AC untuk cek unit AC split 1 PK yang hanya keluar angin dan indikasi freon berkurang di area Jakarta Selatan."}', 85000, 250000, NOW() - INTERVAL 2 DAY)
ON DUPLICATE KEY UPDATE `damage_detail`=VALUES(`damage_detail`);

-- 5. Tabel Postingan Keluhan Customer (Posts)
CREATE TABLE IF NOT EXISTS `posts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `customer_id` INT NOT NULL,
    `consultation_id` INT NULL,
    `category_id` INT NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT NOT NULL,
    `image` VARCHAR(255) NULL,
    `ai_analysis` TEXT NULL,
    `estimated_min_price` INT NOT NULL DEFAULT 0,
    `estimated_max_price` INT NOT NULL DEFAULT 0,
    `status` ENUM('Mencari Teknisi', 'Ada Teknisi', 'Selesai', 'Dibatalkan') NOT NULL DEFAULT 'Mencari Teknisi',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`customer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`consultation_id`) REFERENCES `consultations`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Data Postingan Keluhan
INSERT INTO `posts` (`id`, `customer_id`, `consultation_id`, `category_id`, `title`, `description`, `image`, `ai_analysis`, `estimated_min_price`, `estimated_max_price`, `status`, `created_at`) VALUES
(1, 2, 1, 2, 'Perbaikan AC Tidak Dingin & Cek Kebocoran Freon', 'Membutuhkan teknisi AC untuk cek unit AC split 1 PK yang hanya keluar angin biasa dan pipa luar timbul es di area Jakarta Selatan.', NULL, 'Kemungkinan kebocoran halus pada sambungan pipa refrigrant (freon) atau kondensor kotor tersumbat debu.', 85000, 250000, 'Ada Teknisi', NOW() - INTERVAL 1 DAY),
(2, 2, NULL, 1, 'Lampu Dapur & Stop Kontak Mati Mendadak (Korslet)', 'Setelah menyalakan oven listrik, MCB turun dan stop kontak dapur tidak menyala lagi. Butuh teknisi periksa kabel panel.', NULL, 'Kemungkinan terjadi arus pendek (korsleting) atau kelebihan beban pada jalur MCB.', 80000, 300000, 'Mencari Teknisi', NOW() - INTERVAL 3 HOUR),
(3, 2, NULL, 3, 'Kunci Pintu Kamar Terjebak dari Dalam', 'Gagang pintu macet dan kunci putar tidak bisa ditarik. Butuh bantuan cepat.', NULL, 'Kemungkinan pin silinder aus atau mekanisme kait pintu macet.', 50000, 200000, 'Selesai', NOW() - INTERVAL 5 DAY)
ON DUPLICATE KEY UPDATE `title`=VALUES(`title`), `status`=VALUES(`status`);

-- 6. Tabel Response Penawaran Teknisi (Technician Responses)
CREATE TABLE IF NOT EXISTS `technician_responses` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `post_id` INT NOT NULL,
    `technician_id` INT NOT NULL,
    `status` ENUM('offered', 'selected', 'rejected') NOT NULL DEFAULT 'offered',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_post_tech` (`post_id`, `technician_id`),
    FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`technician_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Data Response Penawaran Teknisi
INSERT INTO `technician_responses` (`id`, `post_id`, `technician_id`, `status`, `created_at`) VALUES
(1, 1, 3, 'selected', NOW() - INTERVAL 1 DAY),
(2, 2, 4, 'offered', NOW() - INTERVAL 2 HOUR)
ON DUPLICATE KEY UPDATE `status`=VALUES(`status`);

-- 7. Tabel Percakapan Internal (Internal Conversations)
CREATE TABLE IF NOT EXISTS `conversations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `post_id` INT NOT NULL,
    `customer_id` INT NOT NULL,
    `technician_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`customer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`technician_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_conversation` (`post_id`, `customer_id`, `technician_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Tabel Pesan Chat (Chat Messages)
CREATE TABLE IF NOT EXISTS `messages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `conversation_id` INT NOT NULL,
    `sender_id` INT NOT NULL,
    `message` TEXT NOT NULL,
    `is_read` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`conversation_id`) REFERENCES `conversations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`sender_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Data Percakapan Default untuk Post 1 (Customer Budi dan Teknisi Agus)
INSERT INTO `conversations` (`id`, `post_id`, `customer_id`, `technician_id`, `created_at`) VALUES
(1, 1, 2, 3, NOW() - INTERVAL 1 DAY)
ON DUPLICATE KEY UPDATE `post_id`=VALUES(`post_id`);

INSERT INTO `messages` (`id`, `conversation_id`, `sender_id`, `message`, `is_read`, `created_at`) VALUES
(1, 1, 2, 'Halo Mas Agus, terima kasih sudah menerima pekerjaan ini. Kira-kira kapan bisa datang cek AC ke lokasi?', 1, NOW() - INTERVAL 23 HOUR),
(2, 1, 3, 'Halo Pak Budi, sama-sama. Saya bisa datang hari ini sekitar jam 2 siang. Apakah lokasinya sesuai alamat di aplikasi?', 1, NOW() - INTERVAL 22 HOUR),
(3, 1, 2, 'Baik, jam 2 siang pas. Lokasi tepat di Jalan Kemang No. 12 ya mas.', 0, NOW() - INTERVAL 21 HOUR)
ON DUPLICATE KEY UPDATE `message`=VALUES(`message`);

-- 9. Tabel Notifikasi In-App (In-App Notifications)
CREATE TABLE IF NOT EXISTS `notifications` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `type` VARCHAR(50) NOT NULL DEFAULT 'system',
    `title` VARCHAR(200) NOT NULL,
    `message` TEXT NOT NULL,
    `link` VARCHAR(255) NOT NULL DEFAULT '',
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_user_unread` (`user_id`, `is_read`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Data Notifikasi Demo (untuk Customer Budi & Teknisi Agus)
INSERT INTO `notifications` (`user_id`, `type`, `title`, `message`, `link`, `is_read`, `created_at`) VALUES
(2, 'new_response',      'Teknisi Baru Merespons!',        'Agus Supriyadi telah mengajukan tawaran bantuan untuk postingan Anda: "Perbaikan AC Tidak Dingin".', 'posts/detail/1', 0, NOW() - INTERVAL 1 DAY),
(2, 'response_selected', 'Teknisi Berhasil Dipilih!',      'Anda telah memilih Agus Supriyadi sebagai teknisi untuk postingan "Perbaikan AC Tidak Dingin". Percakapan chat sudah dibuka.', 'chat', 1, NOW() - INTERVAL 23 HOUR),
(3, 'response_selected', 'Tawaran Anda Diterima!',         'Pelanggan Budi Santoso telah memilih Anda untuk menangani: "Perbaikan AC Tidak Dingin & Cek Kebocoran Freon". Segera hubungi pelanggan via Chat.', 'chat', 0, NOW() - INTERVAL 22 HOUR),
(2, 'chat_message',      'Pesan Baru dari Agus Supriyadi', 'Halo Pak Budi, sama-sama. Saya bisa datang hari ini sekitar jam 2 siang...', 'chat/room/1', 1, NOW() - INTERVAL 22 HOUR)
ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);

-- 10. Tabel Pengaturan Sistem & AI Multi-Model (System Settings)
CREATE TABLE IF NOT EXISTS `system_settings` (
    `setting_key` VARCHAR(100) NOT NULL PRIMARY KEY,
    `setting_value` MEDIUMTEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data Awal Konfigurasi AI Default (Google Gemini / Internal Engine)
INSERT INTO `system_settings` (`setting_key`, `setting_value`) VALUES
('ai_active_provider', 'gemini'),
('ai_api_key', ''),
('ai_model', 'gemini-1.5-flash'),
('ai_custom_url', ''),
('ai_temperature', '0.6'),
('ai_fallback_enabled', '1')
ON DUPLICATE KEY UPDATE `setting_key`=VALUES(`setting_key`);

