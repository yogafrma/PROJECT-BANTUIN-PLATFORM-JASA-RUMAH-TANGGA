<?php
/**
 * Admin Controller
 * Project: Teknisi Rumah Tangga
 * Protected Area: Admin Role Only
 */

class AdminController extends Controller {
    private $userModel;
    private $postModel;
    private $categoryModel;
    private $techProfileModel;
    private $consultationModel;

    public function __construct() {
        Auth::requireRole(['admin']);
        $this->userModel = $this->model('UserModel');
        $this->postModel = $this->model('PostModel');
        $this->categoryModel = $this->model('CategoryModel');
        $this->techProfileModel = $this->model('TechnicianProfileModel');
        $this->consultationModel = $this->model('ConsultationModel');
    }

    /**
     * Admin Dashboard Overview
     * URL: /admin
     *
     * @return void
     */
    public function index() {
        if (isset($_SESSION['_mock_posts'])) {
            unset($_SESSION['_mock_posts']);
        }
        $userCounts = $this->userModel->getUserCounts();
        $totalPosts = $this->postModel->getTotalPostsCount();
        $allCategories = $this->categoryModel->getAllCategories();
        $totalCategories = count($allCategories);
        $recentPosts = $this->postModel->getAllPostsForAdmin(null, null);
        $recentUsers = $this->userModel->getAllUsers();

        $data = [
            'title' => 'Dashboard Admin - ' . APP_NAME,
            'currentPage' => 'admin_dashboard',
            'user' => Auth::user(),
            'stats' => [
                'total_customers' => $userCounts['customers'],
                'total_technicians' => $userCounts['technicians'],
                'total_posts' => $totalPosts,
                'total_categories' => $totalCategories
            ],
            'categories' => $allCategories,
            'recentPosts' => array_slice($recentPosts, 0, 5),
            'recentUsers' => array_slice($recentUsers, 0, 5)
        ];

        $this->view('admin/dashboard', $data);
    }

    // ==========================================
    // USER MANAGEMENT
    // ==========================================

    /**
     * List all users with optional role filtering
     * URL: /admin/users
     *
     * @return void
     */
    public function users() {
        // Clear any stale session mock deleted flags to ensure full database consistency
        if (isset($_SESSION['_deleted_user_ids'])) {
            unset($_SESSION['_deleted_user_ids']);
        }

        $selectedRole = sanitize($_GET['role'] ?? 'all');
        $users = $this->userModel->getAllUsers($selectedRole);

        $data = [
            'title' => 'Manajemen Pengguna - Admin ' . APP_NAME,
            'currentPage' => 'admin_users',
            'user' => Auth::user(),
            'users' => $users,
            'selectedRole' => $selectedRole
        ];

        $this->view('admin/users/index', $data);
    }

    /**
     * View user detail
     * URL: /admin/userDetail/:id
     *
     * @param int|string $id
     * @return void
     */
    public function userDetail($id = null) {
        if (!$id) {
            redirect('admin/users');
        }

        $targetUser = $this->userModel->findById((int)$id);
        if (!$targetUser) {
            set_flash('danger', 'User tidak ditemukan.');
            redirect('admin/users');
        }

        $techProfile = null;
        $userPosts = [];
        $userResponses = [];
        $userConsultations = [];

        if ($targetUser->role === 'technician') {
            $techProfile = $this->techProfileModel->getProfileByUserId($targetUser->id);
            $userResponses = $this->postModel->getResponsesByTechnicianId($targetUser->id);
        } elseif ($targetUser->role === 'customer') {
            $userPosts = $this->postModel->getPostsByCustomerId($targetUser->id);
            $userConsultations = $this->consultationModel->getConsultationsByCustomerId($targetUser->id);
        }

        $data = [
            'title' => 'Detail Pengguna: ' . $targetUser->name . ' - Admin',
            'currentPage' => 'admin_users',
            'user' => Auth::user(),
            'targetUser' => $targetUser,
            'techProfile' => $techProfile,
            'userPosts' => $userPosts,
            'userResponses' => $userResponses,
            'userConsultations' => $userConsultations
        ];

        $this->view('admin/users/detail', $data);
    }

