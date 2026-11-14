<?php
/**
 * Email Service (Pure Native PHP SMTP Mailer)
 * Project: Teknisi Rumah Tangga (BANTUIN)
 * 
 * Supports sending real emails using SMTP (e.g. Gmail SSL port 465 or TLS port 587)
 * without requiring Composer or external dependencies.
 */

class EmailService {

    /**
     * Mask an email address for privacy display (e.g. budi.santoso@gmail.com -> b***o@gmail.com)
     *
     * @param string $email
     * @return string
     */
    public static function maskEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }

        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1] ?? '';

        $len = strlen($name);
        if ($len <= 2) {
            $maskedName = substr($name, 0, 1) . '*';
        } elseif ($len <= 4) {
            $maskedName = substr($name, 0, 1) . str_repeat('*', $len - 2) . substr($name, -1);
        } else {
            $maskedName = substr($name, 0, 2) . str_repeat('*', $len - 4) . substr($name, -2);
        }

        return $maskedName . '@' . $domain;
    }

    /**
     * Send OTP Verification Email
     *
     * @param string $toEmail Recipient email address
     * @param string $recipientName Recipient full name
     * @param string $otpCode 6-digit numeric OTP code
     * @return array ['success' => bool, 'message' => string]
     */
    public static function sendOtpEmail($toEmail, $recipientName, $otpCode) {
        $subject = "Kode Verifikasi Pendaftaran Akun - " . APP_NAME;
        $appName = defined('APP_NAME') ? APP_NAME : 'BANTUIN';

        // Professional HTML Email Body
        $htmlContent = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>' . htmlspecialchars($subject) . '</title>
<style>
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 20px; color: #1e293b; }
    .email-container { max-width: 520px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
    .email-header { background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); padding: 32px 24px; text-align: center; color: #ffffff; }
    .email-header h1 { margin: 0; font-size: 24px; font-weight: 800; letter-spacing: 0.5px; }
    .email-header p { margin: 6px 0 0; font-size: 13px; opacity: 0.9; }
    .email-body { padding: 32px 28px; }
    .greeting { font-size: 16px; font-weight: 600; margin-bottom: 12px; }
    .message { font-size: 14px; line-height: 1.6; color: #475569; margin-bottom: 24px; }
    .otp-box { background: #f1f5f9; border: 2px dashed #cbd5e1; border-radius: 12px; padding: 20px; text-align: center; margin-bottom: 24px; }
    .otp-label { font-size: 12px; text-transform: uppercase; font-weight: 700; color: #64748b; letter-spacing: 1px; margin-bottom: 8px; }
    .otp-code { font-family: "Courier New", Courier, monospace; font-size: 36px; font-weight: 800; color: #0d6efd; letter-spacing: 8px; margin: 0; }
    .otp-expiry { font-size: 12px; color: #ef4444; font-weight: 600; margin-top: 8px; }
    .warning-box { background: #fffbeb; border-left: 4px solid #f59e0b; padding: 12px 16px; border-radius: 6px; font-size: 13px; color: #92400e; line-height: 1.5; margin-bottom: 24px; }
    .email-footer { background: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
</style>
</head>
<body>
<div class="email-container">
    <div class="email-header">
        <h1>' . htmlspecialchars($appName) . '</h1>
        <p>Layanan Perbaikan & Teknisi Rumah Tangga</p>
    </div>
    <div class="email-body">
        <div class="greeting">Halo, ' . htmlspecialchars($recipientName) . '!</div>
        <div class="message">
            Terima kasih telah mendaftar di <strong>' . htmlspecialchars($appName) . '</strong>. Untuk menyelesaikan pembuatan akun dan memastikan keamanan email Anda, silakan gunakan kode verifikasi (OTP) berikut:
        </div>
        <div class="otp-box">
            <div class="otp-label">Kode Verifikasi OTP Anda:</div>
            <div class="otp-code">' . htmlspecialchars($otpCode) . '</div>
            <div class="otp-expiry">⏳ Berlaku selama 5 menit</div>
        </div>
        <div class="warning-box">
            <strong>Penting:</strong> Jangan berikan kode OTP ini kepada siapa pun, termasuk pihak yang mengatasnamakan tim BANTUIN. Jika Anda tidak merasa mendaftar, Anda dapat mengabaikan email ini.
        </div>
        <div class="message" style="margin-bottom: 0;">
            Salam hangat,<br>
            <strong>Tim ' . htmlspecialchars($appName) . '</strong>
        </div>
    </div>
    <div class="email-footer">
        Email ini dikirim otomatis oleh sistem ' . htmlspecialchars($appName) . '. Jangan membalas email ini.
    </div>
</div>
</body>
</html>';

        $plainContent = "Halo {$recipientName},\n\n"
                      . "Kode verifikasi (OTP) pendaftaran akun BANTUIN Anda adalah: {$otpCode}\n\n"
                      . "Kode ini berlaku selama 5 menit. Jangan berikan kode ini kepada siapapun demi keamanan akun Anda.\n\n"
                      . "Terima kasih,\nTim BANTUIN";

        return self::sendSmtp($toEmail, $recipientName, $subject, $htmlContent, $plainContent);
    }

    /**
     * Send Notification Email: Postingan Baru untuk Mitra Teknisi
     */
    public static function sendNewPostNotificationToTechnician($toEmail, $technicianName, $customerName, $postTitle, $categoryName, $postLink) {
        $appName = defined('APP_NAME') ? APP_NAME : 'BANTUIN';
        $subject = "[{$appName}] Permintaan Jasa Baru: " . mb_substr($postTitle, 0, 50) . " ({$categoryName})";

        $htmlContent = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 20px; color: #1e293b; }
    .email-container { max-width: 520px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
    .email-header { background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); padding: 28px 24px; text-align: center; color: #ffffff; }
    .email-header h1 { margin: 0; font-size: 22px; font-weight: 800; }
    .email-body { padding: 28px 24px; }
    .greeting { font-size: 16px; font-weight: 600; margin-bottom: 12px; }
    .info-card { background: #f8fafc; border-left: 4px solid #0d6efd; padding: 16px; border-radius: 8px; margin: 18px 0; font-size: 14px; }
    .btn-action { display: inline-block; background-color: #0d6efd; color: #ffffff !important; padding: 12px 24px; border-radius: 50px; text-decoration: none; font-weight: 600; font-size: 14px; margin-top: 14px; }
    .email-footer { background: #f8fafc; padding: 16px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
</style>
</head>
<body>
<div class="email-container">
    <div class="email-header">
        <h1>' . htmlspecialchars($appName) . '</h1>
        <p style="margin: 4px 0 0; font-size: 13px; opacity: 0.9;">Pemberitahuan Pekerjaan Baru</p>
    </div>
    <div class="email-body">
        <div class="greeting">Halo, ' . htmlspecialchars($technicianName) . '! 👋</div>
        <p style="font-size: 14px; line-height: 1.6; color: #475569;">
            Pelanggan <strong>' . htmlspecialchars($customerName) . '</strong> baru saja mempublikasikan permintaan jasa perbaikan baru di platform BANTUIN:
        </p>
        <div class="info-card">
            <div style="font-weight: 700; color: #0f172a; margin-bottom: 6px;">' . htmlspecialchars($postTitle) . '</div>
            <div style="color: #64748b; font-size: 13px;">Kategori: <span style="color: #0d6efd; font-weight: 600;">' . htmlspecialchars($categoryName) . '</span></div>
        </div>
        <p style="font-size: 14px; color: #475569;">
            Segera periksa detail keluhan dan ajukan tawaran bantuan Anda agar terpilih sebagai teknisi!
        </p>
        <div style="text-align: center; margin: 24px 0;">
            <a href="' . htmlspecialchars($postLink) . '" class="btn-action">Lihat Postingan & Ajukan Bantuan</a>
        </div>
    </div>
    <div class="email-footer">
        Email otomatis dari sistem ' . htmlspecialchars($appName) . '.
    </div>
</div>
</body>
</html>';

        $plainContent = "Halo {$technicianName},\n\n"
                      . "Pelanggan {$customerName} baru saja mempublikasikan permintaan jasa baru di BANTUIN:\n"
                      . "Judul: {$postTitle}\n"
                      . "Kategori: {$categoryName}\n\n"
                      . "Buka tautan berikut untuk melihat keluhan & mengajukan tawaran bantuan:\n"
                      . "{$postLink}\n\n"
                      . "Salam,\nTim BANTUIN";

        return self::sendSmtp($toEmail, $technicianName, $subject, $htmlContent, $plainContent);
    }

    /**
     * Send Notification Email: Ada Teknisi yang Siap Membantu untuk Customer
     */
    public static function sendOfferNotificationToCustomer($toEmail, $customerName, $technicianName, $postTitle, $postLink) {
        $appName = defined('APP_NAME') ? APP_NAME : 'BANTUIN';
        $subject = "[{$appName}] Ada Teknisi yang Siap Membantu: " . mb_substr($postTitle, 0, 50);

        $htmlContent = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 20px; color: #1e293b; }
    .email-container { max-width: 520px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
    .email-header { background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 28px 24px; text-align: center; color: #ffffff; }
    .email-header h1 { margin: 0; font-size: 22px; font-weight: 800; }
    .email-body { padding: 28px 24px; }
    .greeting { font-size: 16px; font-weight: 600; margin-bottom: 12px; }
    .info-card { background: #f0fdf4; border-left: 4px solid #10b981; padding: 16px; border-radius: 8px; margin: 18px 0; font-size: 14px; }
    .btn-action { display: inline-block; background-color: #10b981; color: #ffffff !important; padding: 12px 24px; border-radius: 50px; text-decoration: none; font-weight: 600; font-size: 14px; margin-top: 14px; }
    .email-footer { background: #f8fafc; padding: 16px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
</style>
</head>
<body>
<div class="email-container">
    <div class="email-header">
        <h1>' . htmlspecialchars($appName) . '</h1>
        <p style="margin: 4px 0 0; font-size: 13px; opacity: 0.9;">Tawaran Bantuan Teknisi</p>
    </div>
    <div class="email-body">
        <div class="greeting">Halo, ' . htmlspecialchars($customerName) . '! 👋</div>
        <p style="font-size: 14px; line-height: 1.6; color: #475569;">
            Kabar baik! Mitra Teknisi <strong>' . htmlspecialchars($technicianName) . '</strong> menyatakan siap membantu menangani keluhan Anda:
        </p>
        <div class="info-card">
            <div style="font-weight: 700; color: #065f46; margin-bottom: 6px;">"' . htmlspecialchars($postTitle) . '"</div>
            <div style="color: #047857; font-size: 13px;">Teknisi Penawar: <strong>' . htmlspecialchars($technicianName) . '</strong></div>
        </div>
        <p style="font-size: 14px; color: #475569;">
            Silakan buka postingan Anda untuk meninjau profil teknisi dan mengonfirmasi pilihan Anda.
        </p>
        <div style="text-align: center; margin: 24px 0;">
            <a href="' . htmlspecialchars($postLink) . '" class="btn-action">Lihat Tawaran & Pilih Teknisi</a>
        </div>
    </div>
    <div class="email-footer">
        Email otomatis dari sistem ' . htmlspecialchars($appName) . '.
    </div>
</div>
</body>
</html>';

        $plainContent = "Halo {$customerName},\n\n"
                      . "Mitra Teknisi {$technicianName} siap membantu keluhan Anda: \"{$postTitle}\".\n\n"
                      . "Buka tautan berikut untuk melihat tawaran dan mengonfirmasi teknisi:\n"
                      . "{$postLink}\n\n"
                      . "Salam,\nTim BANTUIN";

        return self::sendSmtp($toEmail, $customerName, $subject, $htmlContent, $plainContent);
    }

    /**
     * Send Notification Email: Customer Mengkonfirmasi Bantuan Anda untuk Teknisi
     */
    public static function sendConfirmationNotificationToTechnician($toEmail, $technicianName, $customerName, $postTitle, $chatLink) {
        $appName = defined('APP_NAME') ? APP_NAME : 'BANTUIN';
        $subject = "[{$appName}] Customer Mengkonfirmasi Bantuan Anda!";

        $htmlContent = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 20px; color: #1e293b; }
    .email-container { max-width: 520px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
    .email-header { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); padding: 28px 24px; text-align: center; color: #ffffff; }
    .email-header h1 { margin: 0; font-size: 22px; font-weight: 800; }
    .email-body { padding: 28px 24px; }
    .greeting { font-size: 16px; font-weight: 600; margin-bottom: 12px; }
    .info-card { background: #fffbeb; border-left: 4px solid #f59e0b; padding: 16px; border-radius: 8px; margin: 18px 0; font-size: 14px; }
    .btn-action { display: inline-block; background-color: #f59e0b; color: #ffffff !important; padding: 12px 24px; border-radius: 50px; text-decoration: none; font-weight: 600; font-size: 14px; margin-top: 14px; }
    .email-footer { background: #f8fafc; padding: 16px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
</style>
</head>
<body>
<div class="email-container">
    <div class="email-header">
        <h1>' . htmlspecialchars($appName) . '</h1>
        <p style="margin: 4px 0 0; font-size: 13px; opacity: 0.9;">Bantuan Terkonfirmasi</p>
    </div>
    <div class="email-body">
        <div class="greeting">Selamat, ' . htmlspecialchars($technicianName) . '! 🎉</div>
        <p style="font-size: 14px; line-height: 1.6; color: #475569;">
            Pelanggan <strong>' . htmlspecialchars($customerName) . '</strong> telah <strong>mengkonfirmasi bantuan Anda</strong> untuk pekerjaan:
        </p>
        <div class="info-card">
            <div style="font-weight: 700; color: #92400e; margin-bottom: 6px;">"' . htmlspecialchars($postTitle) . '"</div>
            <div style="color: #78350f; font-size: 13px;">Pelanggan: <strong>' . htmlspecialchars($customerName) . '</strong></div>
        </div>
        <p style="font-size: 14px; color: #475569;">
            Percakapan Chat Internal dan WhatsApp dengan pelanggan telah siap. Segera hubungi pelanggan untuk menyepakati jadwal kunjungan!
        </p>
        <div style="text-align: center; margin: 24px 0;">
            <a href="' . htmlspecialchars($chatLink) . '" class="btn-action">Buka Chat Sekarang</a>
        </div>
    </div>
    <div class="email-footer">
        Email otomatis dari sistem ' . htmlspecialchars($appName) . '.
    </div>
</div>
</body>
</html>';

        $plainContent = "Selamat {$technicianName}!\n\n"
                      . "Pelanggan {$customerName} telah mengkonfirmasi bantuan Anda untuk: \"{$postTitle}\".\n\n"
                      . "Buka percakapan chat sekarang di:\n"
                      . "{$chatLink}\n\n"
                      . "Salam,\nTim BANTUIN";

        return self::sendSmtp($toEmail, $technicianName, $subject, $htmlContent, $plainContent);
    }

    /**
     * Send Notification Email: Konfirmasi Pemilihan Teknisi Berhasil untuk Customer
     */
    public static function sendConfirmationSuccessToCustomer($toEmail, $customerName, $technicianName, $postTitle, $chatLink, $techWhatsapp = '') {
        $appName = defined('APP_NAME') ? APP_NAME : 'BANTUIN';
        $subject = "[{$appName}] Konfirmasi Berhasil: Teknisi {$technicianName} Siap Membantu Anda!";

        $waButton = '';
        if (!empty($techWhatsapp)) {
            $waMsg = "Halo Teknisi {$technicianName}, saya {$customerName} dari platform BANTUIN yang memilih Anda untuk perbaikan: {$postTitle}.";
            $waUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $techWhatsapp) . "?text=" . urlencode($waMsg);
            $waButton = '<a href="' . htmlspecialchars($waUrl) . '" class="btn-action" style="background-color: #25d366; margin-left: 8px;">WhatsApp Teknisi</a>';
        }

        $htmlContent = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 20px; color: #1e293b; }
    .email-container { max-width: 520px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
    .email-header { background: linear-gradient(135deg, #003049 0%, #024b74 100%); padding: 28px 24px; text-align: center; color: #ffffff; }
    .email-header h1 { margin: 0; font-size: 22px; font-weight: 800; }
    .email-body { padding: 28px 24px; }
    .greeting { font-size: 16px; font-weight: 600; margin-bottom: 12px; }
    .info-card { background: #f0fdf4; border-left: 4px solid #16a34a; padding: 16px; border-radius: 8px; margin: 18px 0; font-size: 14px; }
    .btn-action { display: inline-block; background-color: #003049; color: #ffffff !important; padding: 12px 22px; border-radius: 50px; text-decoration: none; font-weight: 600; font-size: 14px; margin-top: 10px; }
    .email-footer { background: #f8fafc; padding: 16px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
</style>
</head>
<body>
<div class="email-container">
    <div class="email-header">
        <h1>' . htmlspecialchars($appName) . '</h1>
        <p style="margin: 4px 0 0; font-size: 13px; opacity: 0.9;">Konfirmasi Bantuan Berhasil</p>
    </div>
    <div class="email-body">
        <div class="greeting">Halo, ' . htmlspecialchars($customerName) . '! 👋</div>
        <p style="font-size: 14px; line-height: 1.6; color: #475569;">
            Anda telah berhasil mengonfirmasi mitra teknisi <strong>' . htmlspecialchars($technicianName) . '</strong> untuk menangani keluhan perbaikan rumah Anda:
        </p>
        <div class="info-card">
            <div style="font-weight: 700; color: #166534; margin-bottom: 6px;">"' . htmlspecialchars($postTitle) . '"</div>
            <div style="color: #14532d; font-size: 13px;">Teknisi Terpilih: <strong>' . htmlspecialchars($technicianName) . '</strong></div>
            ' . (!empty($techWhatsapp) ? '<div style="color: #14532d; font-size: 13px; margin-top: 4px;">No. WhatsApp: <strong>' . htmlspecialchars($techWhatsapp) . '</strong></div>' : '') . '
        </div>
        <p style="font-size: 14px; color: #475569;">
            Ruang Chat Internal telah aktif. Anda dapat langsung berdiskusi mengenai jadwal kedatangan dan biaya pengerjaan.
        </p>
        <div style="text-align: center; margin: 24px 0;">
            <a href="' . htmlspecialchars($chatLink) . '" class="btn-action">Buka Chat Internal</a>
            ' . $waButton . '
        </div>
    </div>
    <div class="email-footer">
        Email otomatis dari sistem ' . htmlspecialchars($appName) . '.
    </div>
</div>
</body>
</html>';

        $plainContent = "Halo {$customerName},\n\n"
                      . "Anda telah berhasil mengonfirmasi teknisi {$technicianName} untuk: \"{$postTitle}\".\n\n"
                      . "Buka percakapan chat sekarang di:\n"
                      . "{$chatLink}\n\n"
                      . "Salam,\nTim BANTUIN";

        return self::sendSmtp($toEmail, $customerName, $subject, $htmlContent, $plainContent);
    }

    /**
     * Send Notification Email: Pekerjaan Telah Selesai untuk Customer
     */
    public static function sendJobCompletedNotification($toEmail, $customerName, $technicianName, $postTitle, $detailLink) {
        $appName = defined('APP_NAME') ? APP_NAME : 'BANTUIN';
        $subject = "[{$appName}] Pekerjaan Selesai: Keluhan \"{$postTitle}\" Telah Ditangani!";

        $htmlContent = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 20px; color: #1e293b; }
    .email-container { max-width: 520px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
    .email-header { background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); padding: 28px 24px; text-align: center; color: #ffffff; }
    .email-header h1 { margin: 0; font-size: 22px; font-weight: 800; }
    .email-body { padding: 28px 24px; }
    .greeting { font-size: 16px; font-weight: 600; margin-bottom: 12px; }
    .info-card { background: #f0fdf4; border-left: 4px solid #16a34a; padding: 16px; border-radius: 8px; margin: 18px 0; font-size: 14px; }
    .btn-action { display: inline-block; background-color: #16a34a; color: #ffffff !important; padding: 12px 24px; border-radius: 50px; text-decoration: none; font-weight: 600; font-size: 14px; margin-top: 10px; }
    .email-footer { background: #f8fafc; padding: 16px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
</style>
</head>
<body>
<div class="email-container">
    <div class="email-header">
        <h1>' . htmlspecialchars($appName) . '</h1>
        <p style="margin: 4px 0 0; font-size: 13px; opacity: 0.9;">Pekerjaan Telah Selesai</p>
    </div>
    <div class="email-body">
        <div class="greeting">Halo, ' . htmlspecialchars($customerName) . '! 🎉</div>
        <p style="font-size: 14px; line-height: 1.6; color: #475569;">
            Mitra Teknisi <strong>' . htmlspecialchars($technicianName) . '</strong> telah menandai bahwa perbaikan untuk keluhan Anda telah selesai dikerjakan:
        </p>
        <div class="info-card">
            <div style="font-weight: 700; color: #166534; margin-bottom: 6px;">"' . htmlspecialchars($postTitle) . '"</div>
            <div style="color: #14532d; font-size: 13px;">Teknisi Pelaksana: <strong>' . htmlspecialchars($technicianName) . '</strong></div>
            <div style="color: #15803d; font-size: 13px; margin-top: 4px;">Status: <strong>Selesai</strong></div>
        </div>
        <p style="font-size: 14px; color: #475569;">
            Silakan periksa hasil perbaikan di rumah Anda. Anda dapat melihat detail postingan melalui tautan di bawah ini.
        </p>
        <div style="text-align: center; margin: 24px 0;">
            <a href="' . htmlspecialchars($detailLink) . '" class="btn-action">Lihat Detail Pekerjaan</a>
        </div>
    </div>
    <div class="email-footer">
        Email otomatis dari sistem ' . htmlspecialchars($appName) . '.
    </div>
</div>
</body>
</html>';

        $plainContent = "Halo {$customerName},\n\n"
                      . "Mitra Teknisi {$technicianName} telah menandai pekerjaan \"{$postTitle}\" telah Selesai.\n\n"
                      . "Lihat detail pekerjaan di:\n"
                      . "{$detailLink}\n\n"
                      . "Salam,\nTim BANTUIN";

        return self::sendSmtp($toEmail, $customerName, $subject, $htmlContent, $plainContent);
    }

    /**
     * Get dynamic SMTP configuration with fallback to config.php
     *
     * @return array
     */
    public static function getSmtpConfig() {
        $host = defined('SMTP_HOST') ? SMTP_HOST : 'smtp.gmail.com';
        $port = defined('SMTP_PORT') ? (int)SMTP_PORT : 465;
        $user = defined('SMTP_USER') ? trim(SMTP_USER) : '';
        $pass = defined('SMTP_PASS') ? trim(SMTP_PASS) : '';
        $fromName = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : (defined('APP_NAME') ? APP_NAME . ' - Platform Jasa' : 'BANTUIN');
        $fromEmail = defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : $user;
        $encryption = ($port === 465) ? 'ssl' : 'tls';

        try {
            require_once __DIR__ . '/../models/SettingModel.php';
            $settingModel = new SettingModel();
            
            $dbHost = $settingModel->get('smtp_host', null);
            if ($dbHost !== null && $dbHost !== '') $host = $dbHost;

            $dbPort = $settingModel->get('smtp_port', null);
            if ($dbPort !== null && $dbPort !== '') $port = (int)$dbPort;

            $dbUser = $settingModel->get('smtp_user', null);
            if ($dbUser !== null && $dbUser !== '') $user = $dbUser;

            $dbPass = $settingModel->get('smtp_pass', null);
            if ($dbPass !== null && $dbPass !== '') $pass = $dbPass;

            $dbFromName = $settingModel->get('smtp_from_name', null);
            if ($dbFromName !== null && $dbFromName !== '') $fromName = $dbFromName;

            $dbFromEmail = $settingModel->get('smtp_from_email', null);
            if ($dbFromEmail !== null && $dbFromEmail !== '') $fromEmail = $dbFromEmail;

            $dbEncryption = $settingModel->get('smtp_encryption', null);
            if ($dbEncryption !== null && $dbEncryption !== '') $encryption = $dbEncryption;
        } catch (\Throwable $e) {
            // Fall back to constants
        }

        if (empty($fromEmail)) {
            $fromEmail = !empty($user) ? $user : 'no-reply@bantuin.id';
        }

        $passMasked = '';
        if (!empty($pass)) {
            $len = strlen($pass);
            if ($len > 6) {
                $passMasked = substr($pass, 0, 2) . str_repeat('•', max(4, $len - 4)) . substr($pass, -2);
            } else {
                $passMasked = '••••••••';
            }
        }

        return [
            'host' => $host,
            'port' => $port,
            'user' => $user,
            'pass' => $pass,
            'pass_masked' => $passMasked,
            'from_name' => $fromName,
            'from_email' => $fromEmail,
            'encryption' => $encryption,
            'configured' => (!empty($user) && !empty($pass))
        ];
    }

    /**
     * Save dynamic SMTP configuration to SettingModel
     *
     * @param array $data
     * @return bool
     */
    public static function saveSmtpConfig(array $data) {
        try {
            require_once __DIR__ . '/../models/SettingModel.php';
            $settingModel = new SettingModel();

            if (isset($data['smtp_host'])) {
                $settingModel->set('smtp_host', trim($data['smtp_host']));
            }
            if (isset($data['smtp_port'])) {
                $settingModel->set('smtp_port', (int)$data['smtp_port']);
            }
            if (isset($data['smtp_encryption'])) {
                $settingModel->set('smtp_encryption', trim($data['smtp_encryption']));
            }
            if (isset($data['smtp_user'])) {
                $settingModel->set('smtp_user', trim($data['smtp_user']));
            }
            if (isset($data['smtp_pass']) && trim($data['smtp_pass']) !== '') {
                $settingModel->set('smtp_pass', str_replace(' ', '', trim($data['smtp_pass'])));
            }
            if (isset($data['smtp_from_name'])) {
                $settingModel->set('smtp_from_name', trim($data['smtp_from_name']));
            }
            if (isset($data['smtp_from_email'])) {
                $settingModel->set('smtp_from_email', trim($data['smtp_from_email']));
            }

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Send Email via SMTP Socket
     *
     * @param string $toEmail
     * @param string $toName
     * @param string $subject
     * @param string $htmlBody
     * @param string $plainBody
     * @return array
     */
    public static function sendSmtp($toEmail, $toName, $subject, $htmlBody, $plainBody = '') {
        $cfg = self::getSmtpConfig();
        $host = $cfg['host'];
        $port = (int)$cfg['port'];
        $user = trim($cfg['user']);
        $pass = trim($cfg['pass']);
        $pass = str_replace(' ', '', $pass);
        $fromName = $cfg['from_name'];
        $fromEmail = !empty($cfg['from_email']) ? $cfg['from_email'] : ($user ?: 'no-reply@bantuin.id');
        $encryption = strtolower($cfg['encryption']);

        // Always log outgoing email for auditing & local development fallback
        self::logEmail($toEmail, $subject, $plainBody);

        // If credentials are not yet configured
        if (empty($user) || empty($pass)) {
            return [
                'success' => false,
                'not_configured' => true,
                'message' => 'Konfigurasi SMTP belum lengkap (Username & Sandi Aplikasi harus diisi).'
            ];
        }

        // Establish Secure Socket Connection
        $timeout = 10;
        $isSsl = ($port === 465 || $encryption === 'ssl');
        $socketProtocol = $isSsl ? 'ssl://' : 'tcp://';
        $connectionString = $socketProtocol . $host . ':' . $port;

        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);

        $socket = @stream_socket_client($connectionString, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, $context);
        if (!$socket) {
            return [
                'success' => false,
                'message' => "Gagal terhubung ke SMTP server ({$host}:{$port}): {$errstr} ({$errno})"
            ];
        }

        // Helper closures
        $readResponse = function() use ($socket) {
            $data = '';
            while ($str = fgets($socket, 515)) {
                $data .= $str;
                if (substr($str, 3, 1) === ' ') {
                    break;
                }
            }
            return $data;
        };

        $sendCommand = function($cmd, $expectedCode) use ($socket, $readResponse) {
            fputs($socket, $cmd . "\r\n");
            $response = $readResponse();
            $code = substr($response, 0, 3);
            if ($code != $expectedCode) {
                throw new Exception("SMTP Error: expected {$expectedCode}, received {$response}");
            }
            return $response;
        };

        try {
            // Initial server greeting
            $greeting = $readResponse();
            if (substr($greeting, 0, 3) != '220') {
                throw new Exception("Koneksi ditolak oleh server: {$greeting}");
            }

            // EHLO
            $clientHost = $_SERVER['SERVER_NAME'] ?? 'localhost';
            $sendCommand("EHLO {$clientHost}", '250');

            // Handle STARTTLS for port 587 or TLS encryption
            if ($port === 587 || $encryption === 'tls') {
                $sendCommand("STARTTLS", '220');
                if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    throw new Exception("Gagal mengaktifkan enkripsi TLS.");
                }
                $sendCommand("EHLO {$clientHost}", '250');
            }

            // AUTH LOGIN
            $sendCommand("AUTH LOGIN", '334');
            $sendCommand(base64_encode($user), '334');
            $sendCommand(base64_encode($pass), '235');

            // MAIL FROM & RCPT TO
            $sendCommand("MAIL FROM: <{$fromEmail}>", '250');
            $sendCommand("RCPT TO: <{$toEmail}>", '250');

            // DATA
            $sendCommand("DATA", '354');

            // Prepare MIME message
            $boundary = "----=_Part_" . md5(uniqid(time()));
            $domain = !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'bantuin.local';
            $messageId = "<" . time() . "." . md5(uniqid(mt_rand(), true)) . "@" . preg_replace('/:[0-9]+$/', '', $domain) . ">";

            $headers  = "Message-ID: {$messageId}\r\n";
            $headers .= "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <{$fromEmail}>\r\n";
            $headers .= "Reply-To: <{$fromEmail}>\r\n";
            $headers .= "To: =?UTF-8?B?" . base64_encode($toName) . "?= <{$toEmail}>\r\n";
            $headers .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
            $headers .= "Date: " . date('r') . "\r\n";
            $headers .= "X-Mailer: BANTUIN Mailer (PHP/" . phpversion() . ")\r\n";
            $headers .= "X-Priority: 3 (Normal)\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";

            $body  = "--{$boundary}\r\n";
            $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
            $body .= $plainBody . "\r\n\r\n";

            $body .= "--{$boundary}\r\n";
            $body .= "Content-Type: text/html; charset=UTF-8\r\n";
            $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
            $body .= $htmlBody . "\r\n\r\n";
            $body .= "--{$boundary}--\r\n";

            $fullPayload = $headers . "\r\n" . $body . "\r\n.";
            $sendCommand($fullPayload, '250');

            // QUIT
            $sendCommand("QUIT", '221');
            fclose($socket);

            self::logEmail($toEmail, $subject, "[STATUS: SMTP DELIVERED (250 OK)]\n" . $plainBody);

            return [
                'success' => true,
                'message' => 'Email verifikasi berhasil dikirim ke ' . self::maskEmail($toEmail)
            ];

        } catch (Exception $e) {
            @fclose($socket);
            self::logEmail($toEmail, $subject, "[STATUS: SMTP FAILED (" . $e->getMessage() . ")]\n" . $plainBody);
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Local logging fallback helper
     */
    private static function logEmail($to, $subject, $body) {
        $logDir = dirname(__DIR__) . '/storage/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0777, true);
        }
        $logFile = $logDir . '/mail.log';
        $entry = "[" . date('Y-m-d H:i:s') . "] TO: {$to} | SUBJECT: {$subject}\n{$body}\n----------------------------------------\n";
        @file_put_contents($logFile, $entry, FILE_APPEND);
    }
}
