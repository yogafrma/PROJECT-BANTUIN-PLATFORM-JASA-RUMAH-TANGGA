<div class="dashboard-wrapper py-4">
    <div class="container py-lg-2">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-md-11">
                
                <!-- Breadcrumb & Back -->
                <div class="mb-3">
                    <a href="<?= !empty($consultation) ? base_url('customer/consultationDetail/' . $consultation->id) : base_url('posts/my') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>

                <!-- Card Form -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="auth-header-accent"></div>
                    
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                            <div class="brand-icon-box shadow-sm">
                                <i class="bi bi-send-check-fill text-white fs-4"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0">Publikasikan Postingan Keluhan</h4>
                                <p class="text-muted small mb-0">Postingan Anda akan dilihat oleh mitra teknisi terpercaya untuk menawarkan bantuan.</p>
                            </div>
                        </div>

                        <!-- Info Box jika dari Konsultasi AI -->
                        <?php if (!empty($consultation)): ?>
                            <div class="alert alert-info bg-info bg-opacity-10 border border-info border-opacity-25 rounded-4 p-3 mb-4">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge bg-warning text-dark rounded-pill px-2 py-1 xs-text fw-bold">
                                        <i class="bi bi-stars"></i> Terhubung dari Konsultasi AI
                                    </span>
                                    <span class="text-muted xs-text">Data & estimasi harga terisi otomatis dari hasil diagnosis</span>
                                </div>
                                <div class="row g-2 small">
                                    <div class="col-md-6">
                                        <span class="text-muted">Kemungkinan Kerusakan:</span>
                                        <div class="fw-semibold text-dark"><?= e($prefill['ai_analysis'] ?? 'Terdeteksi anomali perangkat') ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted">Estimasi Biaya AI:</span>
                                        <div class="fw-bold text-success">Rp <?= number_format($prefill['min_price'], 0, ',', '.') ?> - Rp <?= number_format($prefill['max_price'], 0, ',', '.') ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- General Error -->
                        <?php if (!empty($errors['general'])): ?>
                            <div class="alert alert-danger rounded-3 small py-2 d-flex align-items-center gap-2 mb-4" role="alert">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <div><?= e($errors['general']) ?></div>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('posts/create' . (!empty($consultation) ? '?consultation_id=' . $consultation->id : '')) ?>" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            
                            <!-- Hidden AI Values -->
                            <input type="hidden" name="ai_analysis" value="<?= e($prefill['ai_analysis'] ?? '') ?>">
                            <input type="hidden" name="estimated_min_price" value="<?= e($prefill['min_price'] ?? 0) ?>">
                            <input type="hidden" name="estimated_max_price" value="<?= e($prefill['max_price'] ?? 0) ?>">
                            <input type="hidden" name="existing_image" value="<?= e($prefill['image'] ?? '') ?>">

                            <!-- Judul Postingan -->
                            <div class="mb-3">
                                <label for="title" class="form-label fw-semibold small text-dark d-flex justify-content-between">
                                    <span><i class="bi bi-card-heading text-primary me-1"></i> Judul Postingan <span class="text-danger">*</span></span>
                                    <span class="text-muted xs-text">Dapat diedit agar lebih jelas</span>
                                </label>
                                <input type="text" 
                                       class="form-control form-control-lg rounded-3 fs-6 <?= !empty($errors['title']) ? 'is-invalid' : '' ?>" 
                                       id="title" 
                                       name="title" 
                                       placeholder="Contoh: Perbaikan AC Split 1 PK Bocor & Tidak Dingin" 
                                       value="<?= old('title', $prefill['title']) ?>" 
                                       required autofocus>
                                <?php if (!empty($errors['title'])): ?>
                                    <div class="invalid-feedback small"><?= e($errors['title']) ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Kategori Jasa -->
                            <div class="mb-3">
                                <label for="category_id" class="form-label fw-semibold small text-dark">
                                    <i class="bi bi-grid-fill text-primary me-1"></i> Kategori Layanan <span class="text-danger">*</span>
                                </label>
                                <select class="form-select rounded-3" id="category_id" name="category_id" required>
                                    <?php if (!empty($categories)): ?>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= $cat->id ?>" <?= old('category_id', $prefill['category_id']) == $cat->id ? 'selected' : '' ?>>
                                                <?= e($cat->name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Deskripsi Keluhan -->
                            <div class="mb-4">
                                <label for="description" class="form-label fw-semibold small text-dark d-flex justify-content-between">
                                    <span><i class="bi bi-card-text text-primary me-1"></i> Deskripsi & Detail Kerusakan <span class="text-danger">*</span></span>
                                    <span class="text-muted xs-text">Dapat diedit untuk melengkapi detail</span>
                                </label>
                                <textarea class="form-control rounded-3 <?= !empty($errors['description']) ? 'is-invalid' : '' ?>" 
                                          id="description" 
                                          name="description" 
                                          rows="5" 
                                          placeholder="Jelaskan kondisi kerusakan, alamat/patokan rumah, dan waktu luang Anda untuk dikunjungi teknisi..." 
                                          required><?= old('description', $prefill['description']) ?></textarea>
                                <?php if (!empty($errors['description'])): ?>
                                    <div class="invalid-feedback small"><?= e($errors['description']) ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Foto Kerusakan (Tampil jika sudah ada atau upload baru) -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold small text-dark d-block">
                                    <i class="bi bi-camera-fill text-primary me-1"></i> Foto Kerusakan
                                </label>
                                
                                <?php if (!empty($prefill['image'])): ?>
                                    <div class="mb-2 d-flex align-items-center gap-3 p-2 bg-light rounded-3 border">
                                        <img src="<?= asset($prefill['image']) ?>" alt="Foto Konsultasi" class="rounded-2 border" style="width: 70px; height: 70px; object-fit: cover;">
                                        <div>
                                            <div class="small fw-semibold text-dark">Foto dari Konsultasi AI terlampir</div>
                                            <div class="text-muted xs-text">Foto ini akan otomatis digunakan pada postingan Anda.</div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <input type="file" 
                                       class="form-control rounded-3 <?= !empty($errors['image']) ? 'is-invalid' : '' ?>" 
                                       id="image" 
                                       name="image" 
                                       accept="image/jpeg,image/png,image/webp">
                                <?php if (!empty($errors['image'])): ?>
                                    <div class="invalid-feedback small"><?= e($errors['image']) ?></div>
                                <?php else: ?>
                                    <div class="form-text xs-text">Unggah foto baru jika ingin mengganti foto sebelumnya (Maks 5 MB).</div>
                                <?php endif; ?>
                            </div>

                            <!-- Tombol Submit -->
                            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                                <a href="<?= base_url('posts/my') ?>" class="btn btn-light rounded-pill px-4 fw-semibold">Batal</a>
                                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                                    <i class="bi bi-send-fill me-1"></i> Publikasikan Postingan Sekarang
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
