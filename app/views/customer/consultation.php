<div class="dashboard-wrapper py-4">
    <div class="container">
        
        <!-- Breadcrumb & Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small">
                        <li class="breadcrumb-item"><a href="<?= base_url('customer') ?>" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Konsultasi AI</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-1 text-dark">
                    <i class="bi bi-stars text-warning me-2"></i>Konsultasi AI: Diagnosis Kerusakan Rumah
                </h3>
                <p class="text-muted small mb-0">
                    Dapatkan estimasi penyebab kerusakan, kategori teknisi yang tepat, serta perkiraan rentang biaya perbaikan secara otomatis.
                </p>
            </div>
            <div>
                <a href="<?= base_url('customer') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
                </a>
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

        <!-- Hasil Analisis AI (Jika Baru Saja Submit / Membuka Detail) -->
        <?php if (!empty($result)): ?>
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white mb-5 animate-fade-in" id="aiResultSection">
                <div class="bg-gradient-ai p-4 text-white position-relative">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                        <div>
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fw-bold mb-2">
                                <i class="bi bi-robot me-1"></i> Hasil Analisis & Diagnosis AI
                            </span>
                            <h4 class="fw-bold mb-0 text-white">Rekomendasi Penanganan Masalah Rumah Anda</h4>
                        </div>
                        <a href="<?= base_url('customer/consultation') ?>" class="btn btn-light btn-sm rounded-pill px-3 fw-semibold">
                            <i class="bi bi-plus-circle me-1"></i> Konsultasi Baru
                        </a>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">
                    
                    <!-- Disclaimer Alert -->
                    <div class="alert alert-warning bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-3 small py-2 px-3 mb-4 d-flex align-items-start gap-2">
                        <i class="bi bi-shield-exclamation text-warning fs-5 flex-shrink-0"></i>
                        <div>
                            <strong>Catatan Penting:</strong> Hasil diagnosis di bawah ini merupakan <em>perkiraan awal berdasarkan informasi yang diberikan</em> dan bukan merupakan kepastian mutlak. Teknisi akan melakukan inspeksi fisik langsung untuk konfirmasi suku cadang.
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        
                        <!-- Kemungkinan Kerusakan (Ringkas & Informatif) -->
                        <div class="col-lg-7">
                            <div class="p-4 rounded-4 bg-light border border-light-subtle h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                            <span class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white" style="width: 28px; height: 28px; font-size: 0.85rem;">
                                                <i class="bi bi-search"></i>
                                            </span>
                                            <span>Diagnosa AI: Kemungkinan Kerusakan</span>
                                        </h6>
                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 xs-text fw-bold">
                                            Akurasi Analisis
                                        </span>
                                    </div>

                                    <div class="p-3 bg-white rounded-3 border-start border-primary border-4 shadow-xs mb-3">
                                        <div class="fw-bold text-dark mb-1" style="font-size: 1rem; line-height: 1.4;">
                                            <?= e($result['possible_damage'] ?? 'Terdeteksi kendala teknis pada perangkat.') ?>
                                        </div>
                                    </div>

                                    <div class="p-3 rounded-3 bg-white border border-light-subtle">
                                        <div class="xs-text text-muted fw-bold text-uppercase mb-1" style="letter-spacing: 0.5px;">
                                            <i class="bi bi-info-circle text-primary me-1"></i>Ringkasan Penjelasan:
                                        </div>
                                        <p class="text-secondary small mb-0 lh-base" style="font-size: 0.88rem;">
                                            <?= e($result['explanation'] ?? 'Diperlukan inspeksi langsung oleh teknisi berpengalaman untuk memastikan titik permasalahan.') ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kategori & Estimasi Biaya -->
                        <div class="col-lg-5">
                            <div class="d-flex flex-column gap-3 h-100">
                                
                                <?php
                                    $catName = $result['category_name'] ?? 'Teknisi Umum';
                                    $catIcon = $result['category_icon'] ?? 'bi-tools';
                                    if (stripos($catName, 'ac') !== false) {
                                        $catIcon = 'bi-snow';
                                    } elseif (stripos($catName, 'listrik') !== false) {
                                        $catIcon = 'bi-lightning-charge-fill';
                                    } elseif (stripos($catName, 'kunci') !== false) {
                                        $catIcon = 'bi-key-fill';
                                    } elseif (stripos($catName, 'elektronik') !== false) {
                                        $catIcon = 'bi-tv-fill';
                                    }
                                ?>

                                <!-- Kategori Teknisi Rekomendasi -->
                                <div class="p-3.5 rounded-4 shadow-xs" style="background: #ffffff; border: 1px solid rgba(0, 120, 212, 0.25);">
                                    <span class="text-muted xs-text fw-bold text-uppercase d-block mb-2" style="letter-spacing: 0.5px;">Kategori Mitra yang Sesuai:</span>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white shadow-xs" style="width: 44px; height: 44px; background: linear-gradient(135deg, #0078D4, #005A9E); font-size: 1.25rem;">
                                            <i class="bi <?= e($catIcon) ?>"></i>
                                        </div>
                                        <div>
                                            <h5 class="fw-bold mb-0" style="color: #071E3D;"><?= e($catName) ?></h5>
                                            <span class="xs-text text-muted">Spesialisasi Terverifikasi</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Estimasi Rentang Biaya -->
                                <div class="p-4 rounded-4 text-center flex-grow-1 d-flex flex-column justify-content-center shadow-xs" style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.25);">
                                    <span class="text-success fw-bold xs-text text-uppercase d-block mb-1" style="letter-spacing: 0.5px;">Perkiraan Estimasi Biaya</span>
                                    <div class="display-6 fw-bold text-success mb-1" style="font-size: 1.7rem;">
                                        Rp <?= number_format($result['estimated_min_price'] ?? 75000, 0, ',', '.') ?> - Rp <?= number_format($result['estimated_max_price'] ?? 250000, 0, ',', '.') ?>
                                    </div>
                                    <span class="text-muted xs-text">Estimasi jasa standar (belum termasuk suku cadang khusus jika ada).</span>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Draft Postingan Permintaan Jasa -->
                    <div class="card border border-light-subtle rounded-4 p-4 bg-white shadow-sm mb-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                            <h6 class="fw-bold text-dark mb-0">
                                <i class="bi bi-file-earmark-text-fill text-warning me-2"></i>Draf Postingan Rekomendasi AI:
                            </h6>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 xs-text fw-semibold" id="btnCopyDraft">
                                    <i class="bi bi-clipboard me-1"></i> Salin Draf
                                </button>
                                <?php if (!empty($consultationId)): ?>
                                    <a href="<?= base_url('posts/create?consultation_id=' . $consultationId) ?>" class="btn btn-primary btn-sm rounded-pill px-3 xs-text fw-semibold">
                                        <i class="bi bi-send-fill me-1"></i> Publikasikan Keluhan Ini
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <div class="fw-bold text-dark mb-1" id="draftTitleText"><?= e($result['draft_post_title'] ?? 'Permintaan Servis') ?></div>
                            <p class="text-muted small mb-0" id="draftDescText"><?= e($result['draft_post_description'] ?? '') ?></p>
                        </div>
                        <?php if (!empty($consultationId)): ?>
                            <div class="text-end">
                                <a href="<?= base_url('posts/create?consultation_id=' . $consultationId) ?>" class="btn btn-warning text-dark fw-bold rounded-pill px-4 shadow-sm">
                                    <i class="bi bi-megaphone-fill me-1"></i> Buat & Publikasikan Postingan Keluhan Sekarang &rarr;
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Rekomendasi Teknisi Terkait & Aksi WhatsApp -->
                    <div>
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-person-check-fill text-success me-2"></i>Mitra Teknisi Siap Dihubungi:</h6>
                        <div class="row g-3">
                            <?php if (!empty($recommendedTechs)): ?>
                                <?php foreach ($recommendedTechs as $tech): ?>
                                    <div class="col-md-4">
                                        <div class="p-3 rounded-3 border bg-white h-100 d-flex flex-column justify-content-between">
                                            <div>
                                                <div class="d-flex justify-content-between align-items-start mb-1">
                                                    <h6 class="fw-bold mb-0 text-dark"><?= e($tech->name) ?></h6>
                                                    <div class="text-warning xs-text fw-bold">
                                                        <i class="bi bi-star-fill"></i> <?= number_format($tech->rating ?? 5.0, 1) ?>
                                                    </div>
                                                </div>
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill xs-text mb-2">
                                                    <?= e($tech->category_name ?? $result['category_name']) ?>
                                                </span>
                                                <div class="text-muted xs-text mb-3">
                                                    <i class="bi bi-geo-alt me-1 text-danger"></i> <?= e($tech->location ?? 'Jabodetabek') ?>
                                                </div>
                                            </div>
                                            <?php
                                                $aiWaMsg = WhatsAppService::getConsultationFollowupMessage($result['draft_post_title'] ?? 'perbaikan rumah', $tech->name);
                                                $aiWaUrl = wa_url($tech->whatsapp, $aiWaMsg);
                                            ?>
                                            <a href="<?= $aiWaUrl ?>" 
                                               target="_blank" 
                                               rel="noopener noreferrer"
                                               class="btn btn-outline-success btn-sm rounded-pill w-100 fw-bold d-inline-flex align-items-center justify-content-center gap-1 shadow-xs">
                                                <i class="bi bi-whatsapp"></i> Chat WhatsApp
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        <?php endif; ?>

        <!-- Form Konsultasi AI -->
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="card-header bg-white py-3 border-light-subtle d-flex align-items-center gap-2">
                        <div class="avatar-circle bg-warning text-dark fw-bold">
                            <i class="bi bi-cpu"></i>
                        </div>
                        <h6 class="fw-bold mb-0 text-dark">Formulir Konsultasi Kerusakan</h6>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        
                        <!-- General Error -->
                        <?php if (!empty($errors['general'])): ?>
                            <div class="alert alert-danger rounded-3 small py-2 d-flex align-items-center gap-2 mb-4" role="alert">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <div><?= e($errors['general']) ?></div>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('customer/consultation') ?>" method="POST" enctype="multipart/form-data" id="consultationForm">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            
                            <!-- 1. Upload Foto Kerusakan -->
                            <div class="mb-4">
                                <label for="image" class="form-label fw-semibold small text-dark d-flex justify-content-between">
                                    <span><i class="bi bi-camera-fill text-primary me-1"></i> 1. Upload Foto Kerusakan (Opsional)</span>
                                    <span class="text-muted xs-text">Maks 5 MB (JPG, PNG, WEBP)</span>
                                </label>
                                <input type="file" 
                                       class="form-control rounded-3 <?= !empty($errors['image']) ? 'is-invalid' : '' ?>" 
                                       id="imageInput" 
                                       name="image" 
                                       accept="image/jpeg,image/png,image/webp">
                                <?php if (!empty($errors['image'])): ?>
                                    <div class="invalid-feedback small"><?= e($errors['image']) ?></div>
                                <?php endif; ?>

                                <!-- Image Preview Container -->
                                <div id="imagePreviewWrapper" class="mt-3 d-none">
                                    <div class="position-relative d-inline-block">
                                        <img id="imagePreview" src="" alt="Preview Foto Kerusakan" class="rounded-3 border shadow-sm" style="max-height: 200px; max-width: 100%; object-fit: cover;">
                                        <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 m-1" id="btnRemoveImage" title="Hapus Foto">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. Detail Kerusakan -->
                            <div class="mb-4">
                                <label for="damage_detail" class="form-label fw-semibold small text-dark">
                                    <i class="bi bi-card-text text-primary me-1"></i> 2. Detail Keluhan / Kerusakan <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control rounded-3 <?= !empty($errors['damage_detail']) ? 'is-invalid' : '' ?>" 
                                          id="damage_detail" 
                                          name="damage_detail" 
                                          rows="3" 
                                          placeholder="Contoh: AC kamar mengeluarkan suara mendesis dan hanya hembusan angin biasa tanpa rasa dingin sama sekali..." 
                                          required><?= old('damage_detail') ?></textarea>
                                <?php if (!empty($errors['damage_detail'])): ?>
                                    <div class="invalid-feedback small"><?= e($errors['damage_detail']) ?></div>
                                <?php else: ?>
                                    <div class="form-text xs-text">Jelaskan tanda fisik kerusakan (suara, bau gosong, rembesan air, mati total, lampu indikator, dll).</div>
                                <?php endif; ?>
                            </div>

                            <!-- 3. Kronologi Kejadian -->
                            <div class="mb-4">
                                <label for="chronology" class="form-label fw-semibold small text-dark">
                                    <i class="bi bi-clock-history text-primary me-1"></i> 3. Kronologi Kejadian <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control rounded-3 <?= !empty($errors['chronology']) ? 'is-invalid' : '' ?>" 
                                          id="chronology" 
                                          name="chronology" 
                                          rows="3" 
                                          placeholder="Contoh: Terjadi kemarin malam setelah pemakaian seharian, tiba-tiba listrik padam sejenak dan saat dinyalakan kembali muncul gejala tersebut..." 
                                          required><?= old('chronology') ?></textarea>
                                <?php if (!empty($errors['chronology'])): ?>
                                    <div class="invalid-feedback small"><?= e($errors['chronology']) ?></div>
                                <?php else: ?>
                                    <div class="form-text xs-text">Ceritakan kapan mulai rusak dan aktivitas sebelum kerusakan terjadi.</div>
                                <?php endif; ?>
                            </div>

                            <!-- 4. Tombol Submit Konsultasi -->
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill w-100 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" id="btnSubmitConsultation">
                                <i class="bi bi-stars fs-5"></i>
                                <span>Analisis Kerusakan dengan AI Sekarang</span>
                            </button>

                        </form>
                    </div>
                </div>
            </div>

            <!-- Petunjuk & Riwayat Konsultasi -->
            <div class="col-lg-4">
                
                <!-- Info Petunjuk Box -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                    <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-lightbulb-fill text-warning me-2"></i>Tips Konsultasi Akurat:</h6>
                    <ul class="list-unstyled small text-muted d-flex flex-column gap-2 mb-0">
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                            <span>Sertakan foto yang jelas dari unit/peralatan yang bermasalah.</span>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                            <span>Cantumkan merek atau jenis perangkat jika diketahui.</span>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                            <span>Ceritakan apakah perangkat pernah diservis sebelumnya.</span>
                        </li>
                    </ul>
                </div>

                <!-- Riwayat Konsultasi Saya -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history text-primary me-2"></i>Riwayat Konsultasi</h6>
                        <span class="badge bg-light text-muted border xs-text"><?= count($history) ?> Analisis</span>
                    </div>

                    <?php if (!empty($history)): ?>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($history as $item): ?>
                                <div class="p-3 rounded-3 border bg-light hover-shadow transition">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill xs-text">
                                            <?= e($item->ai_data['category_name'] ?? 'Teknisi') ?>
                                        </span>
                                        <span class="text-muted xs-text"><?= e($item->created_at) ?></span>
                                    </div>
                                    <p class="text-dark small fw-semibold mb-1 line-clamp-2">
                                        <?= e($item->damage_detail) ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-light-subtle xs-text">
                                        <span class="text-success fw-bold">Rp <?= number_format($item->estimated_min_price, 0, ',', '.') ?> - <?= number_format($item->estimated_max_price, 0, ',', '.') ?></span>
                                        <a href="<?= base_url('customer/consultationDetail/' . $item->id) ?>" class="text-decoration-none fw-semibold text-primary">
                                            Lihat <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted xs-text">
                            Belum ada riwayat konsultasi. Isi formulir di samping untuk memulai.
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Image Preview Handling
    const imageInput = document.getElementById('imageInput');
    const previewWrapper = document.getElementById('imagePreviewWrapper');
    const imagePreview = document.getElementById('imagePreview');
    const btnRemoveImage = document.getElementById('btnRemoveImage');

    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    imagePreview.src = e.target.result;
                    previewWrapper.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            }
        });

        if (btnRemoveImage) {
            btnRemoveImage.addEventListener('click', function () {
                imageInput.value = '';
                imagePreview.src = '';
                previewWrapper.classList.add('d-none');
            });
        }
    }

    // Copy Draft Text Button
    const btnCopyDraft = document.getElementById('btnCopyDraft');
    if (btnCopyDraft) {
        btnCopyDraft.addEventListener('click', () => {
            const title = document.getElementById('draftTitleText')?.innerText || '';
            const desc = document.getElementById('draftDescText')?.innerText || '';
            const fullText = `${title}\n\n${desc}`;

            navigator.clipboard.writeText(fullText).then(() => {
                const originalHtml = btnCopyDraft.innerHTML;
                btnCopyDraft.innerHTML = '<i class="bi bi-check-lg text-success me-1"></i> Tersalin!';
                setTimeout(() => {
                    btnCopyDraft.innerHTML = originalHtml;
                }, 2000);
            });
        });
    }

    // Scroll to AI Result if present
    const aiResultSection = document.getElementById('aiResultSection');
    if (aiResultSection) {
        aiResultSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
});
</script>
