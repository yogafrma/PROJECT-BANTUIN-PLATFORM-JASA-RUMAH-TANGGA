<div class="dashboard-wrapper py-4">
    <div class="container py-lg-2">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                
                <!-- Breadcrumb & Back -->
                <div class="mb-3">
                    <a href="<?= base_url('posts/detail/' . $post->id) ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Detail Postingan
                    </a>
                </div>

                <!-- Card Form -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="auth-header-accent"></div>
                    
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                            <div class="brand-icon-box shadow-sm">
                                <i class="bi bi-pencil-square text-white fs-4"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0">Edit Postingan Keluhan</h4>
                                <p class="text-muted small mb-0">Perbarui informasi postingan agar teknisi mendapatkan konteks yang jelas.</p>
                            </div>
                        </div>

                        <!-- General Error -->
                        <?php if (!empty($errors['general'])): ?>
                            <div class="alert alert-danger rounded-3 small py-2 d-flex align-items-center gap-2 mb-4" role="alert">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <div><?= e($errors['general']) ?></div>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('posts/edit/' . $post->id) ?>" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            
                            <!-- Judul Postingan -->
                            <div class="mb-3">
                                <label for="title" class="form-label fw-semibold small text-dark">
                                    <i class="bi bi-card-heading text-primary me-1"></i> Judul Postingan <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control form-control-lg rounded-3 fs-6 <?= !empty($errors['title']) ? 'is-invalid' : '' ?>" 
                                       id="title" 
                                       name="title" 
                                       value="<?= old('title', $post->title) ?>" 
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
                                            <option value="<?= $cat->id ?>" <?= old('category_id', $post->category_id) == $cat->id ? 'selected' : '' ?>>
                                                <?= e($cat->name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Deskripsi Keluhan -->
                            <div class="mb-4">
                                <label for="description" class="form-label fw-semibold small text-dark">
                                    <i class="bi bi-card-text text-primary me-1"></i> Deskripsi & Detail Kerusakan <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control rounded-3 <?= !empty($errors['description']) ? 'is-invalid' : '' ?>" 
                                          id="description" 
                                          name="description" 
                                          rows="5" 
                                          required><?= old('description', $post->description) ?></textarea>
                                <?php if (!empty($errors['description'])): ?>
                                    <div class="invalid-feedback small"><?= e($errors['description']) ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Foto Kerusakan -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold small text-dark d-block">
                                    <i class="bi bi-camera-fill text-primary me-1"></i> Foto Kerusakan
                                </label>
                                
                                <?php if (!empty($post->image)): ?>
                                    <div class="mb-2 d-flex align-items-center gap-3 p-2 bg-light rounded-3 border">
                                        <img src="<?= asset($post->image) ?>" alt="Foto Kerusakan" class="rounded-2 border" style="width: 70px; height: 70px; object-fit: cover;">
                                        <div>
                                            <div class="small fw-semibold text-dark">Foto saat ini terpasang</div>
                                            <div class="text-muted xs-text">Pilih file baru di bawah jika ingin mengganti foto.</div>
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
                                <?php endif; ?>
                            </div>

                            <!-- Tombol Submit -->
                            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                                <a href="<?= base_url('posts/detail/' . $post->id) ?>" class="btn btn-light rounded-pill px-4 fw-semibold">Batal</a>
                                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                                    <i class="bi bi-save me-1"></i> Simpan Perubahan
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
