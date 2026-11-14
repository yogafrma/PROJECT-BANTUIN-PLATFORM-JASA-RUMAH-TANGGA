<?php
// app/views/admin/users/detail.php
$roleBadge = 'bg-primary';
if ($targetUser->role === 'admin') $roleBadge = 'bg-danger';
elseif ($targetUser->role === 'technician') $roleBadge = 'bg-success';
?>
<div class="container py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-7">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>" class="text-decoration-none">Admin</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/users') ?>" class="text-decoration-none">Pengguna</a></li>
                    <li class="breadcrumb-item active">Detail: <?= htmlspecialchars($targetUser->name) ?></li>
                </ol>
            </nav>
            <h2 class="fw-bold mb-0"><i class="bi bi-person-badge-fill text-primary me-2"></i>Detail Informasi Pengguna</h2>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0">
            <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
            </a>
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

    <div class="row g-4">
        <!-- Profil Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white text-center">
                <div class="avatar-circle <?= $roleBadge ?> text-white fw-bold mx-auto mb-3" style="width: 70px; height: 70px; font-size: 28px;">
                    <?= strtoupper(substr($targetUser->name, 0, 1)) ?>
                </div>
                <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($targetUser->name) ?></h5>
                <p class="text-muted small mb-3"><?= htmlspecialchars($targetUser->email) ?></p>

                <div class="d-flex justify-content-center gap-2 mb-4">
                    <span class="badge <?= $roleBadge ?> bg-opacity-10 text-<?= ($targetUser->role === 'admin' ? 'danger' : ($targetUser->role === 'technician' ? 'success' : 'primary')) ?> border rounded-pill px-3 py-2 small">
                        Role: <?= ucfirst($targetUser->role) ?>
                    </span>
                    <?php if ($targetUser->status === 'active'): ?>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill px-3 py-2 small">
                            <i class="bi bi-check-circle-fill me-1"></i> Aktif
                        </span>
                    <?php else: ?>
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle rounded-pill px-3 py-2 small">
                            <i class="bi bi-x-circle-fill me-1"></i> Nonaktif
                        </span>
                    <?php endif; ?>
                </div>

                <div class="p-3 bg-light rounded-3 text-start mb-3">
                    <div class="mb-2">
                        <small class="text-muted d-block"><i class="bi bi-whatsapp text-success me-1"></i>Nomor WhatsApp:</small>
                        <a href="<?= wa_url($targetUser->whatsapp, 'Halo ' . $targetUser->name . ', ini dari Admin BANTUIN.') ?>" 
                           target="_blank" 
                           class="text-decoration-none text-success fw-bold">
                            <?= htmlspecialchars($targetUser->whatsapp) ?>
                        </a>
                    </div>
                    <div>
                        <small class="text-muted d-block"><i class="bi bi-calendar3 me-1"></i>Tanggal Terdaftar:</small>
                        <span class="fw-semibold text-dark"><?= date('d F Y, H:i', strtotime($targetUser->created_at ?? 'now')) ?></span>
                    </div>
                </div>

                <?php if ($targetUser->role !== 'admin' || (int)$targetUser->id !== (int)Auth::user()->id): ?>
                    <form action="<?= base_url('admin/toggleUserStatus/' . $targetUser->id) ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <?php if ($targetUser->status === 'active'): ?>
                            <button type="submit" class="btn btn-outline-warning w-100 rounded-pill fw-semibold shadow-sm mb-2" 
                                    onclick="return confirm('Nonaktifkan akun pengguna ini?');">
                                <i class="bi bi-slash-circle me-1"></i> Nonaktifkan Akun Pengguna
                            </button>
                        <?php else: ?>
                            <button type="submit" class="btn btn-success w-100 rounded-pill fw-semibold shadow-sm mb-2" 
                                    onclick="return confirm('Aktifkan kembali akun pengguna ini?');">
                                <i class="bi bi-check-circle me-1"></i> Aktifkan Akun Pengguna
                            </button>
                        <?php endif; ?>
                    </form>

                    <form action="<?= base_url('admin/deleteUser/' . $targetUser->id) ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <button type="submit" class="btn btn-outline-danger w-100 rounded-pill fw-semibold shadow-sm" 
                                onclick="return confirm('PERINGATAN: Apakah Anda yakin ingin MENGHAPUS PERMANEN akun pengguna ini? Seluruh data terkait akan dihapus secara permanen.');">
                            <i class="bi bi-trash-fill me-1"></i> Hapus Akun Permanen
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Detail Aktivitas Sesuai Role -->
        <div class="col-lg-8">
            <?php if ($targetUser->role === 'technician'): ?>
                <!-- Profil Keahlian Teknisi -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                    <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-tools text-primary me-2"></i>Keahlian & Profil Teknisi</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <span class="text-muted xs-text d-block">Kategori Keahlian:</span>
                                <strong class="text-primary fs-6"><?= htmlspecialchars($techProfile->category_name ?? 'Teknisi Umum') ?></strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <span class="text-muted xs-text d-block">Rating Kinerja:</span>
                                <strong class="text-warning fs-6"><i class="bi bi-star-fill me-1"></i> <?= number_format($techProfile->rating ?? 5.0, 1) ?> / 5.0</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 bg-light rounded-3">
                                <span class="text-muted xs-text d-block">Wilayah Operasional:</span>
                                <span class="text-dark fw-semibold"><i class="bi bi-geo-alt text-danger me-1"></i> <?= htmlspecialchars($techProfile->location ?? 'Jabodetabek') ?></span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 bg-light rounded-3">
                                <span class="text-muted xs-text d-block">Deskripsi & Pengalaman:</span>
                                <p class="text-muted small mb-0"><?= htmlspecialchars($techProfile->description ?? '-') ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Riwayat Respons Tawaran Teknisi -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-clock-history text-success me-2"></i>Riwayat Tawaran Bantuan (<?= count($userResponses) ?>)</h5>
                    <?php if (!empty($userResponses)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle small mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Judul Keluhan</th>
                                        <th>Pelanggan</th>
                                        <th>Status Tawaran</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($userResponses as $resp): ?>
                                        <tr>
                                            <td class="fw-bold"><?= htmlspecialchars($resp->post_title ?? 'Postingan') ?></td>
                                            <td><?= htmlspecialchars($resp->customer_name ?? 'Customer') ?></td>
                                            <td>
                                                <span class="badge bg-<?= ($resp->status === 'selected' ? 'success' : 'secondary') ?> rounded-pill">
                                                    <?= ucfirst($resp->status) ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <a href="<?= base_url('admin/postDetail/' . ($resp->post_id ?? 1)) ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                                                    Lihat Post
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted small mb-0">Belum ada riwayat tawaran bantuan dari teknisi ini.</p>
                    <?php endif; ?>
                </div>

            <?php elseif ($targetUser->role === 'customer'): ?>
                <!-- Riwayat Postingan Keluhan Customer -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                    <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-file-earmark-text text-primary me-2"></i>Postingan Keluhan Customer (<?= count($userPosts) ?>)</h5>
                    <?php if (!empty($userPosts)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle small mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Judul Keluhan</th>
                                        <th>Kategori</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($userPosts as $p): ?>
                                        <?php
                                            $pId = is_object($p) ? $p->id : $p['id'];
                                            $pTitle = is_object($p) ? $p->title : $p['title'];
                                            $pCat = is_object($p) ? ($p->category_name ?? 'Umum') : ($p['category_name'] ?? 'Umum');
                                            $pStatus = is_object($p) ? $p->status : $p['status'];
                                            $pDate = is_object($p) ? $p->created_at : $p['created_at'];
                                        ?>
                                        <tr>
                                            <td class="fw-bold"><?= htmlspecialchars($pTitle) ?></td>
                                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($pCat) ?></span></td>
                                            <td>
                                                <span class="badge bg-<?= ($pStatus === 'Mencari Teknisi' ? 'warning text-dark' : ($pStatus === 'Ada Teknisi' ? 'info text-white' : ($pStatus === 'Selesai' ? 'success' : 'secondary'))) ?> rounded-pill">
                                                    <?= htmlspecialchars($pStatus) ?>
                                                </span>
                                            </td>
                                            <td class="text-muted"><?= date('d M Y', strtotime($pDate ?? 'now')) ?></td>
                                            <td class="text-end">
                                                <a href="<?= base_url('admin/postDetail/' . $pId) ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                                                    Detail
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted small mb-0">Belum ada postingan keluhan dari pelanggan ini.</p>
                    <?php endif; ?>
                </div>

                <!-- Riwayat Konsultasi AI -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-stars text-warning me-2"></i>Riwayat Konsultasi AI (<?= count($userConsultations) ?>)</h5>
                    <?php if (!empty($userConsultations)): ?>
                        <div class="d-flex flex-column gap-2">
                            <?php foreach ($userConsultations as $c): ?>
                                <?php
                                    $cDetail = is_object($c) ? $c->damage_detail : $c['damage_detail'];
                                    $cDate = is_object($c) ? $c->created_at : $c['created_at'];
                                ?>
                                <div class="p-3 bg-light rounded-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold text-dark small"><?= htmlspecialchars(mb_strimwidth($cDetail, 0, 80, '...')) ?></div>
                                        <small class="text-muted"><?= date('d M Y, H:i', strtotime($cDate ?? 'now')) ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted small mb-0">Belum ada riwayat konsultasi AI.</p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white text-center py-5">
                    <i class="bi bi-shield-lock fs-1 text-danger mb-2"></i>
                    <h6 class="fw-bold">Akun Administrator Sistem</h6>
                    <p class="text-muted small mb-0">Akun ini memiliki hak akses penuh ke seluruh panel manajemen platform.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
