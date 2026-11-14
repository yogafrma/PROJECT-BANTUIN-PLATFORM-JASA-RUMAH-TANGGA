<?php
/**
 * Helper Functions
 * Project: Teknisi Rumah Tangga
 */

if (!function_exists('base_url')) {
    /**
     * Generate full application URL
     *
     * @param string $path
     * @return string
     */
    function base_url($path = '') {
        $path = ltrim($path, '/');
        return BASE_URL . ($path ? '/' . $path : '');
    }
}

if (!function_exists('asset')) {
    /**
     * Generate URL for public assets (css, js, images)
     *
     * @param string $path
     * @return string
     */
    function asset($path = '') {
        $path = ltrim($path, '/');
        return BASE_URL . '/' . $path;
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to specific application path
     *
     * @param string $path
     * @return void
     */
    function redirect($path = '') {
        $target = (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) 
            ? $path 
            : base_url($path);
        
        header("Location: {$target}");
        exit;
    }
}

if (!function_exists('sanitize')) {
    /**
     * Sanitize input string
     *
     * @param string $data
     * @return string
     */
    function sanitize($data) {
        return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('e')) {
    /**
     * Short alias for sanitize/htmlspecialchars
     *
     * @param string $data
     * @return string
     */
    function e($data) {
        return sanitize($data);
    }
}

if (!function_exists('set_flash')) {
    /**
     * Set a flash message in session
     *
     * @param string $type (success, danger, warning, info)
     * @param string $message
     * @return void
     */
    function set_flash($type, $message) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }
}

if (!function_exists('has_flash')) {
    /**
     * Check if flash message exists
     *
     * @return bool
     */
    function has_flash() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['flash']);
    }
}

if (!function_exists('get_flash')) {
    /**
     * Get flash message and clear it from session
     *
     * @return array|null
     */
    function get_flash() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}

if (!function_exists('old')) {
    /**
     * Retain form input value after submission
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function old($key, $default = '') {
        return isset($_POST[$key]) ? sanitize($_POST[$key]) : $default;
    }
}

require_once __DIR__ . '/../services/WhatsAppService.php';

if (!function_exists('normalize_phone')) {
    /**
     * Normalize a phone number to standard international format (e.g. 628123456789)
     *
     * @param string|null $phone
     * @return string
     */
    function normalize_phone($phone) {
        return WhatsAppService::normalizePhone($phone);
    }
}

if (!function_exists('wa_url')) {
    /**
     * Generate complete WhatsApp URL
     *
     * @param string|null $phone
     * @param string $message
     * @return string
     */
    function wa_url($phone, $message = '') {
        return WhatsAppService::generateUrl($phone, $message);
    }
}

if (!function_exists('wa_message_post')) {
    /**
     * Generate default complaint post message from customer to technician
     *
     * @param string $postTitle
     * @param string $technicianName
     * @return string
     */
    function wa_message_post($postTitle, $technicianName = '') {
        return WhatsAppService::getCustomerToTechnicianPostMessage($postTitle, $technicianName);
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Generate and return CSRF token
     *
     * @return string
     */
    function csrf_token() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('verify_csrf_token')) {
    /**
     * Verify provided CSRF token against session
     *
     * @param string $token
     * @return bool
     */
    function verify_csrf_token($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}

