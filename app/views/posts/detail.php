<div class="dashboard-wrapper py-4">
    <div class="container">
        
        <!-- Breadcrumb & Back -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small">
                        <?php if ($user->role === 'customer'): ?>
                            <li class="breadcrumb-item"><a href="<?= base_url('posts/my') ?>" class="text-decoration-none">Postingan Saya</a></li>
                        <?php else: ?>
                            <li class="breadcrumb-item"><a href="<?= base_url('posts') ?>" class="text-decoration-none">Board Lowongan Pekerjaan</a></li>
                        <?php endif; ?>
                        <li class="breadcrumb-item active" aria-current="page">Detail Postingan #<?= $post->id ?></li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0 text-dark"><?= e($post->title) ?></h3>
            </div>
            <div>
                <?php if ($user->role === 'customer'): ?>
                    <a href="<?= base_url('posts/my') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Postingan Saya
                    </a>
                <?php else: ?>
                    <a href="<?= base_url('posts') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Board Pekerjaan
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if (has_flash()): ?>
            <?php $flash = get_flash(); ?>
            <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show rounded-3 small py-2 d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-info-circle-fill"></i>
                <div><?= e($flash['message']) ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            
            <!-- Kolom Utama: Rincian Postingan Keluhan -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
                    
                    <!-- Header Info -->
                    <div class="p-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 fw-semibold">
                                <i class="bi <?= e($post->category_icon ?? 'bi-tools') ?> me-1"></i> <?= e($post->category_name ?? 'Kategori') ?>
                            </span>
                            
                            <!-- Status Badge -->
                            <?php 
                                $status = $post->status ?? 'Mencari Teknisi';
                                $badgeColor = 'primary';
                                if ($status === 'Ada Teknisi') $badgeColor = 'warning text-dark';
                                elseif ($status === 'Selesai') $badgeColor = 'success';
                                elseif ($status === 'Dibatalkan') $badgeColor = 'danger';
                            ?>
                            <span class="badge bg-<?= $badgeColor ?> rounded-pill px-3 py-1 fw-bold">
                                Status: <?= e($status) ?>
                            </span>
                        </div>

                        <div class="text-muted xs-text">
                            <i class="bi bi-clock me-1"></i> Diposting: <?= e($post->created_at ?? 'Baru saja') ?>
                        </div>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        
                        <!-- Foto Kerusakan (Jika Ada) -->
                        <?php if (!empty($post->image)): ?>
                            <div class="mb-4">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-camera-fill text-primary me-2"></i>Foto Kondisi Kerusakan:</h6>
                                <img src="<?= asset($post->image) ?>" alt="Foto Kerusakan" class="rounded-4 border shadow-sm img-fluid" style="max-height: 380px; width: 100%; object-fit: cover;">
                            </div>
                        <?php endif; ?>

                        <!-- Deskripsi Keluhan -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-card-text text-primary me-2"></i>Rincian Keluhan & Lokasi:</h6>
                            <div class="p-3 bg-light rounded-3 border text-dark lh-base">
                                <?= nl2br(e($post->description)) ?>
                            </div>
                        </div>

                        <!-- Analisis AI & Rentang Estimasi Harga (Jika Ada) -->
                        <?php if (!empty($post->ai_analysis) || !empty($post->estimated_min_price)): ?>
                            <div class="p-4 rounded-4 bg-gradient-ai text-white mb-4">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge bg-warning text-dark rounded-pill px-2 py-1 xs-text fw-bold">
                                        <i class="bi bi-stars"></i> Diagnosis Cerdas AI
                                    </span>
                                    <span class="text-white-50 xs-text">Hasil rekomendasi penanganan</span>
                                </div>

                                <?php if (!empty($post->ai_analysis)): ?>
                                    <div class="mb-3">
                                        <span class="text-white-50 xs-text d-block">Kemungkinan Kerusakan Awal:</span>
                                        <div class="fw-semibold fs-6 text-white"><?= e($post->ai_analysis) ?></div>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($post->estimated_min_price) && !empty($post->estimated_max_price)): ?>
                                    <div class="p-3 bg-white bg-opacity-10 rounded-3 d-flex justify-content-between align-items-center">
                                        <span class="text-white-50 small">Perkiraan Rentang Biaya Standar:</span>
                                        <span class="fw-bold text-warning fs-5">
                                            Rp <?= number_format($post->estimated_min_price, 0, ',', '.') ?> - Rp <?= number_format($post->estimated_max_price, 0, ',', '.') ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Aksi Teknisi: Tombol "Saya Bisa Membantu" -->
                        <?php if ($user->role === 'technician'): ?>
                            <div class="p-4 bg-light rounded-4 border text-center mt-4">
                                <?php if ($post->status === 'Mencari Teknisi'): ?>
                                    <?php if ($hasResponded): ?>
                                        <div class="alert alert-success d-inline-flex align-items-center gap-2 px-4 py-2 rounded-pill mb-0">
                                            <i class="bi bi-check-circle-fill"></i>
                                            <strong>Tawaran Bantuan Anda Telah Terkirim!</strong> Menunggu konfirmasi dari Pelanggan.
                                        </div>
                                    <?php else: ?>
                                        <h5 class="fw-bold text-dark mb-2">Tertarik Menangani Pekerjaan Ini?</h5>
                                        <p class="text-muted small mb-3">Kirimkan penawaran bantuan Anda. Pelanggan akan melihat profil keahlian Anda dan dapat memilih Anda.</p>
                                        <form action="<?= base_url('posts/offer/' . $post->id) ?>" method="POST" class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                            <button type="button" 
                                                    class="btn btn-success btn-lg rounded-pill px-5 fw-bold shadow-sm btn-confirm-action"
                                                    data-confirm-title="Tawarkan Bantuan?"
                                                    data-confirm-text="Ajukan penawaran bantuan untuk keluhan '<?= e($post->title) ?>'? Pelanggan akan menerima notifikasi bahwa Anda siap membantu."
                                                    data-confirm-icon="question"
                                                    data-confirm-btn="Ya, Saya Bisa Membantu">
                                                <i class="bi bi-hand-thumbs-up-fill me-1"></i> Saya Bisa Membantu
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                <?php elseif ($post->status === 'Ada Teknisi'): ?>
                                    <?php 
                                        $isSelectedTech = false;
                                        foreach ($responses as $r) {
                                            if ((int)$r->technician_id === (int)$user->id && $r->status === 'selected') {
                                                $isSelectedTech = true; break;
                                            }
                                        }
                                    ?>
                                    <?php if ($isSelectedTech): ?>
                                        <?php
                                            $techToCustMsg = "Halo Pak/Ibu " . ($post->customer_name ?? 'Pelanggan') . ", saya Teknisi " . ($user->name ?? '') . " yang Anda pilih di BANTUIN untuk keluhan: " . ($post->title ?? '') . ". Kapan waktu yang tepat untuk kunjungan/pemeriksaan?";
                                            $techToCustUrl = wa_url($post->customer_whatsapp, $techToCustMsg);
                                        ?>
                                        <div class="alert alert-success rounded-4 p-4 text-center mb-0">
                                            <i class="bi bi-patch-check-fill fs-2 d-block mb-2 text-success"></i>
                                            <h5 class="fw-bold text-success mb-1">Selamat! Anda Telah Dipilih oleh Pelanggan</h5>
                                            <p class="text-muted small mb-3">Silakan hubungi Pelanggan melalui Chat Internal atau WhatsApp untuk konfirmasi kedatangan dan waktu pengerjaan.</p>
                                            <div class="d-flex justify-content-center gap-2 flex-wrap">
                                                <?php if (!empty($conversation)): ?>
                                                    <a href="<?= base_url('chat/room/' . $conversation->id) ?>" 
                                                       class="btn btn-primary btn-lg rounded-pill px-4 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                                                        <i class="bi bi-chat-dots-fill fs-5"></i> Chat Pelanggan
                                                    </a>
                                                <?php endif; ?>
                                                <a href="<?= $techToCustUrl ?>" 
                                                   target="_blank" 
                                                   rel="noopener noreferrer"
                                                   class="btn btn-whatsapp-pill btn-lg px-4 shadow-sm d-inline-flex align-items-center gap-2">
                                                    <i class="bi bi-whatsapp fs-5"></i> WhatsApp Pelanggan (<?= e($post->customer_whatsapp) ?>)
                                                </a>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="badge bg-warning text-dark p-3 rounded-3 fs-6 d-block">
                                            Pekerjaan ini sudah memiliki teknisi yang ditugaskan.
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="badge bg-secondary p-3 rounded-3 fs-6 d-block">
                                        Postingan ini berstatus <?= e($post->status) ?>.
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

                <!-- ================================================================
                     Bukti Transaksi & Struk Pembayaran Resmi (Khusus Status: Selesai & Ada Pembayaran)
                     ================================================================ -->
                <?php if ($post->status === 'Selesai' && !empty($payment)): ?>
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4 border-top border-4 border-success position-relative overflow-hidden print-area" id="receiptCard">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3 pb-3 border-bottom border-light-subtle">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-success text-white shadow-xs" style="width: 46px; height: 46px;">
                                    <i class="bi bi-receipt fs-4"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <h5 class="fw-bold mb-0 text-dark">Bukti Pembayaran &amp; Struk Transaksi Resmi</h5>
                                        <span class="badge bg-success rounded-pill px-3 py-1 xs-text fw-bold">
                                            <i class="bi bi-check-circle-fill me-1"></i> LUNAS
                                        </span>
                                    </div>
                                    <div class="text-muted xs-text mt-0.5">Platform Layanan Jasa Rumah Tangga &bull; BANTUIN</div>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 xs-text fw-semibold d-inline-flex align-items-center gap-1" onclick="window.print()">
                                    <i class="bi bi-printer-fill"></i> Cetak Struk
                                </button>
                            </div>
                        </div>

                        <!-- Receipt Details Grid -->
                        <div class="p-3.5 rounded-3 bg-light border border-light-subtle mb-3" style="font-size: 0.92rem;">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="text-muted xs-text fw-semibold">No. Invoice Resmi</div>
                                    <div class="fw-bold text-dark font-monospace"><?= e($payment->invoice_no) ?></div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="text-muted xs-text fw-semibold">Waktu Transaksi</div>
                                    <div class="fw-bold text-dark"><?= date('d F Y, H:i', strtotime($payment->paid_at ?? $payment->created_at)) ?> WIB</div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="text-muted xs-text fw-semibold">Mitra Teknisi</div>
                                    <div class="fw-bold text-dark"><?= e($payment->technician_name ?? ($assignedTechnician->name ?? 'Mitra Teknisi')) ?></div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="text-muted xs-text fw-semibold">Metode Pembayaran</div>
                                    <div>
                                        <?php if ($payment->payment_method === 'qris'): ?>
                                            <span class="badge bg-dark rounded-pill px-2.5 py-1 text-white xs-text">
                                                <i class="bi bi-qr-code-scan text-warning me-1"></i> QRIS Instant (E-Wallet/Bank)
                                            </span>
                                        <?php elseif ($payment->payment_method === 'cash'): ?>
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2.5 py-1 xs-text">
                                                <i class="bi bi-cash-stack me-1"></i> Tunai Langsung (Cash on Delivery)
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-2.5 py-1 xs-text">
                                                <i class="bi bi-bank me-1"></i> Transfer Bank Langsung
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Price Breakdown -->
                        <div class="p-3 bg-white rounded-3 border mb-3">
                            <div class="d-flex justify-content-between align-items-center py-1 small border-bottom border-light-subtle">
                                <span class="text-muted">Biaya Jasa &amp; Kesepakatan:</span>
                                <span class="fw-semibold text-dark">Rp <?= number_format($payment->amount, 0, ',', '.') ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-1 small border-bottom border-light-subtle">
                                <span class="text-muted">Biaya Layanan Aplikasi BANTUIN:</span>
                                <span class="text-success fw-bold">Rp 0 (GRATIS)</span>
                            </div>
                            <?php if (!empty($payment->notes)): ?>
                                <div class="py-2 xs-text text-muted border-bottom border-light-subtle">
                                    <i class="bi bi-chat-quote me-1"></i>Catatan: <em>"<?= e($payment->notes) ?>"</em>
                                </div>
                            <?php endif; ?>
                            <div class="d-flex justify-content-between align-items-center pt-2">
                                <span class="fw-bold text-dark">Total Pembayaran Lunas:</span>
                                <span class="display-6 fw-bold text-success" style="font-size: 1.5rem;">
                                    Rp <?= number_format($payment->amount, 0, ',', '.') ?>
                                </span>
                            </div>
                        </div>

                        <div class="xs-text text-muted text-center">
                            <i class="bi bi-shield-check text-success me-1"></i>
                            Pembayaran sah dan terlindungi dalam program garansi servis 30 hari BANTUIN.
                        </div>
                    </div>
                <?php endif; ?>

                <!-- ================================================================
                     Section Review & Penilaian Teknisi (Khusus Status: Selesai)
                     ================================================================ -->
                <?php if ($post->status === 'Selesai'): ?>
                    <?php 
                        $techName = $assignedTechnician->name ?? ($review->technician_name ?? 'Mitra Teknisi');
                        $techCategory = $assignedTechnician->category_name ?? ($post->category_name ?? 'Teknisi Servis');
                        $techCategoryIcon = $assignedTechnician->category_icon ?? ($post->category_icon ?? 'bi-tools');
                        $techUserId = $assignedTechnician->user_id ?? ($review->technician_id ?? 0);
                        $techLocation = $assignedTechnician->location ?? ($review->technician_location ?? 'Jabodetabek');
                    ?>
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4 border-start border-4 border-warning" id="reviewSection">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 pb-2 border-bottom">
                            <div>
                                <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <span>Review &amp; Penilaian Kinerja Teknisi</span>
                                </h5>
                                <p class="text-muted xs-text mb-0">Evaluasi performa pengerjaan mitra teknisi untuk menjaga kualitas layanan BANTUIN.</p>
                            </div>
                            <?php if (!empty($review)): ?>
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1.5 fw-bold">
                                    <i class="bi bi-patch-check-fill me-1"></i> Ulasan Terverifikasi
                                </span>
                            <?php else: ?>
                                <span class="badge bg-warning bg-opacity-10 text-dark rounded-pill px-3 py-1.5 fw-semibold">
                                    <i class="bi bi-hourglass-split me-1"></i> Menunggu Ulasan
                                </span>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($review)): ?>
                            <!-- Review Selesai Diberikan (Tampilan Detail) -->
                            <div class="p-4 rounded-4 bg-light border border-light-subtle" id="viewReviewBlock">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-circle rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                                             style="width: 48px; height: 48px; background: linear-gradient(135deg, #0078D4, #005A9E); font-size: 1.25rem;">
                                            <?= strtoupper(substr($techName, 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <h6 class="fw-bold mb-0 text-dark"><?= e($techName) ?></h6>
                                                <span class="badge rounded-pill xs-text" style="background: rgba(0, 120, 212, 0.08); color: #0078D4; border: 1px solid rgba(0, 120, 212, 0.2);">
                                                    <i class="bi <?= e($techCategoryIcon) ?> me-1"></i><?= e($techCategory) ?>
                                                </span>
                                            </div>
                                            <div class="text-muted xs-text mt-0.5"><i class="bi bi-geo-alt me-1 text-danger"></i><?= e($techLocation) ?></div>
                                        </div>
                                    </div>

                                    <!-- Rating Stars Display -->
                                    <div class="text-md-end">
                                        <div class="d-flex align-items-center gap-1 text-warning fs-5">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="bi <?= ($i <= (int)$review->rating) ? 'bi-star-fill' : 'bi-star' ?>"></i>
                                            <?php endfor; ?>
                                            <span class="ms-1 fw-bold text-dark fs-6"><?= number_format((int)$review->rating, 1) ?>/5.0</span>
                                        </div>
                                        <div class="xs-text text-muted">
                                            <?= ((int)$review->rating >= 5) ? 'Sangat Puas &amp; Memuaskan' : (((int)$review->rating >= 4) ? 'Puas &amp; Bagus' : 'Cukup Baik') ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Review Comment Box -->
                                <div class="p-3 bg-white rounded-3 border mb-3 shadow-sm position-relative">
                                    <i class="bi bi-quote text-warning opacity-25 fs-1 position-absolute top-0 end-0 me-3 mt-1"></i>
                                    <p class="text-dark mb-0 lh-base position-relative z-1" style="font-size: 0.95rem;">
                                        "<?= nl2br(e($review->comment)) ?>"
                                    </p>
                                </div>

                                <div class="d-flex flex-wrap justify-content-between align-items-center xs-text text-muted gap-2">
                                    <div>
                                        <i class="bi bi-person-check-fill text-primary me-1"></i>
                                        Oleh: <strong><?= e($review->customer_name) ?></strong> (Pelanggan)
                                        <span class="mx-1">&bull;</span>
                                        <i class="bi bi-clock me-1"></i><?= date('d M Y, H:i', strtotime($review->created_at)) ?>
                                    </div>
                                    <?php if ($isOwner): ?>
                                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 xs-text fw-semibold" id="btnToggleEditReview">
                                            <i class="bi bi-pencil-square me-1"></i> Ubah Ulasan Anda
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Form Input Review (Tampil jika belum review atau jika klik Ubah) -->
                        <?php if ($isOwner): ?>
                            <div class="<?= !empty($review) ? 'd-none mt-3 pt-3 border-top' : '' ?>" id="formReviewBlock">
                                <div class="p-3 rounded-3 bg-light border border-light-subtle mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-circle rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                                             style="width: 44px; height: 44px; background: linear-gradient(135deg, #0078D4, #005A9E);">
                                            <?= strtoupper(substr($techName, 0, 1)) ?>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark"><?= e($techName) ?></h6>
                                            <span class="text-muted xs-text"><i class="bi <?= e($techCategoryIcon) ?> me-1"></i><?= e($techCategory) ?></span>
                                        </div>
                                    </div>
                                </div>

                                <form action="<?= base_url('posts/review/' . $post->id) ?>" method="POST" id="reviewForm">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="technician_id" value="<?= (int)$techUserId ?>">
                                    <input type="hidden" name="rating" id="selectedRatingInput" value="<?= !empty($review) ? (int)$review->rating : 5 ?>">

                                    <!-- Interactive Star Rating -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-dark small mb-1">
                                            Beri Rating Bintang:
                                        </label>
                                        <div class="d-flex align-items-center gap-3 flex-wrap">
                                            <div class="star-rating-interactive d-flex align-items-center gap-1 fs-3 text-warning" id="starRatingContainer">
                                                <?php $curRating = !empty($review) ? (int)$review->rating : 5; ?>
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="bi <?= ($i <= $curRating) ? 'bi-star-fill' : 'bi-star' ?> star-item" data-value="<?= $i ?>" style="cursor: pointer; transition: transform 0.15s ease;"></i>
                                                <?php endfor; ?>
                                            </div>
                                            <span class="badge bg-warning bg-opacity-25 text-dark rounded-pill px-3 py-1.5 fw-bold xs-text" id="starRatingLabel">
                                                <?= ($curRating === 5) ? '5/5 Bintang — Sangat Memuaskan' : "{$curRating}/5 Bintang" ?>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Quick Review Chips -->
                                    <div class="mb-3">
                                        <label class="form-label text-muted xs-text fw-semibold mb-1">Kesan Pengerjaan (Klik untuk menambah teks):</label>
                                        <div class="d-flex flex-wrap gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill xs-text review-tag-chip" data-text="Pengerjaan sangat rapi dan teliti. ">+ Pengerjaan Rapi</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill xs-text review-tag-chip" data-text="Datang tepat waktu dan cekatan. ">+ Tepat Waktu</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill xs-text review-tag-chip" data-text="Teknisi sangat ramah, jujur, dan komunikatif. ">+ Ramah &amp; Komunikatif</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill xs-text review-tag-chip" data-text="Solusi tepat sasaran dan hasil memuaskan. ">+ Solusi Tepat</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill xs-text review-tag-chip" data-text="Sangat direkomendasikan untuk perbaikan lainnya! ">+ Sangat Rekomended</button>
                                        </div>
                                    </div>

                                    <!-- Comment Textarea -->
                                    <div class="mb-3">
                                        <label for="reviewComment" class="form-label fw-bold text-dark small mb-1">
                                            Ulasan &amp; Testimoni Anda:
                                        </label>
                                        <textarea name="comment" 
                                                  id="reviewComment" 
                                                  class="form-control rounded-3" 
                                                  rows="3" 
                                                  placeholder="Ceritakan pengalaman Anda mengenai kualitas perbaikan, keramahan, dan ketepatan waktu teknisi..." 
                                                  required><?= !empty($review) ? e($review->comment) : 'Pengerjaan sangat memuaskan, teknisi ramah, dan masalah terselesaikan dengan baik. Terima kasih BANTUIN!' ?></textarea>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <?php if (!empty($review)): ?>
                                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" id="btnCancelEditReview">
                                                Batal
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted xs-text"><i class="bi bi-shield-check text-success me-1"></i>Ulasan publik terverifikasi</span>
                                        <?php endif; ?>
                                        
                                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                                            <i class="bi bi-star-fill text-warning"></i>
                                            <span><?= !empty($review) ? 'Perbarui Ulasan' : 'Kirim Review Teknisi' ?></span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        <?php elseif (empty($review)): ?>
                            <div class="p-4 rounded-4 bg-light border text-center">
                                <i class="bi bi-clock-history fs-2 text-warning d-block mb-2"></i>
                                <h6 class="fw-bold text-dark mb-1">Menunggu Review dari Pelanggan</h6>
                                <p class="text-muted xs-text mb-0">Pelanggan belum memberikan ulasan dan rating performa untuk pengerjaan perbaikan ini.</p>
                            </div>
                        <?php endif; ?>

                    </div>
                <?php endif; ?>

                <!-- Section Penawaran Teknisi (Khusus Dilihat Customer Pemilik) -->
                <?php if ($isOwner): ?>
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="bi bi-people-fill text-primary me-2"></i>Daftar Teknisi yang Menawarkan Bantuan (<?= count($responses) ?>)
                            </h5>
                            <span class="badge bg-info bg-opacity-10 text-info-emphasis rounded-pill px-3 py-1 small">
                                <?= ($post->status === 'Ada Teknisi') ? 'Teknisi Telah Dipilih' : 'Pilih Salah Satu Mitra' ?>
                            </span>
                        </div>

                        <?php if (!empty($responses)): ?>
                            <div class="d-flex flex-column gap-3">
                                <?php foreach ($responses as $resp): ?>
                                    <?php 
                                        $techWaMsg = wa_message_post($post->title, $resp->technician_name);
                                        $techWaUrl = wa_url($resp->technician_whatsapp, $techWaMsg);
                                    ?>
                                    <div class="p-3 rounded-4 border <?= ($resp->status === 'selected') ? 'border-success bg-success bg-opacity-10' : 'bg-light border-light-subtle' ?>">
                                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                            <div>
                                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                                    <h6 class="fw-bold mb-0 text-dark"><?= e($resp->technician_name) ?></h6>
                                                    <div class="d-flex align-items-center text-warning xs-text fw-bold">
                                                        <i class="bi bi-star-fill me-1"></i> <?= number_format($resp->technician_rating ?? 5.0, 1) ?>
                                                    </div>
                                                    <span class="badge bg-secondary bg-opacity-25 text-dark rounded-pill xs-text">
                                                        <i class="bi bi-tools me-1"></i> <?= e($resp->category_name ?? $post->category_name) ?>
                                                    </span>
                                                    <?php if ($resp->status === 'selected'): ?>
                                                        <span class="badge bg-success rounded-pill px-3 py-1 xs-text">
                                                            <i class="bi bi-check-circle-fill me-1"></i> Teknisi Terpilih
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="text-muted xs-text mb-1">
                                                    <i class="bi bi-geo-alt text-danger me-1"></i> <?= e($resp->technician_location ?? 'Jabodetabek') ?>
                                                    <span class="ms-2"><i class="bi bi-whatsapp text-success me-1"></i> <?= e($resp->technician_whatsapp) ?></span>
                                                </div>
                                                <p class="text-muted small mb-0"><?= e($resp->technician_description ?? 'Mitra teknisi profesional siap melayani kebutuhan perbaikan.') ?></p>
                                            </div>

                                            <div class="d-flex flex-wrap gap-2 flex-shrink-0 align-items-center">
                                                <?php if ($resp->status === 'selected' && !empty($conversation)): ?>
                                                    <!-- Tombol Chat Teknisi -->
                                                    <a href="<?= base_url('chat/room/' . $conversation->id) ?>" 
                                                       class="btn btn-primary rounded-pill px-3 fw-bold btn-sm shadow-sm d-inline-flex align-items-center gap-1">
                                                        <i class="bi bi-chat-dots-fill fs-6"></i> Chat Teknisi
                                                    </a>
                                                <?php endif; ?>

                                                <!-- Tombol WhatsApp -->
                                                <a href="<?= $techWaUrl ?>" 
                                                   target="_blank" 
                                                   rel="noopener noreferrer"
                                                   class="btn btn-outline-success rounded-pill px-3 fw-bold btn-sm shadow-sm d-inline-flex align-items-center gap-1">
                                                    <i class="bi bi-whatsapp fs-6"></i> WhatsApp
                                                </a>

                                                <?php if ($post->status === 'Mencari Teknisi' && $resp->status !== 'selected'): ?>
                                                    <form action="<?= base_url('posts/select/' . $post->id . '/' . $resp->id) ?>" method="POST" class="d-inline">
                                                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                        <button type="button" 
                                                                class="btn btn-primary rounded-pill px-3 fw-semibold btn-sm shadow-sm d-inline-flex align-items-center gap-1 btn-confirm-action"
                                                                data-confirm-title="Konfirmasi Pilihan Teknisi?"
                                                                data-confirm-text="Pilih <?= e($resp->technician_name) ?> sebagai mitra teknisi Anda? Sistem akan mengirimkan notifikasi resmi (lonceng & email) dan membuka ruang percakapan."
                                                                data-confirm-icon="question"
                                                                data-confirm-btn="Ya, Konfirmasi Teknisi Ini">
                                                            <i class="bi bi-check-lg"></i> Pilih Teknisi Ini
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4 text-muted small">
                                <i class="bi bi-hourglass-split fs-2 d-block mb-2 opacity-50"></i>
                                Belum ada teknisi yang mengajukan penawaran. Postingan Anda sedang aktif ditampilkan ke seluruh mitra teknisi terverifikasi.
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div>

            <!-- Kolom Samping: Info Pemilik & Pengaturan Status -->
            <div class="col-lg-4">
                
                <!-- Info Customer Pemilik -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                    <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-person-circle text-primary me-2"></i>Informasi Pelanggan</h6>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar-circle bg-primary text-white fw-bold" style="width: 44px; height: 44px;">
                            <?= strtoupper(substr($post->customer_name, 0, 1)) ?>
                        </div>
                        <div>
                            <div class="fw-bold text-dark"><?= e($post->customer_name) ?></div>
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill xs-text">Pemilik Postingan</span>
                        </div>
                    </div>
                    <?php if ($user->role === 'technician' && $post->status === 'Ada Teknisi'): ?>
                        <div class="p-2 bg-light rounded-3 small">
                            <span class="text-muted d-block xs-text">Nomor WhatsApp Pelanggan:</span>
                            <span class="fw-bold text-success"><i class="bi bi-whatsapp me-1"></i> <?= e($post->customer_whatsapp) ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Kelola Postingan (Khusus Pemilik Customer) -->
                <?php if ($isOwner): ?>
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                        <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-gear-wide-connected text-primary me-2"></i>Kelola Postingan Anda</h6>
                        
                        <div class="d-flex flex-column gap-2 mb-3">
                            <a href="<?= base_url('posts/edit/' . $post->id) ?>" class="btn btn-outline-primary rounded-pill btn-sm fw-semibold">
                                <i class="bi bi-pencil-square me-1"></i> Edit Judul & Deskripsi
                            </a>
                        </div>

                        <?php if ($post->status !== 'Selesai'): ?>
                            <div class="mb-3">
                                <button type="button" 
                                        class="btn btn-success rounded-pill btn-sm w-100 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2 py-2"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#paymentModal">
                                    <i class="bi bi-cash-coin fs-6"></i>
                                    <span>Selesaikan &amp; Konfirmasi Bayar</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <!-- Update Status Form -->
                        <form action="<?= base_url('posts/status/' . $post->id) ?>" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <label class="form-label fw-semibold xs-text text-muted mb-1">Ubah Status Postingan:</label>
                            <div class="d-flex gap-2">
                                <select name="status" class="form-select form-select-sm rounded-pill">
                                    <option value="Mencari Teknisi" <?= $post->status === 'Mencari Teknisi' ? 'selected' : '' ?>>Mencari Teknisi</option>
                                    <option value="Ada Teknisi" <?= $post->status === 'Ada Teknisi' ? 'selected' : '' ?>>Ada Teknisi</option>
                                    <option value="Selesai" <?= $post->status === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                                    <option value="Dibatalkan" <?= $post->status === 'Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                                </select>
                                <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3 fw-semibold">Update</button>
                            </div>
                        </form>

                        <?php if ($post->status === 'Selesai'): ?>
                            <div class="mt-3 pt-3 border-top">
                                <?php if (!empty($review)): ?>
                                    <div class="p-3 rounded-3 bg-success bg-opacity-10 border border-success border-opacity-25 text-center">
                                        <div class="text-warning mb-1">
                                            <?php for ($s = 1; $s <= 5; $s++): ?>
                                                <i class="bi <?= ($s <= (int)$review->rating) ? 'bi-star-fill' : 'bi-star' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <div class="xs-text fw-bold text-success mb-0.5">Ulasan Telah Dikirim</div>
                                        <div class="text-muted xs-text"><?= (int)$review->rating ?> / 5 Bintang</div>
                                    </div>
                                <?php else: ?>
                                    <a href="#reviewSection" class="btn btn-warning text-dark btn-sm rounded-pill fw-bold w-100 shadow-sm d-flex align-items-center justify-content-center gap-1">
                                        <i class="bi bi-star-fill text-dark"></i>
                                        <span>Beri Review Teknisi</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Status Pengerjaan (Khusus Teknisi Terpilih) -->
                <?php if (!empty($isSelectedTechnician)): ?>
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4 border-start border-4 border-primary">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-briefcase-fill text-primary me-2"></i>Status Tugas Anda</h6>
                            <?php if ($post->status === 'Selesai'): ?>
                                <span class="badge bg-success rounded-pill px-3 py-1 xs-text"><i class="bi bi-check-circle-fill me-1"></i>Selesai</span>
                            <?php else: ?>
                                <span class="badge bg-primary text-white rounded-pill px-3 py-1 xs-text fw-semibold d-inline-flex align-items-center gap-1">
                                    <span class="spinner-grow spinner-grow-sm" style="width: 0.5rem; height: 0.5rem;" role="status"></span>
                                    Sedang Dikerjakan
                                </span>
                            <?php endif; ?>
                        </div>
                        <p class="text-muted xs-text mb-3">
                            Anda adalah mitra teknisi terpilih yang ditugaskan untuk menangani order keluhan perbaikan ini.
                        </p>

                        <div class="d-flex flex-column gap-2">
                            <a href="<?= base_url('chat/open/' . $post->id) ?>" class="btn btn-warning text-dark btn-sm rounded-pill fw-semibold shadow-sm">
                                <i class="bi bi-chat-dots-fill me-1"></i> Buka Chat Internal dengan Customer
                            </a>

                            <?php if ($post->status !== 'Selesai'): ?>
                                <form action="<?= base_url('posts/status/' . $post->id) ?>" method="POST" class="mt-2">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="status" value="Selesai">
                                    <button type="button" 
                                            class="btn btn-success btn-sm rounded-pill w-100 fw-bold btn-confirm-action shadow-sm"
                                            data-confirm-title="Selesaikan Pekerjaan?"
                                            data-confirm-text="Apakah Anda telah menyelesaikan pekerjaan perbaikan untuk keluhan ini? Status order akan diubah menjadi Selesai dan pelanggan akan diberi notifikasi."
                                            data-confirm-icon="question"
                                            data-confirm-btn="Ya, Tandai Selesai">
                                        <i class="bi bi-check2-circle me-1"></i> Tandai Pekerjaan Selesai
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

        </div>

    </div>
</div>

<!-- Modal Konfirmasi Pembayaran & Penyelesaian Transaksi (Gojek / Grab Style) -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            
            <div class="modal-header border-0 bg-dark text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-success text-white shadow-xs" style="width: 44px; height: 44px;">
                        <i class="bi bi-shield-check fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="paymentModalLabel">Konfirmasi Pembayaran &amp; Selesaikan Layanan</h5>
                        <p class="text-white-50 xs-text mb-0">Pastikan hasil perbaikan telah dicek dan berfungsi dengan baik</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('posts/payment/' . $post->id) ?>" method="POST" id="confirmPaymentForm">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                
                <div class="modal-body p-4 p-md-4">
                    
                    <!-- 1. Ringkasan Pesanan / Invoice -->
                    <div class="p-3 rounded-4 bg-light border border-light-subtle mb-4">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-2 pb-2 border-bottom border-light-subtle">
                            <div>
                                <span class="text-muted xs-text fw-semibold d-block">Judul Keluhan / Layanan:</span>
                                <h6 class="fw-bold text-dark mb-0"><?= e($post->title) ?></h6>
                            </div>
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 xs-text fw-semibold">
                                <i class="bi <?= e($post->category_icon ?? 'bi-tools') ?> me-1"></i><?= e($post->category_name ?? 'Layanan') ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center xs-text text-muted">
                            <div>
                                <i class="bi bi-person-fill text-primary me-1"></i>
                                Mitra Teknisi: <strong class="text-dark"><?= e($assignedTechnician->name ?? 'Mitra Teknisi') ?></strong>
                            </div>
                            <div>
                                <i class="bi bi-geo-alt text-danger me-1"></i>
                                <?= e($assignedTechnician->location ?? 'Jabodetabek') ?>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Nominal Biaya Kesepakatan -->
                    <?php 
                        $initAmount = (int)($post->agreed_price ?: ($post->estimated_min_price ?: 100000));
                    ?>
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark small mb-1">
                            Total Biaya Kesepakatan (Rp):
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 fw-bold text-muted">Rp</span>
                            <input type="number" 
                                   name="amount" 
                                   id="modalPayAmount" 
                                   class="form-control form-control-lg fw-bold text-success border-start-0 fs-3" 
                                   value="<?= $initAmount ?>" 
                                   min="0" 
                                   step="1000"
                                   required>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <span class="xs-text text-muted">
                                <?= !empty($post->agreed_price) ? '✨ Terisi otomatis dari kesepakatan chat' : 'Sesuaikan bila ada penambahan spare part/komponen' ?>
                            </span>
                            <span class="xs-text fw-semibold text-success">Biaya Layanan: Rp 0 (Bebas Biaya)</span>
                        </div>
                    </div>

                    <!-- 3. Pilihan Metode Pembayaran (Gojek/Grab Style) -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark small mb-2">
                            Pilih Metode Pembayaran:
                        </label>
                        <div class="row g-2">
                            <!-- QRIS Card -->
                            <div class="col-md-4">
                                <label class="payment-method-card p-3 rounded-4 border d-flex flex-column justify-content-between h-100 cursor-pointer transition hover-shadow position-relative bg-white" style="cursor: pointer;">
                                    <input type="radio" name="payment_method" value="qris" class="form-check-input position-absolute top-0 end-0 m-3" checked>
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-dark text-white" style="width: 34px; height: 34px;">
                                                <i class="bi bi-qr-code-scan"></i>
                                            </div>
                                            <span class="badge bg-warning text-dark rounded-pill xs-text fw-bold">Instan</span>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">QRIS Instant</h6>
                                        <p class="text-muted xs-text mb-0">GoPay, OVO, DANA, BCA, ShopeePay</p>
                                    </div>
                                </label>
                            </div>

                            <!-- Cash Card -->
                            <div class="col-md-4">
                                <label class="payment-method-card p-3 rounded-4 border d-flex flex-column justify-content-between h-100 cursor-pointer transition hover-shadow position-relative bg-white" style="cursor: pointer;">
                                    <input type="radio" name="payment_method" value="cash" class="form-check-input position-absolute top-0 end-0 m-3">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white" style="width: 34px; height: 34px;">
                                                <i class="bi bi-cash-stack"></i>
                                            </div>
                                            <span class="badge bg-light text-muted border rounded-pill xs-text">COD</span>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">Tunai (Cash)</h6>
                                        <p class="text-muted xs-text mb-0">Bayar langsung di tempat ke teknisi</p>
                                    </div>
                                </label>
                            </div>

                            <!-- Transfer Card -->
                            <div class="col-md-4">
                                <label class="payment-method-card p-3 rounded-4 border d-flex flex-column justify-content-between h-100 cursor-pointer transition hover-shadow position-relative bg-white" style="cursor: pointer;">
                                    <input type="radio" name="payment_method" value="transfer" class="form-check-input position-absolute top-0 end-0 m-3">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-info text-white" style="width: 34px; height: 34px;">
                                                <i class="bi bi-bank"></i>
                                            </div>
                                            <span class="badge bg-light text-muted border rounded-pill xs-text">Virtual</span>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">Transfer Bank</h6>
                                        <p class="text-muted xs-text mb-0">BCA, Mandiri, BRI, BNI</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Dynamic Payment Preview Area -->
                    <!-- A. QRIS Preview -->
                    <div id="qrisPreviewBox" class="p-4 rounded-4 bg-light border border-light-subtle text-center mb-3">
                        <div class="d-inline-block p-3 bg-white rounded-3 border shadow-sm mb-2 position-relative">
                            <div class="bg-dark text-white text-center py-1 px-3 rounded-2 fw-bold xs-text mb-2 d-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-qr-code"></i> QRIS STANDAR PEMBAYARAN NASIONAL
                            </div>
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=BANTUIN-PAYMENT-POST-<?= $post->id ?>-<?= $initAmount ?>" 
                                 alt="QRIS Code" 
                                 class="img-fluid rounded-2 mb-2" 
                                 id="qrisImageCode"
                                 style="width: 160px; height: 160px;">
                            <div class="fw-bold text-dark small">BANTUIN &bull; Mitra <?= e($assignedTechnician->name ?? 'Teknisi') ?></div>
                            <div class="text-muted xs-text">NMID: ID1020261114<?= str_pad((string)$post->id, 4, '0', STR_PAD_LEFT) ?></div>
                        </div>
                        <div class="xs-text text-muted">
                            <i class="bi bi-phone me-1"></i> Buka GoPay, OVO, DANA, BCA Mobile, atau ShopeePay lalu pindai kode QR di atas.
                        </div>
                    </div>

                    <!-- B. Cash Notice -->
                    <div id="cashNoticeBox" class="p-4 rounded-4 bg-light border border-light-subtle d-none mb-3">
                        <div class="d-flex align-items-start gap-3">
                            <i class="bi bi-info-circle-fill text-primary fs-3"></i>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Pembayaran Tunai (Cash on Delivery)</h6>
                                <p class="text-muted small mb-0">
                                    Serahkan uang tunai sebesar <strong class="text-success" id="cashAmountDisplay">Rp <?= number_format($initAmount, 0, ',', '.') ?></strong> langsung ke teknisi setelah Anda menguji dan menyetujui hasil perbaikan.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- C. Transfer Bank Info -->
                    <div id="transferNoticeBox" class="p-4 rounded-4 bg-light border border-light-subtle d-none mb-3">
                        <div class="d-flex flex-column gap-2">
                            <h6 class="fw-bold text-dark mb-1"><i class="bi bi-bank me-2 text-info"></i>Rekening Resmi Mitra BANTUIN:</h6>
                            <div class="p-3 bg-white rounded-3 border d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold text-dark">Bank BCA</div>
                                    <div class="font-monospace text-primary fw-bold" id="bcaAccNo">8830 1928 4721</div>
                                    <div class="xs-text text-muted">a.n. BANTUIN Mitra <?= e($assignedTechnician->name ?? 'Teknisi') ?></div>
                                </div>
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 xs-text" onclick="navigator.clipboard.writeText('883019284721'); alert('Nomor rekening BCA disalin!');">
                                    <i class="bi bi-copy me-1"></i> Salin
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- 5. Catatan Tambahan (Opsional) -->
                    <div class="mb-2">
                        <label class="form-label text-muted xs-text fw-semibold mb-1">Catatan Tambahan Transaksi (Opsional):</label>
                        <input type="text" name="notes" class="form-control rounded-3 form-control-sm" placeholder="Contoh: Sudah termasuk pergantian baut &amp; handle baru">
                    </div>

                </div>

                <div class="modal-footer border-0 bg-light p-3 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted xs-text d-block">Total Pelunasan:</span>
                        <div class="fw-bold text-success fs-5" id="footerTotalDisplay">Rp <?= number_format($initAmount, 0, ',', '.') ?></div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center gap-2">
                            <i class="bi bi-check2-circle fs-5"></i>
                            <span>Konfirmasi &amp; Selesaikan</span>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const starContainer = document.getElementById('starRatingContainer');
    const ratingInput = document.getElementById('selectedRatingInput');
    const ratingLabel = document.getElementById('starRatingLabel');
    const commentInput = document.getElementById('reviewComment');
    const btnToggleEdit = document.getElementById('btnToggleEditReview');
    const btnCancelEdit = document.getElementById('btnCancelEditReview');
    const viewBlock = document.getElementById('viewReviewBlock');
    const formBlock = document.getElementById('formReviewBlock');

    const ratingDescriptions = {
        1: '1/5 Bintang — Kurang Memuaskan',
        2: '2/5 Bintang — Cukup',
        3: '3/5 Bintang — Standar / Baik',
        4: '4/5 Bintang — Memuaskan &amp; Bagus',
        5: '5/5 Bintang — Sangat Memuaskan'
    };

    if (starContainer && ratingInput) {
        const stars = starContainer.querySelectorAll('.star-item');

        function updateStars(val) {
            stars.forEach(star => {
                const sVal = parseInt(star.getAttribute('data-value'), 10);
                if (sVal <= val) {
                    star.className = 'bi bi-star-fill star-item text-warning';
                } else {
                    star.className = 'bi bi-star star-item text-secondary opacity-50';
                }
            });
            if (ratingLabel) {
                ratingLabel.innerHTML = ratingDescriptions[val] || `${val}/5 Bintang`;
            }
        }

        stars.forEach(star => {
            star.addEventListener('click', function() {
                const val = parseInt(this.getAttribute('data-value'), 10);
                ratingInput.value = val;
                updateStars(val);
            });

            star.addEventListener('mouseenter', function() {
                const val = parseInt(this.getAttribute('data-value'), 10);
                updateStars(val);
                this.style.transform = 'scale(1.25)';
            });

            star.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
                updateStars(parseInt(ratingInput.value, 10));
            });
        });
    }

    // Quick review chips
    document.querySelectorAll('.review-tag-chip').forEach(btn => {
        btn.addEventListener('click', function() {
            if (commentInput) {
                const text = this.getAttribute('data-text');
                if (!commentInput.value.includes(text.trim())) {
                    if (commentInput.value.trim().length > 0 && !commentInput.value.endsWith(' ')) {
                        commentInput.value += ' ';
                    }
                    commentInput.value += text;
                }
                commentInput.focus();
            }
        });
    });

    // Toggle edit review
    if (btnToggleEdit && formBlock && viewBlock) {
        btnToggleEdit.addEventListener('click', function() {
            viewBlock.classList.add('d-none');
            formBlock.classList.remove('d-none');
        });
    }
    if (btnCancelEdit && formBlock && viewBlock) {
        btnCancelEdit.addEventListener('click', function() {
            formBlock.classList.add('d-none');
            viewBlock.classList.remove('d-none');
        });
    }

    // Payment Modal Interactive Logic (Gojek / Grab Style)
    const payRadios = document.querySelectorAll('input[name="payment_method"]');
    const qrisBox = document.getElementById('qrisPreviewBox');
    const cashBox = document.getElementById('cashNoticeBox');
    const transferBox = document.getElementById('transferNoticeBox');
    const amountInput = document.getElementById('modalPayAmount');
    const cashDisplay = document.getElementById('cashAmountDisplay');
    const footerDisplay = document.getElementById('footerTotalDisplay');
    const qrisImg = document.getElementById('qrisImageCode');
    const payModalEl = document.getElementById('paymentModal');

    function updatePaymentUi() {
        let selectedMethod = 'qris';
        payRadios.forEach(r => {
            const card = r.closest('.payment-method-card');
            if (r.checked) {
                selectedMethod = r.value;
                if (card) {
                    card.classList.add('active');
                }
            } else {
                if (card) {
                    card.classList.remove('active');
                }
            }
        });

        if (qrisBox) qrisBox.classList.toggle('d-none', selectedMethod !== 'qris');
        if (cashBox) cashBox.classList.toggle('d-none', selectedMethod !== 'cash');
        if (transferBox) transferBox.classList.toggle('d-none', selectedMethod !== 'transfer');

        const amt = parseInt(amountInput ? amountInput.value : 0, 10) || 0;
        const formatted = 'Rp ' + amt.toLocaleString('id-ID');
        if (cashDisplay) cashDisplay.textContent = formatted;
        if (footerDisplay) footerDisplay.textContent = formatted;
        if (qrisImg) {
            const postId = <?= (int)$post->id ?>;
            qrisImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=BANTUIN-PAYMENT-POST-${postId}-${amt}`;
        }
    }

    payRadios.forEach(r => r.addEventListener('change', updatePaymentUi));
    if (amountInput) {
        amountInput.addEventListener('input', updatePaymentUi);
    }
    updatePaymentUi();

    // Auto open payment modal if ?open_payment=1 in query string
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('open_payment') === '1' && payModalEl && typeof bootstrap !== 'undefined') {
        const modalInstance = bootstrap.Modal.getOrCreateInstance(payModalEl);
        modalInstance.show();
    }

    // Intercept standard status change to 'Selesai' so payment modal opens instead
    const statusForm = document.querySelector('form[action*="posts/status/<?= $post->id ?>"]');
    if (statusForm && payModalEl) {
        statusForm.addEventListener('submit', function(e) {
            const statusSelect = statusForm.querySelector('select[name="status"]');
            if (statusSelect && statusSelect.value === 'Selesai') {
                e.preventDefault();
                bootstrap.Modal.getOrCreateInstance(payModalEl).show();
            }
        });
    }
});
</script>
