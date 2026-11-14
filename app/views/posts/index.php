<?php
// app/views/posts/index.php
?>
<div class="container py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <h2 class="fw-bold mb-1"><i class="bi bi-briefcase text-primary me-2"></i>Bursa Pekerjaan & Keluhan Customer</h2>
            <p class="text-muted mb-0">Temukan permintaan perbaikan rumah tangga dari customer yang membutuhkan keahlian Anda.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fs-6">
                <i class="bi bi-broadcast me-1"></i> <?= count($posts) ?> Lowongan Aktif
            </span>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div><?= htmlspecialchars($_SESSION['success']) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div><?= htmlspecialchars($_SESSION['error']) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Category Filters -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="fw-semibold text-muted me-2 small"><i class="bi bi-funnel me-1"></i>Filter Kategori:</span>
                <a href="<?= base_url('posts') ?>" class="btn btn-sm <?= empty($selectedCategory) ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3">
                    Semua Kategori
                </a>
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $cat): ?>
                        <a href="<?= base_url('posts?category_id=' . $cat['id']) ?>" 
                           class="btn btn-sm <?= ($selectedCategory == $cat['id']) ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3">
                            <?= htmlspecialchars($cat['name']) ?>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Job Posts Grid -->
    <?php if (empty($posts)): ?>
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <div class="card-body">
                <div class="avatar-lg mx-auto mb-3 text-muted">
                    <i class="bi bi-clipboard-x fs-1"></i>
                </div>
                <h5 class="fw-bold text-dark">Tidak Ada Permintaan Aktif Saat Ini</h5>
                <p class="text-muted mx-auto" style="max-width: 450px;">
                    Saat ini belum ada customer yang mencari teknisi pada kategori ini. Silakan cek kembali beberapa saat lagi.
                </p>
                <?php if (!empty($selectedCategory)): ?>
                    <a href="<?= base_url('posts') ?>" class="btn btn-outline-primary rounded-pill px-4">
                        Lihat Semua Kategori
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($posts as $post): ?>
                <?php
                    $pId = is_object($post) ? $post->id : $post['id'];
                    $pTitle = is_object($post) ? $post->title : $post['title'];
                    $pCatName = is_object($post) ? ($post->category_name ?? 'Umum') : ($post['category_name'] ?? 'Umum');
                    $pCustName = is_object($post) ? ($post->customer_name ?? 'Customer') : ($post['customer_name'] ?? 'Customer');
                    $pDesc = is_object($post) ? $post->description : $post['description'];
                    $pImage = is_object($post) ? ($post->image ?? '') : ($post['image'] ?? '');
                    $pMin = is_object($post) ? ($post->estimated_min_price ?? 0) : ($post['estimated_min_price'] ?? 0);
                    $pMax = is_object($post) ? ($post->estimated_max_price ?? 0) : ($post['estimated_max_price'] ?? 0);
                    $pOffers = is_object($post) ? ($post->response_count ?? ($post->offers_count ?? 0)) : ($post['response_count'] ?? ($post['offers_count'] ?? 0));
                    $pCreatedAt = is_object($post) ? $post->created_at : $post['created_at'];
                ?>
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100 d-flex flex-column hover-shadow transition">
                        <div class="card-body p-4 flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill">
                                    <i class="bi bi-tools me-1"></i> <?= htmlspecialchars($pCatName) ?>
                                </span>
                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                                    <i class="bi bi-search me-1"></i> Mencari Teknisi
                                </span>
                            </div>

                            <h5 class="fw-bold text-dark mb-2">
                                <a href="<?= base_url('posts/detail/' . $pId) ?>" class="text-decoration-none text-dark">
                                    <?= htmlspecialchars($pTitle) ?>
                                </a>
                            </h5>

                            <div class="d-flex align-items-center text-muted small mb-3">
                                <span class="me-3"><i class="bi bi-person me-1"></i><?= htmlspecialchars($pCustName) ?></span>
                                <span><i class="bi bi-clock me-1"></i><?= date('d M Y, H:i', strtotime($pCreatedAt)) ?></span>
                            </div>
                            
                            <p class="text-secondary small mb-3 text-truncate-2">
                                <?= htmlspecialchars(mb_strimwidth($pDesc, 0, 150, '...')) ?>
                            </p>

                            <?php if (!empty($pImage)): ?>
                                <div class="mb-3 position-relative">
                                    <img src="<?= base_url($pImage) ?>" alt="Foto Kerusakan" class="img-fluid rounded-3 w-100 object-fit-cover" style="height: 180px;">
                                    <span class="position-absolute bottom-0 start-0 m-2 badge bg-dark bg-opacity-75 rounded-pill">
                                        <i class="bi bi-camera me-1"></i> Foto Kerusakan
                                    </span>
                                </div>
                            <?php endif; ?>

                            <div class="bg-light p-3 rounded-3 mb-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted d-block"><i class="bi bi-tag me-1"></i>Estimasi Biaya AI</small>
                                    <strong class="text-success fs-6">
                                        Rp <?= number_format($pMin, 0, ',', '.') ?> - Rp <?= number_format($pMax, 0, ',', '.') ?>
                                    </strong>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block"><i class="bi bi-people me-1"></i>Teknisi Berminat</small>
                                    <span class="badge bg-secondary rounded-pill px-3">
                                        <?= (int)$pOffers ?> Tawaran
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-white border-top-0 p-4 pt-0 d-flex justify-content-between align-items-center">
                            <a href="<?= base_url('posts/detail/' . $pId) ?>" class="btn btn-outline-primary rounded-pill px-3">
                                <i class="bi bi-eye me-1"></i> Lihat Detail
                            </a>
                            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'technician'): ?>
                                <form action="<?= base_url('posts/offer/' . $pId) ?>" method="POST" class="d-inline">
                                    <button type="button" 
                                            class="btn btn-success rounded-pill px-3 shadow-sm btn-confirm-action"
                                            data-confirm-title="Tawarkan Bantuan?"
                                            data-confirm-text="Apakah Anda yakin ingin menawarkan bantuan untuk keluhan '<?= e($pTitle) ?>'?"
                                            data-confirm-icon="question"
                                            data-confirm-btn="Ya, Saya Bisa Membantu">
                                        <i class="bi bi-hand-thumbs-up me-1"></i> Saya Bisa Membantu
                                    </button>
                                </form>
                            <?php else: ?>
                                <a href="<?= base_url('posts/detail/' . $pId) ?>" class="btn btn-primary rounded-pill px-3">
                                    Buka Postingan <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.hover-shadow {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-shadow:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
}
</style>
