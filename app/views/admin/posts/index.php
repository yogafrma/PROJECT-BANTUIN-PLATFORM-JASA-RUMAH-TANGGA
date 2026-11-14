<?php
/**
 * Admin Posts Management View (Modern BANTUIN Theme)
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
                            <i class="bi bi-card-checklist"></i> Manajemen Keluhan
                        </span>
                        <span class="text-white-50 small">Moderasi Permintaan Jasa</span>
                    </div>
                    <h2 class="fw-bold mb-1 text-white">Postingan Keluhan Pelanggan</h2>
                    <p class="text-white-50 mb-0 small">
                        Pantau status pengerjaan, respon teknisi, dan kelola keluhan perbaikan rumah tangga.
                    </p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
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

        <!-- Filter Bar -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
            <div class="card-body p-3">
                <form method="GET" action="<?= base_url('admin/posts') ?>" class="row g-2 align-items-center">
                    <!-- Filter Status -->
                    <div class="col-md-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-funnel me-1"></i> Status:</span>
                            <select name="status" class="form-select border-start-0" onchange="this.form.submit()">
                                <option value="all" <?= ($selectedStatus === 'all') ? 'selected' : '' ?>>Semua Status</option>
                                <option value="Mencari Teknisi" <?= ($selectedStatus === 'Mencari Teknisi') ? 'selected' : '' ?>>Mencari Teknisi</option>
                                <option value="Ada Teknisi" <?= ($selectedStatus === 'Ada Teknisi') ? 'selected' : '' ?>>Ada Teknisi</option>
                                <option value="Selesai" <?= ($selectedStatus === 'Selesai') ? 'selected' : '' ?>>Selesai</option>
                                <option value="Dibatalkan" <?= ($selectedStatus === 'Dibatalkan') ? 'selected' : '' ?>>Dibatalkan</option>
                            </select>
                        </div>
                    </div>

                    <!-- Filter Kategori -->
                    <div class="col-md-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-tags me-1"></i> Kategori:</span>
                            <select name="category_id" class="form-select border-start-0" onchange="this.form.submit()">
                                <option value="all" <?= ($selectedCategory === 'all') ? 'selected' : '' ?>>Semua Kategori</option>
                                <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $cat): ?>
                                        <?php 
                                            $cId = is_object($cat) ? $cat->id : ($cat['id'] ?? '');
                                            $cName = is_object($cat) ? $cat->name : ($cat['name'] ?? '');
                                        ?>
                                        <option value="<?= e($cId) ?>" <?= ($selectedCategory == $cId) ? 'selected' : '' ?>>
                                            <?= e($cName) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Reset Button -->
                    <div class="col-md-2 text-end">
                        <a href="<?= base_url('admin/posts') ?>" class="btn btn-sm btn-outline-secondary rounded-pill w-100 fw-semibold">
                            Reset Filter
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Posts Table Card -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
            <div class="card-header bg-white py-3 border-light-subtle d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-table text-primary"></i>
                    <span>Daftar Keluhan Customer</span>
                </h6>
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1.5 small fw-semibold">
                    Total: <?= count($posts) ?> Postingan
                </span>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small text-muted" style="font-size: 0.8rem;">
                                <th class="ps-4 py-3">KELUHAN / JUDUL</th>
                                <th class="py-3">PELANGGAN</th>
                                <th class="py-3">KATEGORI</th>
                                <th class="py-3">ESTIMASI BIAYA</th>
                                <th class="py-3 text-center">STATUS</th>
                                <th class="py-3 text-center">TAWARAN</th>
                                <th class="text-end pe-4 py-3">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($posts)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary opacity-50"></i>
                                        Tidak ada postingan keluhan yang sesuai dengan kriteria filter.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($posts as $p): ?>
                                    <?php
                                        $pId = is_object($p) ? $p->id : $p['id'];
                                        $pTitle = is_object($p) ? $p->title : $p['title'];
                                        $pCustName = is_object($p) ? ($p->customer_name ?? 'Customer') : ($p['customer_name'] ?? 'Customer');
                                        $pCustWa = is_object($p) ? ($p->customer_whatsapp ?? '') : ($p['customer_whatsapp'] ?? '');
                                        $pCat = is_object($p) ? ($p->category_name ?? 'Umum') : ($p['category_name'] ?? 'Umum');
                                        $pMin = is_object($p) ? ($p->estimated_min_price ?? 0) : ($p['estimated_min_price'] ?? 0);
                                        $pMax = is_object($p) ? ($p->estimated_max_price ?? 0) : ($p['estimated_max_price'] ?? 0);
                                        $pStatus = is_object($p) ? $p->status : $p['status'];
                                        $pOffers = is_object($p) ? ($p->offers_count ?? 0) : ($p['offers_count'] ?? 0);

                                        $badgeClass = 'bg-secondary text-white';
                                        if ($pStatus === 'Mencari Teknisi') $badgeClass = 'bg-warning text-dark';
                                        elseif ($pStatus === 'Ada Teknisi') $badgeClass = 'bg-info text-white';
                                        elseif ($pStatus === 'Selesai') $badgeClass = 'bg-success text-white';
                                        elseif ($pStatus === 'Dibatalkan') $badgeClass = 'bg-danger text-white';
                                    ?>
                                    <tr style="border-bottom: 1px solid #F1F5F9;">
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark">
                                                <a href="<?= base_url('admin/postDetail/' . $pId) ?>" class="text-decoration-none text-dark hover-primary">
                                                    <?= e($pTitle) ?>
                                                </a>
                                            </div>
                                            <small class="text-muted font-monospace">ID: #<?= $pId ?></small>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark small"><?= e($pCustName) ?></div>
                                            <?php if (!empty($pCustWa)): ?>
                                                <a href="<?= wa_url($pCustWa, 'Halo ' . $pCustName . ', ini dari Admin BANTUIN terkait keluhan: ' . $pTitle) ?>" 
                                                   target="_blank" class="text-success xs-text text-decoration-none d-inline-flex align-items-center">
                                                    <i class="bi bi-whatsapp me-1"></i> <?= e($pCustWa) ?>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-primary border rounded-pill px-2.5 py-1 xs-text">
                                                <?= e($pCat) ?>
                                            </span>
                                        </td>
                                        <td class="small text-success fw-bold font-monospace">
                                            Rp <?= number_format($pMin, 0, ',', '.') ?> - <?= number_format($pMax, 0, ',', '.') ?>
                                        </td>
                                        <td class="text-center">
                                            <!-- Quick Status Dropdown -->
                                            <form action="<?= base_url('admin/postStatus/' . $pId) ?>" method="POST" class="d-inline">
                                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                <select name="status" class="form-select form-select-sm rounded-pill border-0 <?= $badgeClass ?> fw-bold py-1 px-3 text-center" 
                                                        onchange="if(confirm('Ubah status postingan #<?= $pId ?> menjadi ' + this.value + '?')) { this.form.submit(); } else { location.reload(); }">
                                                    <option value="Mencari Teknisi" <?= ($pStatus === 'Mencari Teknisi') ? 'selected' : '' ?>>Mencari Teknisi</option>
                                                    <option value="Ada Teknisi" <?= ($pStatus === 'Ada Teknisi') ? 'selected' : '' ?>>Ada Teknisi</option>
                                                    <option value="Selesai" <?= ($pStatus === 'Selesai') ? 'selected' : '' ?>>Selesai</option>
                                                    <option value="Dibatalkan" <?= ($pStatus === 'Dibatalkan') ? 'selected' : '' ?>>Dibatalkan</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2.5 py-1 xs-text fw-semibold">
                                                <?= (int)$pOffers ?> Penawar
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= base_url('admin/postDetail/' . $pId) ?>" class="btn btn-outline-primary rounded-pill px-3 me-1">
                                                    <i class="bi bi-eye me-1"></i> Detail
                                                </a>
                                                <?php if ($pStatus !== 'Dibatalkan'): ?>
                                                    <form action="<?= base_url('admin/postDelete/' . $pId) ?>" method="POST" class="d-inline">
                                                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                        <button type="submit" class="btn btn-outline-danger rounded-pill px-3 fw-semibold shadow-sm" 
                                                                onclick="return confirm('Apakah Anda yakin ingin MENGHAPUS / MEMBATALKAN postingan #<?= $pId ?>?');">
                                                            <i class="bi bi-trash"></i> Hapus
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
