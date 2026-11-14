<?php include '../app/views/layouts/header.php'; ?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <h2>Detail Postingan Keluhan #<?= htmlspecialchars($post->id) ?></h2>
            <a href="<?= BASEURL ?>/admin/posts" class="btn btn-secondary">Kembali ke Daftar Postingan</a>
        </div>
    </div>

    <?php flash(); ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Informasi Keluhan</h5>
                </div>
                <div class="card-body">
                    <?php if ($post->image): ?>
                        <div class="text-center mb-3">
                            <img src="<?= BASEURL ?>/public/uploads/<?= htmlspecialchars($post->image) ?>" alt="Foto Keluhan" class="img-fluid rounded" style="max-height: 400px; object-fit: contain;">
                        </div>
                    <?php else: ?>
                        <div class="alert alert-secondary text-center">
                            Tidak ada foto keluhan yang diunggah.
                        </div>
                    <?php endif; ?>

                    <h4><?= htmlspecialchars($post->title) ?></h4>
                    <p class="text-muted mb-3">
                        <i class="bi bi-tag-fill me-1"></i> <?= htmlspecialchars($post->category_name ?? 'Kategori Tidak Diketahui') ?>
                    </p>

                    <h6 class="fw-bold mt-4">Deskripsi Keluhan:</h6>
                    <p class="text-break"><?= nl2br(htmlspecialchars($post->description)) ?></p>

                    <h6 class="fw-bold mt-4">Hasil Analisis AI:</h6>
                    <?php if ($post->ai_analysis): ?>
                        <div class="bg-light p-3 rounded text-break">
                            <?= nl2br(htmlspecialchars($post->ai_analysis)) ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Tidak ada hasil analisis AI.</p>
                    <?php endif; ?>

                    <h6 class="fw-bold mt-4">Estimasi Harga (AI):</h6>
                    <?php if ($post->estimated_min_price && $post->estimated_max_price): ?>
                        <p class="text-primary fw-bold fs-5">
                            Rp <?= number_format($post->estimated_min_price, 0, ',', '.') ?> - 
                            Rp <?= number_format($post->estimated_max_price, 0, ',', '.') ?>
                        </p>
                    <?php else: ?>
                        <p class="text-muted">Tidak ada estimasi harga.</p>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Status Saat Ini:</strong> 
                        <?php
                        $statusClass = 'secondary';
                        if ($post->status === 'Mencari Teknisi') $statusClass = 'warning text-dark';
                        elseif ($post->status === 'Ada Teknisi') $statusClass = 'info';
                        elseif ($post->status === 'Selesai') $statusClass = 'success';
                        elseif ($post->status === 'Dibatalkan') $statusClass = 'danger';
                        ?>
                        <span class="badge bg-<?= $statusClass ?> fs-6"><?= htmlspecialchars($post->status) ?></span>
                    </div>
                </div>
            </div>

            <!-- Admin Actions -->
            <div class="card mb-4 shadow-sm border-0 border-top border-danger border-3">
                <div class="card-header bg-white text-danger">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Tindakan Admin</h5>
                </div>
                <div class="card-body">
                    <p>Ubah status postingan atau hapus (soft delete) jika melanggar aturan platform.</p>
                    <div class="d-flex gap-2 flex-wrap">
                        <form action="<?= BASEURL ?>/admin/postStatus/<?= $post->id ?>" method="POST" class="d-flex gap-2">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <select name="status" class="form-select" required>
                                <option value="">-- Ubah Status --</option>
                                <option value="Mencari Teknisi" <?= $post->status === 'Mencari Teknisi' ? 'selected' : '' ?>>Mencari Teknisi</option>
                                <option value="Ada Teknisi" <?= $post->status === 'Ada Teknisi' ? 'selected' : '' ?>>Ada Teknisi</option>
                                <option value="Selesai" <?= $post->status === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                                <option value="Dibatalkan" <?= $post->status === 'Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                            </select>
                            <button type="submit" class="btn btn-primary" onclick="return confirm('Apakah Anda yakin ingin mengubah status postingan ini?')">Update</button>
                        </form>

                        <?php if ($post->status !== 'Dibatalkan'): ?>
                        <form action="<?= BASEURL ?>/admin/postDelete/<?= $post->id ?>" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin membatalkan/menghapus (soft delete) postingan ini karena melanggar aturan?')">
                                <i class="bi bi-trash-fill"></i> Hapus Postingan
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Informasi Customer -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Informasi Customer</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="rounded-circle bg-primary text-white d-inline-flex justify-content-center align-items-center" style="width: 60px; height: 60px; font-size: 24px;">
                            <?= strtoupper(substr($post->customer_name ?? 'C', 0, 1)) ?>
                        </div>
                    </div>
                    <h6 class="fw-bold mb-1"><?= htmlspecialchars($post->customer_name ?? 'Customer Tidak Diketahui') ?></h6>
                    
                    <a href="<?= BASEURL ?>/admin/userDetail/<?= $post->customer_id ?>" class="btn btn-sm btn-outline-primary mt-2">Lihat Profil Customer</a>
                </div>
            </div>

            <!-- Daftar Teknisi yang Merespons -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Respons Teknisi</h5>
                    <span class="badge bg-secondary rounded-pill"><?= count($responses) ?></span>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($responses)): ?>
                        <div class="p-4 text-center text-muted">
                            Belum ada teknisi yang merespons.
                        </div>
                    <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($responses as $resp): ?>
                                <li class="list-group-item p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="fw-bold mb-0">
                                            <a href="<?= BASEURL ?>/admin/userDetail/<?= $resp->technician_id ?>" class="text-decoration-none">
                                                <?= htmlspecialchars($resp->technician_name) ?>
                                            </a>
                                        </h6>
                                        <?php
                                        $respStatusClass = 'secondary';
                                        if ($resp->status === 'offered') $respStatusClass = 'warning text-dark';
                                        elseif ($resp->status === 'selected') $respStatusClass = 'success';
                                        elseif ($resp->status === 'rejected') $respStatusClass = 'danger';
                                        ?>
                                        <span class="badge bg-<?= $respStatusClass ?>"><?= htmlspecialchars($resp->status) ?></span>
                                    </div>
                                    <div class="small text-muted mb-2">
                                        Tanggal Respons: <?= date('d M Y, H:i', strtotime($resp->created_at)) ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../app/views/layouts/footer.php'; ?>
