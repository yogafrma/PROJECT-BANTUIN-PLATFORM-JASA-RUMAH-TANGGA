<?php
/**
 * Admin Dashboard View (Modern BANTUIN Theme)
 * Project: BANTUIN — Platform Layanan Jasa Rumah Tangga
 */
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
             1. Unified Admin Command Center Hero Banner
             (Deep Navy & Azure Gradient, Glowing Ambient Accents)
             ================================================================ -->
        <div class="customer-hero-card mb-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-4 position-relative" style="z-index: 2;">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="customer-hero-pill">
                            <span class="fluent-pulse-dot" style="background: #6366F1; box-shadow: 0 0 8px #6366F1;"></span>
                            <span>Pusat Kendali Administrator BANTUIN</span>
                        </span>
                    </div>

                    <h1 class="customer-hero-title mb-2">
                        Selamat Datang, <?= e($user->name) ?>!
                    </h1>
                    
                    <p class="customer-hero-subtitle mb-3">
                        Pantau metrik operasional, transaksi, mitra teknisi, dan konfigurasi platform secara real-time.
                    </p>

                    <div class="text-white-50 small d-flex align-items-center gap-3 flex-wrap" style="font-size: 0.84rem;">
                        <span><i class="bi bi-envelope me-1 text-info"></i> <?= e($user->email) ?></span>
                        <?php if (!empty($user->whatsapp)): ?>
                            <span><i class="bi bi-whatsapp me-1 text-success"></i> <?= e($user->whatsapp) ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Admin Quick Action Buttons -->
                <div class="d-flex gap-2 flex-wrap flex-shrink-0">
                    <a href="<?= base_url('admin/settings') ?>" class="btn-hero-primary">
                        <i class="bi bi-sliders2-vertical"></i>
                        <span>Konfigurasi Sistem</span>
                    </a>
                    <a href="<?= base_url('categories') ?>" class="btn-hero-secondary">
                        <i class="bi bi-tags-fill"></i>
                        <span>Kelola Kategori</span>
                    </a>
                    <a href="<?= base_url('profile') ?>" class="btn-hero-secondary">
                        <i class="bi bi-person-gear"></i>
                        <span>Edit Profil</span>
                    </a>
                    <a href="<?= base_url('logout') ?>" class="btn-hero-secondary text-danger-emphasis">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- ================================================================
             2. 4 Utama Statistik Platform (Stat Counters - Selaras & Harmonis)
             ================================================================ -->
        <div class="row g-3 mb-4">
            
            <!-- 1. Total Customer -->
            <div class="col-6 col-lg-3">
                <div class="customer-stat-card">
                    <div class="stat-icon-wrap" style="background: rgba(0, 120, 212, 0.1); color: #0078D4;">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <div class="stat-value"><?= (int)$stats['total_customers'] ?></div>
                        <div class="stat-label">Total Pelanggan</div>
                    </div>
                </div>
            </div>

            <!-- 2. Total Teknisi -->
            <div class="col-6 col-lg-3">
                <div class="customer-stat-card">
                    <div class="stat-icon-wrap" style="background: rgba(13, 148, 136, 0.1); color: #0D9488;">
                        <i class="bi bi-tools"></i>
                    </div>
                    <div>
                        <div class="stat-value"><?= (int)$stats['total_technicians'] ?></div>
                        <div class="stat-label">Mitra Teknisi</div>
                    </div>
                </div>
            </div>

            <!-- 3. Total Postingan Permintaan -->
            <div class="col-6 col-lg-3">
                <div class="customer-stat-card">
                    <div class="stat-icon-wrap" style="background: rgba(245, 158, 11, 0.1); color: #D97706;">
                        <i class="bi bi-card-checklist"></i>
                    </div>
                    <div>
                        <div class="stat-value"><?= (int)$stats['total_posts'] ?></div>
                        <div class="stat-label">Permintaan Jasa</div>
                    </div>
                </div>
            </div>

            <!-- 4. Total Kategori Layanan -->
            <div class="col-6 col-lg-3">
                <div class="customer-stat-card">
                    <div class="stat-icon-wrap" style="background: rgba(99, 102, 241, 0.1); color: #6366F1;">
                        <i class="bi bi-grid"></i>
                    </div>
                    <div>
                        <div class="stat-value"><?= (int)$stats['total_categories'] ?></div>
                        <div class="stat-label">
                            <a href="<?= base_url('categories') ?>" class="text-primary text-decoration-none fw-semibold">
                                Kelola Kategori &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- ================================================================
             3. Monitoring Section: Permintaan Jasa & Kategori Layanan
             ================================================================ -->
        <div class="row g-4">
            
            <!-- Monitoring Postingan Terbaru (8 Cols) -->
            <div class="col-lg-8">
                <div class="customer-content-card">
                    
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-light-subtle flex-wrap gap-2">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                                <i class="bi bi-activity text-primary"></i>
                                <span>Postingan Permintaan Jasa Terbaru</span>
                            </h5>
                            <p class="text-muted xs-text mb-0">Pantau transaksi dan permintaan perbaikan terkini.</p>
                        </div>
                        <span class="badge rounded-pill px-3 py-1.5 xs-text fw-semibold d-inline-flex align-items-center gap-1.5" style="background: rgba(16, 185, 129, 0.12); color: #059669; border: 1px solid rgba(16, 185, 129, 0.28);">
                            <span class="fluent-pulse-dot" style="background: #10B981; box-shadow: 0 0 6px #10B981;"></span>
                            <span>Live Monitoring</span>
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle small mb-0">
                            <thead>
                                <tr class="text-muted" style="border-bottom: 2px solid #E2E8F0; font-size: 0.82rem;">
                                    <th class="ps-2 py-2.5">ID</th>
                                    <th class="py-2.5">Pelanggan</th>
                                    <th class="py-2.5">Kategori</th>
                                    <th class="py-2.5">Judul Permintaan</th>
                                    <th class="py-2.5 text-center">Status</th>
                                    <th class="pe-2 py-2.5 text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recentPosts)): ?>
                                    <?php foreach ($recentPosts as $p): ?>
                                        <?php 
                                            $st = $p->status ?? 'pending';
                                            $statusBadgeClass = 'badge-status-mencari';
                                            $statusLabel = 'Mencari Teknisi';

                                            if ($st === 'in_progress' || $st === 'Ada Teknisi') {
                                                $statusBadgeClass = 'badge-status-proses';
                                                $statusLabel = 'Diproses';
                                            } elseif ($st === 'completed' || $st === 'Selesai') {
                                                $statusBadgeClass = 'badge-status-selesai';
                                                $statusLabel = 'Selesai';
                                            } elseif ($st === 'Dibatalkan' || $st === 'cancelled') {
                                                $statusBadgeClass = 'badge-status-batal';
                                                $statusLabel = 'Dibatalkan';
                                            }
                                        ?>
                                        <tr style="border-bottom: 1px solid #F1F5F9;">
                                            <td class="fw-bold ps-2 text-secondary">#<?= $p->id ?></td>
                                            <td>
                                                <div class="fw-semibold text-dark"><?= e($p->customer_name) ?></div>
                                                <?php if (!empty($p->customer_whatsapp)): ?>
                                                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', (string)$p->customer_whatsapp) ?>" target="_blank" class="text-muted xs-text text-decoration-none">
                                                        <i class="bi bi-whatsapp text-success me-1"></i><?= e($p->customer_whatsapp) ?>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge rounded-pill px-2.5 py-1 xs-text" style="background: rgba(0, 120, 212, 0.08); color: #0078D4; border: 1px solid rgba(0, 120, 212, 0.2);">
                                                    <?= e($p->category_name) ?>
                                                </span>
                                            </td>
                                            <td class="fw-medium text-dark" style="max-width: 250px;">
                                                <div class="text-truncate" title="<?= e($p->title) ?>">
                                                    <?= e($p->title) ?>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge <?= $statusBadgeClass ?> rounded-pill px-2.5 py-1 xs-text">
                                                    <?= $statusLabel ?>
                                                </span>
                                            </td>
                                            <td class="pe-2 text-end">
                                                <a href="<?= base_url('posts/detail/' . $p->id) ?>" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 xs-text fw-semibold" title="Buka Detail">
                                                    <i class="bi bi-arrow-right"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary opacity-50"></i>
                                            Belum ada postingan permintaan jasa customer saat ini.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

            <!-- Kategori Jasa Overview (4 Cols) -->
            <div class="col-lg-4">
                <div class="customer-content-card">
                    
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-light-subtle">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                                <i class="bi bi-tags-fill text-primary"></i>
                                <span>Kategori Layanan</span>
                            </h5>
                            <p class="text-muted xs-text mb-0">Klasifikasi bidang perbaikan aktif.</p>
                        </div>
                        <a href="<?= base_url('categories') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 xs-text fw-semibold">
                            Kelola
                        </a>
                    </div>

                    <div class="d-flex flex-column gap-2">
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $cat): ?>
                                <div class="d-flex justify-content-between align-items-center p-2.5 px-3 rounded-3 border border-light-subtle bg-light bg-opacity-50 transition" style="margin-bottom: 3px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(0, 120, 212, 0.1); color: #0078D4;">
                                            <i class="bi <?= e($cat->icon ?? 'bi-tools') ?>"></i>
                                        </div>
                                        <span class="fw-bold text-dark small"><?= e($cat->name) ?></span>
                                    </div>
                                    <?php if (($cat->status ?? 'active') === 'active'): ?>
                                        <span class="badge rounded-pill xs-text" style="background: rgba(16, 124, 65, 0.12); color: #107C41; border: 1px solid rgba(16, 124, 65, 0.25);">
                                            Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill xs-text" style="background: rgba(100, 116, 139, 0.12); color: #64748B; border: 1px solid rgba(100, 116, 139, 0.25);">
                                            Nonaktif
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

        </div>

    </div>
</div>
