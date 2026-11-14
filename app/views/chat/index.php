<div class="chat-index-wrapper py-4">
    <div class="container" style="max-width: 960px;">
        
        <!-- Header Page -->
        <div class="card border-0 shadow-sm rounded-4 bg-primary text-white p-4 mb-4 position-relative overflow-hidden">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 position-relative z-1">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-white text-primary rounded-pill px-3 py-1 fw-bold">
                            <i class="bi bi-chat-dots-fill me-1"></i> Percakapan Internal
                        </span>
                        <span class="text-white-50 small">Komunikasi Langsung Sistem</span>
                    </div>
                    <h2 class="fw-bold mb-1 text-white">Daftar Chat & Konsultasi</h2>
                    <p class="text-white-50 mb-0 small">
                        Percakapan aktif antara Anda dengan <?= ($user->role === 'customer') ? 'Mitra Teknisi pilihan' : 'Pelanggan terkait postingan' ?>.
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= base_url($user->role === 'customer' ? 'posts/my' : 'technician/my-responses') ?>" class="btn btn-light rounded-pill btn-sm px-3 fw-semibold">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke <?= ($user->role === 'customer') ? 'Postingan Saya' : 'Tawaran Saya' ?>
                    </a>
                </div>
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

        <!-- List Conversations -->
        <?php if (!empty($conversations)): ?>
            <div class="row g-3">
                <?php foreach ($conversations as $conv): ?>
                    <?php 
                        $isCust = ((int)$user->id === (int)$conv->customer_id);
                        $partnerName = $isCust ? $conv->technician_name : $conv->customer_name;
                        $partnerWhatsapp = $isCust ? $conv->technician_whatsapp : $conv->customer_whatsapp;
                        $partnerRole = $isCust ? 'Mitra Teknisi' : 'Pelanggan';
                        $partnerRoleBadge = $isCust ? 'bg-success' : 'bg-primary';
                        $hasUnread = ((int)$conv->unread_count > 0);

                        $waMsg = "Halo " . $partnerName . ", saya " . $user->name . " terkait postingan '" . $conv->post_title . "' di BANTUIN.";
                        $waUrl = wa_url($partnerWhatsapp, $waMsg);
                        $roomUrl = base_url('chat/room/' . $conv->id);
                    ?>
                    <div class="col-12">
                        <div class="card border rounded-4 p-3 p-md-4 bg-white conversation-card shadow-sm position-relative <?= $hasUnread ? 'border-primary border-opacity-50' : 'border-light-subtle' ?>"
                             onclick="window.location.href='<?= $roomUrl ?>'"
                             style="cursor: pointer; transition: all 0.2s ease;">
                            
                            <div class="d-flex align-items-start gap-3">
                                <!-- Avatar -->
                                <div class="avatar-circle rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm flex-shrink-0"
                                     style="width: 50px; height: 50px; background: linear-gradient(135deg, <?= $isCust ? '#198754, #20c997' : '#003049, #024b74' ?>); font-size: 1.25rem;">
                                    <?= strtoupper(substr($partnerName ?? 'U', 0, 1)) ?>
                                </div>
                                
                                <!-- Info Content -->
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-1">
                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                            <h5 class="fw-bold text-dark mb-0"><?= e($partnerName) ?></h5>
                                            <span class="badge <?= $partnerRoleBadge ?> rounded-pill xs-text">
                                                <?= $partnerRole ?>
                                            </span>
                                            <span class="badge bg-light text-secondary border rounded-pill xs-text">
                                                <i class="bi <?= e($conv->category_icon ?? 'bi-tools') ?> me-1"></i><?= e($conv->category_name) ?>
                                            </span>
                                            <?php if ($hasUnread): ?>
                                                <span class="badge bg-danger rounded-pill px-2 py-1 xs-text fw-semibold">
                                                    <i class="bi bi-envelope-fill me-1"></i><?= (int)$conv->unread_count ?> baru
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($conv->last_message_time)): ?>
                                            <span class="text-muted xs-text flex-shrink-0">
                                                <i class="bi bi-clock me-1"></i><?= date('d M, H:i', strtotime($conv->last_message_time)) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Post Info -->
                                    <div class="text-muted small mb-2 d-flex align-items-center gap-2 text-truncate">
                                        <span class="text-truncate fw-semibold text-dark" title="<?= e($conv->post_title) ?>">
                                            <i class="bi bi-card-heading text-primary me-1"></i><?= e($conv->post_title) ?>
                                        </span>
                                        <span class="text-secondary flex-shrink-0">&bull;</span>
                                        <span class="badge bg-info bg-opacity-10 text-info-emphasis rounded-pill xs-text flex-shrink-0"><?= e($conv->post_status) ?></span>
                                    </div>

                                    <!-- Last Message Preview -->
                                    <div class="p-2 px-3 rounded-3 bg-light text-dark small d-flex align-items-center justify-content-between gap-2 border border-light-subtle mb-3">
                                        <div class="text-truncate">
                                            <?php if (!empty($conv->last_message)): ?>
                                                <span class="text-muted me-1">
                                                    <?= ((int)$conv->last_sender_id === (int)$user->id) ? '<strong>Anda:</strong>' : '<strong>' . e($partnerName) . ':</strong>' ?>
                                                </span>
                                                <span class="<?= $hasUnread ? 'fw-bold text-dark' : 'text-secondary' ?>"><?= e($conv->last_message) ?></span>
                                            <?php else: ?>
                                                <em class="text-muted">Belum ada pesan. Klik untuk mulai chat pribadi.</em>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Action Buttons: Clean, left-aligned, natural flow, never pushed too far to the right -->
                                    <div class="d-flex flex-wrap align-items-center gap-2 pt-2 border-top" onclick="event.stopPropagation();">
                                        <a href="<?= $roomUrl ?>" 
                                           class="btn btn-primary rounded-pill btn-sm px-3 py-1 fw-bold shadow-sm d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-chat-dots-fill"></i>
                                            <span>Balas Chat</span>
                                        </a>

                                        <a href="<?= $waUrl ?>" 
                                           target="_blank" 
                                           rel="noopener noreferrer"
                                           class="btn btn-outline-success rounded-pill btn-sm px-3 py-1 fw-bold d-inline-flex align-items-center gap-1"
                                           title="Buka Chat WhatsApp">
                                            <i class="bi bi-whatsapp"></i> WhatsApp
                                        </a>

                                        <a href="<?= base_url('posts/detail/' . $conv->post_id) ?>" 
                                           class="btn btn-light border rounded-pill btn-sm px-3 py-1 fw-semibold text-secondary"
                                           title="Lihat Detail Postingan">
                                            <i class="bi bi-file-earmark-text me-1"></i> Postingan
                                        </a>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm rounded-4 p-5 bg-white text-center">
                <div class="py-4">
                    <div class="d-inline-flex p-4 rounded-circle bg-primary bg-opacity-10 text-primary mb-3">
                        <i class="bi bi-chat-square-text fs-1"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Belum Ada Percakapan Chat</h5>
                    <p class="text-muted small mx-auto mb-4" style="max-width: 500px;">
                        Percakapan internal otomatis dibuat saat Pelanggan memilih Mitra Teknisi untuk menangani postingan keluhan.
                    </p>
                    <a href="<?= base_url($user->role === 'customer' ? 'posts/my' : 'posts') ?>" class="btn btn-primary rounded-pill px-4 fw-bold">
                        <i class="bi bi-search me-1"></i> <?= ($user->role === 'customer') ? 'Kelola Postingan Keluhan' : 'Jelajahi Postingan Keluhan' ?>
                    </a>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<style>
.hover-shadow {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important;
}
.xs-text {
    font-size: 0.75rem;
}
</style>
