<div class="auth-wrapper py-5">
    <div class="container py-lg-4">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-8 col-sm-10">
                <div class="card auth-card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                    <div class="auth-header-accent" style="height: 5px; background: linear-gradient(90deg, #0d6efd, #0dcaf0);"></div>
                    
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle mb-3" style="width: 64px; height: 64px;">
                                <i class="bi bi-envelope-check-fill fs-1"></i>
                            </div>
                            <h3 class="fw-bold mb-1">Verifikasi Email Anda</h3>
                            <p class="text-muted small mb-1">
                                Kode verifikasi 6 digit telah dikirimkan ke:
                            </p>
                            <div class="fw-bold text-dark fs-6">
                                <i class="bi bi-envelope-at text-primary me-1"></i>
                                <?= e(EmailService::maskEmail($pending['email'] ?? '')) ?>
                            </div>
                            <div class="mt-2">
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 small">
                                    Daftar sebagai: <?= ($pending['role'] ?? 'customer') === 'technician' ? 'Mitra Teknisi' : 'Pelanggan' ?>
                                </span>
                            </div>
                        </div>

                        <!-- Flash Messages -->
                        <?php if (has_flash()): ?>
                            <?php $flash = get_flash(); ?>
                            <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show rounded-3 small py-2 d-flex align-items-center gap-2 mb-3" role="alert">
                                <i class="bi bi-info-circle-fill"></i>
                                <div><?= e($flash['message']) ?></div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Errors -->
                        <?php if (!empty($errors['general'])): ?>
                            <div class="alert alert-danger rounded-3 small py-2 d-flex align-items-center gap-2 mb-3" role="alert">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <div><?= e($errors['general']) ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($errors['otp'])): ?>
                            <div class="alert alert-danger rounded-3 small py-2 d-flex align-items-center gap-2 mb-3" role="alert">
                                <i class="bi bi-exclamation-circle-fill"></i>
                                <div><?= e($errors['otp']) ?></div>
                            </div>
                        <?php endif; ?>

                        <!-- Help Notice -->
                        <div class="alert alert-info-subtle border-0 rounded-3 p-3 mb-4 d-flex align-items-start gap-2">
                            <i class="bi bi-info-circle-fill text-info mt-1"></i>
                            <div class="small text-dark">
                                Silakan buka aplikasi <strong>Gmail / Email</strong> Anda, lalu salin 6 digit kode OTP yang diterima ke dalam kotak di bawah ini. Cek juga folder <em>Spam / Promosi</em> jika belum muncul.
                            </div>
                        </div>

                        <!-- OTP Submission Form -->
                        <form action="<?= base_url('verify-otp') ?>" method="POST" id="otpForm" autocomplete="off">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <input type="hidden" name="otp" id="combinedOtp" value="">

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-dark d-block text-center mb-3">
                                    Masukkan 6 Digit Kode OTP:
                                </label>
                                
                                <!-- 6-Digit Grid Inputs -->
                                <div class="d-flex justify-content-center gap-2 mb-2" id="otpBoxContainer">
                                    <?php for ($i = 0; $i < 6; $i++): ?>
                                        <input type="text" 
                                               maxlength="1" 
                                               inputmode="numeric" 
                                               pattern="[0-9]*" 
                                               class="form-control text-center fs-3 fw-bold font-monospace rounded-3 border-2 otp-digit" 
                                               style="width: 48px; height: 56px; border-color: #dee2e6;"
                                               data-index="<?= $i ?>"
                                               autocomplete="one-time-code"
                                               required>
                                    <?php endfor; ?>
                                </div>
                                <div class="text-center text-muted small mt-2">
                                    <i class="bi bi-clock-history me-1"></i> Kode berlaku selama 5 menit
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3 fw-semibold shadow-sm mb-3 d-flex align-items-center justify-content-center gap-2" id="submitBtn">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Verifikasi & Aktifkan Akun</span>
                            </button>
                        </form>

                        <!-- Resend & Change Email Section -->
                        <div class="text-center pt-2 border-top">
                            <p class="small text-muted mb-2">Belum menerima email?</p>
                            <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                                <a href="<?= base_url('resend-otp') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1" id="resendBtn">
                                    <i class="bi bi-arrow-clockwise"></i> Kirim Ulang Email
                                </a>
                                <span class="text-muted small" id="timerContainer">
                                    (bisa klik lagi dalam <span id="timerText" class="fw-bold text-dark">60</span> detik)
                                </span>
                            </div>

                            <div class="mt-3">
                                <a href="<?= base_url('cancel-register') ?>" class="text-decoration-none small text-muted hover-primary" onclick="return confirm('Apakah Anda ingin membatalkan pendaftaran ini dan mengubah data?')">
                                    <i class="bi bi-arrow-left me-1"></i> Ubah Email / Data Pendaftaran
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const digits = Array.from(document.querySelectorAll('.otp-digit'));
    const combinedOtpInput = document.getElementById('combinedOtp');
    const otpForm = document.getElementById('otpForm');

    // Auto-focus first input
    if (digits[0]) {
        digits[0].focus();
    }

    // Input handlers
    digits.forEach((input, index) => {
        // Prevent non-digits
        input.addEventListener('input', function (e) {
            this.value = this.value.replace(/[^0-9]/g, '');

            if (this.value.length === 1) {
                this.classList.add('border-primary', 'bg-light');
                if (index < digits.length - 1) {
                    digits[index + 1].focus();
                }
            } else {
                this.classList.remove('border-primary', 'bg-light');
            }
            updateCombined();
        });

        // Handle Backspace navigation
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace') {
                if (this.value === '' && index > 0) {
                    digits[index - 1].focus();
                    digits[index - 1].value = '';
                    digits[index - 1].classList.remove('border-primary', 'bg-light');
                } else {
                    this.value = '';
                    this.classList.remove('border-primary', 'bg-light');
                }
                updateCombined();
            } else if (e.key === 'ArrowLeft' && index > 0) {
                digits[index - 1].focus();
            } else if (e.key === 'ArrowRight' && index < digits.length - 1) {
                digits[index + 1].focus();
            }
        });

        // Handle Paste (auto distribute 6 characters)
        input.addEventListener('paste', function (e) {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
            if (pasted.length > 0) {
                for (let i = 0; i < digits.length; i++) {
                    if (pasted[i]) {
                        digits[i].value = pasted[i];
                        digits[i].classList.add('border-primary', 'bg-light');
                    }
                }
                const focusIdx = Math.min(pasted.length, digits.length - 1);
                digits[focusIdx].focus();
                updateCombined();
            }
        });
    });

    function updateCombined() {
        const fullOtp = digits.map(d => d.value).join('');
        combinedOtpInput.value = fullOtp;
    }

    // Submit handler
    otpForm.addEventListener('submit', function (e) {
        updateCombined();
        if (combinedOtpInput.value.length !== 6) {
            e.preventDefault();
            alert('Silakan masukkan lengkap 6 digit kode OTP verifikasi.');
            const emptyIdx = digits.findIndex(d => d.value === '');
            if (emptyIdx !== -1) {
                digits[emptyIdx].focus();
            }
        }
    });

    // Countdown Timer for Resend Button (60 seconds)
    const resendBtn = document.getElementById('resendBtn');
    const timerText = document.getElementById('timerText');
    const timerContainer = document.getElementById('timerContainer');
    let timeLeft = 60;

    resendBtn.classList.add('disabled');
    resendBtn.style.pointerEvents = 'none';
    resendBtn.style.opacity = '0.5';

    const countdownInterval = setInterval(function () {
        timeLeft--;
        if (timeLeft <= 0) {
            clearInterval(countdownInterval);
            resendBtn.classList.remove('disabled');
            resendBtn.style.pointerEvents = 'auto';
            resendBtn.style.opacity = '1';
            if (timerContainer) {
                timerContainer.style.display = 'none';
            }
        } else {
            if (timerText) {
                timerText.textContent = timeLeft;
            }
        }
    }, 1000);
});
</script>
