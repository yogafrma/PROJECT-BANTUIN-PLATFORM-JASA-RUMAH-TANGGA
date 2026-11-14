<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Temukan teknisi terpercaya untuk berbagai kebutuhan rumah tangga. Konsultasikan masalah Anda dengan BANTUIN AI dan temukan teknisi yang sesuai.">
    <meta name="keywords" content="BANTUIN, jasa rumah tangga, service ac, teknisi listrik, tukang kunci, service elektronik, perbaikan rumah, teknisi panggilan">
    <title><?= isset($title) ? e($title) : (APP_NAME . ' - ' . APP_TAGLINE) ?></title>

    <!-- Favicon & Touch Icon -->
    <link rel="icon" type="image/png" href="<?= asset('images/logo-bantuin-icon.png') ?>?v=<?= time() ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/logo-bantuin-rounded.png') ?>?v=<?= time() ?>">

    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom Modern BANTUIN CSS -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>?v=<?= time() ?>">
    <?php if (isset($currentPage) && $currentPage === 'home'): ?>
        <!-- UI/UX Pro Max Crafted Landing Stylesheet & Motion System -->
        <link rel="stylesheet" href="<?= asset('css/landing.css') ?>?v=<?= time() ?>">
    <?php endif; ?>
    <!-- Chatbot AI (BANTUIN Assistant) CSS -->
    <link rel="stylesheet" href="<?= asset('css/chatbot.css') ?>?v=<?= time() ?>">

    <!-- Global App Base URL & Current User for JS / Chatbot -->
    <script>
        window.BASE_URL = '<?= base_url() ?>';
        window.CURRENT_USER = <?= Auth::check() ? json_encode([
            'id' => (int) Auth::id(),
            'name' => Auth::name(),
            'email' => Auth::user()->email ?? '',
            'role' => Auth::role(),
            'whatsapp' => Auth::whatsapp(),
            'status' => Auth::user()->status ?? 'active'
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : 'null' ?>;
    </script>
</head>
<body class="<?= (isset($currentPage) && $currentPage === 'chat_room') ? 'chat-room-page-body' : '' ?>">

    <!-- Main Navigation Bar -->
    <nav class="navbar navbar-expand-lg sticky-top main-navbar py-2 shadow-sm">
        <div class="container">
            
            <!-- Brand Logo BANTUIN -->
            <a class="brand-logo-wrap" href="<?= base_url() ?>">
                <img src="<?= asset('images/logo-bantuin-rounded.png') ?>?v=<?= time() ?>" 
                     alt="BANTUIN Logo" 
                     class="brand-logo-img" 
                     width="46" 
                     height="46">
                <div class="brand-text-wrap">
                    <span class="brand-title">
                        BANTU<span class="brand-title-accent">IN</span>
                    </span>
                    <span class="brand-tagline">Platform Jasa Rumah Tangga</span>
                </div>
            </a>

            <!-- Mobile Toggle Button -->
            <button class="navbar-toggler border-0 shadow-none p-1" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Menu Items -->
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1" id="mainNavMenu">
                    <li class="nav-item">
                        <a class="nav-link landing-nav-item <?= (isset($currentPage) && $currentPage === 'home') ? 'active' : '' ?>" 
                           href="<?= base_url() ?>#beranda" 
                           data-nav-section="beranda">
                            Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link landing-nav-item" 
                           href="<?= base_url() ?>#kategori-layanan" 
                           data-nav-section="kategori-layanan">
                            Layanan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link landing-nav-item" 
                           href="<?= base_url() ?>#cara-kerja" 
                           data-nav-section="cara-kerja">
                            Cara Pesan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link landing-nav-item" 
                           href="<?= base_url() ?>#keunggulan" 
                           data-nav-section="keunggulan">
                            Keunggulan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link landing-nav-item" 
                           href="<?= base_url() ?>#bantuan" 
                           data-nav-section="bantuan">
                            Tanya Jawab
                        </a>
                    </li>
                </ul>

                <!-- Auth Navigation Section -->
                <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
                    <?php if (Auth::check()): ?>
                        <?php 
                            $authUser = Auth::user(); 
                            $roleName = ($authUser->role === 'admin') ? 'Admin' : (($authUser->role === 'technician') ? 'Teknisi' : 'Customer');
                            $roleKey = $authUser->role;
                            $avatarRoleClass = 'avatar-role-' . $roleKey;
                            $badgeRoleClass = 'badge-role-' . $roleKey;
                            $dashboardUrl = ($authUser->role === 'admin') ? base_url('admin') : (($authUser->role === 'technician') ? base_url('technician') : base_url('customer'));

                            // Chat & Notif are only shown in dashboard / internal workspace, NOT on public home or auth pages
                            $showNavbarChatAndNotif = isset($currentPage) && $currentPage !== 'home' && !in_array($currentPage, ['login', 'register', 'verify-otp'], true);

                            // Unread Chat Count
                            $chatUnreadCount = 0;
                            if ($showNavbarChatAndNotif && in_array($authUser->role, ['customer', 'technician'], true)) {
                                try {
                                    require_once __DIR__ . '/../../models/ChatModel.php';
                                    $headerChatModel = new ChatModel();
                                    $chatUnreadCount = $headerChatModel->getUnreadCount($authUser->id);
                                } catch (Exception $e) {}
                            }
                        ?>

                        <?php if ($showNavbarChatAndNotif): ?>
                            <?php if (in_array($authUser->role, ['customer', 'technician'], true)): ?>
                                <!-- Chat Menu Button in Navbar (Only in Dashboard Area) -->
                                <a href="<?= base_url('chat') ?>" 
                                   class="btn btn-soft-green rounded-pill px-3 py-1 btn-sm fw-semibold position-relative d-inline-flex align-items-center gap-1 <?= (isset($currentPage) && in_array($currentPage, ['chat', 'chat_room'])) ? 'active' : '' ?>"
                                   title="Pesan Chat Internal">
                                    <i class="bi bi-chat-dots-fill text-primary"></i>
                                    <span>Chat</span>
                                    <?php if ($chatUnreadCount > 0): ?>
                                        <span class="badge rounded-pill bg-danger ms-1">
                                            <?= $chatUnreadCount ?>
                                        </span>
                                    <?php endif; ?>
                                </a>
                            <?php endif; ?>

                            <!-- In-App Notification Dropdown (Only in Dashboard Area) -->
                            <div class="dropdown me-1 position-relative" id="notificationDropdownWrapper">
                                <button class="btn btn-light border rounded-circle p-0 position-relative d-flex align-items-center justify-content-center" 
                                        type="button" 
                                        id="notifDropdownBtn"
                                        data-bs-toggle="dropdown" 
                                        data-bs-auto-close="outside"
                                        aria-expanded="false" 
                                        style="width: 38px; height: 38px;" 
                                        title="Notifikasi">
                                    <i class="bi bi-bell-fill text-secondary" id="notif-bell-icon"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" id="notif-badge" style="font-size: 0.65rem;">
                                        0
                                    </span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2 p-0 overflow-hidden" 
                                     id="notif-dropdown-menu" 
                                     style="width: 360px; max-width: 90vw; z-index: 1060;">
                                    <div class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between">
                                        <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                            <i class="bi bi-bell-fill text-primary"></i>
                                            <span>Notifikasi</span>
                                            <span class="badge bg-primary-subtle text-primary rounded-pill small" id="notif-header-count">0</span>
                                        </div>
                                        <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none text-muted" id="btn-mark-all-read" style="font-size: 0.75rem;">
                                            <i class="bi bi-check2-all me-1"></i>Tandai Semua Dibaca
                                        </button>
                                    </div>
                                    <div class="list-group list-group-flush overflow-auto" id="notif-dropdown-list" style="max-height: 380px;">
                                        <div class="p-4 text-center text-muted small" id="notif-empty-state">
                                            <i class="bi bi-bell-slash fs-3 d-block text-secondary opacity-50 mb-2"></i>
                                            Belum ada notifikasi baru
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- User Profile Dropdown with Fluent Role Colors (Refined & Harmonized) -->
                        <div class="dropdown">
                            <button class="btn btn-light border rounded-pill px-3 py-1 dropdown-toggle d-flex align-items-center shadow-none" 
                                    type="button" 
                                    data-bs-toggle="dropdown" 
                                    aria-expanded="false"
                                    style="gap: 10px; padding-top: 5px; padding-bottom: 5px;">
                                <div class="avatar-circle <?= $avatarRoleClass ?> text-white small fw-bold flex-shrink-0 d-flex align-items-center justify-content-center">
                                    <?= strtoupper(substr($authUser->name, 0, 1)) ?>
                                </div>
                                <div class="text-start pe-1 d-flex flex-column justify-content-center" style="gap: 3px;">
                                    <div class="small fw-bold text-dark text-truncate" style="line-height: 1.25; max-width: 140px; font-size: 0.86rem;"><?= e($authUser->name) ?></div>
                                    <span class="badge <?= $badgeRoleClass ?> rounded-pill" style="font-size: 0.68rem; padding: 0.22em 0.65em; width: fit-content; line-height: 1.1;"><?= $roleName ?></span>
                                </div>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 mt-2 p-2" style="min-width: 230px;">
                                <li class="px-1 py-1 mb-1">
                                    <div class="p-2 rounded-3 bg-light border border-light-subtle">
                                        <div class="text-muted xs-text fw-bold text-uppercase" style="font-size: 0.66rem; letter-spacing: 0.05em;">Masuk Sebagai</div>
                                        <div class="fw-bold text-dark text-truncate small" title="<?= e($authUser->email) ?>"><?= e($authUser->email) ?></div>
                                    </div>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 small rounded-3 d-flex align-items-center gap-2 <?= (isset($currentPage) && in_array($currentPage, ['admin_dashboard', 'customer_dashboard', 'technician_dashboard'])) ? 'active bg-primary-subtle text-primary' : '' ?>" href="<?= $dashboardUrl ?>">
                                        <i class="bi bi-speedometer2"></i> <span>Dashboard <?= $roleName ?></span>
                                    </a>
                                </li>
                                <?php if ($authUser->role === 'customer'): ?>
                                    <li>
                                        <a class="dropdown-item py-2 small rounded-3 d-flex align-items-center gap-2" href="<?= base_url('customer/consultation') ?>">
                                            <i class="bi bi-stars"></i> <span>Konsultasi AI</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2 small rounded-3 d-flex align-items-center gap-2" href="<?= base_url('posts/my') ?>">
                                            <i class="bi bi-card-checklist"></i> <span>Postingan Saya</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2 small rounded-3 d-flex align-items-center justify-content-between" href="<?= base_url('chat') ?>">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-chat-dots"></i> <span>Chat</span>
                                            </div>
                                            <?php if ($chatUnreadCount > 0): ?>
                                                <span class="badge bg-danger rounded-pill"><?= $chatUnreadCount ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2 small rounded-3 d-flex align-items-center gap-2 <?= (isset($currentPage) && $currentPage === 'history') ? 'active bg-primary-subtle text-primary' : '' ?>" href="<?= base_url('history') ?>">
                                            <i class="bi bi-clock-history"></i> <span>Riwayat Transaksi</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if ($authUser->role === 'technician'): ?>
                                    <li>
                                        <a class="dropdown-item py-2 small rounded-3 d-flex align-items-center gap-2" href="<?= base_url('posts') ?>">
                                            <i class="bi bi-search"></i> <span>Cari Postingan</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2 small rounded-3 d-flex align-items-center gap-2" href="<?= base_url('technician/my-responses') ?>">
                                            <i class="bi bi-check2-circle"></i> <span>Respons Saya</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2 small rounded-3 d-flex align-items-center justify-content-between" href="<?= base_url('chat') ?>">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-chat-dots"></i> <span>Chat</span>
                                            </div>
                                            <?php if ($chatUnreadCount > 0): ?>
                                                <span class="badge bg-danger rounded-pill"><?= $chatUnreadCount ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2 small rounded-3 d-flex align-items-center gap-2 <?= (isset($currentPage) && $currentPage === 'history') ? 'active bg-primary-subtle text-primary' : '' ?>" href="<?= base_url('history') ?>">
                                            <i class="bi bi-clock-history"></i> <span>Riwayat Servis</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if ($authUser->role === 'admin'): ?>
                                    <li>
                                        <a class="dropdown-item py-2 small rounded-3 d-flex align-items-center gap-2 <?= (isset($currentPage) && $currentPage === 'admin_users') ? 'active bg-primary-subtle text-primary' : '' ?>" href="<?= base_url('admin/users') ?>">
                                            <i class="bi bi-people"></i> <span>Kelola Users</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2 small rounded-3 d-flex align-items-center gap-2 <?= (isset($currentPage) && $currentPage === 'admin_posts') ? 'active bg-primary-subtle text-primary' : '' ?>" href="<?= base_url('admin/posts') ?>">
                                            <i class="bi bi-card-checklist"></i> <span>Kelola Postingan</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2 small rounded-3 d-flex align-items-center gap-2 <?= (isset($currentPage) && $currentPage === 'admin_categories') ? 'active bg-primary-subtle text-primary' : '' ?>" href="<?= base_url('categories') ?>">
                                            <i class="bi bi-tags"></i> <span>Kategori Jasa</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2 small rounded-3 d-flex align-items-center gap-2 <?= (isset($currentPage) && $currentPage === 'admin_settings') ? 'active bg-primary-subtle text-primary' : '' ?>" href="<?= base_url('admin/settings') ?>">
                                            <i class="bi bi-sliders2"></i> <span>Konfigurasi Sistem</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2 small rounded-3 d-flex align-items-center gap-2 <?= (isset($currentPage) && $currentPage === 'history') ? 'active bg-primary-subtle text-primary' : '' ?>" href="<?= base_url('history') ?>">
                                            <i class="bi bi-receipt-cutoff"></i> <span>Riwayat Transaksi</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <li>
                                    <a class="dropdown-item py-2 small rounded-3 d-flex align-items-center gap-2 <?= (isset($currentPage) && $currentPage === 'profile') ? 'active bg-primary-subtle text-primary' : '' ?>" href="<?= base_url('profile') ?>">
                                        <i class="bi bi-person"></i> <span>Profil Saya</span>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <a class="dropdown-item py-2 small rounded-3 text-danger d-flex align-items-center gap-2" href="<?= base_url('logout') ?>">
                                        <i class="bi bi-box-arrow-right"></i> <span>Keluar (Logout)</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?= base_url('login') ?>" class="nav-btn-login">
                            Masuk
                        </a>
                        <a href="<?= base_url('register') ?>" class="nav-btn-register">
                            Daftar
                        </a>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </nav>

    <!-- Alert if MySQL Database is Disconnected -->
    <?php
        $dbStatusCheck = new Database();
        if (!$dbStatusCheck->isConnected()):
    ?>
        <div class="alert alert-danger mb-0 rounded-0 py-2 small d-flex align-items-center justify-content-center gap-2 border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
            <div>
                <strong>Peringatan Sistem:</strong> Database MySQL belum aktif / terputus! 
                Silakan buka <strong>XAMPP Control Panel</strong> dan klik <strong>Start pada modul MySQL</strong> agar semua akun dan data asli Anda tampil kembali.
            </div>
        </div>
    <?php endif; ?>
