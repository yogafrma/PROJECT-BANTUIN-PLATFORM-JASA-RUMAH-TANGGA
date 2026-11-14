<div class="auth-wrapper">
    <div class="container py-0 my-0">
        
        <!-- Top Navigation Bar: Return to Home (Matching Reference) -->
        <div class="auth-top-bar">
            <a href="<?= base_url() ?>" class="auth-back-btn">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali ke Beranda</span>
            </a>
            <div class="text-secondary small d-none d-sm-flex align-items-center gap-2">
                <span class="fluent-pulse-dot"></span>
                <span>BANTUIN ID Single Sign-On</span>
            </div>
        </div>

        <!-- Main Unified Dual-Pane Split Card -->
        <div class="auth-split-card">
            <div class="row g-0 align-items-stretch">
                
                <!-- ============================================================
                     Left Showcase Pane: Dark Branded Vector Illustration Pane
                     (Inspired by user's reference model: dark background,
                     BANTUIN logo, animated vector technician, status pill)
                     ============================================================ -->
                <div class="col-lg-6 d-none d-lg-flex flex-column justify-content-between auth-pane-showcase">
                    
                    <!-- Top Logo & Brand Identity -->
                    <div class="auth-pane-brand">
                        <img src="<?= asset('images/logo-bantuin-rounded.png') ?>?v=<?= time() ?>" 
                             alt="BANTUIN" 
                             width="38" 
                             height="38" 
                             class="rounded-3 shadow-sm"
                             style="width: 38px; height: 38px; object-fit: cover;">
                        <div>
                            <h4 class="auth-pane-brand-title">BANTU<span style="color: #38BDF8;">IN</span></h4>
                            <p class="auth-pane-brand-tagline">Platform Jasa Perbaikan Rumah Tangga</p>
                        </div>
                    </div>

                    <!-- Center: Animated Modern Vector Technician -->
                    <div class="auth-pane-graphic my-auto py-2">
                        <svg class="auth-tech-vector" viewBox="0 0 320 360" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="Ilustrasi Teknisi BANTUIN">
                            <defs>
                                <radialGradient id="techAuraGlow" cx="50%" cy="50%" r="50%">
                                    <stop offset="0%" stop-color="#0078D4" stop-opacity="0.32" />
                                    <stop offset="70%" stop-color="#0078D4" stop-opacity="0.08" />
                                    <stop offset="100%" stop-color="#0078D4" stop-opacity="0" />
                                </radialGradient>
                                <linearGradient id="techJacketGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#0E2E50" />
                                    <stop offset="100%" stop-color="#06182B" />
                                </linearGradient>
                                <linearGradient id="techVestGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#0078D4" />
                                    <stop offset="100%" stop-color="#005A9E" />
                                </linearGradient>
                                <linearGradient id="techHelmetGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" stop-color="#FBBF24" />
                                    <stop offset="100%" stop-color="#D97706" />
                                </linearGradient>
                                <linearGradient id="techTabletScreen" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#0284C7" />
                                    <stop offset="100%" stop-color="#0369A1" />
                                </linearGradient>
                            </defs>

                            <!-- Background Ambient Aura Glow -->
                            <circle cx="160" cy="180" r="130" fill="url(#techAuraGlow)" />

                            <!-- Animated Sparkles / Twinkling Stars (Around Technician) -->
                            <!-- Star 1 (Top Right - Bright Cyan) -->
                            <g class="auth-sparkle-1" transform="translate(255, 75)">
                                <path d="M0 -12 C0 -3 3 0 12 0 C3 0 0 3 0 12 C0 3 -3 0 -12 0 C-3 0 0 -3 0 -12 Z" fill="#38BDF8" opacity="0.95" />
                            </g>
                            <!-- Star 2 (Top Left - Warm Gold) -->
                            <g class="auth-sparkle-2" transform="translate(65, 110)">
                                <path d="M0 -10 C0 -2.5 2.5 0 10 0 C2.5 0 0 2.5 0 10 C0 2.5 -2.5 0 -10 0 C-2.5 0 0 -2.5 0 -10 Z" fill="#FBBF24" opacity="0.9" />
                            </g>
                            <!-- Star 3 (Bottom Left - Azure Blue) -->
                            <g class="auth-sparkle-3" transform="translate(60, 245)">
                                <path d="M0 -8 C0 -2 2 0 8 0 C2 0 0 2 0 8 C0 2 -2 0 -8 0 C-2 0 0 -2 0 -8 Z" fill="#60A5FA" opacity="0.85" />
                            </g>
                            <!-- Star 4 (Bottom Right - Golden Spark) -->
                            <g class="auth-sparkle-1" transform="translate(265, 235)">
                                <path d="M0 -9 C0 -2.2 2.2 0 9 0 C2.2 0 0 2.2 0 9 C0 2.2 -2.2 0 -9 0 C-2.2 0 0 -2.2 0 -9 Z" fill="#FDE047" opacity="0.9" />
                            </g>

                            <!-- Ground Pedestal Breathing Shadow -->
                            <ellipse class="auth-anim-shadow" cx="160" cy="336" rx="80" ry="12" fill="#021422" opacity="0.45" />

                            <!-- Floating Technician Character Group -->
                            <g class="auth-anim-float">
                                <!-- Legs & Boots -->
                                <rect x="136" y="240" width="21" height="66" rx="5" fill="#1E293B" />
                                <rect x="163" y="240" width="21" height="66" rx="5" fill="#1E293B" />
                                
                                <!-- Boots -->
                                <path d="M130 300 H157 C159 300 160 304 160 310 L160 315 C160 318 158 320 155 320 H128 C125 320 124 317 125 314 L127 304 C128 301 129 300 130 300 Z" fill="#0F172A" />
                                <rect x="125" y="316" width="35" height="4" rx="2" fill="#334155" />
                                
                                <path d="M163 300 H190 C192 300 193 301 194 304 L196 314 C197 317 195 320 192 320 H165 C162 320 160 318 160 315 L160 310 C160 304 161 300 163 300 Z" fill="#0F172A" />
                                <rect x="160" y="316" width="36" height="4" rx="2" fill="#334155" />

                                <!-- Belt & Buckle -->
                                <rect x="131" y="233" width="58" height="9" rx="3" fill="#0F172A" />
                                <rect x="155" y="231" width="10" height="13" rx="2" fill="#CBD5E1" stroke="#94A3B8" stroke-width="1.5" />

                                <!-- Torso: Jacket & Service Vest -->
                                <path d="M125 155 Q160 148 195 155 L189 235 Q160 238 131 235 Z" fill="url(#techJacketGrad)" />
                                
                                <!-- Blue Vest Panels -->
                                <path d="M135 153 L133 234 L151 234 L148 152 Z" fill="url(#techVestGrad)" />
                                <path d="M185 153 L187 234 L169 234 L172 152 Z" fill="url(#techVestGrad)" />
                                
                                <!-- High-Vis Safety Reflective Strips -->
                                <rect x="133" y="196" width="54" height="6" fill="#F8FAFC" opacity="0.85" rx="1" />
                                <rect x="133" y="211" width="54" height="5" fill="#38BDF8" opacity="0.95" rx="1" />

                                <!-- Shirt Collar & Lanyard -->
                                <polygon points="152,150 160,170 168,150" fill="#F0F9FF" />
                                <line x1="160" y1="170" x2="160" y2="194" stroke="#0284C7" stroke-width="2.5" />
                                <!-- ID Badge -->
                                <rect x="154" y="194" width="12" height="15" rx="2" fill="#FFFFFF" stroke="#CBD5E1" stroke-width="1" />
                                <rect x="156" y="196" width="8" height="5" rx="1" fill="#0078D4" />

                                <!-- Left Arm Holding Digital Diagnostic Tablet -->
                                <path d="M127 158 L104 200 L118 206 L137 168 Z" fill="url(#techJacketGrad)" />
                                <path d="M104 200 L112 245 L128 240 L118 206 Z" fill="url(#techJacketGrad)" />
                                <circle cx="118" cy="245" r="9" fill="#FDBA74" />
                                <!-- Tablet Device -->
                                <g transform="rotate(-8 95 240)">
                                    <rect x="70" y="210" width="50" height="64" rx="6" fill="#0F172A" stroke="#334155" stroke-width="2" />
                                    <rect x="74" y="214" width="42" height="56" rx="4" fill="url(#techTabletScreen)" />
                                    <!-- Screen Repair Icons -->
                                    <circle cx="85" cy="226" r="4" fill="#38BDF8" />
                                    <rect x="93" y="224" width="17" height="3" rx="1.5" fill="#FFFFFF" />
                                    <circle cx="85" cy="239" r="4" fill="#34D399" />
                                    <rect x="93" y="237" width="17" height="3" rx="1.5" fill="#FFFFFF" />
                                    <circle cx="85" cy="252" r="4" fill="#FBBF24" />
                                    <rect x="93" y="250" width="17" height="3" rx="1.5" fill="#FFFFFF" />
                                </g>

                                <!-- Right Arm: Confident Posture with Chrome Wrench -->
                                <path d="M193 158 L216 196 L202 204 L183 168 Z" fill="url(#techJacketGrad)" />
                                <path d="M216 196 L226 166 L240 171 L226 204 Z" fill="url(#techJacketGrad)" />
                                <circle cx="234" cy="164" r="9" fill="#FDBA74" />
                                <!-- Modern Chrome Wrench -->
                                <path d="M239 135 L244 140 L228 180 L223 176 Z" fill="#94A3B8" stroke="#64748B" stroke-width="1.5" />
                                <circle cx="241" cy="138" r="6" fill="none" stroke="#94A3B8" stroke-width="3" />

                                <!-- Head, Neck & Friendly Profile -->
                                <rect x="152" y="136" width="16" height="18" fill="#FDBA74" />
                                <path d="M143 115 C143 135 150 148 160 148 C170 148 177 135 177 115 C177 105 172 100 160 100 C148 100 143 105 143 115 Z" fill="#FDBA74" />
                                <!-- Ears -->
                                <circle cx="142" cy="120" r="5" fill="#FDBA74" />
                                <circle cx="178" cy="120" r="5" fill="#FDBA74" />
                                <!-- Smile -->
                                <path d="M153 133 Q160 139 167 133" stroke="#9A3412" stroke-width="2.2" stroke-linecap="round" fill="none" />
                                <!-- Eyes -->
                                <circle cx="152" cy="120" r="2.8" fill="#0F172A" />
                                <circle cx="153" cy="119" r="0.9" fill="#FFFFFF" />
                                <circle cx="168" cy="120" r="2.8" fill="#0F172A" />
                                <circle cx="169" cy="119" r="0.9" fill="#FFFFFF" />
                                <!-- Eyebrows -->
                                <path d="M149 114 Q153 112 155 114" stroke="#0F172A" stroke-width="1.8" stroke-linecap="round" fill="none" />
                                <path d="M165 114 Q167 112 171 114" stroke="#0F172A" stroke-width="1.8" stroke-linecap="round" fill="none" />

                                <!-- Professional Safety Helmet -->
                                <path d="M136 106 C136 78 184 78 184 106 Z" fill="url(#techHelmetGrad)" />
                                <path d="M130 106 C130 103 134 103 140 103 H180 C186 103 190 103 190 106 C190 109 186 111 180 111 H140 C134 111 130 109 130 106 Z" fill="#D97706" />
                                <!-- Helmet Gloss -->
                                <path d="M144 86 C152 82 168 82 175 86" stroke="#FEF08A" stroke-width="2.5" stroke-linecap="round" opacity="0.75" fill="none" />
                                <!-- BANTUIN Crest on Helmet -->
                                <polygon points="160,89 165,93 163,100 160,102 157,100 155,93" fill="#0078D4" stroke="#FFFFFF" stroke-width="0.8" />
                            </g>
                        </svg>
                    </div>

                    <!-- Bottom Status Pill (Clean match to reference layout) -->
                    <div class="text-center pt-1">
                        <div class="auth-pane-pill">
                            <span class="fluent-pulse-dot" style="background: #10B981; box-shadow: 0 0 10px #10B981;"></span>
                            <span>Siaga 24 Jam &bull; Mitra Terverifikasi</span>
                        </div>
                    </div>

                </div>

                <!-- ============================================================
                     Right Form Pane: Clean, Ergonomic, Spacious BANTUIN Form
                     ============================================================ -->
                <div class="col-lg-6 d-flex align-items-center auth-pane-form">
                    <div class="w-100 p-2 p-md-3">
                        
                        <!-- Mobile Brand Logo (Visible only when left pane is hidden) -->
                        <div class="d-lg-none text-center mb-3">
                            <img src="<?= asset('images/logo-bantuin-rounded.png') ?>?v=<?= time() ?>" 
                                 alt="BANTUIN" 
                                 width="46" 
                                 height="46" 
                                 class="rounded-3 shadow-sm mb-2" 
                                 style="width: 46px; height: 46px; object-fit: cover;">
                        </div>

                        <!-- Header Branding & Welcome -->
                        <div class="mb-3">
                            <h2 class="auth-form-title">Masuk ke Akun</h2>
                            <p class="auth-form-subtitle">Kelola kebutuhan perbaikan rumah tangga Anda di BANTUIN.</p>
                        </div>

                        <!-- Flash Messages -->
                        <?php if (has_flash()): ?>
                            <?php $flash = get_flash(); ?>
                            <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show rounded-3 small py-2 d-flex align-items-center gap-2 mb-3" role="alert">
                                <i class="bi bi-info-circle-fill text-primary"></i>
                                <div><?= e($flash['message']) ?></div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <!-- General Error -->
                        <?php if (!empty($errors['general'])): ?>
                            <div class="alert alert-danger rounded-3 small py-2 d-flex align-items-center gap-2 mb-3" role="alert">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <div><?= e($errors['general']) ?></div>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('login') ?>" method="POST" autocomplete="off">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            
                            <!-- Email Field -->
                            <div class="mb-2" style="margin-bottom: 0.65rem !important;">
                                <label for="email" class="form-label">
                                    <i class="bi bi-envelope text-primary"></i> Alamat Email
                                </label>
                                <input type="email" 
                                       class="form-control <?= !empty($errors['email']) ? 'is-invalid' : '' ?>" 
                                       id="email" 
                                       name="email" 
                                       placeholder="nama@email.com" 
                                       value="<?= old('email') ?>" 
                                       required autofocus>
                                <?php if (!empty($errors['email'])): ?>
                                    <div class="invalid-feedback small"><?= e($errors['email']) ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Password Field with View Password Toggle -->
                            <div class="mb-2" style="margin-bottom: 0.65rem !important;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="password" class="form-label mb-0">
                                        <i class="bi bi-key text-primary"></i> Kata Sandi
                                    </label>
                                </div>
                                <div class="auth-input-group">
                                    <input type="password" 
                                           class="form-control <?= !empty($errors['password']) ? 'is-invalid' : '' ?>" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Masukkan kata sandi" 
                                           required>
                                    <button type="button" class="auth-toggle-pwd" id="togglePasswordBtn" aria-label="Lihat kata sandi">
                                        <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                    </button>
                                </div>
                                <?php if (!empty($errors['password'])): ?>
                                    <div class="invalid-feedback d-block small"><?= e($errors['password']) ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Remember Me Option -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="rememberMe" name="remember" value="1">
                                    <label class="form-check-label text-secondary small user-select-none" for="rememberMe">
                                        Ingat saya
                                    </label>
                                </div>
                            </div>

                            <!-- Submit Button: Fluent Blue (Full Width matching inputs above) -->
                            <button type="submit" class="auth-btn-primary w-100 mb-2">
                                <span>Masuk Sekarang</span>
                                <i class="bi bi-arrow-right fs-5"></i>
                            </button>

                            <div class="text-center small text-secondary mt-2">
                                Belum punya akun? <a href="<?= base_url('register') ?>" class="text-primary fw-bold text-decoration-none">Daftar di sini</a>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>

        <!-- Subtle Security Guarantee Note -->
        <div class="text-center mt-2 text-secondary small d-flex align-items-center justify-content-center gap-2" style="font-size: 0.82rem; opacity: 0.85;">
            <i class="bi bi-shield-check text-primary"></i>
            <span>Terlindungi dengan Enkripsi SSL &bull; BANTUIN ID Official</span>
        </div>

    </div>
</div>

<!-- Password Visibility Toggle Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('togglePasswordBtn');
    const pwdInput = document.getElementById('password');
    const toggleIcon = document.getElementById('togglePasswordIcon');
    if (toggleBtn && pwdInput && toggleIcon) {
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                pwdInput.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
        });
    }
});
</script>
