<?php
/**
 * Authentication & Role-Based Access Control (RBAC) Helper
 * Project: Teknisi Rumah Tangga
 */

class Auth {
    /**
     * Start session if not started
     *
     * @return void
     */
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Check if user is logged in
     *
     * @return bool
     */
    public static function check() {
        self::init();
        return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
    }

    /**
     * Get authenticated user details as object
     *
     * @return object|null
     */
    public static function user() {
        self::init();
        if (self::check()) {
            return (object) $_SESSION['user'];
        }
        return null;
    }

    /**
     * Get user ID
     *
     * @return int|null
     */
    public static function id() {
        $user = self::user();
        return $user ? $user->id : null;
    }

    /**
     * Get user role
     *
     * @return string|null
     */
    public static function role() {
        $user = self::user();
        return $user ? $user->role : null;
    }

    /**
     * Get user name
     *
     * @return string|null
     */
    public static function name() {
        $user = self::user();
        return $user ? $user->name : null;
    }

    /**
     * Get user WhatsApp number
     *
     * @return string|null
     */
    public static function whatsapp() {
        $user = self::user();
        return $user ? $user->whatsapp : null;
    }

    /**
     * Set login session for user
     *
     * @param object|array $user
     * @return void
     */
    public static function login($user) {
        self::init();
        $userArray = is_object($user) ? (array) $user : $user;
        
        // Remove password hash from session storage for security
        unset($userArray['password']);

        $_SESSION['user'] = $userArray;
        $_SESSION['logged_in_time'] = time();
    }

    /**
     * Destroy user session
     *
     * @return void
     */
    public static function logout() {
        self::init();
        unset($_SESSION['user']);
        unset($_SESSION['logged_in_time']);
        
        // Regenerate session ID to prevent session fixation
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    /**
     * Middleware: Require User to be logged in
     *
     * @return void
     */
    public static function requireLogin() {
        if (!self::check()) {
            set_flash('danger', 'Silakan masuk ke akun Anda terlebih dahulu.');
            redirect('login');
        }
    }

    /**
     * Middleware: Require User to be a Guest (not logged in)
     *
     * @return void
     */
    public static function requireGuest() {
        if (self::check()) {
            self::redirectByRole();
        }
    }

    /**
     * Middleware: Enforce Role-Based Access Control
     *
     * @param string|array $allowedRoles
     * @return void
     */
    public static function requireRole($allowedRoles) {
        self::requireLogin();

        if (is_string($allowedRoles)) {
            $allowedRoles = [$allowedRoles];
        }

        $currentRole = self::role();
        if (!in_array($currentRole, $allowedRoles, true)) {
            set_flash('danger', 'Akses ditolak! Akun Anda (' . ucfirst($currentRole) . ') tidak memiliki izin untuk mengakses halaman tersebut.');
            self::redirectByRole();
        }
    }

    /**
     * Redirect user to their respective role dashboard
     *
     * @return void
     */
    public static function redirectByRole() {
        $role = self::role();
        switch ($role) {
            case 'admin':
                redirect('admin');
                break;
            case 'technician':
                redirect('technician');
                break;
            case 'customer':
            default:
                redirect('customer');
                break;
        }
    }
}
