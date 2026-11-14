<?php
// app/views/admin/technicians/index.php
?>
<div class="container py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-7">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>" class="text-decoration-none">Admin</a></li>
                    <li class="breadcrumb-item active">Mitra Teknisi</li>
                </ol>
            </nav>
            <h2 class="fw-bold mb-0"><i class="bi bi-tools text-success me-2"></i>Daftar Mitra Teknisi</h2>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0">
            <span class="badge bg-success px-3 py-2 rounded-pill fs-6">
                Total: <?= count($technicians) ?> Teknisi
            </span>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_SESSION['flash'])): ?>
        <?php $flash = get_flash(); ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-info-circle-fill me-2 fs-5"></i>
            <div><?= htmlspecialchars($flash['message']) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="card-header bg-white py-3 border-light-subtle d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-wrench-adjustable me-2 text-secondary"></i>Mitra Teknisi Terdaftar & Kategori Keahlian</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-muted">
                            <th class="ps-4">NAMA TEKNISI</th>
                            <th>KEAHLIAN UTAMA</th>
                            <th>RATING</th>
                            <th>WILAYAH LAYANAN</th>
                            <th>NO. WHATSAPP</th>
                            <th>STATUS AKUN</th>
                            <th class="text-end pe-4">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($technicians)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                                    Belum ada data teknisi yang terdaftar.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($technicians as $t): ?>
                                <?php
                                    $tId = is_object($t) ? $t->id : $t['id'];
                                    $tName = is_object($t) ? $t->name : $t['name'];
                                    $tEmail = is_object($t) ? $t->email : $t['email'];
                                    $tWa = is_object($t) ? $t->whatsapp : $t['whatsapp'];
                                    $tCat = is_object($t) ? ($t->category_name ?? 'Teknisi AC') : ($t['category_name'] ?? 'Teknisi AC');
                                    $tRating = is_object($t) ? ($t->rating ?? 5.0) : ($t['rating'] ?? 5.0);
                                    $tLoc = is_object($t) ? ($t->location ?? 'Jabodetabek') : ($t['location'] ?? 'Jabodetabek');
                                    $tStatus = is_object($t) ? $t->status : $t['status'];
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-circle bg-success text-white fw-bold">
                                                <?= strtoupper(substr($tName, 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($tName) ?></div>
                                                <div class="text-muted xs-text"><?= htmlspecialchars($tEmail) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border rounded-pill px-3 py-1">
                                            <i class="bi bi-tools me-1"></i> <?= htmlspecialchars($tCat) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-warning small fw-bold">
                                            <i class="bi bi-star-fill me-1"></i> <?= number_format($tRating, 1) ?>
                                        </div>
                                    </td>
                                    <td class="text-muted small">
                                        <i class="bi bi-geo-alt text-danger me-1"></i> <?= htmlspecialchars($tLoc) ?>
                                    </td>
                                    <td>
                                        <a href="<?= wa_url($tWa, 'Halo Teknisi ' . $tName . ', ini dari Admin BANTUIN.') ?>" 
                                           target="_blank" 
                                           class="text-decoration-none text-success small fw-semibold">
                                            <i class="bi bi-whatsapp me-1"></i> <?= htmlspecialchars($tWa) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php if ($tStatus === 'active'): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill px-3 py-1 small">
                                                <i class="bi bi-check-circle-fill me-1"></i> Aktif
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle rounded-pill px-3 py-1 small">
                                                <i class="bi bi-x-circle-fill me-1"></i> Nonaktif
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            <a href="<?= base_url('admin/userDetail/' . $tId) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1">
                                                <i class="bi bi-eye me-1"></i> Detail
                                            </a>
                                            <form action="<?= base_url('admin/toggleUserStatus/' . $tId) ?>" method="POST" class="d-inline">
                                                <?php if ($tStatus === 'active'): ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" 
                                                            onclick="return confirm('Nonaktifkan akun teknisi <?= htmlspecialchars($tName) ?>? Teknisi tidak akan dapat menawarkan bantuan lagi.');">
                                                        <i class="bi bi-slash-circle"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3" 
                                                            onclick="return confirm('Aktifkan kembali akun teknisi <?= htmlspecialchars($tName) ?>?');">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </form>
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
