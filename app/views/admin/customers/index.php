<?php
// app/views/admin/customers/index.php
?>
<div class="container py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-7">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>" class="text-decoration-none">Admin</a></li>
                    <li class="breadcrumb-item active">Pelanggan</li>
                </ol>
            </nav>
            <h2 class="fw-bold mb-0"><i class="bi bi-person-check-fill text-primary me-2"></i>Daftar Pelanggan (Customer)</h2>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0">
            <span class="badge bg-primary px-3 py-2 rounded-pill fs-6">
                Total: <?= count($customers) ?> Pelanggan
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
            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-people me-2 text-secondary"></i>Data Pelanggan Terdaftar</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-muted">
                            <th class="ps-4">NAMA PELANGGAN</th>
                            <th>EMAIL</th>
                            <th>NO. WHATSAPP</th>
                            <th>TOTAL POSTINGAN</th>
                            <th>STATUS AKUN</th>
                            <th>BERGABUNG</th>
                            <th class="text-end pe-4">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($customers)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                                    Belum ada data pelanggan yang terdaftar.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($customers as $c): ?>
                                <?php
                                    $cId = is_object($c) ? $c->id : $c['id'];
                                    $cName = is_object($c) ? $c->name : $c['name'];
                                    $cEmail = is_object($c) ? $c->email : $c['email'];
                                    $cWa = is_object($c) ? $c->whatsapp : $c['whatsapp'];
                                    $cStatus = is_object($c) ? $c->status : $c['status'];
                                    $cTotalPosts = is_object($c) ? ($c->total_posts ?? 0) : ($c['total_posts'] ?? 0);
                                    $cCreatedAt = is_object($c) ? $c->created_at : $c['created_at'];
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-circle bg-primary text-white fw-bold">
                                                <?= strtoupper(substr($cName, 0, 1)) ?>
                                            </div>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($cName) ?></div>
                                        </div>
                                    </td>
                                    <td class="text-muted small"><?= htmlspecialchars($cEmail) ?></td>
                                    <td>
                                        <a href="<?= wa_url($cWa, 'Halo ' . $cName . ', ini dari Admin BANTUIN.') ?>" 
                                           target="_blank" 
                                           class="text-decoration-none text-success small fw-semibold">
                                            <i class="bi bi-whatsapp me-1"></i> <?= htmlspecialchars($cWa) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border rounded-pill px-3 py-1">
                                            <?= (int)$cTotalPosts ?> Keluhan
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($cStatus === 'active'): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill px-3 py-1 small">
                                                <i class="bi bi-check-circle-fill me-1"></i> Aktif
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle rounded-pill px-3 py-1 small">
                                                <i class="bi bi-x-circle-fill me-1"></i> Nonaktif
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted small">
                                        <?= date('d M Y', strtotime($cCreatedAt ?? 'now')) ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            <a href="<?= base_url('admin/userDetail/' . $cId) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1">
                                                <i class="bi bi-eye me-1"></i> Detail
                                            </a>
                                            <form action="<?= base_url('admin/toggleUserStatus/' . $cId) ?>" method="POST" class="d-inline">
                                                <?php if ($cStatus === 'active'): ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" 
                                                            onclick="return confirm('Nonaktifkan akun pelanggan <?= htmlspecialchars($cName) ?>?');">
                                                        <i class="bi bi-slash-circle"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3" 
                                                            onclick="return confirm('Aktifkan kembali akun pelanggan <?= htmlspecialchars($cName) ?>?');">
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
