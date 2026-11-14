<div class="auth-wrapper py-5">
    <div class="container py-lg-4">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9 col-sm-11">
                <div class="card auth-card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                    <div class="auth-header-accent"></div>
                    
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <img src="<?= asset('images/logo-bantuin-rounded.png') ?>?v=<?= time() ?>" alt="BANTUIN" width="56" height="56" class="rounded-3 shadow mb-3" style="width: 56px; height: 56px; object-fit: cover;">
                            <h3 class="fw-bold mb-1">Daftar Akun Baru</h3>
                            <p class="text-muted small">Bergabunglah sebagai Pelanggan atau Mitra Teknisi Profesional.</p>
                        </div>

                        <!-- Flash Messages -->
                        <?php if (has_flash()): ?>
                            <?php $flash = get_flash(); ?>
                            <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show rounded-3 small py-2 d-flex align-items-center gap-2" role="alert">
                                <i class="bi bi-info-circle-fill"></i>
                                <div><?= e($flash['message']) ?></div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <!-- General Error -->
                        <?php if (!empty($errors['general'])): ?>
                            <div class="alert alert-danger rounded-3 small py-2 d-flex align-items-center gap-2" role="alert">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <div><?= e($errors['general']) ?></div>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('register') ?>" method="POST" autocomplete="off">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            
                            <!-- Role Selection (Customer or Technician) -->
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-dark d-block mb-2">
                                    <i class="bi bi-person-badge text-primary me-1"></i> Pilih Peran Akun Anda:
                                </label>
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label class="role-selector-card d-block p-3 rounded-3 border cursor-pointer <?= old('role', 'customer') === 'customer' ? 'selected' : '' ?>">
                                            <input type="radio" name="role" value="customer" class="d-none role-radio" <?= old('role', 'customer') === 'customer' ? 'checked' : '' ?>>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="role-icon-box role-icon-customer">
                                                    <i class="bi bi-person-fill fs-4"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark fs-6">Pelanggan</div>
                                                    <div class="text-muted xs-text">Pesan teknisi untuk rumah</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="role-selector-card d-block p-3 rounded-3 border cursor-pointer <?= old('role') === 'technician' ? 'selected' : '' ?>">
                                            <input type="radio" name="role" value="technician" class="d-none role-radio" <?= old('role') === 'technician' ? 'checked' : '' ?>>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="role-icon-box role-icon-technician">
                                                    <i class="bi bi-wrench-adjustable-circle-fill fs-4"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark fs-6">Mitra Teknisi</div>
                                                    <div class="text-muted xs-text">Terima order pengerjaan</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <?php if (!empty($errors['role'])): ?>
                                    <div class="text-danger small mt-1"><?= e($errors['role']) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="row g-3 mb-3">
                                <!-- Full Name -->
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-semibold small text-dark">
                                        <i class="bi bi-person me-1 text-primary"></i> Nama Lengkap
                                    </label>
                                    <input type="text" 
                                           class="form-control rounded-3 <?= !empty($errors['name']) ? 'is-invalid' : '' ?>" 
                                           id="name" 
                                           name="name" 
                                           placeholder="Contoh: Budi Santoso" 
                                           value="<?= old('name') ?>" 
                                           required>
                                    <?php if (!empty($errors['name'])): ?>
                                        <div class="invalid-feedback small"><?= e($errors['name']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold small text-dark">
                                        <i class="bi bi-envelope me-1 text-primary"></i> Alamat Email
                                    </label>
                                    <input type="email" 
                                           class="form-control rounded-3 <?= !empty($errors['email']) ? 'is-invalid' : '' ?>" 
                                           id="email" 
                                           name="email" 
                                           placeholder="nama@email.com" 
                                           value="<?= old('email') ?>" 
                                           required>
                                    <div class="form-text xs-text text-muted mt-1">
                                        <i class="bi bi-shield-check text-primary me-1"></i> Notifikasi kode OTP verifikasi akan dikirimkan ke email ini.
                                    </div>
                                    <?php if (!empty($errors['email'])): ?>
                                        <div class="invalid-feedback small"><?= e($errors['email']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- WhatsApp (MANDATORY) -->
                            <div class="mb-3">
                                <label for="whatsapp" class="form-label fw-semibold small text-dark d-flex justify-content-between">
                                    <span><i class="bi bi-whatsapp me-1 text-success"></i> Nomor WhatsApp <span class="badge bg-danger ms-1">WAJIB</span></span>
                                    <span class="text-muted xs-text">Digunakan untuk fitur Go to WhatsApp</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted small fw-semibold">+62 / 08</span>
                                    <input type="text" 
                                           class="form-control rounded-end-3 <?= !empty($errors['whatsapp']) ? 'is-invalid' : '' ?>" 
                                           id="whatsapp" 
                                           name="whatsapp" 
                                           placeholder="081234567890" 
                                           value="<?= old('whatsapp') ?>" 
                                           required>
                                    <?php if (!empty($errors['whatsapp'])): ?>
                                        <div class="invalid-feedback small"><?= e($errors['whatsapp']) ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="form-text xs-text text-muted">
                                    Pastikan nomor WhatsApp aktif untuk komunikasi langsung antara Teknisi dan Pelanggan.
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <!-- Password -->
                                <div class="col-md-6">
                                    <label for="password" class="form-label fw-semibold small text-dark">
                                        <i class="bi bi-key me-1 text-primary"></i> Kata Sandi
                                    </label>
                                    <input type="password" 
                                           class="form-control rounded-3 <?= !empty($errors['password']) ? 'is-invalid' : '' ?>" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Minimal 6 karakter" 
                                           required>
                                    <?php if (!empty($errors['password'])): ?>
                                        <div class="invalid-feedback small"><?= e($errors['password']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <!-- Password Confirmation -->
                                <div class="col-md-6">
                                    <label for="password_confirm" class="form-label fw-semibold small text-dark">
                                        <i class="bi bi-check2-circle me-1 text-primary"></i> Konfirmasi Sandi
                                    </label>
                                    <input type="password" 
                                           class="form-control rounded-3 <?= !empty($errors['password_confirm']) ? 'is-invalid' : '' ?>" 
                                           id="password_confirm" 
                                           name="password_confirm" 
                                           placeholder="Ulangi kata sandi" 
                                           required>
                                    <?php if (!empty($errors['password_confirm'])): ?>
                                        <div class="invalid-feedback small"><?= e($errors['password_confirm']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow-sm mb-3 d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-envelope-check"></i>
                                <span>Daftar & Kirim Kode OTP ke Email</span>
                            </button>

                            <div class="text-center small text-muted">
                                Sudah memiliki akun? <a href="<?= base_url('login') ?>" class="text-primary fw-bold text-decoration-none">Masuk di sini</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
