<div class="dashboard-wrapper py-4">
    <div class="container py-lg-2">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                
                <!-- Breadcrumb / Back button -->
                <div class="mb-3">
                    <?php 
                        $backUrl = ($user->role === 'admin') ? base_url('admin') : (($user->role === 'technician') ? base_url('technician') : base_url('customer'));
                    ?>
                    <a href="<?= $backUrl ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
                    </a>
                </div>

                <!-- Flash Messages -->
                <?php if (has_flash()): ?>
                    <?php $flash = get_flash(); ?>
                    <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show rounded-3 small py-2 d-flex align-items-center gap-2 mb-4" role="alert">
                        <i class="bi bi-check-circle-fill"></i>
                        <div><?= e($flash['message']) ?></div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
                    <div class="auth-header-accent"></div>
                    
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                            <div class="avatar-circle bg-primary text-white fs-4" style="width: 54px; height: 54px;">
                                <?= strtoupper(substr($user->name, 0, 1)) ?>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0"><?= e($user->name) ?></h4>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <span class="badge bg-<?= ($user->role === 'admin') ? 'danger' : (($user->role === 'technician') ? 'success' : 'primary') ?> rounded-pill px-3 py-1 small">
                                        Peran: <?= ucfirst($user->role) ?>
                                    </span>
                                    <span class="text-muted xs-text">&bull; ID: #<?= e($user->id) ?></span>
                                </div>
                            </div>
                        </div>

                        <form action="<?= base_url('profile') ?>" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-person-lines-fill text-primary me-2"></i>Informasi Dasar Akun</h6>
                            
                            <!-- Email (Read Only) -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-muted">Alamat Email (Akun)</label>
                                <input type="email" class="form-control bg-light" value="<?= e($user->email) ?>" readonly disabled>
                                <div class="form-text xs-text">Email digunakan untuk otentikasi login dan tidak dapat diubah sembarangan.</div>
                            </div>

                            <div class="row g-3 mb-4">
                                <!-- Name -->
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-semibold small text-dark">Nama Lengkap</label>
                                    <input type="text" 
                                           class="form-control <?= !empty($errors['name']) ? 'is-invalid' : '' ?>" 
                                           id="name" 
                                           name="name" 
                                           value="<?= old('name', $user->name) ?>" 
                                           required>
                                    <?php if (!empty($errors['name'])): ?>
                                        <div class="invalid-feedback small"><?= e($errors['name']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <!-- WhatsApp -->
                                <div class="col-md-6">
                                    <label for="whatsapp" class="form-label fw-semibold small text-dark d-flex justify-content-between">
                                        <span>Nomor WhatsApp <span class="badge bg-danger">WAJIB</span></span>
                                    </label>
                                    <input type="text" 
                                           class="form-control <?= !empty($errors['whatsapp']) ? 'is-invalid' : '' ?>" 
                                           id="whatsapp" 
                                           name="whatsapp" 
                                           value="<?= old('whatsapp', $user->whatsapp) ?>" 
                                           required>
                                    <?php if (!empty($errors['whatsapp'])): ?>
                                        <div class="invalid-feedback small"><?= e($errors['whatsapp']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Khusus Role Teknisi (Technician Profile Fields) -->
                            <?php if ($user->role === 'technician'): ?>
                                <hr class="my-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-bold text-success mb-0"><i class="bi bi-wrench-adjustable-circle-fill me-2"></i>Detail Spesialisasi & Jangkauan Layanan</h6>
                                    <span class="badge bg-warning text-dark small fw-bold">
                                        <i class="bi bi-star-fill text-dark me-1"></i> Rating: <?= number_format($techProfile->rating ?? 5.0, 1) ?>
                                    </span>
                                </div>

                                <!-- Category Dropdown (Dynamically loaded from categories table) -->
                                <div class="mb-3">
                                    <label for="category_id" class="form-label fw-semibold small text-dark">Kategori Spesialisasi Layanan</label>
                                    <select class="form-select" id="category_id" name="category_id">
                                        <?php if (!empty($categories)): ?>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?= $cat->id ?>" <?= (isset($techProfile->category_id) && $techProfile->category_id == $cat->id) ? 'selected' : '' ?>>
                                                    <?= e($cat->name) ?><?= (($cat->status ?? 'active') === 'inactive') ? ' (Nonaktif)' : '' ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <div class="form-text xs-text">Pilihan kategori diambil secara dinamis dari tabel master data kategori.</div>
                                </div>

                                <!-- Location -->
                                <div class="mb-3">
                                    <label for="location" class="form-label fw-semibold small text-dark">Area / Wilayah Jangkauan Layanan</label>
                                    <input type="text" 
                                           class="form-control <?= !empty($errors['location']) ? 'is-invalid' : '' ?>" 
                                           id="location" 
                                           name="location" 
                                           placeholder="Contoh: Jakarta Selatan, Depok, dan Tangerang Selatan" 
                                           value="<?= old('location', $techProfile->location ?? '') ?>" 
                                           required>
                                    <?php if (!empty($errors['location'])): ?>
                                        <div class="invalid-feedback small"><?= e($errors['location']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <!-- Description -->
                                <div class="mb-4">
                                    <label for="description" class="form-label fw-semibold small text-dark">Deskripsi Keahlian & Pengalaman Kerja</label>
                                    <textarea class="form-control <?= !empty($errors['description']) ? 'is-invalid' : '' ?>" 
                                              id="description" 
                                              name="description" 
                                              rows="4" 
                                              placeholder="Jelaskan jenis perbaikan yang biasa Anda tangani, sertifikasi, atau garansi yang Anda berikan..." 
                                              required><?= old('description', $techProfile->description ?? '') ?></textarea>
                                    <?php if (!empty($errors['description'])): ?>
                                        <div class="invalid-feedback small"><?= e($errors['description']) ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                                <a href="<?= $backUrl ?>" class="btn btn-light rounded-pill px-4 fw-semibold">Batal</a>
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
