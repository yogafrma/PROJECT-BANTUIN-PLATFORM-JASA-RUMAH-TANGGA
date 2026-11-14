<div class="dashboard-wrapper py-4">
    <div class="container">
        
        <!-- Header Banner (BANTUIN Modern Command Center Theme) -->
        <div class="settings-hero-card mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 position-relative z-1">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="settings-hero-pill">
                            <i class="bi bi-sliders2"></i> Pusat Kendali Sistem
                        </span>
                        <span class="text-white-50 small">Pengaturan &amp; Status Platform</span>
                    </div>
                    <h2 class="fw-bold mb-1 text-white">Konfigurasi Sistem</h2>
                    <p class="text-white-50 mb-0 small">
                        Kelola gateway notifikasi email SMTP, integrasi model AI pintar, dan diagnostik server BANTUIN.
                    </p>
                </div>
                <div>
                    <a href="<?= base_url('admin') ?>" class="btn btn-outline-light rounded-pill btn-sm px-3 fw-semibold d-inline-flex align-items-center gap-1">
                        <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if (has_flash()): ?>
            <?php $flash = get_flash(); ?>
            <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show rounded-3 small py-3 d-flex align-items-center gap-2 mb-4 shadow-sm" role="alert">
                <i class="bi <?= $flash['type'] === 'success' ? 'bi-check-circle-fill text-success fs-5' : 'bi-exclamation-triangle-fill text-danger fs-5' ?>"></i>
                <div><?= $flash['message'] ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Quick Summary Ribbon -->
        <div class="settings-summary-strip mb-4">
            <div class="row g-2 align-items-center">
                <!-- 1. Database MySQL -->
                <div class="col-lg-3 col-sm-6 border-end-lg">
                    <div class="settings-summary-item">
                        <div class="settings-summary-icon bg-success-subtle text-success">
                            <i class="bi bi-database-check"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">Database MySQL</div>
                            <div class="fw-bold text-dark d-flex align-items-center gap-1">
                                <span class="d-inline-block rounded-circle bg-success" style="width: 8px; height: 8px;"></span>
                                <?= $dbStatus['connected'] ? 'Terhubung' : 'Terputus' ?>
                            </div>
                            <div class="text-muted text-xs font-monospace" style="font-size: 0.75rem;"><?= e($dbStatus['database']) ?></div>
                        </div>
                    </div>
                </div>

                <!-- 2. Email SMTP -->
                <div class="col-lg-3 col-sm-6 border-end-lg">
                    <div class="settings-summary-item">
                        <div class="settings-summary-icon bg-primary-subtle text-primary">
                            <i class="bi bi-envelope-check"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">Notifikasi Email</div>
                            <div class="fw-bold text-dark d-flex align-items-center gap-1">
                                <span class="d-inline-block rounded-circle <?= $smtp['configured'] ? 'bg-success' : 'bg-warning' ?>" style="width: 8px; height: 8px;"></span>
                                <?= $smtp['configured'] ? 'SMTP Aktif' : 'Perlu Diatur' ?>
                            </div>
                            <div class="text-muted text-xs font-monospace text-truncate" style="font-size: 0.75rem; max-width: 170px;" title="<?= e($smtp['user'] ?: 'Belum diatur') ?>">
                                <?= e($smtp['user'] ?: 'Belum diatur') ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. AI Active Model -->
                <div class="col-lg-3 col-sm-6 border-end-lg">
                    <div class="settings-summary-item">
                        <div class="settings-summary-icon bg-info-subtle text-info">
                            <i class="bi bi-cpu"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">Mesin AI Aktif</div>
                            <div class="fw-bold text-dark text-truncate" style="max-width: 170px;" title="<?= e($aiConfig['provider_info']['name'] ?? 'BANTUIN AI') ?>">
                                <?= e($aiConfig['provider_info']['name'] ?? 'Google Gemini') ?>
                            </div>
                            <div class="text-muted text-xs font-monospace text-truncate" style="font-size: 0.75rem; max-width: 170px;" title="<?= e($aiConfig['model']) ?>">
                                <?= e($aiConfig['model']) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Server Time & PHP -->
                <div class="col-lg-3 col-sm-6">
                    <div class="settings-summary-item">
                        <div class="settings-summary-icon bg-secondary-subtle text-secondary">
                            <i class="bi bi-hdd-network"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">Server Runtime</div>
                            <div class="fw-bold text-dark">PHP <?= e($server['php_version']) ?></div>
                            <div class="text-muted text-xs" style="font-size: 0.75rem;"><?= e($server['timezone']) ?> (<?= e($server['app_env']) ?>)</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs (Modern Segmented Navigation) -->
        <div class="mb-4">
            <div class="settings-tab-container">
                <button type="button" class="settings-tab-btn active" id="tab-btn-smtp" onclick="switchSettingsTab('smtp')">
                    <i class="bi bi-envelope"></i>
                    <span>Notifikasi Email (SMTP)</span>
                </button>
                <button type="button" class="settings-tab-btn" id="tab-btn-ai" onclick="switchSettingsTab('ai')">
                    <i class="bi bi-cpu"></i>
                    <span>Integrasi Mesin AI</span>
                </button>
                <button type="button" class="settings-tab-btn" id="tab-btn-playground" onclick="switchSettingsTab('playground')">
                    <i class="bi bi-terminal"></i>
                    <span>AI Live Playground</span>
                </button>
                <button type="button" class="settings-tab-btn" id="tab-btn-system" onclick="switchSettingsTab('system')">
                    <i class="bi bi-hdd-stack"></i>
                    <span>Status Server &amp; Database</span>
                </button>
            </div>
        </div>

        <!-- TAB CONTENT CONTAINER -->

        <!-- =========================================================================
             TAB 1: NOTIFIKASI EMAIL (SMTP)
             ========================================================================= -->
        <div class="settings-tab-pane" id="tab-pane-smtp">
            <div class="row g-4">
                <!-- Left: Form Konfigurasi SMTP -->
                <div class="col-lg-7">
                    <div class="settings-card h-100">
                        <div class="settings-card-header">
                            <div>
                                <h5 class="settings-title">Formulir Konfigurasi Server SMTP</h5>
                                <p class="settings-subtitle">Atur kredensial email pengirim resmi untuk pengiriman kode verifikasi OTP dan notifikasi platform.</p>
                            </div>
                            <span class="badge <?= $smtp['configured'] ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning-emphasis' ?> rounded-pill px-3 py-2 small fw-bold">
                                <?= $smtp['configured'] ? 'Siap Kirim' : 'Perlu Diatur' ?>
                            </span>
                        </div>

                        <form action="<?= base_url('admin/updateSmtpSettings') ?>" method="POST" id="formSmtpSettings">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                            <div class="row g-3 mb-3">
                                <!-- SMTP Host -->
                                <div class="col-md-7">
                                    <label class="settings-label" for="inputSmtpHost">
                                        <span>Host Server SMTP</span>
                                    </label>
                                    <input type="text" name="smtp_host" id="inputSmtpHost" class="form-control settings-input font-monospace" value="<?= e($smtp['host']) ?>" required placeholder="smtp.gmail.com">
                                    <div class="settings-help">Untuk Gmail gunakan <code>smtp.gmail.com</code>.</div>
                                </div>

                                <!-- SMTP Port & Encryption -->
                                <div class="col-md-5">
                                    <label class="settings-label" for="inputSmtpPort">
                                        <span>Port &amp; Enkripsi</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" name="smtp_port" id="inputSmtpPort" class="form-control settings-input font-monospace" value="<?= e($smtp['port']) ?>" required placeholder="465" style="max-width: 90px;">
                                        <select name="smtp_encryption" id="selectSmtpEncryption" class="form-select settings-select">
                                            <option value="ssl" <?= ($smtp['encryption'] === 'ssl' || $smtp['port'] == 465) ? 'selected' : '' ?>>SSL (Port 465)</option>
                                            <option value="tls" <?= ($smtp['encryption'] === 'tls' || $smtp['port'] == 587) ? 'selected' : '' ?>>TLS (Port 587)</option>
                                        </select>
                                    </div>
                                    <div class="settings-help">SSL (465) direkomendasikan untuk Google.</div>
                                </div>

                                <!-- Email Pengirim (Username) -->
                                <div class="col-12">
                                    <label class="settings-label" for="inputSmtpUser">
                                        <span>Email Akun Pengirim (Username SMTP)</span>
                                    </label>
                                    <input type="email" name="smtp_user" id="inputSmtpUser" class="form-control settings-input font-monospace" value="<?= e($smtp['user']) ?>" required placeholder="contoh: yogafrmai@gmail.com">
                                    <div class="settings-help">Alamat email resmi yang telah memiliki otentikasi SMTP aktif.</div>
                                </div>

                                <!-- Sandi Aplikasi (App Password) -->
                                <div class="col-12">
                                    <label class="settings-label" for="inputSmtpPass">
                                        <span>Sandi Aplikasi (Google App Password)</span>
                                        <?php if (!empty($smtp['pass'])): ?>
                                            <span class="badge bg-light text-muted border font-monospace" style="font-size: 0.72rem;">Sandi Tersimpan Aktif</span>
                                        <?php endif; ?>
                                    </label>
                                    <div class="input-group">
                                        <input type="password" name="smtp_pass" id="inputSmtpPass" class="form-control settings-input font-monospace" placeholder="<?= !empty($smtp['pass']) ? '•••••••••••••••• (Tersimpan - kosongkan jika tidak ingin mengubah)' : 'Masukkan 16 digit Sandi Aplikasi Google' ?>" autocomplete="new-password">
                                        <button class="btn btn-outline-secondary" type="button" onclick="toggleSmtpPassVisibility()" title="Lihat/Sembunyikan Sandi" style="border-radius: 0 12px 12px 0;">
                                            <i class="bi bi-eye" id="iconEyeSmtp"></i>
                                        </button>
                                    </div>
                                    <div class="settings-help">
                                        Gunakan 16 karakter <strong>Sandi Aplikasi (App Password)</strong> yang dibuat dari Keamanan Akun Google, bukan kata sandi akun email biasa.
                                    </div>
                                </div>

                                <!-- Nama Pengirim Resmi -->
                                <div class="col-md-6">
                                    <label class="settings-label" for="inputSmtpFromName">
                                        <span>Nama Pengirim (From Name)</span>
                                    </label>
                                    <input type="text" name="smtp_from_name" id="inputSmtpFromName" class="form-control settings-input" value="<?= e($smtp['from_name']) ?>" required placeholder="BANTUIN - Platform Jasa">
                                    <div class="settings-help">Nama yang akan muncul di kotak masuk penerima.</div>
                                </div>

                                <!-- Email Balasan (From / Reply-To Email) -->
                                <div class="col-md-6">
                                    <label class="settings-label" for="inputSmtpFromEmail">
                                        <span>Email Balasan (Reply-To)</span>
                                    </label>
                                    <input type="email" name="smtp_from_email" id="inputSmtpFromEmail" class="form-control settings-input font-monospace" value="<?= e($smtp['from_email']) ?>" placeholder="no-reply@bantuin.id">
                                    <div class="settings-help">Opsional, default sama dengan email pengirim.</div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end pt-3 border-top">
                                <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm">
                                    <i class="bi bi-check2"></i>
                                    <span>Simpan Pengaturan SMTP</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right: Status Ringkasan & Form Tes Kirim Email -->
                <div class="col-lg-5">
                    <!-- Status Ringkasan SMTP -->
                    <div class="settings-card mb-4">
                        <div class="settings-card-header">
                            <div>
                                <h5 class="settings-title">Status Koneksi Email</h5>
                                <p class="settings-subtitle">Ringkasan status parameter SMTP yang aktif pada sistem saat ini.</p>
                            </div>
                        </div>

                        <div class="table-responsive mb-0">
                            <table class="table table-sm table-borderless small mb-0">
                                <tbody>
                                    <tr>
                                        <td class="text-muted" style="width: 140px;">Status Layanan:</td>
                                        <td>
                                            <?php if ($smtp['configured']): ?>
                                                <span class="text-success fw-bold d-inline-flex align-items-center gap-1">
                                                    <i class="bi bi-check-circle-fill"></i> Terkonfigurasi &amp; Aktif
                                                </span>
                                            <?php else: ?>
                                                <span class="text-warning-emphasis fw-bold d-inline-flex align-items-center gap-1">
                                                    <i class="bi bi-exclamation-triangle-fill"></i> Belum Lengkap (Isi Sandi)
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Host &amp; Port:</td>
                                        <td class="font-monospace text-dark fw-semibold"><?= e($smtp['host']) ?>:<?= e($smtp['port']) ?> (<?= strtoupper(e($smtp['encryption'])) ?>)</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Akun Pengirim:</td>
                                        <td class="font-monospace text-primary fw-semibold"><?= e($smtp['user'] ?: 'Belum diisi') ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Nama Pengirim:</td>
                                        <td class="text-dark"><?= e($smtp['from_name']) ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Sandi Aplikasi:</td>
                                        <td>
                                            <span class="badge bg-light text-muted border font-monospace">
                                                <?= !empty($smtp['pass']) ? e($smtp['pass_masked']) : 'Belum diatur' ?>
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Uji Coba Pengiriman Email (Live Test) -->
                    <div class="settings-card mb-4">
                        <div class="settings-card-header">
                            <div>
                                <h5 class="settings-title">Uji Pengiriman Email</h5>
                                <p class="settings-subtitle">Kirim email simulasi verifikasi OTP ke alamat penerima untuk menguji koneksi SMTP.</p>
                            </div>
                        </div>

                        <form action="<?= base_url('admin/testEmail') ?>" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                            <div class="mb-3">
                                <label class="settings-label" for="inputTargetEmail">
                                    <span>Alamat Email Tujuan Uji Coba</span>
                                </label>
                                <input type="email" name="target_email" id="inputTargetEmail" class="form-control settings-input font-monospace" placeholder="nama@email.com" value="<?= e($smtp['user'] ?: (Auth::user()->email ?? '')) ?>" required>
                                <div class="settings-help">Sistem akan mengirimkan 1 email simulasi OTP resmi dari BANTUIN.</div>
                            </div>

                            <button type="submit" class="btn btn-outline-primary rounded-pill w-100 py-2 fw-semibold d-inline-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-send"></i>
                                <span>Kirim Email Uji Coba Sekarang</span>
                            </button>
                        </form>
                    </div>

                    <!-- Petunjuk Sandi Aplikasi Gmail -->
                    <div class="p-3 rounded-4 bg-light border">
                        <div class="fw-bold text-dark small mb-2 d-flex align-items-center gap-2">
                            <i class="bi bi-info-circle text-primary"></i>
                            <span>Panduan Singkat Sandi Aplikasi Gmail</span>
                        </div>
                        <ol class="small text-muted ps-3 mb-0" style="font-size: 0.78rem; line-height: 1.55;">
                            <li>Buka <strong>myaccount.google.com</strong> &rarr; menu <strong>Keamanan</strong>.</li>
                            <li>Pastikan <strong>Verifikasi 2 Langkah</strong> sudah aktif.</li>
                            <li>Cari <strong>Sandi Aplikasi</strong> (App Passwords) lalu buat nama baru <em>BANTUIN</em>.</li>
                            <li>Salin 16 karakter tanpa spasi ke form Sandi Aplikasi di samping.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- =========================================================================
             TAB 2: INTEGRASI MESIN AI
             ========================================================================= -->
        <div class="settings-tab-pane d-none" id="tab-pane-ai">
            <div class="row g-4">
                <!-- Left: Form Konfigurasi AI -->
                <div class="col-lg-7">
                    <div class="settings-card h-100" id="ai-settings-card">
                        <div class="settings-card-header">
                            <div>
                                <h5 class="settings-title">Pengaturan Provider &amp; Model AI</h5>
                                <p class="settings-subtitle">Pilih penyedia mesin kecerdasan buatan untuk fitur Diagnosa Kerusakan Rumah dan Chatbot Asisten.</p>
                            </div>
                            <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 small fw-bold" id="badgeActiveAi">
                                Aktif: <?= e($aiConfig['provider_info']['name'] ?? 'Google Gemini') ?>
                            </span>
                        </div>

                        <form action="<?= base_url('admin/updateAiSettings') ?>" method="POST" id="formAiSettings">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <input type="hidden" name="ai_provider" id="inputAiProvider" value="<?= e($aiConfig['provider']) ?>">

                            <!-- 1. Pemilihan Provider AI (Clean Cards) -->
                            <div class="mb-4">
                                <label class="settings-label mb-2">
                                    <span>1. Pilih Penyedia Layanan AI (Provider)</span>
                                </label>
                                <div class="row g-3" id="providerCardsGrid">
                                    <?php foreach ($aiProviders as $pId => $p): ?>
                                        <?php $isActive = ($aiConfig['provider'] === $pId); ?>
                                        <div class="col-md-6 col-12">
                                            <div class="settings-provider-card <?= $isActive ? 'selected' : '' ?>" 
                                                 role="button" 
                                                 data-provider-id="<?= e($pId) ?>"
                                                 data-provider-name="<?= e($p['name']) ?>"
                                                 data-default-model="<?= e($p['default_model']) ?>"
                                                 data-doc-url="<?= e($p['doc_url']) ?>"
                                                 data-placeholder="<?= e($p['key_placeholder']) ?>"
                                                 data-needs-url="<?= !empty($p['needs_custom_url']) ? '1' : '0' ?>"
                                                 data-is-offline="<?= !empty($p['is_offline']) ? '1' : '0' ?>"
                                                 onclick="selectAiProvider('<?= e($pId) ?>')">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <div class="settings-provider-icon" style="background-color: <?= e($p['color']) ?>;">
                                                        <i class="bi <?= e($p['icon']) ?>"></i>
                                                    </div>
                                                    <span class="badge <?= $isActive ? 'bg-primary' : 'bg-light text-secondary border' ?> rounded-pill small check-badge">
                                                        <?= $isActive ? 'Terpilih' : 'Pilih' ?>
                                                    </span>
                                                </div>
                                                <div class="fw-bold text-dark small mb-1"><?= e($p['name']) ?></div>
                                                <div class="text-muted text-xs" style="font-size: 0.76rem; line-height: 1.35;">
                                                    <?= e($p['description']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- 2. Detail Model & Kredensial -->
                            <div class="p-3 rounded-4 bg-light border mb-4">
                                <div class="fw-bold text-dark small mb-3 d-flex align-items-center gap-2">
                                    <i class="bi bi-shield-lock text-primary"></i>
                                    <span>2. Kredensial Kunci API &amp; Model</span>
                                </div>

                                <div class="row g-3">
                                    <!-- API Key -->
                                    <div class="col-12" id="apiKeyWrapper">
                                        <label class="settings-label" for="inputAiKey">
                                            <span>API Key</span>
                                            <a href="#" id="linkGetApiKey" target="_blank" class="text-decoration-none text-primary fw-bold" style="font-size: 0.75rem;">
                                                Dapatkan API Key Resmi <i class="bi bi-box-arrow-up-right ms-1"></i>
                                            </a>
                                        </label>
                                        <div class="input-group">
                                            <input type="password" name="ai_api_key" id="inputAiKey" class="form-control settings-input font-monospace" placeholder="Tempel API Key di sini..." value="<?= e($aiConfig['api_key']) ?>" autocomplete="off">
                                            <button class="btn btn-outline-secondary" type="button" id="btnToggleKeyVisibility" onclick="toggleKeyVisibility()" title="Lihat/Sembunyikan Kunci" style="border-radius: 0 12px 12px 0;">
                                                <i class="bi bi-eye" id="iconEyeKey"></i>
                                            </button>
                                        </div>
                                        <!-- Dynamic Auto-Detection Badge Alert -->
                                        <div id="keyDetectionBadge"></div>
                                        <div class="settings-help">
                                            Kunci tersimpan saat ini: <code class="text-dark fw-bold"><?= e($aiConfig['api_key_masked']) ?></code>
                                        </div>
                                    </div>

                                    <!-- Model Name -->
                                    <div class="col-12" id="modelWrapper">
                                        <label class="settings-label" for="inputAiModel">
                                            <span>Model AI yang Digunakan</span>
                                        </label>
                                        <input type="text" name="ai_model" id="inputAiModel" class="form-control settings-input font-monospace" value="<?= e($aiConfig['model']) ?>" required placeholder="Contoh: gemini-flash-lite-latest">
                                        <div class="mt-2" id="modelPresetsContainer">
                                            <span class="text-muted small me-2" style="font-size: 0.75rem;">Preset Rekomendasi:</span>
                                            <div class="d-inline-flex flex-wrap gap-1" id="modelPillBadges">
                                                <!-- Dynamically filled by JS -->
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Custom Endpoint URL (Visible if Custom/Local) -->
                                    <div class="col-12" id="customUrlWrapper" style="<?= empty($aiConfig['provider_info']['needs_custom_url']) ? 'display: none;' : '' ?>">
                                        <label class="settings-label" for="inputCustomUrl">
                                            <span>Custom Base URL Endpoint</span>
                                        </label>
                                        <input type="url" name="ai_custom_url" id="inputCustomUrl" class="form-control settings-input font-monospace" value="<?= e($aiConfig['custom_url']) ?>" placeholder="http://localhost:11434/v1/chat/completions">
                                        <div class="settings-help">Gunakan endpoint lokal jika menjalankan Ollama atau model lokal kustom.</div>
                                    </div>

                                    <!-- Temperature Slider -->
                                    <div class="col-md-6">
                                        <label class="settings-label" for="rangeTemperature">
                                            <span>Kreativitas (Temperature)</span>
                                            <span class="badge bg-light text-dark border font-monospace" id="labelTemperature"><?= e($aiConfig['temperature']) ?></span>
                                        </label>
                                        <input type="range" class="form-range" name="ai_temperature" id="rangeTemperature" min="0.0" max="1.0" step="0.1" value="<?= e($aiConfig['temperature']) ?>" oninput="document.getElementById('labelTemperature').innerText = this.value">
                                        <div class="d-flex justify-content-between text-muted" style="font-size: 0.72rem;">
                                            <span>0.0 (Presisi &amp; Faktual)</span>
                                            <span>1.0 (Kreatif)</span>
                                        </div>
                                    </div>

                                    <!-- Fallback Switch -->
                                    <div class="col-md-6 d-flex align-items-center">
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" name="ai_fallback_enabled" id="switchFallback" value="1" <?= !empty($aiConfig['fallback_enabled']) ? 'checked' : '' ?>>
                                            <label class="form-check-label small fw-semibold text-dark" for="switchFallback">
                                                Fallback ke Smart Local Engine
                                            </label>
                                            <div class="text-muted" style="font-size: 0.72rem;">
                                                Alihkan otomatis ke mesin lokal jika kuota API habis agar layanan tidak terputus.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Live Test Result Box Container -->
                            <div id="aiTestResultBox" class="mb-3 d-none"></div>

                            <!-- Action Buttons -->
                            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center pt-2">
                                <button type="button" class="btn btn-outline-secondary rounded-pill btn-sm px-3 fw-semibold d-inline-flex align-items-center gap-1" id="btnTestAi" onclick="runAiConnectionTest()">
                                    <i class="bi bi-lightning-charge"></i>
                                    <span>Uji Sambungan AI</span>
                                </button>

                                <button type="submit" class="btn btn-primary rounded-pill px-4 btn-sm fw-bold shadow-sm d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-check2"></i>
                                    <span>Simpan Konfigurasi AI</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right: Status Runtime AI & Info -->
                <div class="col-lg-5">
                    <!-- Status Runtime AI Gateway -->
                    <div class="settings-card mb-4">
                        <div class="settings-card-header">
                            <div>
                                <h5 class="settings-title">Status Runtime AI Gateway</h5>
                                <p class="settings-subtitle">Monitoring konektivitas mesin kecerdasan buatan aktif.</p>
                            </div>
                            <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 small">
                                <span class="d-inline-block rounded-circle bg-success me-1" style="width: 6px; height: 6px;"></span> Online
                            </span>
                        </div>

                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                                <span class="text-muted">Penyedia Aktif:</span>
                                <span class="fw-bold" style="color: <?= e($aiConfig['provider_info']['color'] ?? '#0078D4') ?>;">
                                    <i class="bi <?= e($aiConfig['provider_info']['icon'] ?? 'bi-cpu') ?> me-1"></i>
                                    <?= e($aiConfig['provider_info']['name']) ?>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                                <span class="text-muted">Model Terpasang:</span>
                                <span class="badge bg-light text-dark border font-monospace"><?= e($aiConfig['model']) ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                                <span class="text-muted">Kredensial API:</span>
                                <span class="font-monospace text-muted"><?= !empty($aiConfig['api_key']) ? e($aiConfig['api_key_masked']) : '<span class="badge bg-warning-subtle text-warning-emphasis">Belum Diisi</span>' ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                                <span class="text-muted">Kreativitas (Temp):</span>
                                <span class="badge bg-secondary rounded-pill font-monospace"><?= e($aiConfig['temperature']) ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                                <span class="text-muted">Fallback Guardrail:</span>
                                <span class="badge <?= !empty($aiConfig['fallback_enabled']) ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' ?>">
                                    <?= !empty($aiConfig['fallback_enabled']) ? 'Aktif (Auto-failover)' : 'Nonaktif' ?>
                                </span>
                            </li>
                        </ul>
                    </div>

                    <!-- Petunjuk Rekomendasi Model -->
                    <div class="p-3 rounded-4 bg-light border">
                        <div class="fw-bold text-dark small mb-2 d-flex align-items-center gap-2">
                            <i class="bi bi-lightbulb text-primary"></i>
                            <span>Rekomendasi Pemilihan Model</span>
                        </div>
                        <p class="small text-muted mb-2" style="font-size: 0.78rem; line-height: 1.55;">
                            Untuk kecepatan respon maksimal dalam diagnosa keluhan perbaikan rumah tangga, disarankan menggunakan model <strong>gemini-flash-lite-latest</strong> dari Google AI Studio yang memiliki kuota gratis memadai dan latensi rendah.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- =========================================================================
             TAB 3: AI LIVE PLAYGROUND
             ========================================================================= -->
        <div class="settings-tab-pane d-none" id="tab-pane-playground">
            <div class="row g-4">
                <div class="col-lg-8 mx-auto">
                    <div class="settings-card">
                        <div class="settings-card-header">
                            <div>
                                <h5 class="settings-title">AI Live Playground &amp; Prompt Tester</h5>
                                <p class="settings-subtitle">Uji coba respons dan latensi jawaban mesin AI secara langsung di dashboard tanpa membuka halaman pelanggan.</p>
                            </div>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 small fw-bold">
                                Sandbox Mode
                            </span>
                        </div>

                        <!-- Quick Presets -->
                        <div class="mb-3">
                            <label class="settings-label mb-1">
                                <span>Pilih Pertanyaan Uji Cepat:</span>
                            </label>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="settings-pill-preset" onclick="setPlaygroundPrompt('AC rumah saya tiba-tiba mati total dan ada bau terbakar, apa penyebab dan solusinya?')">
                                    Servis AC Mati Total
                                </button>
                                <button type="button" class="settings-pill-preset" onclick="setPlaygroundPrompt('MCB listrik rumah sering anjlok saat menyalakan pompa air, bagaimana mengatasinya?')">
                                    MCB Listrik Anjlok
                                </button>
                                <button type="button" class="settings-pill-preset" onclick="setPlaygroundPrompt('Kunci pintu kamar macet patah di dalam silinder, apakah perlu ganti rumah kunci?')">
                                    Kunci Pintu Patah
                                </button>
                            </div>
                        </div>

                        <!-- Prompt Input -->
                        <div class="mb-3">
                            <label class="settings-label" for="playgroundInputPrompt">
                                <span>Pertanyaan / Prompt Uji</span>
                            </label>
                            <textarea id="playgroundInputPrompt" class="form-control settings-input font-monospace" rows="3" placeholder="Tuliskan skenario keluhan atau pertanyaan perbaikan rumah tangga di sini..."></textarea>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small" style="font-size: 0.78rem;">
                                Target Mesin: <strong id="playgroundTargetName" class="text-primary"><?= e($aiConfig['provider_info']['name'] ?? 'Google Gemini') ?></strong>
                            </span>
                            <button type="button" id="btnSendPlayground" class="btn btn-primary rounded-pill px-4 btn-sm fw-bold shadow-sm d-inline-flex align-items-center gap-2" onclick="runPlaygroundPrompt()">
                                <i class="bi bi-send" id="iconSendPlayground"></i>
                                <span id="textSendPlayground">Kirim Uji Coba</span>
                            </button>
                        </div>

                        <!-- Playground Result Wrapper -->
                        <div id="playgroundResultWrapper" class="d-none">
                            <div class="p-3 rounded-4 bg-light border">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <span class="small fw-bold text-dark d-flex align-items-center gap-2">
                                        <i class="bi bi-cpu text-primary"></i> <span id="playgroundResProvider">AI Response</span>
                                    </span>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill font-monospace" id="playgroundResLatency">0ms</span>
                                </div>
                                <div class="text-dark small" id="playgroundResContent" style="white-space: pre-wrap; font-size: 0.85rem; line-height: 1.55;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- =========================================================================
             TAB 4: STATUS SERVER & DATABASE
             ========================================================================= -->
        <div class="settings-tab-pane d-none" id="tab-pane-system">
            <div class="row g-4">
                <!-- Database MySQL Card -->
                <div class="col-lg-6">
                    <div class="settings-card h-100">
                        <div class="settings-card-header">
                            <div>
                                <h5 class="settings-title">Koneksi Basis Data (MySQL / MariaDB)</h5>
                                <p class="settings-subtitle">Status parameter koneksi database aplikasi yang sedang berjalan.</p>
                            </div>
                            <span class="badge <?= $dbStatus['connected'] ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?> rounded-pill px-3 py-1 small fw-bold">
                                <?= $dbStatus['connected'] ? 'Terhubung' : 'Terputus' ?>
                            </span>
                        </div>

                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                                <span class="text-muted">Host &amp; Port:</span>
                                <span class="fw-bold font-monospace text-dark"><?= e($dbStatus['host']) ?>:<?= e($dbStatus['port']) ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                                <span class="text-muted">Nama Database:</span>
                                <span class="fw-bold font-monospace text-primary"><?= e($dbStatus['database']) ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                                <span class="text-muted">Versi MySQL / Database:</span>
                                <span class="fw-bold text-dark"><?= e($dbStatus['version']) ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                                <span class="text-muted">Driver Koneksi:</span>
                                <span class="badge bg-light text-dark border font-monospace">PHP PDO (MySQL)</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Server Runtime & Environment Card -->
                <div class="col-lg-6">
                    <div class="settings-card h-100">
                        <div class="settings-card-header">
                            <div>
                                <h5 class="settings-title">Spesifikasi Server &amp; Runtime</h5>
                                <p class="settings-subtitle">Informasi lingkungan eksekusi web server dan runtime PHP.</p>
                            </div>
                            <span class="badge bg-light text-dark border rounded-pill px-3 py-1 small fw-bold">
                                <?= strtoupper(e($server['app_env'])) ?>
                            </span>
                        </div>

                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                                <span class="text-muted">Versi PHP:</span>
                                <span class="fw-bold font-monospace text-dark">PHP <?= e($server['php_version']) ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                                <span class="text-muted">Perangkat Lunak Web:</span>
                                <span class="fw-bold text-dark"><?= e($server['server_software']) ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                                <span class="text-muted">Versi Aplikasi:</span>
                                <span class="fw-bold text-dark">v<?= e($server['app_version']) ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                                <span class="text-muted">Zona Waktu Sistem:</span>
                                <span class="fw-bold text-dark"><?= e($server['timezone']) ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                                <span class="text-muted">Waktu Server Saat Ini:</span>
                                <span class="fw-bold text-dark"><?= e($server['server_time']) ?></span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Kontak Bantuan & CS -->
                <div class="col-12">
                    <div class="p-3 rounded-4 bg-light border d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <div class="fw-bold text-dark small mb-1">Pusat Bantuan &amp; Dukungan Teknis Platform</div>
                            <div class="text-muted small">Jika mengalami kendala konfigurasi server atau integrasi API, hubungi saluran pengembang resmi.</div>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="<?= wa_url(defined('ADMIN_SUPPORT_PHONE') ? ADMIN_SUPPORT_PHONE : '0895635434025', 'Halo CS BANTUIN') ?>" target="_blank" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-semibold d-inline-flex align-items-center gap-1">
                                <i class="bi bi-whatsapp"></i> CS WhatsApp
                            </a>
                            <a href="mailto:<?= defined('ADMIN_SUPPORT_EMAIL') ? ADMIN_SUPPORT_EMAIL : 'yogafrmai@gmail.com' ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold d-inline-flex align-items-center gap-1">
                                <i class="bi bi-envelope"></i> Email Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    const AI_PROVIDERS_DATA = <?= json_encode($aiProviders, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
    let currentSelectedProvider = '<?= e($aiConfig['provider']) ?>';

    // Switch Settings Tab
    function switchSettingsTab(tabKey) {
        // Update Buttons
        document.querySelectorAll('.settings-tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        const activeBtn = document.getElementById('tab-btn-' + tabKey);
        if (activeBtn) activeBtn.classList.add('active');

        // Update Panes
        document.querySelectorAll('.settings-tab-pane').forEach(pane => {
            pane.classList.add('d-none');
        });
        const activePane = document.getElementById('tab-pane-' + tabKey);
        if (activePane) activePane.classList.remove('d-none');

        // Update URL Hash without scroll jump
        if (history.replaceState) {
            history.replaceState(null, null, '#' + tabKey);
        } else {
            window.location.hash = tabKey;
        }
    }

    // Toggle Visibility for SMTP Password
    function toggleSmtpPassVisibility() {
        const input = document.getElementById('inputSmtpPass');
        const icon = document.getElementById('iconEyeSmtp');
        if (!input) return;

        if (input.type === 'password') {
            input.type = 'text';
            if (icon) icon.className = 'bi bi-eye-slash';
        } else {
            input.type = 'password';
            if (icon) icon.className = 'bi bi-eye';
        }
    }

    // Toggle Visibility for AI API Key
    function toggleKeyVisibility() {
        const input = document.getElementById('inputAiKey');
        const icon = document.getElementById('iconEyeKey');
        if (!input) return;

        if (input.type === 'password') {
            input.type = 'text';
            if (icon) icon.className = 'bi bi-eye-slash';
        } else {
            input.type = 'password';
            if (icon) icon.className = 'bi bi-eye';
        }
    }

    // AI Provider Selection
    function selectAiProvider(providerId) {
        currentSelectedProvider = providerId;
        const inputProv = document.getElementById('inputAiProvider');
        if (inputProv) inputProv.value = providerId;

        // Highlight card
        document.querySelectorAll('.settings-provider-card').forEach(card => {
            const isThis = card.getAttribute('data-provider-id') === providerId;
            if (isThis) {
                card.classList.add('selected');
                const badge = card.querySelector('.check-badge');
                if (badge) {
                    badge.className = 'badge bg-primary rounded-pill small check-badge';
                    badge.innerText = 'Terpilih';
                }
            } else {
                card.classList.remove('selected');
                const badge = card.querySelector('.check-badge');
                if (badge) {
                    badge.className = 'badge bg-light text-secondary border rounded-pill small check-badge';
                    badge.innerText = 'Pilih';
                }
            }
        });

        const pData = AI_PROVIDERS_DATA[providerId];
        if (!pData) return;

        // Update Active Badge
        const activeBadge = document.getElementById('badgeActiveAi');
        if (activeBadge) activeBadge.innerText = 'Aktif: ' + pData.name;

        // Update Playground Target Name
        const pgTarget = document.getElementById('playgroundTargetName');
        if (pgTarget) pgTarget.innerText = pData.name;

        // Update default model
        const modelInput = document.getElementById('inputAiModel');
        if (modelInput) modelInput.value = pData.default_model;

        // Update Model Presets Pills
        renderModelPills(pData.available_models, pData.default_model);

        // Handle Key wrapper & docs
        const keyWrapper = document.getElementById('apiKeyWrapper');
        const keyInput = document.getElementById('inputAiKey');
        const docLink = document.getElementById('linkGetApiKey');

        if (pData.is_offline) {
            if (keyWrapper) keyWrapper.style.display = 'none';
        } else {
            if (keyWrapper) keyWrapper.style.display = 'block';
            if (keyInput) keyInput.placeholder = pData.key_placeholder || 'Tempel API Key di sini...';
            if (docLink) {
                if (pData.doc_url) {
                    docLink.style.display = 'inline-block';
                    docLink.href = pData.doc_url;
                } else {
                    docLink.style.display = 'none';
                }
            }
        }

        // Handle custom URL wrapper
        const customUrlWrapper = document.getElementById('customUrlWrapper');
        if (customUrlWrapper) {
            if (pData.needs_custom_url) {
                customUrlWrapper.style.display = 'block';
                const inputCustom = document.getElementById('inputCustomUrl');
                if (inputCustom && !inputCustom.value) {
                    inputCustom.value = pData.default_url || 'http://localhost:11434/v1/chat/completions';
                }
            } else {
                customUrlWrapper.style.display = 'none';
            }
        }
    }

    function renderModelPills(models, defaultModel) {
        const container = document.getElementById('modelPillBadges');
        if (!container) return;
        container.innerHTML = '';
        if (!models) return;

        Object.keys(models).forEach(mKey => {
            const badge = document.createElement('button');
            badge.type = 'button';
            badge.className = 'settings-pill-preset font-monospace';
            badge.innerText = mKey;
            badge.onclick = function() {
                const mInput = document.getElementById('inputAiModel');
                if (mInput) mInput.value = mKey;
            };
            container.appendChild(badge);
        });
    }

    // Smart API Key Pattern Detector
    function detectProviderFromKey(key) {
        key = (key || '').trim();
        if (!key) return null;
        if (key.startsWith('nvapi-')) return 'nvidia';
        if (key.startsWith('AIzaSy') || key.startsWith('AQ.')) return 'gemini';
        return null;
    }

    function handleApiKeyInput(keyValue) {
        const rawKey = (keyValue || '').trim();
        const detectedId = detectProviderFromKey(rawKey);
        const badge = document.getElementById('keyDetectionBadge');

        if (!detectedId) {
            if (badge) badge.innerHTML = '';
            return;
        }

        if (rawKey.startsWith('AQ.')) {
            if (currentSelectedProvider !== 'gemini') {
                selectAiProvider('gemini');
            }
            if (badge) {
                badge.innerHTML = `
                    <div class="alert alert-warning py-1 px-2 mt-2 mb-0 d-flex align-items-center gap-2 rounded-3 border-0 small" style="font-size: 0.78rem;">
                        <i class="bi bi-exclamation-circle text-warning fs-6"></i>
                        <div>
                            <strong>Kunci berawalan 'AQ.' (OAuth Token):</strong> Google Gemini API membutuhkan API Key resmi berawalan <code>AIzaSy...</code> dari Google AI Studio.
                        </div>
                    </div>
                `;
            }
            return;
        }

        const pData = AI_PROVIDERS_DATA[detectedId];
        if (!pData) return;

        if (currentSelectedProvider !== detectedId) {
            selectAiProvider(detectedId);
        }

        if (badge) {
            badge.innerHTML = `
                <div class="alert alert-success py-1 px-2 mt-2 mb-0 d-flex align-items-center gap-2 rounded-3 border-0 small" style="font-size: 0.78rem;">
                    <i class="bi bi-check-circle text-success fs-6"></i>
                    <div>
                        Kunci terdeteksi untuk <strong>${pData.name}</strong>. Model otomatis diselaraskan.
                    </div>
                </div>
            `;
        }
    }

    // Test AI Connection via AJAX
    async function runAiConnectionTest() {
        const btn = document.getElementById('btnTestAi');
        const resultBox = document.getElementById('aiTestResultBox');
        if (!btn || !resultBox) return;

        const originalBtnHtml = btn.innerHTML;
        let provider = document.getElementById('inputAiProvider').value;
        const apiKey = document.getElementById('inputAiKey').value.trim();
        let model = document.getElementById('inputAiModel').value.trim();
        const customUrl = document.getElementById('inputCustomUrl') ? document.getElementById('inputCustomUrl').value : '';
        const csrfToken = document.querySelector('input[name="csrf_token"]').value;

        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status"></span> Menguji...`;

        resultBox.className = 'alert alert-light border rounded-3 p-3 mb-3 d-flex align-items-center gap-2';
        resultBox.classList.remove('d-none');
        resultBox.innerHTML = `<div class="spinner-border spinner-border-sm text-primary"></div><span class="small text-muted">Menghubungi endpoint API ${provider}...</span>`;

        try {
            const formData = new FormData();
            formData.append('csrf_token', csrfToken);
            formData.append('ai_provider', provider);
            formData.append('ai_api_key', apiKey);
            formData.append('ai_model', model);
            formData.append('ai_custom_url', customUrl);
            formData.append('is_ajax', '1');

            const res = await fetch('<?= base_url('admin/testAiConnection') ?>', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await res.json();

            if (data.success) {
                resultBox.className = 'alert alert-success border-0 rounded-3 p-3 mb-3 small shadow-sm';
                resultBox.innerHTML = `
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                        <div class="w-100">
                            <div class="fw-bold text-success mb-1">${data.message}</div>
                            <div class="p-2 bg-white rounded-2 border text-dark font-monospace" style="font-size: 0.8rem;">
                                "${data.reply}"
                            </div>
                        </div>
                    </div>
                `;
            } else {
                resultBox.className = 'alert alert-danger border-0 rounded-3 p-3 mb-3 small shadow-sm';
                resultBox.innerHTML = `
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                        <div>
                            <div class="fw-bold text-danger mb-1">Gagal Terhubung:</div>
                            <div>${data.message}</div>
                        </div>
                    </div>
                `;
            }
        } catch (err) {
            resultBox.className = 'alert alert-danger border-0 rounded-3 p-3 mb-3 small shadow-sm';
            resultBox.innerHTML = `
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                    <div><strong>Error:</strong> Gagal mengirim permintaan (${err.message}).</div>
                </div>
            `;
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalBtnHtml;
        }
    }

    // AI Live Playground
    function setPlaygroundPrompt(text) {
        const input = document.getElementById('playgroundInputPrompt');
        if (input) {
            input.value = text;
            input.focus();
        }
    }

    async function runPlaygroundPrompt() {
        const input = document.getElementById('playgroundInputPrompt');
        const prompt = input ? input.value.trim() : '';
        if (!prompt) {
            if (input) input.focus();
            return;
        }

        const btn = document.getElementById('btnSendPlayground');
        const textSpan = document.getElementById('textSendPlayground');
        const icon = document.getElementById('iconSendPlayground');
        const resultWrapper = document.getElementById('playgroundResultWrapper');
        const resProvider = document.getElementById('playgroundResProvider');
        const resLatency = document.getElementById('playgroundResLatency');
        const resContent = document.getElementById('playgroundResContent');

        btn.disabled = true;
        textSpan.innerText = 'Memproses...';
        icon.className = 'spinner-border spinner-border-sm me-1';

        resultWrapper.classList.remove('d-none');
        resContent.innerHTML = '<span class="text-muted fst-italic">Menunggu respons dari mesin AI...</span>';
        resLatency.className = 'badge bg-secondary-subtle text-secondary rounded-pill font-monospace';
        resLatency.innerText = '...';

        let provider = document.getElementById('inputAiProvider').value;
        const apiKey = document.getElementById('inputAiKey').value.trim();
        let model = document.getElementById('inputAiModel').value.trim();
        const customUrl = document.getElementById('inputCustomUrl') ? document.getElementById('inputCustomUrl').value : '';
        const csrfToken = document.querySelector('input[name="csrf_token"]').value;

        try {
            const formData = new FormData();
            formData.append('csrf_token', csrfToken);
            formData.append('prompt', prompt);
            formData.append('ai_provider', provider);
            formData.append('ai_api_key', apiKey);
            formData.append('ai_model', model);
            formData.append('ai_custom_url', customUrl);

            const res = await fetch('<?= base_url('admin/testAiPrompt') ?>', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await res.json();

            if (data.success) {
                resProvider.innerText = `${data.provider} (${data.model})`;
                resLatency.className = 'badge bg-success-subtle text-success rounded-pill font-monospace';
                resLatency.innerText = `${data.latency_ms}ms`;
                resContent.innerText = data.reply;
            } else {
                resProvider.innerText = `${data.provider || 'AI Error'} (${data.model || ''})`;
                resLatency.className = 'badge bg-danger-subtle text-danger rounded-pill font-monospace';
                resLatency.innerText = `${data.latency_ms || 0}ms`;
                resContent.innerHTML = `<span class="text-danger fw-bold">${data.error}</span>`;
            }
        } catch (e) {
            resProvider.innerText = 'Error Jaringan';
            resLatency.className = 'badge bg-danger-subtle text-danger rounded-pill font-monospace';
            resLatency.innerText = 'Err';
            resContent.innerHTML = `<span class="text-danger">Gagal menghubungi server: ${e.message}</span>`;
        } finally {
            btn.disabled = false;
            textSpan.innerText = 'Kirim Uji Coba';
            icon.className = 'bi bi-send';
        }
    }

    // Document Ready Initialization
    document.addEventListener('DOMContentLoaded', function() {
        // Initial Tab selection based on URL hash (e.g. #smtp, #ai, #playground, #system)
        const hash = window.location.hash.replace('#', '');
        if (hash === 'ai' || hash === 'ai-settings-card') {
            switchSettingsTab('ai');
        } else if (hash === 'playground') {
            switchSettingsTab('playground');
        } else if (hash === 'system') {
            switchSettingsTab('system');
        } else {
            switchSettingsTab('smtp');
        }

        // Render AI Model Pills
        const pData = AI_PROVIDERS_DATA[currentSelectedProvider];
        if (pData) {
            renderModelPills(pData.available_models, pData.default_model);
        }

        // Attach Key Listeners for Auto-Detection
        const keyInput = document.getElementById('inputAiKey');
        if (keyInput) {
            keyInput.addEventListener('input', function() {
                handleApiKeyInput(this.value);
            });
            keyInput.addEventListener('paste', function() {
                setTimeout(() => {
                    handleApiKeyInput(this.value);
                }, 30);
            });
            if (keyInput.value) {
                handleApiKeyInput(keyInput.value);
            }
        }
    });
</script>
