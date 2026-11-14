<?php
/**
 * Auth Controller
 * Project: Teknisi Rumah Tangga
 * Handles Login, Register, Logout, and Form Validations
 */

class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('UserModel');
    }

    /**
     * Display Login Form & Handle Login Submission
     *
     * @return void
     */
    public function login() {
        // Redirect if already logged in
        Auth::requireGuest();

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!verify_csrf_token($csrfToken)) {
                $errors['general'] = 'Permintaan tidak valid (CSRF token mismatch). Silakan muat ulang halaman.';
            } else {
                $email = trim($_POST['email'] ?? '');
                $password = $_POST['password'] ?? '';

            // 1. Validation
            if (empty($email)) {
                $errors['email'] = 'Alamat email wajib diisi.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Format email tidak valid.';
            }

            if (empty($password)) {
                $errors['password'] = 'Kata sandi wajib diisi.';
            }

            // 2. Authentication
            if (empty($errors)) {
                $user = $this->userModel->findByEmail($email);

                if ($user && password_verify($password, $user->password)) {
                    if ($user->status !== 'active') {
                        $errors['general'] = 'Akun Anda saat ini dinonaktifkan. Silakan hubungi Customer Care.';
                    } else {
                        // Successful login
                        Auth::login($user);
                        set_flash('success', 'Selamat datang kembali, ' . e($user->name) . '!');
                        Auth::redirectByRole();
                    }
                } else {
                    $errors['general'] = 'Email atau kata sandi yang Anda masukkan salah.';
                }
            }
            }
        }

        $data = [
            'title' => 'Masuk Akun - ' . APP_NAME,
            'currentPage' => 'login',
            'errors' => $errors
        ];

        $this->view('auth/login', $data);
    }

    /**
     * Display Register Form & Handle User Registration
     *
     * @return void
     */
    public function register() {
        // Redirect if already logged in
        Auth::requireGuest();

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!verify_csrf_token($csrfToken)) {
                $errors['general'] = 'Permintaan tidak valid (CSRF token mismatch). Silakan muat ulang halaman.';
            } else {
                $name = trim($_POST['name'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $whatsapp = trim($_POST['whatsapp'] ?? '');
                $role = trim($_POST['role'] ?? 'customer');
                $password = $_POST['password'] ?? '';
                $passwordConfirm = $_POST['password_confirm'] ?? '';

            // 1. Name Validation
            if (empty($name)) {
                $errors['name'] = 'Nama lengkap wajib diisi.';
            } elseif (strlen($name) < 3) {
                $errors['name'] = 'Nama minimal harus terdiri dari 3 karakter.';
            }

            // 2. Email Validation
            if (empty($email)) {
                $errors['email'] = 'Alamat email wajib diisi.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Format alamat email tidak valid.';
            } elseif ($this->userModel->isEmailExists($email)) {
                $errors['email'] = 'Alamat email ini sudah terdaftar. Silakan gunakan email lain atau login.';
            }

            // 3. WhatsApp Validation (MANDATORY)
            $cleanWa = preg_replace('/[^0-9]/', '', $whatsapp);
            if (empty($whatsapp)) {
                $errors['whatsapp'] = 'Nomor WhatsApp WAJIB diisi untuk konfirmasi pemesanan jasa.';
            } elseif (strlen($cleanWa) < 9 || strlen($cleanWa) > 15) {
                $errors['whatsapp'] = 'Format nomor WhatsApp tidak valid (contoh: 081234567890).';
            }

            // 4. Role Validation
            if (!in_array($role, ['customer', 'technician'], true)) {
                $errors['role'] = 'Silakan pilih peran yang valid (Customer atau Teknisi).';
            }

            // 5. Password Validation
            if (empty($password)) {
                $errors['password'] = 'Kata sandi wajib diisi.';
            } elseif (strlen($password) < 6) {
                $errors['password'] = 'Kata sandi minimal harus 6 karakter.';
            }

            if (empty($passwordConfirm)) {
                $errors['password_confirm'] = 'Konfirmasi kata sandi wajib diisi.';
            } elseif ($password !== $passwordConfirm) {
                $errors['password_confirm'] = 'Konfirmasi kata sandi tidak cocok dengan kata sandi.';
            }

            // 6. Process Registration with Email OTP Notification
            if (empty($errors)) {
                require_once __DIR__ . '/../services/EmailService.php';

                // Generate 6-digit OTP code
                $otpCode = (string)random_int(100000, 999999);
                $sendResult = EmailService::sendOtpEmail($email, $name, $otpCode);

                // Store pending registration data & OTP in session
                $_SESSION['pending_registration'] = [
                    'name' => $name,
                    'email' => $email,
                    'whatsapp' => $cleanWa,
                    'role' => $role,
                    'password' => $password,
                    'otp' => $otpCode,
                    'created_at' => time(),
                    'expires_at' => time() + 300, // 5 minutes
                    'email_sent' => $sendResult['success'] ?? false,
                    'mail_status' => $sendResult['message'] ?? ''
                ];

                if (!empty($sendResult['success'])) {
                    set_flash('success', 'Kode OTP verifikasi telah dikirimkan ke email Anda (' . EmailService::maskEmail($email) . '). Silakan cek kotak masuk (inbox) atau spam.');
                } else {
                    set_flash('info', 'Kode OTP pendaftaran telah dibuat untuk email ' . EmailService::maskEmail($email) . '. Silakan masukkan 6 digit kode untuk verifikasi.');
                }
                redirect('verify-otp');
            }
            }
        }

        $data = [
            'title' => 'Daftar Akun Baru - ' . APP_NAME,
            'currentPage' => 'register',
            'errors' => $errors
        ];

        $this->view('auth/register', $data);
    }

    /**
     * Display OTP Verification Page & Validate OTP Code
     *
     * @return void
     */
    public function verifyOtp() {
        Auth::requireGuest();

        if (empty($_SESSION['pending_registration'])) {
            set_flash('warning', 'Tidak ada proses pendaftaran yang aktif. Silakan isi form pendaftaran terlebih dahulu.');
            redirect('register');
        }

        require_once __DIR__ . '/../services/EmailService.php';

        $pending = $_SESSION['pending_registration'];
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!verify_csrf_token($csrfToken)) {
                $errors['general'] = 'Permintaan tidak valid (CSRF token mismatch). Silakan coba lagi.';
            } else {
                // Collect OTP from either single input or 6 box inputs
                $inputOtp = trim($_POST['otp'] ?? '');
                if (empty($inputOtp) && isset($_POST['digit']) && is_array($_POST['digit'])) {
                    $inputOtp = implode('', $_POST['digit']);
                }
                $inputOtp = preg_replace('/[^0-9]/', '', $inputOtp);

                if (empty($inputOtp)) {
                    $errors['otp'] = 'Silakan masukkan 6 digit kode OTP verifikasi.';
                } elseif (strlen($inputOtp) !== 6) {
                    $errors['otp'] = 'Kode OTP harus terdiri dari 6 digit angka.';
                } elseif (time() > $pending['expires_at']) {
                    $errors['otp'] = 'Kode OTP telah kedaluwarsa (berlaku 5 menit). Silakan klik "Kirim Ulang OTP".';
                } elseif ($inputOtp !== $pending['otp']) {
                    $errors['otp'] = 'Kode OTP yang Anda masukkan salah. Silakan periksa kembali email Anda.';
                } else {
                    // OTP is VALID -> Save user to database
                    $userData = [
                        'name' => $pending['name'],
                        'email' => $pending['email'],
                        'whatsapp' => $pending['whatsapp'],
                        'role' => $pending['role'],
                        'password' => $pending['password']
                    ];

                    $newUserId = $this->userModel->register($userData);

                    if ($newUserId) {
                        $newUser = $this->userModel->findById($newUserId);

                        // If technician, initialize technician profile
                        if ($newUser && $newUser->role === 'technician') {
                            try {
                                $techModel = $this->model('TechnicianProfileModel');
                                $techModel->upsertProfile($newUserId, [
                                    'category_id' => 1,
                                    'description' => 'Mitra teknisi profesional siap melayani kebutuhan perbaikan rumah tangga.',
                                    'location' => 'Jabodetabek'
                                ]);
                            } catch (\Throwable $e) {
                                // Continue
                            }
                        }
                        
                        // Clear pending session
                        unset($_SESSION['pending_registration']);

                        // Auto login
                        Auth::login($newUser);

                        $roleLabel = ($newUser->role === 'technician') ? 'Mitra Teknisi' : 'Pelanggan';
                        set_flash('success', "Verifikasi berhasil! Selamat datang sebagai {$roleLabel} di " . APP_NAME . ".");
                        Auth::redirectByRole();
                    } else {
                        $errors['general'] = 'Terjadi kesalahan sistem saat menyimpan akun. Silakan coba kembali.';
                    }
                }
            }
        }

        $data = [
            'title' => 'Verifikasi Kode OTP - ' . APP_NAME,
            'currentPage' => 'verify-otp',
            'pending' => $pending,
            'errors' => $errors
        ];

        $this->view('auth/verify_otp', $data);
    }

    /**
     * Resend a fresh OTP Code to Email
     *
     * @return void
     */
    public function resendOtp() {
        Auth::requireGuest();

        if (empty($_SESSION['pending_registration'])) {
            set_flash('warning', 'Tidak ada proses pendaftaran yang aktif.');
            redirect('register');
        }

        require_once __DIR__ . '/../services/EmailService.php';

        $newOtp = (string)random_int(100000, 999999);
        $name = $_SESSION['pending_registration']['name'];
        $email = $_SESSION['pending_registration']['email'];

        $sendResult = EmailService::sendOtpEmail($email, $name, $newOtp);

        $_SESSION['pending_registration']['otp'] = $newOtp;
        $_SESSION['pending_registration']['expires_at'] = time() + 300;
        $_SESSION['pending_registration']['email_sent'] = $sendResult['success'] ?? false;
        $_SESSION['pending_registration']['mail_status'] = $sendResult['message'] ?? '';

        if (!empty($sendResult['success'])) {
            set_flash('success', 'Kode OTP baru berhasil dikirimkan ke email Anda!');
        } else {
            set_flash('info', 'Kode OTP baru telah dibuat!');
        }
        redirect('verify-otp');
    }

    /**
     * Cancel pending registration and return to register form
     *
     * @return void
     */
    public function cancelRegister() {
        unset($_SESSION['pending_registration']);
        set_flash('info', 'Pendaftaran sebelumnya dibatalkan. Anda dapat mendaftar dengan data baru.');
        redirect('register');
    }

    /**
     * Handle User Logout
     *
     * @return void
     */
    public function logout() {
        Auth::logout();
        set_flash('success', 'Anda telah berhasil keluar (logout). Sampai jumpa kembali!');
        redirect('login');
    }
}
