<?php
/**
 * Customer Dashboard View (Modern BANTUIN Fluent Theme)
 * Project: BANTUIN — Platform Layanan Jasa Rumah Tangga
 */

$activePostsCount = 0;
$totalResponsesCount = 0;
$completedCount = 0;

if (!empty($myPosts)) {
    foreach ($myPosts as $p) {
        $st = is_object($p) ? $p->status : $p['status'];
        $offers = is_object($p) ? ($p->offers_count ?? 0) : ($p['offers_count'] ?? 0);
        $totalResponsesCount += (int)$offers;
        if ($st === 'Selesai' || $st === 'completed') {
            $completedCount++;
        } elseif ($st !== 'Dibatalkan') {
            $activePostsCount++;
        }
    }
}
?>

<div class="dashboard-wrapper">
    <div class="container">
        
        <!-- Flash Messages -->
        <?php if (has_flash()): ?>
            <?php $flash = get_flash(); ?>
            <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show rounded-4 border-0 shadow-sm small py-3 px-4 d-flex align-items-center gap-2 mb-4 bg-white" role="alert">
                <i class="bi bi-info-circle-fill fs-5 text-primary"></i>
                <div class="fw-medium text-dark"><?= e($flash['message']) ?></div>
                <button type="button" class="btn-close ms-auto shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- ================================================================
             1. Unified Customer Command Center Hero Banner
             (Harmonized with Landing Page: Deep Navy, Azure, Glassmorphism AI)
             ================================================================ -->
        <div class="customer-hero-card">
            <div class="row align-items-center g-4 position-relative" style="z-index: 2;">
                
                <!-- Left Column: User Profile & Quick Actions -->
                <div class="col-lg-7">
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="customer-hero-pill">
                            <span class="fluent-pulse-dot" style="background: #10B981; box-shadow: 0 0 8px #10B981;"></span>
                            <span>Panel Konsumen</span>
                        </span>
                    </div>

                    <h1 class="customer-hero-title mb-2">
                        Selamat Datang, <?= e($user->name) ?>!
                    </h1>
                    
                    <p class="customer-hero-subtitle mb-3">
                        Kelola kebutuhan perbaikan rumah &amp; pantau tawaran teknisi bergaransi.
                    </p>

                    <div class="text-white-50 small mb-0 d-flex align-items-center gap-3 flex-wrap" style="font-size: 0.84rem;">
                        <span><i class="bi bi-envelope me-1 text-info"></i> <?= e($user->email) ?></span>
                        <?php if (!empty($user->whatsapp)): ?>
                            <span><i class="bi bi-whatsapp me-1 text-success"></i> <?= e($user->whatsapp) ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right Column: Glassmorphism AI Assistant Shortcut -->
                <div class="col-lg-5">
                    <div class="customer-ai-glass-card">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="ai-glass-tag">
                                <i class="bi bi-stars text-warning me-1"></i> Diagnosis AI 24 Jam
                            </span>
                            <span class="text-white-50 xs-text">Cepat &bull; Gratis</span>
                        </div>
                        
                        <h4 class="fw-bold text-white mb-2" style="font-size: 1.25rem; letter-spacing: -0.01em;">
                            Konsultasi Kerusakan AI
                        </h4>
                        
                        <p class="text-white-50 small mb-3" style="line-height: 1.45;">
                            Deteksi kendala &amp; estimasi biaya perbaikan rumah secara instan.
                        </p>

                        <a href="<?= base_url('customer/consultation') ?>" class="btn-ai-glass-cta w-100">
                            <i class="bi bi-robot fs-5"></i>
                            <span>Konsultasi Sekarang</span>
                            <i class="bi bi-arrow-right ms-auto"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <!-- ================================================================
             2. Quick Activity Metrics Row (Stat Counters)
             ================================================================ -->
        <div class="row g-3 mb-4">
            
            <!-- Metric 1: Permintaan Aktif -->
            <div class="col-6 col-md-3">
                <div class="customer-stat-card">
                    <div class="stat-icon-wrap" style="background: rgba(0, 120, 212, 0.12); color: #0078D4;">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <div class="stat-value"><?= (int)$activePostsCount ?></div>
                        <div class="stat-label">Permintaan Aktif</div>
                    </div>
                </div>
            </div>

            <!-- Metric 2: Respon Masuk -->
            <div class="col-6 col-md-3">
                <div class="customer-stat-card">
                    <div class="stat-icon-wrap" style="background: rgba(56, 189, 248, 0.14); color: #0284C7;">
                        <i class="bi bi-chat-left-dots-fill"></i>
                    </div>
                    <div>
                        <div class="stat-value"><?= (int)$totalResponsesCount ?></div>
                        <div class="stat-label">Respon Teknisi</div>
                    </div>
                </div>
            </div>

            <!-- Metric 3: Servis Selesai -->
            <div class="col-6 col-md-3">
                <div class="customer-stat-card">
                    <div class="stat-icon-wrap" style="background: rgba(16, 124, 65, 0.12); color: #107C41;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div>
                        <div class="stat-value"><?= (int)$completedCount ?></div>
                        <div class="stat-label">Servis Selesai</div>
                    </div>
                </div>
            </div>

            <!-- Metric 4: Garansi 30 Hari -->
            <div class="col-6 col-md-3">
                <div class="customer-stat-card">
                    <div class="stat-icon-wrap" style="background: rgba(245, 158, 11, 0.14); color: #D97706;">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div>
                        <div class="stat-value text-dark" style="font-size: 1.25rem; padding-top: 0.15rem;">30 Hari</div>
                        <div class="stat-label">Garansi Layanan</div>
                    </div>
                </div>
            </div>

        </div>

        <!-- ================================================================
             3. Main Content: Postingan Keluhan Saya & Rekomendasi Teknisi
             ================================================================ -->
        <div class="row g-4 mb-4">
            
            <!-- Left Column: Postingan Keluhan Saya (8 Cols) -->
            <div class="col-lg-8">
                <div class="customer-content-card">
                    
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-light-subtle flex-wrap gap-2">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                                <i class="bi bi-clipboard2-check text-primary"></i>
                                <span>Postingan Keluhan Saya</span>
                            </h5>
                            <p class="text-muted xs-text mb-0">Status pengerjaan perbaikan aktif.</p>
                        </div>
                        <a href="<?= base_url('posts/my') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold">
                            <span>Lihat Semua</span>
                            <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>

                    <?php if (!empty($myPosts)): ?>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($myPosts as $post): ?>
                                <?php
                                    $pId = is_object($post) ? $post->id : $post['id'];
                                    $pTitle = is_object($post) ? $post->title : $post['title'];
                                    $pCatName = is_object($post) ? ($post->category_name ?? 'Umum') : ($post['category_name'] ?? 'Umum');
                                    $pCatIcon = is_object($post) ? ($post->category_icon ?? 'bi-tools') : ($post['category_icon'] ?? 'bi-tools');
                                    $pDesc = is_object($post) ? $post->description : $post['description'];
                                    $pStatus = is_object($post) ? $post->status : $post['status'];
                                    $pCreatedAt = is_object($post) ? $post->created_at : $post['created_at'];
                                    $pOffers = is_object($post) ? ($post->offers_count ?? 0) : ($post['offers_count'] ?? 0);

                                    $statusBadgeClass = 'badge-status-mencari';
                                    if ($pStatus === 'Ada Teknisi' || $pStatus === 'in_progress') {
                                        $statusBadgeClass = 'badge-status-proses';
                                    } elseif ($pStatus === 'Selesai' || $pStatus === 'completed') {
                                        $statusBadgeClass = 'badge-status-selesai';
                                    } elseif ($pStatus === 'Dibatalkan') {
                                        $statusBadgeClass = 'badge-status-batal';
                                    }
                                ?>
                                <div class="customer-post-item">
                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                        <div>
                                            <span class="badge rounded-pill px-2.5 py-1 xs-text mb-1" style="background: rgba(0, 120, 212, 0.08); color: #0078D4; border: 1px solid rgba(0, 120, 212, 0.2);">
                                                <i class="bi <?= e($pCatIcon) ?> me-1"></i> <?= e($pCatName) ?>
                                            </span>
                                            <h6 class="fw-bold mb-1 text-dark">
                                                <a href="<?= base_url('posts/detail/' . $pId) ?>" class="text-decoration-none text-dark hover-primary">
                                                    <?= e($pTitle) ?>
                                                </a>
                                            </h6>
                                        </div>
                                        
                                        <!-- Modern Pill Status -->
                                        <span class="badge <?= $statusBadgeClass ?> rounded-pill px-3 py-1 small">
                                            <?= e($pStatus) ?>
                                        </span>
                                    </div>

                                    <p class="text-muted xs-text mb-2 line-clamp-2" style="line-height: 1.5;">
                                        <?= e(mb_strimwidth($pDesc, 0, 140, '...')) ?>
                                    </p>

                                    <div class="d-flex flex-wrap justify-content-between align-items-center pt-2 border-top border-light-subtle xs-text text-muted">
                                        <div class="d-flex align-items-center gap-1">
                                            <i class="bi bi-people-fill text-primary"></i>
                                            <span class="fw-semibold text-dark"><?= (int)$pOffers ?></span>
                                            <span>Teknisi Merespons</span>
                                        </div>
                                        <div>
                                            <a href="<?= base_url('posts/detail/' . $pId) ?>" class="fw-bold text-primary text-decoration-none d-flex align-items-center gap-1">
                                                <span>Detail & Tawaran</span>
                                                <i class="bi bi-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <!-- Clean Empty State -->
                        <div class="text-center py-5">
                            <div class="avatar-circle mx-auto mb-3" style="width: 64px; height: 64px; background: rgba(0, 120, 212, 0.1); color: #0078D4; font-size: 1.75rem;">
                                <i class="bi bi-clipboard2-plus"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Belum Ada Permintaan Perbaikan</h6>
                            <p class="text-muted small mb-3 max-w-700 mx-auto">
                                Butuh teknisi perbaikan listrik, AC, kunci, atau barang elektronik? Buat permintaan baru sekarang atau konsultasikan dengan AI.
                            </p>
                            <div class="d-flex gap-2 justify-content-center flex-wrap">
                                <a href="<?= base_url('posts/create') ?>" class="btn-hero-primary">
                                    <i class="bi bi-plus-circle me-1"></i> Buat Permintaan Baru
                                </a>
                                <a href="<?= base_url('customer/consultation') ?>" class="btn btn-outline-primary rounded-pill px-3 py-2 small fw-semibold">
                                    <i class="bi bi-stars me-1"></i> Konsultasi AI
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

            <!-- Right Column: Rekomendasi Teknisi (4 Cols - Real Data Only) -->
            <div class="col-lg-4">
                <div class="customer-content-card">
                    
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-light-subtle">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                                <i class="bi bi-patch-check-fill text-primary"></i>
                                <span>Rekomendasi Teknisi</span>
                            </h5>
                            <p class="text-muted xs-text mb-0">Mitra teknisi siap panggilan.</p>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-3">
                        <?php if (!empty($recommendedTechs)): ?>
                            <?php foreach ($recommendedTechs as $tech): ?>
                                <div class="customer-tech-item">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <div class="d-flex align-items-center gap-1">
                                                <h6 class="fw-bold mb-0 text-dark"><?= e($tech->name ?? 'Mitra Teknisi') ?></h6>
                                                <i class="bi bi-patch-check-fill text-primary" style="font-size: 0.85rem;" title="Mitra Terverifikasi"></i>
                                            </div>
                                            <span class="badge rounded-pill xs-text mt-1" style="background: rgba(0, 120, 212, 0.08); color: #0078D4; border: 1px solid rgba(0, 120, 212, 0.2);">
                                                <i class="bi <?= e($tech->category_icon ?? 'bi-tools') ?> me-1"></i> <?= e($tech->category_name ?? 'Teknisi') ?>
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center gap-1 small fw-bold px-2 py-0.5 rounded-pill" style="background: rgba(245, 158, 11, 0.12); color: #B45309;">
                                            <i class="bi bi-star-fill text-warning"></i>
                                            <span><?= number_format($tech->rating ?? 5.0, 1) ?></span>
                                        </div>
                                    </div>
                                    
                                    <p class="text-muted xs-text mb-2 line-clamp-2" style="line-height: 1.5;">
                                        <?= e($tech->description ?? 'Teknisi terpercaya siap melayani kebutuhan perbaikan rumah tangga.') ?>
                                    </p>

                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-light-subtle">
                                        <span class="text-muted xs-text">
                                            <i class="bi bi-geo-alt me-1 text-danger"></i> <?= e($tech->location ?? 'Jabodetabek') ?>
                                        </span>
                                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', (string)($tech->whatsapp ?? '6281234567890')) ?>?text=Halo%20Pak%20<?= urlencode((string)($tech->name ?? 'Teknisi')) ?>,%20saya%20tertarik%20menggunakan%20jasa%20Anda%20dari%20BANTUIN." 
                                           target="_blank" 
                                           class="btn-chat-wa">
                                            <i class="bi bi-whatsapp"></i>
                                            <span>Chat WA</span>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4 bg-light rounded-4 border border-light-subtle">
                                <i class="bi bi-people fs-2 text-muted mb-2 d-block"></i>
                                <h6 class="fw-semibold text-dark mb-1 small">Belum Ada Teknisi Terdaftar</h6>
                                <p class="text-muted xs-text mb-0">Teknisi aktif akan otomatis tampil di sini.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

        </div>

    </div>
</div>