    /**
     * Toggle active/inactive user status
     * URL: /admin/toggleUserStatus/:id
     *
     * @param int|string $id
     * @return void
     */
    public function toggleUserStatus($id = null) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/users');
        }
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($csrfToken)) {
            set_flash('danger', 'Permintaan tidak valid (CSRF).');
            redirect('admin/users');
        }

        $currentAdmin = Auth::user();
        if ((int)$id === (int)$currentAdmin->id) {
            set_flash('danger', 'Anda tidak dapat menonaktifkan akun admin Anda sendiri.');
            redirect($_SERVER['HTTP_REFERER'] ?? 'admin/users');
        }

        $result = $this->userModel->toggleUserStatus($id);
        if ($result) {
            set_flash('success', 'Status akun pengguna berhasil diperbarui.');
        } else {
            set_flash('danger', 'Gagal memperbarui status pengguna.');
        }

        redirect($_SERVER['HTTP_REFERER'] ?? 'admin/users');
    }

    /**
     * Delete user permanently
     * URL: /admin/deleteUser/:id
     *
     * @param int|string $id
     * @return void
     */
    public function deleteUser($id = null) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$id) {
            redirect('admin/users');
        }

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($csrfToken)) {
            set_flash('danger', 'Permintaan tidak valid (CSRF token mismatch).');
            redirect('admin/users');
        }

        $currentAdmin = Auth::user();
        if ((int)$id === (int)$currentAdmin->id) {
            set_flash('danger', 'Anda tidak dapat menghapus akun admin Anda sendiri yang sedang aktif.');
            redirect($_SERVER['HTTP_REFERER'] ?? 'admin/users');
        }

        $targetUser = $this->userModel->findById((int)$id);
        if (!$targetUser) {
            set_flash('danger', 'Pengguna tidak ditemukan.');
            redirect('admin/users');
        }

        $userName = $targetUser->name ?? 'Pengguna';
        $result = $this->userModel->deleteUser((int)$id);

        if ($result) {
            set_flash('success', "Akun pengguna '{$userName}' beserta seluruh data terkait berhasil dihapus permanen.");
        } else {
            set_flash('danger', "Gagal menghapus akun pengguna '{$userName}'.");
        }

        redirect('admin/users');
    }

    // ==========================================
    // CUSTOMER MANAGEMENT
    // ==========================================

    /**
     * List all customers
     * URL: /admin/customers
     *
     * @return void
     */
    public function customers() {
        $customers = $this->userModel->getCustomersList();

        $data = [
            'title' => 'Daftar Pelanggan (Customer) - Admin',
            'currentPage' => 'admin_customers',
            'user' => Auth::user(),
            'customers' => $customers
        ];

        $this->view('admin/customers/index', $data);
    }

    /**
     * Alias for customer detail
     * URL: /admin/customerDetail/:id
     *
     * @param int|string $id
     * @return void
     */
    public function customerDetail($id = null) {
        $this->userDetail($id);
    }

    // ==========================================
    // TECHNICIAN MANAGEMENT
    // ==========================================

    /**
     * List all technicians with skills and status
     * URL: /admin/technicians
     *
     * @return void
     */
    public function technicians() {
        $technicians = $this->userModel->getTechniciansListWithSkills();

        $data = [
            'title' => 'Daftar Mitra Teknisi - Admin',
            'currentPage' => 'admin_technicians',
            'user' => Auth::user(),
            'technicians' => $technicians
        ];

        $this->view('admin/technicians/index', $data);
    }

    /**
     * Alias for technician detail
     * URL: /admin/technicianDetail/:id
     *
     * @param int|string $id
     * @return void
     */
    public function technicianDetail($id = null) {
        $this->userDetail($id);
    }

    // ==========================================
    // COMPLAINT POSTS MANAGEMENT
    // ==========================================

    /**
     * List all complaint posts with filters
     * URL: /admin/posts
     *
     * @return void
     */
    public function posts() {
        if (isset($_SESSION['_mock_posts'])) {
            unset($_SESSION['_mock_posts']);
        }
        $selectedStatus = sanitize($_GET['status'] ?? 'all');
        $selectedCategory = sanitize($_GET['category_id'] ?? 'all');

        $posts = $this->postModel->getAllPostsForAdmin($selectedStatus, $selectedCategory);
        $categories = $this->categoryModel->getAllCategories();

        $data = [
            'title' => 'Manajemen Postingan Keluhan - Admin',
            'currentPage' => 'admin_posts',
            'user' => Auth::user(),
            'posts' => $posts,
            'categories' => $categories,
            'selectedStatus' => $selectedStatus,
            'selectedCategory' => $selectedCategory
        ];

        $this->view('admin/posts/index', $data);
    }

    /**
     * View detailed complaint post for Admin
     * URL: /admin/postDetail/:id
     *
     * @param int|string $id
     * @return void
     */
    public function postDetail($id = null) {
        if (!$id) {
            redirect('admin/posts');
        }

        $post = $this->postModel->getPostById((int)$id);
        if (!$post) {
            set_flash('danger', 'Postingan keluhan tidak ditemukan.');
            redirect('admin/posts');
        }

        $responses = $this->postModel->getResponsesByPostId((int)$id);

        $data = [
            'title' => 'Detail Postingan #' . $post->id . ' - Admin',
            'currentPage' => 'admin_posts',
            'user' => Auth::user(),
            'post' => $post,
            'responses' => $responses
        ];

        $this->view('admin/posts/detail', $data);
    }

    /**
     * Update post status by Admin
     * URL: /admin/postStatus/:id
     *
     * @param int|string $id
     * @return void
     */
    public function postStatus($id = null) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$id) {
            redirect('admin/posts');
        }
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($csrfToken)) {
            set_flash('danger', 'Permintaan tidak valid (CSRF).');
            redirect('admin/posts');
        }

        $status = sanitize($_POST['status'] ?? '');
        $result = $this->postModel->adminUpdateStatus((int)$id, $status);

        if ($result) {
            set_flash('success', 'Status postingan #' . $id . ' berhasil diubah menjadi: ' . $status);
        } else {
            set_flash('danger', 'Gagal mengubah status postingan.');
        }

        redirect($_SERVER['HTTP_REFERER'] ?? 'admin/posts');
    }

    /**
     * Soft delete post violating rules
     * URL: /admin/postDelete/:id
     *
     * @param int|string $id
     * @return void
     */
    public function postDelete($id = null) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$id) {
            redirect('admin/posts');
        }
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($csrfToken)) {
            set_flash('danger', 'Permintaan tidak valid (CSRF).');
            redirect('admin/posts');
        }

        $result = $this->postModel->adminSoftDeletePost((int)$id);
        if ($result) {
            set_flash('success', 'Postingan #' . $id . ' berhasil dihapus/dibatalkan (Soft Delete) karena melanggar aturan.');
        } else {
            set_flash('danger', 'Gagal menghapus postingan.');
        }

        redirect('admin/posts');
    }

    // ==========================================
    // SYSTEM CONFIGURATION & SETTINGS
    // ==========================================

    /**
     * Display System Configuration & Status Overview
     * URL: /admin/settings
     *
     * @return void
     */
    public function settings() {
        // Test Database connection
        $dbStatus = [
            'connected' => false,
            'driver' => 'MySQL (PDO)',
            'host' => defined('DB_HOST') ? DB_HOST : 'localhost',
            'port' => defined('DB_PORT') ? DB_PORT : '3306',
            'database' => defined('DB_NAME') ? DB_NAME : 'db_teknisi_rumah',
            'version' => 'Unknown'
        ];

        try {
            $db = new Database();
            $db->query("SELECT VERSION() as ver");
            $res = $db->single();
            $dbStatus['connected'] = true;
            $dbStatus['version'] = is_object($res) ? ($res->ver ?? 'MySQL') : 'MySQL';
        } catch (\Throwable $e) {
            $dbStatus['error'] = $e->getMessage();
        }

        // SMTP configuration status (dynamic with fallback to config.php)
        require_once __DIR__ . '/../services/EmailService.php';
        $smtpConfig = EmailService::getSmtpConfig();

        // AI Provider & Multi-Model configuration status
        require_once __DIR__ . '/../services/AIProviderService.php';
        $aiConfig = AIProviderService::getConfig();
        $aiProviders = AIProviderService::getProviders();

        $data = [
            'title' => 'Konfigurasi Sistem - ' . APP_NAME,
            'currentPage' => 'admin_settings',
            'user' => Auth::user(),
            'dbStatus' => $dbStatus,
            'smtp' => $smtpConfig,
            'chatbot' => [
                'mode' => defined('CHATBOT_MODE') ? CHATBOT_MODE : 'php',
                'has_gemini' => !empty($aiConfig['api_key']),
                'gemini_masked' => $aiConfig['api_key_masked']
            ],
            'aiConfig' => $aiConfig,
            'aiProviders' => $aiProviders,
            'server' => [
                'php_version' => PHP_VERSION,
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Apache/XAMPP',
                'timezone' => date_default_timezone_get(),
                'server_time' => date('d M Y, H:i:s T'),
                'base_url' => defined('BASE_URL') ? BASE_URL : 'http://localhost',
                'app_env' => defined('APP_ENV') ? APP_ENV : 'development',
                'app_version' => defined('APP_VERSION') ? APP_VERSION : '2.0.0'
            ]
        ];

        $this->view('admin/settings', $data);
    }

    /**
     * Update SMTP Email Configuration
     * URL: /admin/updateSmtpSettings
     *
     * @return void
     */
    public function updateSmtpSettings() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/settings#smtp');
        }

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($csrfToken)) {
            set_flash('danger', 'Permintaan tidak valid (CSRF token mismatch).');
            redirect('admin/settings#smtp');
        }

        require_once __DIR__ . '/../services/EmailService.php';

        $host = trim(sanitize($_POST['smtp_host'] ?? 'smtp.gmail.com'));
        $port = (int)($_POST['smtp_port'] ?? 465);
        $encryption = trim(sanitize($_POST['smtp_encryption'] ?? 'ssl'));
        $user = trim(sanitize($_POST['smtp_user'] ?? ''));
        $pass = trim($_POST['smtp_pass'] ?? '');
        $fromName = trim(sanitize($_POST['smtp_from_name'] ?? ''));
        $fromEmail = trim(sanitize($_POST['smtp_from_email'] ?? ''));

        if (!empty($user) && !filter_var($user, FILTER_VALIDATE_EMAIL)) {
            set_flash('danger', 'Format alamat email pengirim (SMTP Username) tidak valid.');
            redirect('admin/settings#smtp');
        }

        $saved = EmailService::saveSmtpConfig([
            'smtp_host'       => $host,
            'smtp_port'       => $port,
            'smtp_encryption' => $encryption,
            'smtp_user'       => $user,
            'smtp_pass'       => $pass,
            'smtp_from_name'  => $fromName,
            'smtp_from_email' => $fromEmail
        ]);

        if ($saved) {
            set_flash('success', 'Pengaturan notifikasi email (SMTP) berhasil disimpan!');
        } else {
            set_flash('danger', 'Gagal menyimpan pengaturan SMTP.');
        }

        redirect('admin/settings#smtp');
    }

    /**
     * Update AI Multi-Model Configuration
     * URL: /admin/updateAiSettings
     *
     * @return void
     */
    public function updateAiSettings() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/settings#ai');
        }

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($csrfToken)) {
            set_flash('danger', 'Permintaan tidak valid (CSRF token mismatch).');
            redirect('admin/settings#ai');
        }

        require_once __DIR__ . '/../services/AIProviderService.php';

        $provider = sanitize($_POST['ai_provider'] ?? 'gemini');
        $apiKey = trim($_POST['ai_api_key'] ?? '');
        $model = trim($_POST['ai_model'] ?? '');
        $customUrl = trim($_POST['ai_custom_url'] ?? '');
        $temperature = isset($_POST['ai_temperature']) ? (float)$_POST['ai_temperature'] : 0.6;
        $fallback = isset($_POST['ai_fallback_enabled']) ? 1 : 0;

        // Auto-detect provider if key has clear signature (e.g. nvapi- -> nvidia, AIzaSy -> gemini, gsk_ -> groq)
        $detected = AIProviderService::detectProviderFromKey($apiKey);
        if ($detected && $detected !== $provider) {
            $provider = $detected;
            $providers = AIProviderService::getProviders();
            if (empty($model) || ($detected === 'nvidia' && str_contains($model, 'gemini')) || ($detected === 'gemini' && (str_contains($model, 'llama') || str_contains($model, 'nemotron')))) {
                $model = $providers[$detected]['default_model'] ?? $model;
            }
        }

        $saved = AIProviderService::saveConfig([
            'ai_provider'         => $provider,
            'ai_api_key'          => $apiKey,
            'ai_model'            => $model,
            'ai_custom_url'       => $customUrl,
            'ai_temperature'      => $temperature,
            'ai_fallback_enabled' => $fallback
        ]);

        $providers = AIProviderService::getProviders();
        $providerName = $providers[$provider]['name'] ?? ucfirst($provider);

        if ($saved) {
            set_flash('success', "Konfigurasi AI berhasil disimpan! Provider aktif sekarang: <strong>{$providerName}</strong> (Model: {$model}).");
        } else {
            set_flash('warning', "Konfigurasi AI diperbarui dengan catatan fallback lokal.");
        }

        redirect('admin/settings#ai');
    }

    /**
     * Live Test Connection to AI Provider
     * URL: /admin/testAiConnection
     *
     * @return void
     */
    public function testAiConnection() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/settings#ai');
        }

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($csrfToken)) {
            $err = ['success' => false, 'message' => 'CSRF token mismatch. Silakan refresh halaman.'];
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode($err);
                exit;
            }
            set_flash('danger', $err['message']);
            redirect('admin/settings#ai');
        }

        require_once __DIR__ . '/../services/AIProviderService.php';

        $provider  = sanitize($_POST['ai_provider'] ?? '');
        $apiKey    = trim($_POST['ai_api_key'] ?? '');
        $model     = trim($_POST['ai_model'] ?? '');
        $customUrl = trim($_POST['ai_custom_url'] ?? '');

        // Auto-detect provider if key has clear signature (e.g. nvapi- -> nvidia, AIzaSy -> gemini, gsk_ -> groq)
        $detected = AIProviderService::detectProviderFromKey($apiKey);
        if ($detected && $detected !== $provider) {
            $provider = $detected;
            $providers = AIProviderService::getProviders();
            if (empty($model) || ($detected === 'nvidia' && str_contains($model, 'gemini')) || ($detected === 'gemini' && (str_contains($model, 'llama') || str_contains($model, 'nemotron')))) {
                $model = $providers[$detected]['default_model'] ?? $model;
            }
        }

        // If key is empty in POST, check if saved key exists for this provider
        if (empty($apiKey)) {
            $savedConfig = AIProviderService::getConfig();
            if ($savedConfig['provider'] === $provider) {
                $apiKey = $savedConfig['api_key'];
            }
        }

        $result = AIProviderService::testConnection($provider, $apiKey, $model, $customUrl);

        if ($this->isAjaxRequest()) {
            if (!headers_sent()) {
                header('Content-Type: application/json');
            }
            echo json_encode($result);
            exit;
        }

        if ($result['success']) {
            set_flash('success', "Koneksi berhasil! {$result['message']}<br><em>Respons: \"{$result['reply']}\"</em>");
        } else {
            set_flash('danger', "Gagal terhubung: {$result['message']}");
        }

        redirect('admin/settings#ai');
    }

    /**
     * Check if request was sent via AJAX / fetch
     */
    private function isAjaxRequest() {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
            || isset($_POST['is_ajax']);
    }

    /**
     * Test Send Email from UI
     * URL: /admin/testEmail
     *
     * @return void
     */
    public function testEmail() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/settings#smtp');
        }

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($csrfToken)) {
            set_flash('danger', 'Permintaan tidak valid (CSRF token mismatch).');
            redirect('admin/settings#smtp');
        }

        $targetEmail = trim($_POST['target_email'] ?? '');
        if (empty($targetEmail) || !filter_var($targetEmail, FILTER_VALIDATE_EMAIL)) {
            set_flash('danger', 'Format alamat email tujuan tidak valid.');
            redirect('admin/settings#smtp');
        }

        require_once __DIR__ . '/../services/EmailService.php';

        $testCode = (string)random_int(100000, 999999);
        $result = EmailService::sendOtpEmail($targetEmail, 'Admin BANTUIN', $testCode);

        if (!empty($result['success'])) {
            set_flash('success', "Uji coba berhasil! Email notifikasi verifikasi simulasi telah terkirim ke <strong>{$targetEmail}</strong> via SMTP. Silakan periksa inbox!");
        } else {
            set_flash('danger', "Gagal mengirim email: " . ($result['message'] ?? 'Terjadi kesalahan pada koneksi SMTP.'));
        }

        redirect('admin/settings#smtp');
    }

    /**
     * Interactive AI Playground / Prompt Tester
     * URL: /admin/testAiPrompt
     *
     * @return void
     */
    public function testAiPrompt() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/settings#ai-settings-card');
        }

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($csrfToken)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'CSRF token tidak valid. Silakan refresh halaman.']);
            exit;
        }

        require_once __DIR__ . '/../services/AIProviderService.php';

        $prompt = trim($_POST['prompt'] ?? '');
        if (empty($prompt)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Pertanyaan atau prompt uji tidak boleh kosong.']);
            exit;
        }

        $config = AIProviderService::getConfig();
        $startTime = microtime(true);

        $systemPrompt = "Anda adalah AI Assistant cerdas platform BANTUIN (Layanan Perbaikan & Servis Rumah Tangga Indonesia). Berikan jawaban yang ringkas, ramah, dan solutif dalam bahasa Indonesia.";
        $messages = [
            ['role' => 'user', 'content' => $prompt]
        ];

        // Support live parameters from active form if provided
        $overrideProvider = !empty($_POST['ai_provider']) ? sanitize($_POST['ai_provider']) : null;
        $overrideKey = !empty($_POST['ai_api_key']) ? trim($_POST['ai_api_key']) : null;
        $overrideModel = !empty($_POST['ai_model']) ? trim($_POST['ai_model']) : null;
        $overrideUrl = !empty($_POST['ai_custom_url']) ? trim($_POST['ai_custom_url']) : null;

        $options = [
            'temperature' => $config['temperature']
        ];
        if ($overrideProvider) $options['provider'] = $overrideProvider;
        if ($overrideKey) $options['api_key'] = $overrideKey;
        if ($overrideModel) $options['model'] = $overrideModel;
        if ($overrideUrl) $options['custom_url'] = $overrideUrl;

        $res = AIProviderService::chat($systemPrompt, $messages, $options);
        $latency = round((microtime(true) - $startTime) * 1000);

        header('Content-Type: application/json');
        if ($res['success']) {
            echo json_encode([
                'success' => true,
                'reply' => $res['reply'],
                'provider' => $res['provider_name'],
                'model' => $res['model'],
                'latency_ms' => $latency
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => $res['error'] ?? 'Gagal mendapatkan respon dari AI.',
                'provider' => $res['provider_name'] ?? ($overrideProvider ?? $config['provider_info']['name']),
                'model' => $res['model'] ?? ($overrideModel ?? $config['model']),
                'latency_ms' => $latency
            ]);
        }
        exit;
    }
}

