<?php
/**
 * WhatsApp Service
 * Project: Teknisi Rumah Tangga
 * Handles Phone Number Validation, Normalization, Message Formatting, and WhatsApp URL Generation
 */

class WhatsAppService {

    /**
     * Normalize a phone number to standard international format without leading '+' (e.g. 628123456789)
     *
     * @param string|null $phone
     * @return string
     */
    public static function normalizePhone($phone) {
        if (empty($phone)) {
            return '';
        }

        // 1. Remove non-digit characters except '+' at start if any
        $cleaned = preg_replace('/[^0-9]/', '', (string)$phone);

        // 2. Handle empty after strip
        if (empty($cleaned)) {
            return '';
        }

        // 3. Convert Indonesian leading '0' to '62' (e.g., 08123456789 -> 628123456789)
        if (str_starts_with($cleaned, '0')) {
            $cleaned = '62' . substr($cleaned, 1);
        }

        // 4. If already begins with '62', keep it; if it starts with '8', prepend '62'
        if (str_starts_with($cleaned, '8')) {
            $cleaned = '62' . $cleaned;
        }

        return $cleaned;
    }

    /**
     * Validate whether a phone number is valid
     *
     * @param string|null $phone
     * @return bool
     */
    public static function isValidPhone($phone) {
        $normalized = self::normalizePhone($phone);
        // Standard Indonesian phone numbers typically have 10-15 digits after normalization
        return strlen($normalized) >= 9 && strlen($normalized) <= 16;
    }

    /**
     * Generate complete WhatsApp URL
     * Format: https://wa.me/{nomor_whatsapp}?text={encoded_message}
     *
     * @param string|null $phone
     * @param string $message
     * @return string
     */
    public static function generateUrl($phone, $message = '') {
        $normalized = self::normalizePhone($phone);
        if (empty($normalized)) {
            return '#';
        }

        $url = 'https://wa.me/' . $normalized;

        if (!empty($message)) {
            // Use rawurlencode to handle spaces as %20 and special characters properly
            $url .= '?text=' . rawurlencode(trim($message));
        }

        return $url;
    }

    /**
     * Default message from Customer to Technician regarding a complaint post
     * Example: "Halo, saya tertarik dengan bantuan Anda untuk keluhan: [judul postingan]. Saya ingin mendiskusikan detail perbaikannya."
     *
     * @param string $postTitle
     * @param string $technicianName
     * @return string
     */
    public static function getCustomerToTechnicianPostMessage($postTitle, $technicianName = '') {
        $greeting = !empty($technicianName) ? "Halo Pak/Ibu {$technicianName}," : "Halo,";
        return "{$greeting} saya tertarik dengan bantuan Anda untuk keluhan: {$postTitle}. Saya ingin mendiskusikan detail perbaikannya.";
    }

    /**
     * Message from Technician to Customer regarding taking a job
     *
     * @param string $postTitle
     * @param string $customerName
     * @param string $technicianName
     * @return string
     */
    public static function getTechnicianToCustomerPostMessage($postTitle, $customerName = '', $technicianName = '') {
        $custGreeting = !empty($customerName) ? "Halo Pak/Ibu {$customerName}," : "Halo,";
        $techIntro = !empty($technicianName) ? "saya Teknisi {$technicianName}" : "saya teknisi dari Teknisi Rumah Tangga";
        return "{$custGreeting} {$techIntro} siap membantu perbaikan untuk keluhan: {$postTitle}. Kapan waktu yang tepat untuk kunjungan/pemeriksaan?";
    }

    /**
     * Message for AI Consultation direct follow-up
     *
     * @param string $topic
     * @param string $technicianName
     * @return string
     */
    public static function getConsultationFollowupMessage($topic, $technicianName = '') {
        $greeting = !empty($technicianName) ? "Halo Pak/Ibu {$technicianName}," : "Halo,";
        return "{$greeting} saya telah berkonsultasi melalui AI di platform Teknisi Rumah Tangga mengenai: {$topic}. Apakah Anda bisa membantu menjadwalkan kunjungan perbaikan?";
    }

    /**
     * Message for Registration OTP Notification
     *
     * @param string $otpCode
     * @param string $name
     * @return string
     */
    public static function getOtpMessage($otpCode, $name = '') {
        $greeting = !empty($name) ? "Halo {$name}," : "Halo,";
        return "{$greeting}\n\nKode OTP verifikasi pendaftaran akun BANTUIN Anda adalah:\n*{$otpCode}*\n\nKode ini berlaku selama 5 menit. JANGAN bagikan kode ini kepada siapapun demi keamanan akun Anda.\n\nTerima kasih,\nTim BANTUIN";
    }

    /**
     * Format masked phone number for display (e.g. +62 812-••••-7890)
     *
     * @param string $phone
     * @return string
     */
    public static function maskPhone($phone) {
        $normalized = self::normalizePhone($phone);
        if (strlen($normalized) < 8) {
            return $phone;
        }
        $prefix = substr($normalized, 0, 5);
        $suffix = substr($normalized, -3);
        return '+' . $prefix . '-••••-' . $suffix;
    }
}
