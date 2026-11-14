<?php
/**
 * Category Management View (Modern BANTUIN Theme)
 * Project: BANTUIN — Platform Layanan Jasa Rumah Tangga
 */
$totalCats = count($categories ?? []);
$activeCats = 0;
$inactiveCats = 0;
if (!empty($categories)) {
    foreach ($categories as $c) {
        $st = is_object($c) ? ($c->status ?? 'active') : ($c['status'] ?? 'active');
        if ($st === 'active') $activeCats++;
        else $inactiveCats++;
    }
}
?>

<div class="dashboard-wrapper py-4">
    <div class="container">
        
        <!-- Header Banner (Command Center Theme) -->
        <div class="settings-hero-card mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 position-relative z-1">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="settings-hero-pill">
                            <i class="bi bi-tags-fill"></i> Manajemen Layanan
                        </span>
                        <span class="text-white-50 small">Master Data Kategori</span>
                    </div>
                    <h2 class="fw-bold mb-1 text-white">Manajemen Kategori Jasa</h2>
                    <p class="text-white-50 mb-0 small">
                        Kelola bidang keahlian dan klasifikasi layanan teknisi perbaikan rumah tangga.
                    </p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="<?= base_url('categories/create') ?>" class="btn btn-primary rounded-pill btn-sm px-3 fw-bold d-inline-flex align-items-center gap-1 shadow-sm">
                        <i class="bi bi-plus-lg"></i> Tambah Kategori
                    </a>
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
                <div><?= e($flash['message']) ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Quick Summary Metrics -->
        <div class="row g-3 mb-4">
            <div class="col-sm-4">
                <div class="customer-stat-card">
                    <div class="stat-icon-wrap" style="background: rgba(0, 120, 212, 0.1); color: #0078D4;">
                        <i class="bi bi-grid"></i>
                    </div>
                    <div>
                        <div class="stat-value"><?= (int)$totalCats ?></div>
                        <div class="stat-label">Total Kategori</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="customer-stat-card">
                    <div class="stat-icon-wrap" style="background: rgba(16, 124, 65, 0.1); color: #107C41;">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <div class="stat-value"><?= (int)$activeCats ?></div>
                        <div class="stat-label">Kategori Aktif</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="customer-stat-card">
                    <div class="stat-icon-wrap" style="background: rgba(100, 116, 139, 0.1); color: #64748B;">
                        <i class="bi bi-dash-circle"></i>
                    </div>
                    <div>
                        <div class="stat-value"><?= (int)$inactiveCats ?></div>
                        <div class="stat-label">Kategori Nonaktif</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categories Table Card -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
            <div class="card-header bg-white py-3 border-light-subtle d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-table text-primary"></i>
                    <span>Daftar Kategori Layanan</span>
                </h6>
                <span class="badge bg-light text-muted border xs-text">
                    <?= (int)$totalCats ?> Bidang Layanan
                </span>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small text-muted" style="font-size: 0.8rem;">
                                <th class="ps-4 py-3" style="width: 70px;">IKON</th>
                                <th class="py-3">NAMA KATEGORI</th>
                                <th class="py-3">SLUG</th>
                                <th class="py-3">DESKRIPSI</th>
                                <th class="py-3 text-center">STATUS</th>
                                <th class="text-end pe-4 py-3" style="width: 170px;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <?php
                                        $cId = is_object($cat) ? $cat->id : $cat['id'];
                                        $cName = is_object($cat) ? $cat->name : $cat['name'];
                                        $cIcon = is_object($cat) ? ($cat->icon ?? 'bi-tools') : ($cat['icon'] ?? 'bi-tools');
                                        $cBadge = is_object($cat) ? ($cat->badge ?? '') : ($cat['badge'] ?? '');
                                        $cSlug = is_object($cat) ? ($cat->slug ?? '-') : ($cat['slug'] ?? '-');
                                        $cDesc = is_object($cat) ? ($cat->description ?? '') : ($cat['description'] ?? '');
                                        $cStatus = is_object($cat) ? ($cat->status ?? 'active') : ($cat['status'] ?? 'active');
                                    ?>
                                    <tr style="border-bottom: 1px solid #F1F5F9;">
                                        <td class="ps-4">
                                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(0, 120, 212, 0.1); color: #0078D4;">
                                                <i class="bi <?= e($cIcon) ?> fs-5"></i>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= e($cName) ?></div>
                                            <?php if (!empty($cBadge)): ?>
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill xs-text">
                                                    <?= e($cBadge) ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <code class="text-muted small"><?= e($cSlug) ?></code>
                                        </td>
                                        <td class="text-muted small" style="max-width: 320px;">
                                            <div class="text-truncate" title="<?= e($cDesc) ?>">
                                                <?= e($cDesc) ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($cStatus === 'active'): ?>
                                                <span class="badge rounded-pill xs-text" style="background: rgba(16, 124, 65, 0.12); color: #107C41; border: 1px solid rgba(16, 124, 65, 0.25);">
                                                    Aktif
                                                </span>
                                            <?php else: ?>
                                                <span class="badge rounded-pill xs-text" style="background: rgba(100, 116, 139, 0.12); color: #64748B; border: 1px solid rgba(100, 116, 139, 0.25);">
                                                    Nonaktif
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="btn-group btn-group-sm">
                                                <!-- Toggle Status Button -->
                                                <?php if ($cStatus === 'active'): ?>
                                                    <a href="<?= base_url('categories/toggle/' . $cId) ?>" 
                                                       class="btn btn-outline-secondary rounded-pill px-2.5 me-1" 
                                                       title="Nonaktifkan Kategori"
                                                       onclick="return confirm('Nonaktifkan kategori <?= e($cName) ?>?');">
                                                        <i class="bi bi-eye-slash"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?= base_url('categories/toggle/' . $cId) ?>" 
                                                       class="btn btn-outline-success rounded-pill px-2.5 me-1" 
                                                       title="Aktifkan Kategori"
                                                       onclick="return confirm('Aktifkan kategori <?= e($cName) ?>?');">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                <?php endif; ?>

                                                <!-- Edit Button -->
                                                <a href="<?= base_url('categories/edit/' . $cId) ?>" 
                                                   class="btn btn-outline-primary rounded-pill px-2.5 me-1" 
                                                   title="Edit Kategori">
                                                    <i class="bi bi-pencil"></i>
                                                </a>

                                                <!-- Delete Button -->
                                                <a href="<?= base_url('categories/delete/' . $cId) ?>" 
                                                   class="btn btn-outline-danger rounded-pill px-2.5" 
                                                   title="Hapus Kategori"
                                                   onclick="return confirm('Peringatan: Menghapus kategori ini dapat mempengaruhi teknisi dan postingan terkait. Yakin ingin menghapus?');">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary opacity-50"></i>
                                        Belum ada kategori jasa. Klik "Tambah Kategori" untuk membuat kategori pertama.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
