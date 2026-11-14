<div class="dashboard-wrapper py-4">
    <div class="container py-lg-2">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                
                <!-- Breadcrumb & Back -->
                <div class="mb-3">
                    <a href="<?= base_url('categories') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Kategori
                    </a>
                </div>

                <!-- Card Form -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="auth-header-accent"></div>
                    
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                            <div class="brand-icon-box shadow-sm">
                                <i class="bi bi-plus-circle-fill text-white fs-4"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0">Tambah Kategori Jasa Baru</h4>
                                <p class="text-muted small mb-0">Masukkan informasi kategori layanan perbaikan yang akan ditawarkan.</p>
                            </div>
                        </div>

                        <!-- General Error -->
                        <?php if (!empty($errors['general'])): ?>
                            <div class="alert alert-danger rounded-3 small py-2 d-flex align-items-center gap-2 mb-4" role="alert">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <div><?= e($errors['general']) ?></div>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('categories/create') ?>" method="POST">
                            
                            <!-- Nama Kategori -->
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold small text-dark">
                                    <i class="bi bi-tag-fill text-primary me-1"></i> Nama Kategori <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control form-control-lg rounded-3 fs-6 <?= !empty($errors['name']) ? 'is-invalid' : '' ?>" 
                                       id="name" 
                                       name="name" 
                                       placeholder="Contoh: Teknisi Pompa Air & Pipa" 
                                       value="<?= old('name') ?>" 
                                       required autofocus>
                                <?php if (!empty($errors['name'])): ?>
                                    <div class="invalid-feedback small"><?= e($errors['name']) ?></div>
                                <?php else: ?>
                                    <div class="form-text xs-text">Nama kategori yang akan ditampilkan ke pelanggan dan teknisi.</div>
                                <?php endif; ?>
                            </div>

                            <!-- Deskripsi Kategori -->
                            <div class="mb-3">
                                <label for="description" class="form-label fw-semibold small text-dark">
                                    <i class="bi bi-card-text text-primary me-1"></i> Deskripsi Layanan <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control rounded-3 <?= !empty($errors['description']) ? 'is-invalid' : '' ?>" 
                                          id="description" 
                                          name="description" 
                                          rows="3" 
                                          placeholder="Jelaskan cakupan perbaikan atau servis yang masuk dalam kategori ini..." 
                                          required><?= old('description') ?></textarea>
                                <?php if (!empty($errors['description'])): ?>
                                    <div class="invalid-feedback small"><?= e($errors['description']) ?></div>
                                <?php else: ?>
                                    <div class="form-text xs-text">Minimal 10 karakter untuk menjelaskan jenis perbaikan.</div>
                                <?php endif; ?>
                            </div>

                            <div class="row g-3 mb-3">
                                <!-- Pilihan Ikon (Bootstrap Icons) -->
                                <div class="col-md-6">
                                    <label for="icon" class="form-label fw-semibold small text-dark">
                                        <i class="bi bi-stars text-primary me-1"></i> Ikon Layanan (Bootstrap Icon)
                                    </label>
                                    <select class="form-select rounded-3" id="icon" name="icon">
                                        <option value="bi-tools" <?= old('icon') === 'bi-tools' ? 'selected' : '' ?>>bi-tools (Peralatan Umum)</option>
                                        <option value="bi-lightning-charge-fill" <?= old('icon') === 'bi-lightning-charge-fill' ? 'selected' : '' ?>>bi-lightning-charge-fill (Listrik)</option>
                                        <option value="bi-snow" <?= old('icon') === 'bi-snow' ? 'selected' : '' ?>>bi-snow (AC / Pendingin)</option>
                                        <option value="bi-key-fill" <?= old('icon') === 'bi-key-fill' ? 'selected' : '' ?>>bi-key-fill (Tukang Kunci)</option>
                                        <option value="bi-tv-fill" <?= old('icon') === 'bi-tv-fill' ? 'selected' : '' ?>>bi-tv-fill (Elektronik & TV)</option>
                                        <option value="bi-droplet-fill" <?= old('icon') === 'bi-droplet-fill' ? 'selected' : '' ?>>bi-droplet-fill (Pipa & Sanitasi)</option>
                                        <option value="bi-paint-bucket" <?= old('icon') === 'bi-paint-bucket' ? 'selected' : '' ?>>bi-paint-bucket (Pengecatan)</option>
                                        <option value="bi-shield-check" <?= old('icon') === 'bi-shield-check' ? 'selected' : '' ?>>bi-shield-check (Keamanan Rumah)</option>
                                    </select>
                                </div>

                                <!-- Badge Tag -->
                                <div class="col-md-6">
                                    <label for="badge" class="form-label fw-semibold small text-dark">
                                        <i class="bi bi-bookmark-star-fill text-primary me-1"></i> Label Badge
                                    </label>
                                    <input type="text" 
                                           class="form-control rounded-3" 
                                           id="badge" 
                                           name="badge" 
                                           placeholder="Contoh: Populer, Baru, 24 Jam" 
                                           value="<?= old('badge', 'Layanan Baru') ?>">
                                </div>
                            </div>

                            <!-- Status Awal -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold small text-dark d-block mb-2">
                                    <i class="bi bi-toggle-on text-primary me-1"></i> Status Kategori
                                </label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="statusActive" value="active" <?= old('status', 'active') === 'active' ? 'checked' : '' ?>>
                                        <label class="form-check-label small fw-semibold text-success" for="statusActive">
                                            <i class="bi bi-check-circle-fill me-1"></i> Aktif (Dapat Dipesan)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="statusInactive" value="inactive" <?= old('status') === 'inactive' ? 'checked' : '' ?>>
                                        <label class="form-check-label small fw-semibold text-muted" for="statusInactive">
                                            <i class="bi bi-dash-circle-fill me-1"></i> Nonaktif (Disembunyikan)
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol Submit -->
                            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                                <a href="<?= base_url('categories') ?>" class="btn btn-light rounded-pill px-4 fw-semibold">Batal</a>
                                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                                    <i class="bi bi-check-lg me-1"></i> Simpan Kategori
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
