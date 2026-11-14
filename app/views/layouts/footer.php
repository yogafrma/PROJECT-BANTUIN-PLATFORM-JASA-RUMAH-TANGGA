    <?php if (isset($currentPage) && $currentPage === 'home'): ?>
    <!-- Footer (Khusus Landing Page Sesuai Permintaan) -->
    <footer class="footer-bantuin pt-5 pb-4 mt-5">
        <div class="container">
            <div class="row g-4 mb-4">
                <!-- Brand Info -->
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <img src="<?= asset('images/logo-bantuin-rounded.png') ?>?v=<?= time() ?>" alt="BANTUIN" width="40" height="40" style="width: 40px; height: 40px; object-fit: cover; border-radius: 10px; border: 1px solid rgba(0, 103, 184, 0.2); box-shadow: 0 2px 8px rgba(0, 103, 184, 0.12);">
                        <h5 class="fw-bold mb-0 footer-brand-title">BANTU<span class="footer-brand-accent">IN</span></h5>
                    </div>
                    <p class="footer-desc small pe-lg-3">
                        BANTUIN – Platform Jasa Rumah Tangga: solusi cepat, terpercaya, dan profesional untuk semua kebutuhan perbaikan rumah Anda.
                    </p>
                    <div class="d-flex gap-2 flex-wrap">
                        <span class="badge rounded-pill px-3 py-2 small footer-badge-customer"><i class="bi bi-person-check me-1"></i> Customer</span>
                        <span class="badge rounded-pill px-3 py-2 small footer-badge-tech"><i class="bi bi-wrench-adjustable me-1"></i> Teknisi</span>
                        <span class="badge rounded-pill px-3 py-2 small footer-badge-admin"><i class="bi bi-shield-lock me-1"></i> Admin</span>
                    </div>
                </div>

                <!-- Kategori MVP -->
                <div class="col-lg-3 col-md-6">
                    <h6 class="footer-heading fw-bold mb-3"><i class="bi bi-grid-fill me-2"></i>Kategori Layanan</h6>
                    <ul class="list-unstyled small footer-links mb-0">
                        <li class="mb-2"><a href="#kategori-layanan"><i class="bi bi-chevron-right me-1"></i> Teknisi Listrik</a></li>
                        <li class="mb-2"><a href="#kategori-layanan"><i class="bi bi-chevron-right me-1"></i> Teknisi AC & Pendingin</a></li>
                        <li class="mb-2"><a href="#kategori-layanan"><i class="bi bi-chevron-right me-1"></i> Tukang Kunci 24 Jam</a></li>
                        <li class="mb-2"><a href="#kategori-layanan"><i class="bi bi-chevron-right me-1"></i> Teknisi Elektronik Rumah</a></li>
                    </ul>
                </div>

                <!-- Akses Cepat & Navigasi -->
                <div class="col-lg-2 col-md-6">
                    <h6 class="footer-heading fw-bold mb-3"><i class="bi bi-link-45deg me-2"></i>Akses Cepat</h6>
                    <ul class="list-unstyled small footer-links mb-0">
                        <li class="mb-2"><a href="<?= base_url() ?>#beranda"><i class="bi bi-chevron-right me-1"></i> Beranda</a></li>
                        <li class="mb-2"><a href="#kategori-layanan"><i class="bi bi-chevron-right me-1"></i> Layanan</a></li>
                        <li class="mb-2"><a href="#cara-kerja"><i class="bi bi-chevron-right me-1"></i> Cara Pesan</a></li>
                        <li class="mb-2"><a href="#keunggulan"><i class="bi bi-chevron-right me-1"></i> Keunggulan</a></li>
                        <li class="mb-2"><a href="#bantuan"><i class="bi bi-chevron-right me-1"></i> Tanya Jawab</a></li>
                    </ul>
                </div>

                <!-- System & Tech Specs -->
                <div class="col-lg-3 col-md-6">
                    <h6 class="footer-heading fw-bold mb-3"><i class="bi bi-cpu-fill me-2"></i>Status Sistem</h6>
                    <div class="footer-system-card p-3 rounded-4 small">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="footer-spec-label">Arsitektur:</span>
                            <span class="badge rounded-pill px-2.5 py-1 footer-badge-arch">Custom PHP MVC</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="footer-spec-label">Database Engine:</span>
                            <span class="badge rounded-pill px-2.5 py-1 footer-badge-db">MySQL / PDO</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="footer-spec-label">Status Koneksi DB:</span>
                            <?php if (!empty($isDbConnected)): ?>
                                <span class="badge rounded-pill px-2.5 py-1 footer-badge-connected"><i class="bi bi-check-circle-fill me-1"></i> Terhubung</span>
                            <?php else: ?>
                                <span class="badge rounded-pill px-2.5 py-1 footer-badge-standby"><i class="bi bi-info-circle-fill me-1"></i> Standby (Ready)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="footer-divider my-4">

            <div class="row align-items-center small footer-copyright">
                <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                    &copy; <?= date('Y') ?> <strong><?= APP_NAME ?></strong>. All rights reserved.
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <span>Built with <i class="bi bi-heart-fill text-danger"></i> &bull; Bootstrap 5 &bull; Native PHP MVC</span>
                </div>
            </div>
        </div>
    </footer>
    <?php endif; ?>

    <!-- Bootstrap 5.3 JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- SweetAlert2 (Modern Pop-up Confirmation & Alerts) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom Main JS -->
    <script src="<?= asset('js/main.js') ?>?v=<?= time() ?>"></script>
    <?php if (isset($currentPage) && $currentPage === 'home'): ?>
        <!-- UI/UX Pro Max Landing Interactions & Scroll Motion JS -->
        <script src="<?= asset('js/landing.js') ?>?v=<?= time() ?>"></script>
    <?php endif; ?>
    <!-- Chatbot AI (BANTUIN Assistant) JS -->
    <script src="<?= asset('js/chatbot.js') ?>?v=<?= time() ?>"></script>
</body>
</html>
