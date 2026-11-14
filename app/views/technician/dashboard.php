<div class="dashboard-wrapper py-4">
    <div class="container">
        
        <!-- Welcome Banner -->
        <div class="card border-0 shadow-sm rounded-4 bg-success text-white p-4 mb-4 position-relative overflow-hidden">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 position-relative z-1">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-white text-success rounded-pill px-3 py-1 fw-bold">
                            <i class="bi bi-wrench-adjustable me-1"></i> Mitra Teknisi
                        </span>
                        <span class="text-white-50 small">Pusat Kerja & Penerimaan Order Layanan</span>
                    </div>
                    <h2 class="fw-bold mb-1 text-white">Selamat Bertugas, Pak <?= e($user->name) ?>!</h2>
                    <p class="text-white-50 mb-0 small">
                        Email: <strong><?= e($user->email) ?></strong> &bull; WhatsApp: <strong><?= e($user->whatsapp) ?></strong>
                    </p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="<?= base_url('profile') ?>" class="btn btn-light rounded-pill btn-sm px-3 fw-semibold">
                        <i class="bi bi-person-gear me-1"></i> Edit Profil Teknisi
                    </a>
                    <a href="<?= base_url('logout') ?>" class="btn btn-outline-light rounded-pill btn-sm px-3 fw-semibold">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if (has_flash()): ?>
            <?php $flash = get_flash(); ?>
            <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show rounded-3 small py-2 d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-info-circle-fill"></i>
                <div><?= e($flash['message']) ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Profil Teknisi & Quick Stats -->
        <div class="row g-4 mb-4">
            <!-- Profil Teknisi Card -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0"><i class="bi bi-person-vcard text-success me-2"></i>Profil Keahlian Teknisi</h5>
                        <div class="d-flex gap-2">
                            <a href="<?= base_url('history') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 xs-text fw-semibold">
                                <i class="bi bi-clock-history me-1"></i> Riwayat Servis
                            </a>
                            <a href="<?= base_url('profile') ?>" class="btn btn-outline-success btn-sm rounded-pill px-3 xs-text fw-semibold">
                                <i class="bi bi-pencil-square me-1"></i> Perbarui
                            </a>
                        </div>
                    </div>
                    
                    <div class="row g-3 align-items-center">
                        <div class="col-md-7">
                            <div class="mb-2">
                                <span class="text-muted xs-text d-block">Kategori Keahlian Utama:</span>
                                <span class="badge bg-primary rounded-pill px-3 py-1 fs-6 fw-semibold">
                                    <i class="bi <?= e($profile->category_icon ?? 'bi-tools') ?> me-1"></i> <?= e($profile->category_name ?? 'Teknisi AC') ?>
                                </span>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted xs-text d-block">Jangkauan Wilayah Operasional:</span>
                                <div class="fw-semibold text-dark small"><i class="bi bi-geo-alt text-danger me-1"></i> <?= e($profile->location ?? 'Jabodetabek') ?></div>
                            </div>
                            <div>
                                <span class="text-muted xs-text d-block">Deskripsi & Pengalaman:</span>
                                <p class="text-muted small mb-0"><?= e($profile->description ?? 'Mitra teknisi profesional siap melayani kebutuhan perbaikan rumah.') ?></p>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="p-3 bg-light rounded-3 text-center border">
                                <div class="d-flex justify-content-center align-items-center gap-1 text-warning mb-1">
                                    <i class="bi bi-star-fill fs-3"></i>
                                    <span class="display-6 fw-bold text-dark"><?= number_format($profile->rating ?? 5.0, 1) ?></span>
                                    <span class="text-muted fs-6">/ 5.0</span>
                                </div>
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 xs-text fw-semibold">
                                    Status Akun: Terverifikasi & Aktif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Siap Kerja Card -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                    <h5 class="fw-bold mb-3"><i class="bi bi-broadcast text-success me-2"></i>Status Kesiapan Kerja</h5>
                    <div class="p-3 rounded-3 bg-success bg-opacity-10 border border-success border-opacity-25 mb-3 text-center">
                        <div class="d-flex justify-content-center align-items-center gap-2 mb-1">
                            <span class="status-indicator-dot online"></span>
                            <span class="fw-bold text-success">ONLINE & SIAGA</span>
                        </div>
                        <p class="text-muted xs-text mb-0">Nomor WhatsApp Anda dapat dihubungi oleh pelanggan di sekitar area layanan.</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center small text-muted border-top pt-2 mb-2">
                        <span><i class="bi bi-tools text-primary me-1"></i>Order Sedang Dikerjakan:</span>
                        <span class="badge bg-primary rounded-pill px-2 py-1 fw-bold"><?= (int)($activeJobsCount ?? count($activeJobs ?? [])) ?> Order</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center small text-muted border-top pt-2">
                        <span><i class="bi bi-check2-circle text-success me-1"></i>Pekerjaan Terselesaikan:</span>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 fw-bold"><?= (int)($completedJobsCount ?? 0) ?> Order</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pekerjaan yang Sedang Dikerjakan (Order Aktif) -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4 border-start border-4 border-primary">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">
                        <i class="bi bi-briefcase-fill text-primary me-2"></i>Pekerjaan yang Sedang Dikerjakan (Order Aktif)
                    </h5>
                    <p class="text-muted xs-text mb-0">
                        Order keluhan pelanggan yang telah mengonfirmasi Anda sebagai teknisi pelaksana dan siap dikerjakan.
                    </p>
                </div>
                <div>
                    <span class="badge bg-primary rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1">
                        <i class="bi bi-gear-wide-connected"></i> <?= count($activeJobs ?? []) ?> Order Aktif
                    </span>
                </div>
            </div>

            <?php if (!empty($activeJobs)): ?>
                <div class="row g-3">
                    <?php foreach ($activeJobs as $job): ?>
                        <?php
                            $jPostId = is_object($job) ? $job->post_id : $job['post_id'];
                            $jTitle = is_object($job) ? $job->post_title : $job['post_title'];
                            $jDesc = is_object($job) ? ($job->post_description ?? '') : ($job['post_description'] ?? '');
                            $jCustName = is_object($job) ? $job->customer_name : $job['customer_name'];
                            $jCustWa = is_object($job) ? $job->customer_whatsapp : $job['customer_whatsapp'];
                            $jCatName = is_object($job) ? ($job->category_name ?? 'Layanan') : ($job['category_name'] ?? 'Layanan');
                            $jCatIcon = is_object($job) ? ($job->category_icon ?? 'bi-tools') : ($job['category_icon'] ?? 'bi-tools');
                            $jMin = is_object($job) ? ($job->estimated_min_price ?? 0) : ($job['estimated_min_price'] ?? 0);
                            $jMax = is_object($job) ? ($job->estimated_max_price ?? 0) : ($job['estimated_max_price'] ?? 0);
                            $jConvoId = is_object($job) ? ($job->conversation_id ?? null) : ($job['conversation_id'] ?? null);

                            $jobWaMsg = "Halo Pak/Ibu {$jCustName}, saya Teknisi {$user->name} dari BANTUIN yang menangani keluhan '{$jTitle}'. Kapan waktu yang tepat untuk kunjungan/pemeriksaan?";
                            $jobWaUrl = wa_url($jCustWa, $jobWaMsg);
                        ?>
                        <div class="col-lg-6">
                            <div class="p-3 rounded-4 border border-primary border-opacity-25 bg-white h-100 d-flex flex-column justify-content-between shadow-sm position-relative">
                                <div>
                                    <!-- Badges -->
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 xs-text fw-bold">
                                            <i class="bi <?= e($jCatIcon) ?> me-1"></i> <?= e($jCatName) ?>
                                        </span>
                                        <span class="badge bg-primary text-white rounded-pill px-3 py-1 xs-text fw-semibold d-inline-flex align-items-center gap-1">
                                            <span class="spinner-grow spinner-grow-sm" style="width: 0.55rem; height: 0.55rem;" role="status"></span>
                                            Sedang Dikerjakan
                                        </span>
                                    </div>

                                    <!-- Title -->
                                    <h6 class="fw-bold mb-2">
                                        <a href="<?= base_url('posts/detail/' . $jPostId) ?>" class="text-decoration-none text-dark hover-primary">
                                            <?= e($jTitle) ?>
                                        </a>
                                    </h6>

                                    <!-- Description -->
                                    <p class="text-muted xs-text mb-3">
                                        <?= e(mb_strimwidth($jDesc, 0, 115, '...')) ?>
                                    </p>

                                    <!-- Customer & Cost Info Box -->
                                    <div class="p-2 rounded-3 bg-light border mb-3 xs-text">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="text-muted"><i class="bi bi-person-fill me-1 text-secondary"></i>Customer:</span>
                                            <strong class="text-dark"><?= e($jCustName) ?></strong>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="text-muted"><i class="bi bi-telephone-fill me-1 text-secondary"></i>WhatsApp:</span>
                                            <span class="text-dark font-monospace"><?= e($jCustWa) ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted"><i class="bi bi-tag-fill me-1 text-secondary"></i>Estimasi Biaya:</span>
                                            <strong class="text-success">Rp <?= number_format($jMin, 0, ',', '.') ?> - <?= number_format($jMax, 0, ',', '.') ?></strong>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center pt-2 border-top mt-auto">
                                    <div class="d-flex gap-2">
                                        <!-- Chat Internal Button -->
                                        <a href="<?= base_url('chat/open/' . $jPostId) ?>" 
                                           class="btn btn-warning text-dark btn-sm rounded-pill px-3 fw-semibold xs-text d-inline-flex align-items-center gap-1 shadow-sm">
                                            <i class="bi bi-chat-dots-fill"></i> Chat Internal
                                        </a>
                                        <!-- WhatsApp Button -->
                                        <a href="<?= $jobWaUrl ?>" 
                                           target="_blank" 
                                           rel="noopener noreferrer" 
                                           class="btn btn-outline-success btn-sm rounded-pill px-3 fw-semibold xs-text d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-whatsapp"></i> WA
                                        </a>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <!-- Detail Link -->
                                        <a href="<?= base_url('posts/detail/' . $jPostId) ?>" 
                                           class="btn btn-outline-secondary btn-sm rounded-pill px-2 xs-text" 
                                           title="Lihat Detail Keluhan">
                                            <i class="bi bi-info-circle"></i>
                                        </a>
                                        <!-- Tandai Selesai -->
                                        <form action="<?= base_url('posts/status/' . $jPostId) ?>" method="POST" class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                            <input type="hidden" name="status" value="Selesai">
                                            <button type="button" 
                                                    class="btn btn-success btn-sm rounded-pill px-3 fw-bold xs-text btn-confirm-action shadow-sm"
                                                    data-confirm-title="Selesaikan Pekerjaan?"
                                                    data-confirm-text="Apakah Anda telah menyelesaikan pekerjaan perbaikan untuk '<?= e($jTitle) ?>'? Status order akan diubah menjadi Selesai dan pelanggan akan diberi notifikasi."
                                                    data-confirm-icon="question"
                                                    data-confirm-btn="Ya, Tandai Selesai">
                                                <i class="bi bi-check2-circle me-1"></i> Tandai Selesai
                                            </button>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-4 text-muted small bg-light rounded-3">
                    <i class="bi bi-briefcase text-secondary fs-3 d-block mb-1"></i>
                    Belum ada order pekerjaan aktif yang sedang Anda kerjakan saat ini.
                    <div class="text-muted xs-text mt-1">Tawaran bantuan Anda yang dikonfirmasi oleh pelanggan akan otomatis tampil di sini.</div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Postingan Permintaan Jasa (Tab: Sesuai Keahlian & Semua Terbaru) -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                <div>
                    <h5 class="fw-bold mb-1"><i class="bi bi-inbox-fill text-primary me-2"></i>Permintaan Jasa dari Pelanggan</h5>
                    <p class="text-muted xs-text mb-0">Lihat permintaan perbaikan terbaru dan hubungi customer langsung via WhatsApp.</p>
                </div>
                <!-- Nav Tabs -->
                <ul class="nav nav-pills small gap-2" id="postTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill px-3 py-1 fw-semibold" id="category-posts-tab" data-bs-toggle="tab" data-bs-target="#category-posts" type="button" role="tab">
                            <i class="bi bi-funnel-fill me-1"></i> Sesuai Keahlian (<?= e($profile->category_name ?? 'AC') ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill px-3 py-1 fw-semibold" id="all-posts-tab" data-bs-toggle="tab" data-bs-target="#all-posts" type="button" role="tab">
                            <i class="bi bi-grid-fill me-1"></i> Semua Kategori Terbaru
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content" id="postTabContent">
                <!-- Tab 1: Sesuai Kategori Keahlian -->
                <div class="tab-pane fade show active" id="category-posts" role="tabpanel">
                    <?php if (!empty($categoryPosts)): ?>
                        <div class="row g-3">
                            <?php foreach ($categoryPosts as $p): ?>
                                <?php
                                    $pId = is_object($p) ? $p->id : $p['id'];
                                    $pTitle = is_object($p) ? $p->title : $p['title'];
                                    $pCatName = is_object($p) ? ($p->category_name ?? 'Umum') : ($p['category_name'] ?? 'Umum');
                                    $pDesc = is_object($p) ? $p->description : $p['description'];
                                    $pCustName = is_object($p) ? ($p->customer_name ?? 'Customer') : ($p['customer_name'] ?? 'Customer');
                                    $pMin = is_object($p) ? ($p->estimated_min_price ?? 0) : ($p['estimated_min_price'] ?? 0);
                                    $pMax = is_object($p) ? ($p->estimated_max_price ?? 0) : ($p['estimated_max_price'] ?? 0);
                                ?>
                                <div class="col-md-6">
                                    <div class="p-3 rounded-3 border border-light-subtle bg-light h-100 d-flex flex-column justify-content-between">
                                        <div>
                                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2 py-1 xs-text">
                                                    <?= e($pCatName) ?>
                                                </span>
                                                <span class="badge bg-warning text-dark rounded-pill xs-text">
                                                    Mencari Teknisi
                                                </span>
                                            </div>
                                            <h6 class="fw-bold mb-1 text-dark">
                                                <a href="<?= base_url('posts/detail/' . $pId) ?>" class="text-decoration-none text-dark">
                                                    <?= e($pTitle) ?>
                                                </a>
                                            </h6>
                                            <p class="text-muted xs-text mb-2"><?= e(mb_strimwidth($pDesc, 0, 110, '...')) ?></p>
                                        </div>
                                        <div>
                                            <div class="bg-white p-2 rounded-2 border mb-2 d-flex justify-content-between align-items-center xs-text">
                                                <span class="text-muted"><i class="bi bi-tag me-1"></i>Estimasi AI:</span>
                                                <strong class="text-success">Rp <?= number_format($pMin, 0, ',', '.') ?> - <?= number_format($pMax, 0, ',', '.') ?></strong>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <a href="<?= base_url('posts/detail/' . $pId) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 xs-text">
                                                    Detail
                                                </a>
                                                <form action="<?= base_url('posts/offer/' . $pId) ?>" method="POST" class="d-inline">
                                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                    <button type="button" 
                                                            class="btn btn-success btn-sm rounded-pill px-3 xs-text fw-semibold btn-confirm-action"
                                                            data-confirm-title="Tawarkan Bantuan?"
                                                            data-confirm-text="Ajukan penawaran bantuan untuk keluhan '<?= e($pTitle) ?>'? Pelanggan akan menerima notifikasi bahwa Anda siap membantu."
                                                            data-confirm-icon="question"
                                                            data-confirm-btn="Ya, Saya Bisa Membantu">
                                                        <i class="bi bi-hand-thumbs-up me-1"></i> Saya Bisa Membantu
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted small">
                            Tidak ada keluhan baru saat ini untuk kategori keahlian Anda.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Tab 2: Semua Postingan Terbaru -->
                <div class="tab-pane fade" id="all-posts" role="tabpanel">
                    <?php if (!empty($latestPosts)): ?>
                        <div class="row g-3">
                            <?php foreach ($latestPosts as $p): ?>
                                <?php
                                    $pId = is_object($p) ? $p->id : $p['id'];
                                    $pTitle = is_object($p) ? $p->title : $p['title'];
                                    $pCatName = is_object($p) ? ($p->category_name ?? 'Umum') : ($p['category_name'] ?? 'Umum');
                                    $pDesc = is_object($p) ? $p->description : $p['description'];
                                    $pCustName = is_object($p) ? ($p->customer_name ?? 'Customer') : ($p['customer_name'] ?? 'Customer');
                                    $pMin = is_object($p) ? ($p->estimated_min_price ?? 0) : ($p['estimated_min_price'] ?? 0);
                                    $pMax = is_object($p) ? ($p->estimated_max_price ?? 0) : ($p['estimated_max_price'] ?? 0);
                                ?>
                                <div class="col-md-6">
                                    <div class="p-3 rounded-3 border border-light-subtle bg-white h-100 d-flex flex-column justify-content-between">
                                        <div>
                                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                                <span class="badge bg-secondary bg-opacity-25 text-dark rounded-pill px-2 py-1 xs-text">
                                                    <?= e($pCatName) ?>
                                                </span>
                                                <span class="badge bg-info bg-opacity-10 text-info-emphasis rounded-pill xs-text">
                                                    <?= e($pCustName) ?>
                                                </span>
                                            </div>
                                            <h6 class="fw-bold mb-1 text-dark">
                                                <a href="<?= base_url('posts/detail/' . $pId) ?>" class="text-decoration-none text-dark">
                                                    <?= e($pTitle) ?>
                                                </a>
                                            </h6>
                                            <p class="text-muted xs-text mb-2"><?= e(mb_strimwidth($pDesc, 0, 110, '...')) ?></p>
                                        </div>
                                        <div>
                                            <div class="bg-light p-2 rounded-2 border mb-2 d-flex justify-content-between align-items-center xs-text">
                                                <span class="text-muted"><i class="bi bi-tag me-1"></i>Estimasi:</span>
                                                <strong class="text-success">Rp <?= number_format($pMin, 0, ',', '.') ?> - <?= number_format($pMax, 0, ',', '.') ?></strong>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <a href="<?= base_url('posts/detail/' . $pId) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 xs-text">
                                                    Detail
                                                </a>
                                                <form action="<?= base_url('posts/offer/' . $pId) ?>" method="POST" class="d-inline">
                                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                    <button type="button" 
                                                            class="btn btn-success btn-sm rounded-pill px-3 xs-text fw-semibold btn-confirm-action"
                                                            data-confirm-title="Tawarkan Bantuan?"
                                                            data-confirm-text="Ajukan penawaran bantuan untuk keluhan '<?= e($pTitle) ?>'? Pelanggan akan menerima notifikasi bahwa Anda siap membantu."
                                                            data-confirm-icon="question"
                                                            data-confirm-btn="Ya, Saya Bisa Membantu">
                                                        <i class="bi bi-hand-thumbs-up me-1"></i> Saya Bisa Membantu
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted small">
                            Belum ada keluhan aktif.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Respons Saya (History Respons Pekerjaan) -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-clock-history text-success me-2"></i>Tawaran Bantuan Saya</h5>
                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 small"><?= count($myResponses) ?> Tawaran Dikirim</span>
            </div>

            <?php if (!empty($myResponses)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle small mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Judul Keluhan</th>
                                <th>Nama Customer</th>
                                <th>Kategori</th>
                                <th>Status Tawaran</th>
                                <th>Status Pekerjaan</th>
                                <th>Aksi Koordinasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($myResponses as $resp): ?>
                                <?php
                                    $rPostId = is_object($resp) ? $resp->post_id : $resp['post_id'];
                                    $rTitle = is_object($resp) ? $resp->post_title : $resp['post_title'];
                                    $rCustName = is_object($resp) ? $resp->customer_name : $resp['customer_name'];
                                    $rCustWa = is_object($resp) ? $resp->customer_whatsapp : $resp['customer_whatsapp'];
                                    $rCatName = is_object($resp) ? ($resp->category_name ?? 'Umum') : ($resp['category_name'] ?? 'Umum');
                                    $rStatus = is_object($resp) ? $resp->status : $resp['status'];
                                    $pStatus = is_object($resp) ? ($resp->post_status ?? 'Ada Teknisi') : ($resp['post_status'] ?? 'Ada Teknisi');

                                    $rBadgeClass = 'bg-secondary';
                                    $rLabel = 'Menawarkan';
                                    if ($rStatus === 'offered') {
                                        $rBadgeClass = 'bg-warning text-dark';
                                        $rLabel = 'Menunggu Dipilih Customer';
                                    } elseif ($rStatus === 'selected') {
                                        $rBadgeClass = 'bg-success text-white';
                                        $rLabel = 'Dipilih Customer';
                                    } elseif ($rStatus === 'rejected') {
                                        $rBadgeClass = 'bg-danger text-white';
                                        $rLabel = 'Customer Memilih Teknisi Lain';
                                    }

                                    // Status Pekerjaan Badges
                                    $postBadgeClass = 'bg-secondary';
                                    $postStatusLabel = e($pStatus);
                                    $postIcon = 'bi-info-circle';
                                    if ($pStatus === 'Selesai') {
                                        $postBadgeClass = 'bg-success';
                                        $postStatusLabel = 'Selesai';
                                        $postIcon = 'bi-check-circle-fill';
                                    } elseif ($pStatus === 'Ada Teknisi') {
                                        $postBadgeClass = 'bg-primary';
                                        $postStatusLabel = 'Sedang Dikerjakan';
                                        $postIcon = 'bi-gear-wide-connected';
                                    } elseif ($pStatus === 'Mencari Teknisi') {
                                        $postBadgeClass = 'bg-warning text-dark';
                                        $postStatusLabel = 'Mencari Teknisi';
                                        $postIcon = 'bi-hourglass-split';
                                    } elseif ($pStatus === 'Dibatalkan') {
                                        $postBadgeClass = 'bg-secondary';
                                        $postStatusLabel = 'Dibatalkan';
                                        $postIcon = 'bi-slash-circle';
                                    }
                                ?>
                                <tr>
                                    <td class="fw-bold">
                                        <a href="<?= base_url('posts/detail/' . $rPostId) ?>" class="text-decoration-none text-dark">
                                            <?= e($rTitle) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <div><?= e($rCustName) ?></div>
                                        <div class="text-muted xs-text">WA: <?= e($rCustWa) ?></div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border"><?= e($rCatName) ?></span></td>
                                    <td><span class="badge <?= $rBadgeClass ?> rounded-pill px-2 py-1"><?= $rLabel ?></span></td>
                                    <td>
                                        <span class="badge <?= $postBadgeClass ?> rounded-pill px-2 py-1">
                                            <i class="bi <?= $postIcon ?> me-1"></i><?= $postStatusLabel ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1 align-items-center">
                                            <?php if ($rStatus === 'selected'): ?>
                                                <!-- Chat Internal -->
                                                <a href="<?= base_url('chat/open/' . $rPostId) ?>" 
                                                   class="btn btn-warning text-dark btn-sm rounded-pill px-2 py-1 fw-semibold xs-text d-inline-flex align-items-center gap-1 shadow-sm"
                                                   title="Buka Chat Internal dengan Customer">
                                                    <i class="bi bi-chat-dots-fill"></i> Chat
                                                </a>

                                                <!-- WhatsApp -->
                                                <?php
                                                    $respWaMsg = "Halo Pak/Ibu {$rCustName}, saya Teknisi {$user->name} yang Anda pilih di BANTUIN untuk perbaikan {$rTitle}. Kapan waktu yang tepat untuk kunjungan/pemeriksaan?";
                                                    $respWaUrl = wa_url($rCustWa, $respWaMsg);
                                                ?>
                                                <a href="<?= $respWaUrl ?>" 
                                                   target="_blank" 
                                                   rel="noopener noreferrer" 
                                                   class="btn btn-outline-success btn-sm rounded-pill px-2 py-1 fw-semibold xs-text d-inline-flex align-items-center gap-1"
                                                   title="Hubungi via WhatsApp">
                                                    <i class="bi bi-whatsapp"></i> WA
                                                </a>

                                                <!-- Detail -->
                                                <a href="<?= base_url('posts/detail/' . $rPostId) ?>" 
                                                   class="btn btn-outline-secondary btn-sm rounded-pill px-2 py-1 xs-text" 
                                                   title="Lihat Detail Keluhan">
                                                    <i class="bi bi-eye"></i> Detail
                                                </a>

                                                <!-- Tandai Selesai jika belum Selesai -->
                                                <?php if ($pStatus !== 'Selesai'): ?>
                                                    <form action="<?= base_url('posts/status/' . $rPostId) ?>" method="POST" class="d-inline">
                                                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                        <input type="hidden" name="status" value="Selesai">
                                                        <button type="button" 
                                                                class="btn btn-success btn-sm rounded-pill px-2 py-1 fw-bold xs-text btn-confirm-action shadow-sm"
                                                                data-confirm-title="Selesaikan Pekerjaan?"
                                                                data-confirm-text="Apakah Anda telah menyelesaikan perbaikan untuk '<?= e($rTitle) ?>'? Status order akan diubah menjadi Selesai."
                                                                data-confirm-icon="question"
                                                                data-confirm-btn="Ya, Tandai Selesai">
                                                            <i class="bi bi-check2-circle"></i> Selesai
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <a href="<?= base_url('posts/detail/' . $rPostId) ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1 xs-text">
                                                    Lihat Postingan
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-4 text-muted small">
                    Belum ada riwayat tawaran bantuan. Klik "Saya Bisa Membantu" pada keluhan di atas untuk menawarkan bantuan.
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>
