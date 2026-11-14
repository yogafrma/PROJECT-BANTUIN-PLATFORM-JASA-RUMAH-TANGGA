<div class="fluent-page">

    <!-- ==========================================================================
         HERO SECTION: Centered Viewport Centerpiece with Technician Background
         ========================================================================== -->
    <section id="beranda" class="fluent-hero text-center position-relative">
        <div class="fluent-hero-bg"></div>
        <div class="container position-relative z-3">
            
            <!-- Live Availability Pill (Centered) -->
            <div class="d-flex justify-content-center mb-3 reveal-on-scroll">
                <div class="fluent-badge-pill">
                    <span class="fluent-pulse-dot"></span>
                    <span>Siaga 24 Jam &bull; Mitra Teknisi Siap Meluncur</span>
                </div>
            </div>

            <!-- Crisp High-Impact Headline (Centered) -->
            <h1 class="fluent-headline mb-3 reveal-on-scroll stagger-1">
                Solusi Servis Rumah.<br>
                <span class="fluent-headline-gradient">Lebih Mudah, Lebih Cepat.</span>
            </h1>

            <!-- Single Concise Sentence (Kecilin Dikit) -->
            <p class="fluent-subheadline reveal-on-scroll stagger-2">
                Hubungkan perbaikan listrik, AC, kunci pintu, dan elektronik dengan teknisi profesional bergaransi 30 hari.
            </p>

            <!-- Fluent Action Buttons (Turunin ke Bawah & Dirapetin dengan Badge) -->
            <div class="hero-action-group mt-4 pt-2 reveal-on-scroll stagger-3">
                <div class="d-flex justify-content-center flex-wrap gap-3 mb-2">
                    <?php if (Auth::check() && Auth::role() === 'customer'): ?>
                        <a href="<?= base_url('posts/create') ?>" class="btn-fluent-primary">
                            <i class="bi bi-wrench-adjustable-circle"></i>
                            <span>Buat Permintaan Teknisi</span>
                        </a>
                    <?php else: ?>
                        <a href="#kategori-layanan" class="btn-fluent-primary">
                            <span>Pesan Teknisi Sekarang</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    <?php endif; ?>

                    <a href="<?= base_url('customer/consultation') ?>" class="btn-fluent-secondary">
                        <i class="bi bi-stars text-primary"></i>
                        <span>Diagnosis AI Gratis</span>
                    </a>
                </div>

                <!-- Trust Proof Badge (Dirapetin dengan Tombol) -->
                <div class="d-flex justify-content-center align-items-center mt-1 reveal-on-scroll stagger-4">
                    <div class="fluent-badge-pill hero-trust-badge" style="font-size: 0.86rem; padding: 0.38rem 1.25rem; background: rgba(255, 255, 255, 0.94); border-color: rgba(0, 103, 184, 0.18);">
                        <i class="bi bi-shield-fill-check text-primary fs-6"></i>
                        <span>1.200+ Teknisi Terverifikasi &bull; Rating <i class="bi bi-star-fill text-warning"></i> 4.9/5</span>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- ==========================================================================
         2. LAYANAN PERBAIKAN RUMAH: Microsoft Card Style (Sesuai Foto 1)
         ========================================================================== -->
    <section id="kategori-layanan" class="py-5 ms-section-canvas">
        <div class="container py-lg-4">
            
            <div class="mb-4 reveal-on-scroll">
                <h2 class="fluent-section-title mb-2 fs-2">Layanan Perbaikan Rumah</h2>
                <p class="fluent-section-desc ms-0 mb-0">Pilih kategori yang Anda butuhkan, teknisi siap datang dengan peralatan lengkap.</p>
            </div>

            <div class="row g-4">
                
                <!-- 1. Listrik -->
                <div class="col-lg-3 col-md-6 reveal-on-scroll stagger-1">
                    <div class="ms-card h-100">
                        <div class="ms-card-img-wrap">
                            <img src="<?= asset('images/services/service-listrik.jpg') ?>" alt="Teknisi Listrik" class="ms-card-img" loading="lazy">
                        </div>
                        <div class="ms-card-body">
                            <h3 class="ms-card-title">Teknisi Listrik</h3>
                            <p class="ms-card-desc">
                                Perbaikan korsleting, MCB anjlok, stop kontak terbakar, dan pemasangan lampu baru.
                            </p>
                            <div>
                                <span class="ms-card-price">Mulai Rp 65rb</span>
                            </div>
                            <div class="mt-auto">
                                <a href="<?= Auth::check() ? base_url('posts/create?category=1') : base_url('login') ?>" class="btn-ms-primary">
                                    Pesan Teknisi Listrik
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. AC -->
                <div class="col-lg-3 col-md-6 reveal-on-scroll stagger-2">
                    <div class="ms-card h-100">
                        <div class="ms-card-img-wrap">
                            <img src="<?= asset('images/services/service-ac.jpg') ?>" alt="Teknisi AC" class="ms-card-img" loading="lazy">
                        </div>
                        <div class="ms-card-body">
                            <h3 class="ms-card-title">Teknisi AC</h3>
                            <p class="ms-card-desc">
                                Cuci AC berkala, pengisian freon R32/R410A, dan perbaikan AC bocor netes air di kamar.
                            </p>
                            <div>
                                <span class="ms-card-price">Mulai Rp 75rb</span>
                            </div>
                            <div class="mt-auto">
                                <a href="<?= Auth::check() ? base_url('posts/create?category=2') : base_url('login') ?>" class="btn-ms-primary">
                                    Pesan Teknisi AC
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Kunci -->
                <div class="col-lg-3 col-md-6 reveal-on-scroll stagger-3">
                    <div class="ms-card h-100">
                        <div class="ms-card-img-wrap">
                            <img src="<?= asset('images/services/service-kunci.jpg') ?>" alt="Tukang Kunci" class="ms-card-img" loading="lazy">
                        </div>
                        <div class="ms-card-body">
                            <h3 class="ms-card-title">Tukang Kunci</h3>
                            <p class="ms-card-desc">
                                Pintu darurat terkunci, kunci patah di dalam silinder, ganti master key, &amp; smart lock.
                            </p>
                            <div>
                                <span class="ms-card-price">Mulai Rp 85rb</span>
                            </div>
                            <div class="mt-auto">
                                <a href="<?= Auth::check() ? base_url('posts/create?category=3') : base_url('login') ?>" class="btn-ms-primary">
                                    Pesan Tukang Kunci
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Elektronik -->
                <div class="col-lg-3 col-md-6 reveal-on-scroll stagger-4">
                    <div class="ms-card h-100">
                        <div class="ms-card-img-wrap">
                            <img src="<?= asset('images/services/service-elektronik.jpg') ?>" alt="Teknisi Elektronik" class="ms-card-img" loading="lazy">
                        </div>
                        <div class="ms-card-body">
                            <h3 class="ms-card-title">Elektronik</h3>
                            <p class="ms-card-desc">
                                Reparasi mesin cuci tidak berputar, kulkas kurang dingin, dan TV LED layar gelap.
                            </p>
                            <div>
                                <span class="ms-card-price">Mulai Rp 90rb</span>
                            </div>
                            <div class="mt-auto">
                                <a href="<?= Auth::check() ? base_url('posts/create?category=4') : base_url('login') ?>" class="btn-ms-primary">
                                    Pesan Elektronik
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ==========================================================================
         3. 3 LANGKAH MUDAH: Minimalist Stepper (Bigger Text & Numbers)
         ========================================================================== -->
    <section id="cara-kerja" class="py-5 bg-white border-top border-bottom">
        <div class="container py-lg-4">
            
            <div class="text-center mb-5 reveal-on-scroll">
                <h2 class="fluent-section-title mb-3">3 Langkah Mudah</h2>
                <p class="fluent-section-desc">Pesan teknisi langsung dari ponsel Anda tanpa repot.</p>
            </div>

            <div class="row g-4 justify-content-center">
                
                <div class="col-lg-4 col-md-6 reveal-on-scroll stagger-1">
                    <div class="fluent-step-card">
                        <div class="fluent-step-num">1</div>
                        <h4 class="fw-bold fs-5 mb-2 text-dark">Pilih Kebutuhan</h4>
                        <p class="fluent-card-desc mb-0">Tentukan kategori perbaikan dan lokasi rumah Anda.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 reveal-on-scroll stagger-2">
                    <div class="fluent-step-card">
                        <div class="fluent-step-num">2</div>
                        <h4 class="fw-bold fs-5 mb-2 text-dark">Teknisi Meluncur</h4>
                        <p class="fluent-card-desc mb-0">Mitra terdekat menerima pesanan dan langsung menuju ke alamat Anda.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 reveal-on-scroll stagger-3">
                    <div class="fluent-step-card">
                        <div class="fluent-step-num">3</div>
                        <h4 class="fw-bold fs-5 mb-2 text-dark">Beres &amp; Bergaransi</h4>
                        <p class="fluent-card-desc mb-0">Perbaikan selesai tuntas dan dilindungi garansi servis 30 hari.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ==========================================================================
         4. KEUNGGULAN LAYANAN BANTUIN (4 PILAR KEUNGGULAN PLATFORM)
         ========================================================================== -->
    <section id="keunggulan" class="py-5">
        <div class="container py-lg-4">
            
            <div class="text-center mb-5 reveal-on-scroll">
                <h2 class="fluent-section-title mb-3">Keunggulan Layanan BANTUIN</h2>
                <p class="fluent-section-desc">Standar pelayanan profesional untuk kenyamanan dan rasa aman hunian Anda.</p>
            </div>

            <div class="row g-4">
                
                <div class="col-6 col-lg-3 reveal-on-scroll stagger-1">
                    <div class="fluent-step-card text-center h-100">
                        <div class="fluent-trust-icon mx-auto mb-3" style="width: 52px; height: 52px; font-size: 1.5rem; border-radius: 14px;">
                            <i class="bi bi-lightning-charge-fill"></i>
                        </div>
                        <h3 class="fluent-card-title fs-5 mb-2">Tiba Cepat</h3>
                        <p class="fluent-card-desc mb-0">Rata-rata ~25 menit teknisi tiba di lokasi Anda.</p>
                    </div>
                </div>

                <div class="col-6 col-lg-3 reveal-on-scroll stagger-2">
                    <div class="fluent-step-card text-center h-100">
                        <div class="fluent-trust-icon mx-auto mb-3" style="width: 52px; height: 52px; font-size: 1.5rem; border-radius: 14px;">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h3 class="fluent-card-title fs-5 mb-2">Garansi 30 Hari</h3>
                        <p class="fluent-card-desc mb-0">Gratis perbaikan ulang jika kendala yang sama kambuh.</p>
                    </div>
                </div>

                <div class="col-6 col-lg-3 reveal-on-scroll stagger-3">
                    <div class="fluent-step-card text-center h-100">
                        <div class="fluent-trust-icon mx-auto mb-3" style="width: 52px; height: 52px; font-size: 1.5rem; border-radius: 14px;">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <h3 class="fluent-card-title fs-5 mb-2">Tarif Pasti</h3>
                        <p class="fluent-card-desc mb-0">Disepakati di awal kerja tanpa ada biaya siluman.</p>
                    </div>
                </div>

                <div class="col-6 col-lg-3 reveal-on-scroll stagger-4">
                    <div class="fluent-step-card text-center h-100">
                        <div class="fluent-trust-icon mx-auto mb-3" style="width: 52px; height: 52px; font-size: 1.5rem; border-radius: 14px;">
                            <i class="bi bi-person-check-fill"></i>
                        </div>
                        <h3 class="fluent-card-title fs-5 mb-2">Terverifikasi</h3>
                        <p class="fluent-card-desc mb-0">Mitra beridentitas resmi, catatan bersih, &amp; keahlian teruji.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ==========================================================================
         5. MITRA TEKNISI TERPILIH: Clean Minimalist Tiles
         ========================================================================== -->
    <section class="py-5 bg-white border-top border-bottom">
        <div class="container py-lg-4">
            
            <div class="d-flex justify-content-between align-items-end mb-4 reveal-on-scroll">
                <div>
                    <h2 class="fluent-section-title fs-2 mb-2">Mitra Teknisi Terpilih</h2>
                    <p class="fluent-section-desc ms-0 mb-0">Terverifikasi identitas, keahlian, dan dinilai tinggi oleh pelanggan.</p>
                </div>
                <a href="#kategori-layanan" class="text-primary fw-bold fs-6 text-decoration-none d-none d-md-inline-block">
                    Semua Layanan <i class="bi bi-chevron-right"></i>
                </a>
            </div>

            <div class="row g-3">
                <?php if (!empty($featuredTechnicians)): ?>
                    <?php foreach ($featuredTechnicians as $idx => $tech): ?>
                        <div class="col-lg-3 col-md-6 reveal-on-scroll stagger-<?= ($idx % 4) + 1 ?>">
                            <div class="fluent-tech-card">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="fluent-avatar">
                                        <?= strtoupper(substr($tech->name, 0, 1)) ?>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="fw-bold text-dark text-truncate fs-6"><?= e($tech->name) ?></div>
                                        <div class="text-secondary small fw-semibold">
                                            <?= e($tech->category_name ?? 'Teknisi') ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-3 mt-auto border-top">
                                    <span class="text-warning fw-bold fs-6">
                                        <i class="bi bi-star-fill me-1"></i><?= number_format((float)($tech->rating ?? 4.9), 1) ?>
                                    </span>
                                    <a href="<?= Auth::check() ? base_url('posts/create?category=' . ($tech->category_id ?? 1)) : base_url('login') ?>" class="btn-fluent-secondary py-2 px-3 fw-bold" style="font-size: 0.88rem;">
                                        Pesan Jasa
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>
    </section>

    <!-- ==========================================================================
         6. COPILOT INSPIRED AI DIAGNOSIS BANNER (High-Contrast & Clean)
         ========================================================================== -->
    <section class="py-4">
        <div class="container">
            <div class="fluent-copilot-card">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <div class="d-inline-block mb-2">
                            <span class="copilot-badge">
                                <i class="bi bi-stars"></i> Asisten AI BANTUIN
                            </span>
                        </div>
                        <h3 class="fw-bold fs-2 mb-2 text-dark">Belum Tahu Apa yang Rusak?</h3>
                        <p class="text-secondary fs-6 mb-0" style="max-width: 620px;">
                            Ceritakan gejala atau suara aneh pada perangkat Anda. AI BANTUIN akan memprediksi kerusakan dan estimasi biayanya secara instan.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="<?= base_url('customer/consultation') ?>" class="btn-fluent-primary">
                            <span>Konsultasi AI Gratis</span>
                            <i class="bi bi-arrow-right fs-5"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==========================================================================
         7. ACTION BANNER
         ========================================================================== -->
    <section class="py-5 bg-white border-top border-bottom">
        <div class="container py-lg-3">
            <div class="fluent-cta-card">
                <div class="position-relative z-1" style="max-width: 650px; margin: 0 auto;">
                    <h2 class="fluent-headline fs-1 mb-3 text-dark">Kenyamanan Rumah Anda Kembali Hari Ini.</h2>
                    <p class="text-secondary fs-5 mb-4">
                        Pesan teknisi profesional terdekat atau konsultasikan kendala Anda sekarang.
                    </p>
                    <div class="d-flex justify-content-center flex-wrap gap-3">
                        <?php if (Auth::check()): ?>
                            <a href="<?= base_url('posts/create') ?>" class="btn-fluent-primary">
                                <span>Pesan Teknisi Sekarang</span>
                                <i class="bi bi-arrow-right fs-5"></i>
                            </a>
                        <?php else: ?>
                            <a href="<?= base_url('register') ?>" class="btn-fluent-primary">
                                <span>Daftar &amp; Pesan Teknisi</span>
                                <i class="bi bi-arrow-right fs-5"></i>
                            </a>
                            <a href="<?= base_url('customer/consultation') ?>" class="btn-fluent-secondary">
                                <i class="bi bi-stars text-primary fs-5"></i>
                                <span>Diagnosis AI</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==========================================================================
         8. PERTANYAAN UMUM / FAQ (Paling Bawah - Compact & Hover Reveal)
         ========================================================================== -->
    <section id="bantuan" class="py-5">
        <div class="container py-lg-4">
            
            <div class="text-center mb-4 reveal-on-scroll">
                <h2 class="fluent-section-title fs-2 mb-2">Pertanyaan Umum</h2>
                <p class="fluent-section-desc mb-0" style="font-size: 0.95rem;">Jawaban cepat seputar layanan BANTUIN.</p>
            </div>

            <div class="faq-compact-wrap mx-auto">
                
                <div class="faq-hover-card">
                    <div class="faq-hover-header">
                        <span class="faq-hover-question">Berapa lama teknisi tiba di rumah?</span>
                        <i class="bi bi-chevron-down faq-hover-icon"></i>
                    </div>
                    <div class="faq-hover-collapse">
                        <div class="faq-hover-body">
                            Rata-rata 15 sampai 35 menit setelah pemesanan dikonfirmasi oleh teknisi terdekat di wilayah Anda.
                        </div>
                    </div>
                </div>

                <div class="faq-hover-card">
                    <div class="faq-hover-header">
                        <span class="faq-hover-question">Bagaimana jika masalahnya kambuh lagi?</span>
                        <i class="bi bi-chevron-down faq-hover-icon"></i>
                    </div>
                    <div class="faq-hover-collapse">
                        <div class="faq-hover-body">
                            Setiap pengerjaan dilindungi garansi 30 hari. Cukup hubungi kami dan teknisi akan datang memperbaiki ulang 100% gratis.
                        </div>
                    </div>
                </div>

                <div class="faq-hover-card">
                    <div class="faq-hover-header">
                        <span class="faq-hover-question">Apakah ada biaya tambahan tak terduga?</span>
                        <i class="bi bi-chevron-down faq-hover-icon"></i>
                    </div>
                    <div class="faq-hover-collapse">
                        <div class="faq-hover-body">
                            Tidak ada. Estimasi biaya pengerjaan selalu disepakati bersama sebelum teknisi mulai bekerja.
                        </div>
                    </div>
                </div>

                <div class="faq-hover-card">
                    <div class="faq-hover-header">
                        <span class="faq-hover-question">Kapan pembayaran dilakukan?</span>
                        <i class="bi bi-chevron-down faq-hover-icon"></i>
                    </div>
                    <div class="faq-hover-collapse">
                        <div class="faq-hover-body">
                            Pembayaran dilakukan setelah pengerjaan selesai dan Anda telah memastikan semua berfungsi normal.
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

</div>
