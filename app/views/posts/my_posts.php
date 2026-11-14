<?php
/**
 * Customer Posts View (Modern BANTUIN Theme)
 * Project: BANTUIN — Platform Layanan Jasa Rumah Tangga
 */
?>

<div class="dashboard-wrapper py-4">
    <div class="container">
        
        <!-- Header Banner (Command Center Theme) -->
        <div class="settings-hero-card mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 position-relative z-1">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="settings-hero-pill">
                            <i class="bi bi-file-earmark-text"></i> Postingan Konsumen
                        </span>
                        <span class="text-white-50 small">Kelola Permintaan Jasa</span>
                    </div>
                    <h2 class="fw-bold mb-1 text-white">Postingan Keluhan Saya</h2>
                    <p class="text-white-50 mb-0 small">
                        Kelola keluhan perbaikan rumah &amp; pantau penawaran bantuan dari mitra teknisi.
                    </p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="<?= base_url('posts/create') ?>" class="btn btn-primary rounded-pill btn-sm px-3 fw-bold d-inline-flex align-items-center gap-1 shadow-sm">
                        <i class="bi bi-plus-lg"></i> Buat Postingan Baru
                    </a>
                    <a href="<?= base_url('customer/consultation') ?>" class="btn btn-outline-light rounded-pill btn-sm px-3 fw-semibold d-inline-flex align-items-center gap-1">
                        <i class="bi bi-stars"></i> Konsultasi AI
                    </a>
                    <a href="<?= base_url('customer') ?>" class="btn btn-outline-light rounded-pill btn-sm px-3 fw-semibold d-inline-flex align-items-center gap-1">
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

        <?php if (empty($posts)): ?>
            <!-- Clean Empty State -->
            <div class="card border-0 shadow-sm rounded-4 text-center py-5 bg-white">
                <div class="card-body p-4">
                    <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px; background: rgba(0, 120, 212, 0.1); color: #0078D4; font-size: 1.75rem;">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Belum Ada Postingan Keluhan</h5>
                    <p class="text-muted small mx-auto mb-4" style="max-width: 480px;">
                        Anda belum memiliki permintaan servis aktif. Konsultasikan kerusakan dengan asisten AI atau buat permintaan teknisi secara langsung.
                    </p>
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <a href="<?= base_url('customer/consultation') ?>" class="btn btn-outline-primary rounded-pill px-4 btn-sm fw-semibold">
                            <i class="bi bi-stars me-1"></i> Konsultasi AI
                        </a>
                        <a href="<?= base_url('posts/create') ?>" class="btn btn-primary rounded-pill px-4 btn-sm fw-bold shadow-sm">
                            <i class="bi bi-plus-circle me-1"></i> Buat Permintaan
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($posts as $post): ?>
                    <?php
                        $pId = is_object($post) ? $post->id : $post['id'];
                        $pTitle = is_object($post) ? $post->title : $post['title'];
                        $pCatName = is_object($post) ? ($post->category_name ?? 'Umum') : ($post['category_name'] ?? 'Umum');
                        $pCatIcon = is_object($post) ? ($post->category_icon ?? 'bi-tools') : ($post['category_icon'] ?? 'bi-tools');
                        $pDesc = is_object($post) ? $post->description : $post['description'];
                        $pStatus = is_object($post) ? $post->status : $post['status'];
                        $pImage = is_object($post) ? ($post->image ?? '') : ($post['image'] ?? '');
                        $pMin = is_object($post) ? ($post->estimated_min_price ?? 0) : ($post['estimated_min_price'] ?? 0);
                        $pMax = is_object($post) ? ($post->estimated_max_price ?? 0) : ($post['estimated_max_price'] ?? 0);
                        $pOffers = is_object($post) ? ($post->offers_count ?? ($post->response_count ?? 0)) : ($post['offers_count'] ?? ($post['response_count'] ?? 0));
                        $pCreatedAt = is_object($post) ? $post->created_at : $post['created_at'];

                        $statusBadgeClass = 'badge-status-mencari';
                        $statusIcon = 'bi-search';
                        if ($pStatus === 'Ada Teknisi' || $pStatus === 'in_progress') {
                            $statusBadgeClass = 'badge-status-proses';
                            $statusIcon = 'bi-person-check-fill';
                        } elseif ($pStatus === 'Selesai' || $pStatus === 'completed') {
                            $statusBadgeClass = 'badge-status-selesai';
                            $statusIcon = 'bi-check-circle-fill';
                        } elseif ($pStatus === 'Dibatalkan') {
                            $statusBadgeClass = 'badge-status-batal';
                            $statusIcon = 'bi-x-circle-fill';
                        }
                    ?>
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100 d-flex flex-column bg-white transition hover-shadow overflow-hidden">
                            <div class="card-body p-4 flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-3 gap-2">
                                    <span class="badge rounded-pill px-3 py-1.5 xs-text" style="background: rgba(0, 120, 212, 0.08); color: #0078D4; border: 1px solid rgba(0, 120, 212, 0.2);">
                                        <i class="bi <?= e($pCatIcon) ?> me-1"></i> <?= e($pCatName) ?>
                                    </span>
                                    <span class="badge <?= $statusBadgeClass ?> rounded-pill px-3 py-1.5 xs-text">
                                        <i class="bi <?= $statusIcon ?> me-1"></i> <?= e($pStatus) ?>
                                    </span>
                                </div>

                                <h5 class="fw-bold text-dark mb-2">
                                    <a href="<?= base_url('posts/detail/' . $pId) ?>" class="text-decoration-none text-dark hover-primary">
                                        <?= e($pTitle) ?>
                                    </a>
                                </h5>
                                
                                <p class="text-muted small mb-3 line-clamp-2" style="line-height: 1.5;">
                                    <?= e(mb_strimwidth($pDesc, 0, 140, '...')) ?>
                                </p>

                                <?php if (!empty($pImage)): ?>
                                    <div class="mb-3">
                                        <img src="<?= base_url($pImage) ?>" alt="Kerusakan" class="img-fluid rounded-3 w-100 object-fit-cover" style="max-height: 180px;">
                                    </div>
                                <?php endif; ?>

                                <div class="bg-light p-3 rounded-3 mb-2 d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="text-muted xs-text fw-semibold"><i class="bi bi-tag me-1"></i>Estimasi Biaya</div>
                                        <div class="fw-bold text-success font-monospace small">
                                            Rp <?= number_format($pMin, 0, ',', '.') ?> - Rp <?= number_format($pMax, 0, ',', '.') ?>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="text-muted xs-text fw-semibold"><i class="bi bi-people me-1"></i>Tawaran</div>
                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 xs-text fw-bold">
                                            <?= (int)$pOffers ?> Teknisi
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer bg-white border-top-0 p-4 pt-0 d-flex justify-content-between align-items-center">
                                <small class="text-muted" style="font-size: 0.78rem;">
                                    <i class="bi bi-clock me-1"></i><?= date('d M Y, H:i', strtotime($pCreatedAt)) ?>
                                </small>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= base_url('posts/detail/' . $pId) ?>" class="btn btn-primary rounded-pill px-3 fw-semibold">
                                        Detail &amp; Tawaran <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                    <?php if ($pStatus === 'Mencari Teknisi'): ?>
                                        <a href="<?= base_url('posts/edit/' . $pId) ?>" class="btn btn-outline-secondary rounded-pill ms-1 px-3" title="Edit Postingan">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>
