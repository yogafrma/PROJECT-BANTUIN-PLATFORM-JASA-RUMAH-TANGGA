<?php
// app/views/admin/users/index.php
?>
<div class="container py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-7">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>" class="text-decoration-none">Admin</a></li>
                    <li class="breadcrumb-item active">Kelola Pengguna</li>
                </ol>
            </nav>
            <h2 class="fw-bold mb-0"><i class="bi bi-people-fill text-primary me-2"></i>Manajemen Seluruh Pengguna</h2>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0">
            <span class="badge bg-primary px-3 py-2 rounded-pill fs-6">
                Total: <?= count($users) ?> Akun
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

    <!-- Role Filter Tabs -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="fw-semibold text-muted me-2 small"><i class="bi bi-funnel me-1"></i>Filter Peran:</span>
                <a href="<?= base_url('admin/users') ?>" class="btn btn-sm <?= ($selectedRole === 'all') ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3">
                    Semua Role (<?= count($users) ?>)
                </a>
                <a href="<?= base_url('admin/users?role=customer') ?>" class="btn btn-sm <?= ($selectedRole === 'customer') ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3">
                    <i class="bi bi-person me-1"></i> Pelanggan (Customer)
                </a>
                <a href="<?= base_url('admin/users?role=technician') ?>" class="btn btn-sm <?= ($selectedRole === 'technician') ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3">
                    <i class="bi bi-tools me-1"></i> Teknisi
                </a>
                <a href="<?= base_url('admin/users?role=admin') ?>" class="btn btn-sm <?= ($selectedRole === 'admin') ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3">
                    <i class="bi bi-shield-lock me-1"></i> Admin
                </a>
            </div>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="card-header bg-white py-3 border-light-subtle d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-table me-2 text-secondary"></i>Daftar Akun Pengguna</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-muted">
                            <th class="ps-4">PENGGUNA</th>
                            <th>ROLE</th>
                            <th>NO. WHATSAPP</th>
                            <th>STATUS AKUN</th>
                            <th>TERDAFTAR</th>
                            <th class="text-end pe-4">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                                    Tidak ada data pengguna yang sesuai dengan filter ini.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $u): ?>
                                <?php
                                    $uId = is_object($u) ? $u->id : $u['id'];
                                    $uName = is_object($u) ? $u->name : $u['name'];
                                    $uEmail = is_object($u) ? $u->email : $u['email'];
                                    $uRole = is_object($u) ? $u->role : $u['role'];
                                    $uWa = is_object($u) ? $u->whatsapp : $u['whatsapp'];
                                    $uStatus = is_object($u) ? $u->status : $u['status'];
                                    $uCreatedAt = is_object($u) ? $u->created_at : $u['created_at'];

                                    $roleBadge = 'bg-primary';
                                    if ($uRole === 'admin') $roleBadge = 'bg-danger';
                                    elseif ($uRole === 'technician') $roleBadge = 'bg-success';
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-circle <?= $roleBadge ?> text-white fw-bold">
                                                <?= strtoupper(substr($uName, 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($uName) ?></div>
                                                <div class="text-muted small"><?= htmlspecialchars($uEmail) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge <?= $roleBadge ?> bg-opacity-10 text-<?= ($uRole === 'admin' ? 'danger' : ($uRole === 'technician' ? 'success' : 'primary')) ?> border rounded-pill px-3 py-1 small">
                                            <?= ucfirst($uRole) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= wa_url($uWa, 'Halo ' . $uName . ', ini pesan dari Administrator BANTUIN.') ?>" 
                                           target="_blank" 
                                           class="text-decoration-none text-success small fw-semibold">
                                            <i class="bi bi-whatsapp me-1"></i> <?= htmlspecialchars($uWa) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php if ($uStatus === 'active'): ?>
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
                                        <?= date('d M Y', strtotime($uCreatedAt ?? 'now')) ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            <a href="<?= base_url('admin/userDetail/' . $uId) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1">
                                                <i class="bi bi-eye me-1"></i> Detail
                                            </a>
                                            <?php if ($uRole !== 'admin' || (int)$uId !== (int)Auth::user()->id): ?>
                                                <form action="<?= base_url('admin/toggleUserStatus/' . $uId) ?>" method="POST" class="d-inline">
                                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                    <?php if ($uStatus === 'active'): ?>
                                                        <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill px-3" 
                                                                onclick="return confirm('Apakah Anda yakin ingin MENONAKTIFKAN akun pengguna <?= htmlspecialchars($uName) ?>? Pengguna tidak akan dapat login.');">
                                                            <i class="bi bi-slash-circle me-1"></i> Nonaktifkan
                                                        </button>
                                                    <?php else: ?>
                                                        <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3" 
                                                                onclick="return confirm('Apakah Anda yakin ingin MENGAKTIFKAN kembali akun pengguna <?= htmlspecialchars($uName) ?>?');">
                                                            <i class="bi bi-check-circle me-1"></i> Aktifkan
                                                        </button>
                                                    <?php endif; ?>
                                                </form>

                                                <form action="<?= base_url('admin/deleteUser/' . $uId) ?>" method="POST" class="d-inline ms-1">
                                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" 
                                                            onclick="return confirm('PERINGATAN: Apakah Anda yakin ingin MENGHAPUS PERMANEN akun pengguna <?= htmlspecialchars($uName) ?>? Seluruh data postingan/profil terkait akan terhapus.');">
                                                        <i class="bi bi-trash-fill me-1"></i> Hapus
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
