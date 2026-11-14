<div class="chat-room-wrapper py-2">
    <div class="container" style="max-width: 1040px;">
        
        <!-- Breadcrumb & Back -->
        <div class="chat-top-toolbar d-flex align-items-center justify-content-between mb-2">
            <a href="<?= base_url('chat') ?>" class="btn btn-outline-secondary rounded-pill btn-sm px-3 fw-semibold d-inline-flex align-items-center gap-1 shadow-2xs" style="font-size: 0.82rem; padding: 0.22rem 0.75rem;">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Chat
            </a>
            <a href="<?= base_url('posts/detail/' . $conversation->post_id) ?>" class="text-decoration-none small text-muted d-inline-flex align-items-center gap-1 hover-primary" style="font-size: 0.82rem;" target="_blank">
                <span>Lihat Detail Postingan</span>
                <i class="bi bi-box-arrow-up-right"></i>
            </a>
        </div>

        <!-- Chat Main Card (1-Screen Full Viewport) -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white chat-main-card">
            
            <!-- 1. Header Chat -->
            <div class="card-header bg-white border-bottom py-2.5 px-3 px-md-4">
                <div class="d-flex flex-row justify-content-between align-items-center gap-3">
                    
                    <div class="d-flex align-items-center gap-3 min-w-0">
                        <!-- Avatar Lawan Chat -->
                        <div class="avatar-circle rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                             style="width: 44px; height: 44px; background: linear-gradient(135deg, <?= ($partnerRole === 'technician') ? '#10B981, #059669' : '#0078D4, #005A9E' ?>); font-size: 1.15rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                            <?= strtoupper(substr($partnerName ?? 'U', 0, 1)) ?>
                        </div>

                        <div class="min-w-0 d-flex flex-column gap-1">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <h6 class="fw-bold text-dark mb-0 text-truncate" style="max-width: 240px; font-size: 1.02rem; line-height: 1.2;"><?= e($partnerName) ?></h6>
                                <span class="badge rounded-pill px-2.5 py-1 fw-bold" 
                                      style="font-size: 0.72rem; <?= ($partnerRole === 'technician') ? 'background: #E8F5E9; color: #15803d; border: 1px solid #bbf7d0;' : 'background: #EFF6FF; color: #1d4ed8; border: 1px solid #bfdbfe;' ?>">
                                    <i class="bi <?= ($partnerRole === 'technician') ? 'bi-patch-check-fill' : 'bi-person-fill' ?> me-1"></i><?= $partnerRoleLabel ?>
                                </span>
                                <!-- Kategori Jasa -->
                                <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1 fw-semibold" 
                                      style="font-size: 0.72rem;">
                                    <i class="bi <?= e($conversation->category_icon ?? 'bi-tools') ?> text-primary me-1"></i><?= e($conversation->category_name) ?>
                                </span>
                            </div>

                            <div class="text-muted text-truncate d-flex align-items-center gap-1.5" style="max-width: 460px; font-size: 0.8rem;" title="<?= e($conversation->post_title) ?>">
                                <i class="bi bi-file-earmark-text text-primary flex-shrink-0"></i>
                                <span class="text-truncate"><?= e($conversation->post_title) ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi di Header Chat -->
                    <div class="d-flex align-items-center gap-2 flex-shrink-0 ms-2">
                        <?php 
                            $isPostCompleted = in_array(strtolower($conversation->post_status ?? ''), ['selesai', 'completed'], true);
                        ?>
                        <?php if ($isPostCompleted): ?>
                            <!-- Status Selesai: Nego Terkunci & Tidak Bisa Disentuh Lagi Sesuai Permintaan User -->
                            <button type="button" 
                                    class="btn btn-light text-muted btn-sm rounded-pill fw-bold border d-inline-flex align-items-center gap-1.5 opacity-75 shadow-none"
                                    disabled
                                    style="font-size: 0.82rem; padding: 0.32rem 0.85rem; cursor: not-allowed;"
                                    title="Pengerjaan sudah berstatus selesai. Kesepakatan harga sudah final dan terkunci.">
                                <i class="bi bi-lock-fill text-success"></i>
                                <span>Harga Final</span>
                            </button>
                        <?php else: ?>
                            <button type="button" 
                                    class="btn btn-warning text-dark btn-sm rounded-pill fw-bold shadow-xs d-inline-flex align-items-center gap-1.5"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#negotiateModal"
                                    style="font-size: 0.82rem; padding: 0.32rem 0.85rem;"
                                    title="Ajukan Negosiasi / Kesepakatan Harga">
                                <i class="bi bi-tag-fill"></i>
                                <span class="d-none d-sm-inline">Nego</span> Harga
                            </button>
                        <?php endif; ?>
                        
                        <a href="<?= $waUrl ?>" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="btn btn-whatsapp-pill text-white fw-bold shadow-xs d-inline-flex align-items-center gap-1.5 text-decoration-none"
                           style="font-size: 0.82rem; padding: 0.32rem 0.85rem;"
                           title="Buka Chat WhatsApp">
                            <i class="bi bi-whatsapp"></i>
                            <span class="d-none d-sm-inline">WhatsApp</span>
                        </a>
                    </div>

                </div>
            </div>

            <!-- 2. Body Chat (Scroll Area) -->
            <div id="chatMessagesContainer" class="card-body p-3 p-md-3">
                
                <!-- Notice Info Keamanan & WA -->
                <div class="text-center my-2">
                    <div class="badge bg-white text-muted border px-3 py-2 rounded-pill fw-normal xs-text shadow-sm">
                        <i class="bi bi-shield-check text-success me-1"></i>
                        Percakapan terenkripsi untuk postingan: <strong><?= e($conversation->post_title) ?></strong>
                    </div>
                </div>

                <!-- Status Kesepakatan / Negosiasi Banner -->
                <div id="negotiationBannerContainer">
                    <?php if (!empty($latestNegotiation)): ?>
                        <?php if ($latestNegotiation->status === 'accepted'): ?>
                            <div class="alert alert-success d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 p-3 rounded-4 border-0 shadow-sm mb-3 bg-white">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-success text-white shadow-xs flex-shrink-0" style="width: 38px; height: 38px;">
                                        <i class="bi bi-check-lg fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="xs-text text-muted fw-semibold">Harga Kesepakatan (Deal Disetujui):</div>
                                        <div class="fw-bold text-success fs-5">
                                            Rp <?= number_format($latestNegotiation->proposed_price, 0, ',', '.') ?>
                                            <?php if (!empty($latestNegotiation->note)): ?>
                                                <span class="badge bg-light text-muted fw-normal small ms-1 border"><?= e($latestNegotiation->note) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php if ($user->role === 'customer' && $conversation->post_status !== 'Selesai'): ?>
                                    <a href="<?= base_url('posts/detail/' . $conversation->post_id . '?open_payment=1') ?>" class="btn btn-success btn-sm rounded-pill px-3 fw-bold shadow-xs">
                                        <i class="bi bi-cash-coin me-1"></i> Bayar &amp; Selesaikan
                                    </a>
                                <?php else: ?>
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1.5 fw-bold xs-text">
                                        <i class="bi bi-lock-fill me-1"></i> Terkunci
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php elseif ($latestNegotiation->status === 'pending'): ?>
                            <?php $isMyNego = ((int)$latestNegotiation->sender_id === (int)$user->id); ?>
                            <div class="alert alert-warning d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 p-3 rounded-4 border-0 shadow-sm mb-3 bg-white" id="pendingNegoAlert">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning text-dark shadow-xs flex-shrink-0" style="width: 38px; height: 38px;">
                                        <i class="bi bi-tag-fill fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="xs-text text-muted fw-semibold">
                                            <?= $isMyNego ? 'Tawaran Harga Anda (Menunggu Tanggapan):' : 'Tawaran Harga Masuk dari ' . e($partnerName) . ':' ?>
                                        </div>
                                        <div class="fw-bold text-dark fs-5">
                                            Rp <?= number_format($latestNegotiation->proposed_price, 0, ',', '.') ?>
                                            <?php if (!empty($latestNegotiation->note)): ?>
                                                <span class="badge bg-light text-muted fw-normal small ms-1 border"><?= e($latestNegotiation->note) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 align-items-center">
                                    <?php if (!$isMyNego): ?>
                                        <button type="button" class="btn btn-success btn-sm rounded-pill px-3 fw-bold btn-accept-nego shadow-xs" data-nego-id="<?= (int)$latestNegotiation->id ?>">
                                            <i class="bi bi-check-lg me-1"></i> Terima (Deal)
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-semibold btn-reject-nego" data-nego-id="<?= (int)$latestNegotiation->id ?>">
                                            <i class="bi bi-x-lg me-1"></i> Tolak
                                        </button>
                                    <?php else: ?>
                                        <span class="badge bg-warning bg-opacity-25 text-dark rounded-pill px-3 py-1.5 xs-text fw-semibold">
                                            <i class="bi bi-hourglass-split me-1"></i> Menunggu Konfirmasi
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div id="messagesList" class="d-flex flex-column gap-3 pt-3">
                    <?php 
                        $lastMsgId = 0; 
                        if (!empty($messages)): 
                    ?>
                        <?php foreach ($messages as $msg): ?>
                            <?php 
                                if ((int)$msg->id > $lastMsgId) {
                                    $lastMsgId = (int)$msg->id;
                                }
                                $isCustomerMsg = ($msg->sender_role === 'customer');
                                $isMyMsg = ((int)$msg->sender_id === (int)$user->id);

                                // Check if message is a special negotiation event
                                $isNegoOffer = preg_match('/^\[NEGO:(\d+):(\d+):([^:]+):(.*)\]$/', $msg->message, $nMatch);
                                $isNegoDeal = preg_match('/^\[NEGO_DEAL:(\d+):(\d+)\]$/', $msg->message, $dMatch);
                                $isNegoReject = preg_match('/^\[NEGO_REJECT:(\d+):(\d+)\]$/', $msg->message, $rMatch);
                            ?>

                            <?php if ($isNegoDeal): ?>
                                <!-- Centered Deal Announcement -->
                                <div class="text-center my-3 message-item" data-message-id="<?= (int)$msg->id ?>">
                                    <div class="d-inline-flex flex-column align-items-center p-3 rounded-4 shadow-sm border border-success bg-white" style="max-width: 440px;">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-success text-white mb-2 shadow-xs" style="width: 44px; height: 44px;">
                                            <i class="bi bi-check-lg fs-3 fw-bold"></i>
                                        </div>
                                        <h6 class="fw-bold text-success mb-1">Kesepakatan Harga Disetujui (DEAL)</h6>
                                        <div class="display-6 fw-bold text-dark mb-1" style="font-size: 1.45rem;">
                                            Rp <?= number_format((int)$dMatch[2], 0, ',', '.') ?>
                                        </div>
                                        <p class="text-muted xs-text mb-2">Kedua pihak telah menyepakati biaya pengerjaan. Nominal ini dikunci untuk penyelesaian transaksi.</p>
                                        <?php if ($user->role === 'customer' && $conversation->post_status !== 'Selesai'): ?>
                                            <a href="<?= base_url('posts/detail/' . $conversation->post_id . '?open_payment=1') ?>" class="btn btn-success btn-sm rounded-pill px-4 fw-bold shadow-xs">
                                                <i class="bi bi-cash-coin me-1"></i> Bayar &amp; Selesaikan Sekarang
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>

                            <?php elseif ($isNegoReject): ?>
                                <!-- Centered Rejection Pill -->
                                <div class="text-center my-2 message-item" data-message-id="<?= (int)$msg->id ?>">
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-2 xs-text">
                                        <i class="bi bi-x-circle-fill me-1"></i> Tawaran harga Rp <?= number_format((int)$rMatch[2], 0, ',', '.') ?> belum disepakati
                                    </span>
                                </div>

                            <?php elseif ($isNegoOffer): ?>
                                <?php
                                    $nId = (int)$nMatch[1];
                                    $nPrice = (int)$nMatch[2];
                                    $nStatus = $nMatch[3];
                                    $nNote = base64_decode($nMatch[4]);
                                    $isCurrentNegoStillPending = (!empty($latestNegotiation) && (int)$latestNegotiation->id === $nId && $latestNegotiation->status === 'pending');
                                ?>
                                <!-- Negotiation Proposal Card -->
                                <div class="d-flex <?= $isMyMsg ? 'justify-content-end' : 'justify-content-start' ?> message-item mb-2" data-message-id="<?= (int)$msg->id ?>">
                                    <div class="message-bubble-wrapper" style="max-width: 75%;">
                                        <div class="xs-text mb-1 <?= $isMyMsg ? 'text-end text-primary' : 'text-start text-warning' ?> fw-semibold">
                                            <?= $isMyMsg ? 'Anda (Mengajukan Harga)' : e($msg->sender_name) . ' (Mengajukan Harga)' ?>
                                        </div>
                                        <div class="card border <?= $isMyMsg ? 'border-primary bg-primary text-white' : 'border-warning bg-white text-dark' ?> shadow-sm rounded-4 overflow-hidden" style="min-width: 270px;">
                                            <div class="p-3">
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <span class="badge <?= $isMyMsg ? 'bg-white text-primary' : 'bg-warning bg-opacity-25 text-dark' ?> rounded-pill px-2.5 py-1 xs-text fw-bold">
                                                        <i class="bi bi-tag-fill me-1"></i> Penawaran Harga
                                                    </span>
                                                    <span class="xs-text <?= $isMyMsg ? 'text-white-50' : 'text-muted' ?>"><?= date('H:i', strtotime($msg->created_at)) ?></span>
                                                </div>
                                                <div class="display-6 fw-bold <?= $isMyMsg ? 'text-white' : 'text-dark' ?> mb-1" style="font-size: 1.4rem;">
                                                    Rp <?= number_format($nPrice, 0, ',', '.') ?>
                                                </div>
                                                <?php if (!empty($nNote)): ?>
                                                    <p class="small mb-2 fst-italic <?= $isMyMsg ? 'text-white-50' : 'text-muted' ?>">"<?= e($nNote) ?>"</p>
                                                <?php endif; ?>

                                                <?php if (!$isMyMsg && $isCurrentNegoStillPending && !$isPostCompleted): ?>
                                                    <div class="pt-2 border-top border-light-subtle d-flex gap-2">
                                                        <button type="button" class="btn btn-success btn-sm rounded-pill w-100 fw-bold btn-accept-nego shadow-xs" data-nego-id="<?= $nId ?>">
                                                            <i class="bi bi-check-lg me-1"></i> Terima (Deal)
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill w-50 fw-semibold btn-reject-nego" data-nego-id="<?= $nId ?>">
                                                            Tolak
                                                        </button>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="pt-2 border-top <?= $isMyMsg ? 'border-white border-opacity-25 text-white-50' : 'border-light-subtle text-muted' ?> xs-text d-flex align-items-center gap-1">
                                                        <i class="bi bi-tag"></i>
                                                        <span><?= $isMyMsg ? 'Tawaran telah diajukan' : 'Tawaran harga masuk' ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php else: ?>
                                <!-- Bubble Message Regular: Pengguna aktif di KANAN, Balasan di KIRI -->
                                <div class="d-flex <?= $isMyMsg ? 'justify-content-end' : 'justify-content-start' ?> message-item mb-2" data-message-id="<?= (int)$msg->id ?>">
                                    
                                    <div class="message-bubble-wrapper" style="max-width: 75%;">
                                        
                                        <!-- Sender Label -->
                                        <div class="xs-text mb-1 <?= $isMyMsg ? 'text-end text-primary' : ($isCustomerMsg ? 'text-start text-primary' : 'text-start text-success') ?> fw-semibold">
                                            <?php if ($isMyMsg): ?>
                                                <span>Anda</span> <i class="bi bi-person-fill ms-1"></i>
                                            <?php elseif ($isCustomerMsg): ?>
                                                <i class="bi bi-person-fill me-1"></i><span><?= e($msg->sender_name) ?> (Pelanggan)</span>
                                            <?php else: ?>
                                                <i class="bi bi-tools me-1"></i><span><?= e($msg->sender_name) ?> (Teknisi)</span>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Bubble Content -->
                                        <div class="p-3 rounded-4 shadow-sm <?= $isMyMsg ? 'text-white' : 'bg-white text-dark border border-light-subtle' ?>" 
                                             style="word-break: break-word; <?= $isMyMsg ? 'background: linear-gradient(135deg, #0078D4, #005A9E); border-bottom-right-radius: 4px !important;' : 'border-bottom-left-radius: 4px !important;' ?> text-align: left;">
                                            <div class="message-text" style="white-space: pre-wrap; font-size: 0.95rem;"><?= e($msg->message) ?></div>
                                            
                                            <div class="d-flex align-items-center <?= $isMyMsg ? 'justify-content-end text-white-50' : 'justify-content-start text-muted' ?> gap-1 mt-1 xs-text">
                                                <span><?= date('H:i', strtotime($msg->created_at)) ?></span>
                                                <?php if ($isMyMsg): ?>
                                                    <i class="bi <?= ((int)$msg->is_read === 1) ? 'bi-check-all text-info' : 'bi-check' ?> fs-6" title="<?= ((int)$msg->is_read === 1) ? 'Sudah dibaca' : 'Terkirim' ?>"></i>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div id="emptyChatMessage" class="text-center py-5 text-muted small">
                            <i class="bi bi-chat-heart fs-1 text-primary opacity-50 d-block mb-2"></i>
                            Percakapan belum dimulai. Silakan kirim pesan pertama untuk menyapa dan mendiskusikan perbaikan.
                        </div>
                    <?php endif; ?>
                </div>

            </div>

            <!-- 3. Footer Chat (Input Message & Submit) -->
            <div class="card-footer bg-white border-top p-3 p-md-4">
                <form id="chatForm" class="m-0" autocomplete="off">
                    <input type="hidden" name="csrf_token" id="chatCsrfToken" value="<?= csrf_token() ?>">
                    <input type="hidden" name="conversation_id" id="conversationId" value="<?= (int)$conversation->id ?>">
                    
                    <div class="input-group">
                        <textarea name="message" 
                                  id="messageInput" 
                                  class="form-control rounded-start-pill px-3 py-2 border-primary border-opacity-25" 
                                  rows="1" 
                                  placeholder="Ketik pesan Anda di sini... (Tekan Enter untuk mengirim)"
                                  style="resize: none; max-height: 80px; font-size: 0.92rem;"
                                  required></textarea>
                        
                        <button type="submit" id="btnSendChat" class="btn btn-primary rounded-end-pill px-4 fw-bold shadow-sm d-flex align-items-center gap-2">
                            <span>Kirim</span>
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-1 px-2 xs-text text-muted" style="font-size: 0.76rem;">
                        <span><i class="bi bi-info-circle me-1"></i>Tekan <strong>Enter</strong> untuk kirim, <strong>Shift + Enter</strong> untuk baris baru</span>
                        <span id="charCount">0/2000</span>
                    </div>
                </form>
            </div>

        </div>

    </div>
</div>

<!-- Modal Ajukan Negosiasi / Kesepakatan Harga -->
<div class="modal fade" id="negotiateModal" tabindex="-1" aria-labelledby="negotiateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning text-dark shadow-xs" style="width: 42px; height: 42px;">
                        <i class="bi bi-tag-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark" id="negotiateModalLabel">Negosiasi &amp; Ajukan Harga</h5>
                        <p class="text-muted xs-text mb-0">Sepakati biaya pengerjaan sebelum atau sesudah servis</p>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="negotiateForm">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="conversation_id" value="<?= (int)$conversation->id ?>">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-1">Nominal Harga Kesepakatan (Rp):</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 fw-bold text-muted">Rp</span>
                            <input type="number" 
                                   name="price" 
                                   id="negoPriceInput" 
                                   class="form-control form-control-lg fw-bold text-dark border-start-0 fs-4" 
                                   placeholder="Contoh: 150000" 
                                   min="5000" 
                                   step="5000"
                                   required>
                        </div>
                    </div>

                    <!-- Rekomendasi Cepat -->
                    <div class="mb-3">
                        <label class="form-label text-muted xs-text fw-semibold mb-1">Pilihan Estimasi (Klik untuk memasukkan nominal):</label>
                        <div class="d-flex flex-wrap gap-2">
                            <?php 
                                $minP = (int)($conversation->estimated_min_price ?? 75000);
                                $maxP = (int)($conversation->estimated_max_price ?? 200000);
                                $midP = (int)(($minP + $maxP) / 2);
                            ?>
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill xs-text quick-price-btn" data-val="<?= $minP ?>">
                                Rp <?= number_format($minP, 0, ',', '.') ?> (Min)
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill xs-text quick-price-btn" data-val="<?= $midP ?>">
                                Rp <?= number_format($midP, 0, ',', '.') ?> (Tengah)
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill xs-text quick-price-btn" data-val="<?= $maxP ?>">
                                Rp <?= number_format($maxP, 0, ',', '.') ?> (Maks)
                            </button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark small mb-1">Catatan Kesepakatan (Opsional):</label>
                        <textarea name="note" 
                                  id="negoNoteInput" 
                                  class="form-control rounded-3" 
                                  rows="2" 
                                  placeholder="Misal: Sudah termasuk biaya jasa perbaikan dan penggantian spare part kunci baru"></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="btnSubmitNego" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center gap-2">
                            <i class="bi bi-send-fill"></i>
                            <span>Kirim Penawaran Harga</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.xs-text {
    font-size: 0.75rem;
}
.message-bubble-wrapper {
    transition: all 0.2s ease;
}
#chatMessagesContainer::-webkit-scrollbar {
    width: 6px;
}
#chatMessagesContainer::-webkit-scrollbar-track {
    background: #f1f5f9;
}
#chatMessagesContainer::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
#chatMessagesContainer::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatContainer = document.getElementById('chatMessagesContainer');
    const messagesList = document.getElementById('messagesList');
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    const btnSend = document.getElementById('btnSendChat');
    const charCount = document.getElementById('charCount');
    const csrfToken = document.getElementById('chatCsrfToken').value;
    const conversationId = parseInt(document.getElementById('conversationId').value, 10);
    const currentUserId = <?= (int)$user->id ?>;
    const isPostCompleted = <?= $isPostCompleted ? 'true' : 'false' ?>;

    let lastMessageId = <?= (int)$lastMsgId ?>;

    // Scroll to bottom helper
    function scrollToBottom(smooth = true) {
        if (!chatContainer) return;
        chatContainer.scrollTo({
            top: chatContainer.scrollHeight,
            behavior: smooth ? 'smooth' : 'auto'
        });
    }

    // Auto scroll on load
    scrollToBottom(false);

    // Auto-expand textarea & character counter
    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        charCount.textContent = this.value.length + '/2000';
    });

    // Enter to submit, Shift+Enter for new line
    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            chatForm.dispatchEvent(new Event('submit'));
        }
    });

    // Helper: Build Message Bubble DOM element
    function createMessageElement(msg) {
        const isCustomerMsg = (msg.sender_role === 'customer');
        const isMyMsg = (parseInt(msg.sender_id, 10) === currentUserId);
        const rawText = msg.message || '';

        // Check special negotiation messages
        const negoOfferMatch = rawText.match(/^\[NEGO:(\d+):(\d+):([^:]+):(.*)\]$/);
        const negoDealMatch = rawText.match(/^\[NEGO_DEAL:(\d+):(\d+)\]$/);
        const negoRejectMatch = rawText.match(/^\[NEGO_REJECT:(\d+):(\d+)\]$/);

        if (negoDealMatch) {
            const price = parseInt(negoDealMatch[2], 10).toLocaleString('id-ID');
            const wrapDiv = document.createElement('div');
            wrapDiv.className = 'text-center my-3 message-item animate__animated animate__fadeIn';
            wrapDiv.setAttribute('data-message-id', msg.id);
            wrapDiv.innerHTML = `
                <div class="d-inline-flex flex-column align-items-center p-3 rounded-4 shadow-sm border border-success bg-white" style="max-width: 440px;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-success text-white mb-2 shadow-xs" style="width: 44px; height: 44px;">
                        <i class="bi bi-check-lg fs-3 fw-bold"></i>
                    </div>
                    <h6 class="fw-bold text-success mb-1">Kesepakatan Harga Disetujui (DEAL)</h6>
                    <div class="display-6 fw-bold text-dark mb-1" style="font-size: 1.45rem;">
                        Rp ${price}
                    </div>
                    <p class="text-muted xs-text mb-0">Kedua pihak telah menyepakati biaya pengerjaan.</p>
                </div>
            `;
            return wrapDiv;
        }

        if (negoRejectMatch) {
            const price = parseInt(negoRejectMatch[2], 10).toLocaleString('id-ID');
            const wrapDiv = document.createElement('div');
            wrapDiv.className = 'text-center my-2 message-item animate__animated animate__fadeIn';
            wrapDiv.setAttribute('data-message-id', msg.id);
            wrapDiv.innerHTML = `
                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-2 xs-text">
                    <i class="bi bi-x-circle-fill me-1"></i> Tawaran harga Rp ${price} belum disepakati
                </span>
            `;
            return wrapDiv;
        }

        if (negoOfferMatch) {
            const nId = negoOfferMatch[1];
            const price = parseInt(negoOfferMatch[2], 10).toLocaleString('id-ID');
            let note = '';
            try { note = atob(negoOfferMatch[4]); } catch(e) {}

            const wrapDiv = document.createElement('div');
            wrapDiv.className = `d-flex ${isMyMsg ? 'justify-content-end' : 'justify-content-start'} message-item mb-2 animate__animated animate__fadeInUp`;
            wrapDiv.setAttribute('data-message-id', msg.id);

            wrapDiv.innerHTML = `
                <div class="message-bubble-wrapper" style="max-width: 75%;">
                    <div class="xs-text mb-1 ${isMyMsg ? 'text-end text-primary' : 'text-start text-warning'} fw-semibold">
                        ${isMyMsg ? 'Anda (Mengajukan Harga)' : escapeHtml(msg.sender_name) + ' (Mengajukan Harga)'}
                    </div>
                    <div class="card border ${isMyMsg ? 'border-primary bg-primary text-white' : 'border-warning bg-white text-dark'} shadow-sm rounded-4 overflow-hidden" style="min-width: 270px;">
                        <div class="p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge ${isMyMsg ? 'bg-white text-primary' : 'bg-warning bg-opacity-25 text-dark'} rounded-pill px-2.5 py-1 xs-text fw-bold">
                                    <i class="bi bi-tag-fill me-1"></i> Penawaran Harga
                                </span>
                                <span class="xs-text ${isMyMsg ? 'text-white-50' : 'text-muted'}">${msg.time_formatted || ''}</span>
                            </div>
                            <div class="display-6 fw-bold ${isMyMsg ? 'text-white' : 'text-dark'} mb-1" style="font-size: 1.4rem;">
                                Rp ${price}
                            </div>
                            ${note ? `<p class="small mb-2 fst-italic ${isMyMsg ? 'text-white-50' : 'text-muted'}">"${escapeHtml(note)}"</p>` : ''}
                            ${(!isMyMsg && !isPostCompleted) ? `
                                <div class="pt-2 border-top border-light-subtle d-flex gap-2">
                                    <button type="button" class="btn btn-success btn-sm rounded-pill w-100 fw-bold btn-accept-nego shadow-xs" data-nego-id="${nId}">
                                        <i class="bi bi-check-lg me-1"></i> Terima (Deal)
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill w-50 fw-semibold btn-reject-nego" data-nego-id="${nId}">
                                        Tolak
                                    </button>
                                </div>
                            ` : `
                                <div class="pt-2 border-top ${isMyMsg ? 'border-white border-opacity-25 xs-text text-white-50' : 'border-light-subtle xs-text text-muted'} d-flex align-items-center gap-1">
                                    <i class="bi ${isPostCompleted ? 'bi-lock-fill text-success' : (isMyMsg ? 'bi-hourglass-split text-warning' : 'bi-tag')}"></i>
                                    <span>${isPostCompleted ? 'Pengerjaan Selesai (Harga Final)' : (isMyMsg ? 'Menunggu konfirmasi lawan chat' : 'Tawaran harga masuk')}</span>
                                </div>
                            `}
                        </div>
                    </div>
                </div>
            `;
            return wrapDiv;
        }

        const wrapDiv = document.createElement('div');
        wrapDiv.className = `d-flex ${isMyMsg ? 'justify-content-end' : 'justify-content-start'} message-item mb-2 animate__animated animate__fadeInUp`;
        wrapDiv.setAttribute('data-message-id', msg.id);

        const bubbleWrapper = document.createElement('div');
        bubbleWrapper.className = 'message-bubble-wrapper';
        bubbleWrapper.style.maxWidth = '75%';

        // Label
        const labelDiv = document.createElement('div');
        if (isMyMsg) {
            labelDiv.className = 'xs-text mb-1 text-end text-primary fw-semibold';
            labelDiv.innerHTML = `<span>Anda</span> <i class="bi bi-person-fill ms-1"></i>`;
        } else if (isCustomerMsg) {
            labelDiv.className = 'xs-text mb-1 text-start text-primary fw-semibold';
            labelDiv.innerHTML = `<i class="bi bi-person-fill me-1"></i><span>${escapeHtml(msg.sender_name)} (Pelanggan)</span>`;
        } else {
            labelDiv.className = 'xs-text mb-1 text-start text-success fw-semibold';
            labelDiv.innerHTML = `<i class="bi bi-tools me-1"></i><span>${escapeHtml(msg.sender_name)} (Teknisi)</span>`;
        }

        // Bubble
        const bubble = document.createElement('div');
        bubble.className = `p-3 rounded-4 shadow-sm ${isMyMsg ? 'text-white' : 'bg-white text-dark border border-light-subtle'}`;
        bubble.style.wordBreak = 'break-word';
        bubble.style.textAlign = 'left';
        if (isMyMsg) {
            bubble.style.background = 'linear-gradient(135deg, #0078D4, #005A9E)';
            bubble.style.borderBottomRightRadius = '4px';
        } else {
            bubble.style.borderBottomLeftRadius = '4px';
        }

        const textDiv = document.createElement('div');
        textDiv.className = 'message-text';
        textDiv.style.whiteSpace = 'pre-wrap';
        textDiv.style.fontSize = '0.95rem';
        textDiv.textContent = msg.message;

        const timeDiv = document.createElement('div');
        timeDiv.className = `d-flex align-items-center ${isMyMsg ? 'justify-content-end text-white-50' : 'justify-content-start text-muted'} gap-1 mt-1 xs-text`;
        
        let checkIcon = '';
        if (isMyMsg) {
            checkIcon = `<i class="bi ${parseInt(msg.is_read, 10) === 1 ? 'bi-check-all text-info' : 'bi-check'} fs-6"></i>`;
        }
        timeDiv.innerHTML = `<span>${msg.time_formatted || ''}</span> ${checkIcon}`;

        bubble.appendChild(textDiv);
        bubble.appendChild(timeDiv);

        bubbleWrapper.appendChild(labelDiv);
        bubbleWrapper.appendChild(bubble);
        wrapDiv.appendChild(bubbleWrapper);

        return wrapDiv;
    }

    function escapeHtml(string) {
        if (!string) return '';
        const entityMap = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        };
        return String(string).replace(/[&<>"']/g, function(s) {
            return entityMap[s];
        });
    }

    // Submit Chat Form via AJAX
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const message = messageInput.value.trim();
        if (!message) return;

        btnSend.disabled = true;
        btnSend.innerHTML = `<span class="spinner-border spinner-border-sm"></span>`;

        const payload = new FormData();
        payload.append('csrf_token', csrfToken);
        payload.append('conversation_id', conversationId);
        payload.append('message', message);

        fetch('<?= base_url('chat/send') ?>', {
            method: 'POST',
            body: payload,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(res => {
            btnSend.disabled = false;
            btnSend.innerHTML = `<span>Kirim</span> <i class="bi bi-send-fill"></i>`;

            if (res.success && res.data) {
                // Remove empty state if present
                const emptyMsg = document.getElementById('emptyChatMessage');
                if (emptyMsg) emptyMsg.remove();

                // Append sent message
                const newEl = createMessageElement(res.data);
                messagesList.appendChild(newEl);

                if (res.data.id > lastMessageId) {
                    lastMessageId = res.data.id;
                }

                // Reset form
                messageInput.value = '';
                messageInput.style.height = 'auto';
                charCount.textContent = '0/2000';
                messageInput.focus();

                scrollToBottom(true);
            } else {
                if (window.bantuinAlert) {
                    window.bantuinAlert({ title: 'Gagal Mengirim', text: res.message || 'Gagal mengirim pesan.', icon: 'error', isDanger: true });
                } else {
                    alert(res.message || 'Gagal mengirim pesan.');
                }
            }
        })
        .catch(err => {
            console.error('Chat error:', err);
            btnSend.disabled = false;
            btnSend.innerHTML = `<span>Kirim</span> <i class="bi bi-send-fill"></i>`;
            if (window.bantuinAlert) {
                window.bantuinAlert({ title: 'Kendala Jaringan', text: 'Terjadi kendala jaringan saat mengirim pesan.', icon: 'error', isDanger: true });
            } else {
                alert('Terjadi kendala jaringan saat mengirim pesan.');
            }
        });
    });

    // AJAX Polling for new incoming messages
    function pollNewMessages() {
        if (!conversationId) return;

        fetch(`<?= base_url('chat/fetch/' . $conversation->id) ?>?after_id=${lastMessageId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && Array.isArray(data.messages) && data.messages.length > 0) {
                const emptyMsg = document.getElementById('emptyChatMessage');
                if (emptyMsg) emptyMsg.remove();

                let shouldScroll = false;
                data.messages.forEach(msg => {
                    // Check if already in DOM
                    if (!document.querySelector(`[data-message-id="${msg.id}"]`)) {
                        const el = createMessageElement(msg);
                        messagesList.appendChild(el);
                        if (msg.id > lastMessageId) {
                            lastMessageId = msg.id;
                        }
                        shouldScroll = true;
                    }
                });

                if (shouldScroll) {
                    scrollToBottom(true);
                }
            }
        })
        .catch(e => {
            // Polling error silently ignored to prevent spamming
        });
    }

    // Negosiasi Harga: Submit penawaran baru
    const negoForm = document.getElementById('negotiateForm');
    if (negoForm) {
        negoForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const price = document.getElementById('negoPriceInput').value;
            const note = document.getElementById('negoNoteInput').value;
            const btnSub = document.getElementById('btnSubmitNego');

            if (!price || parseInt(price, 10) <= 0) {
                if (window.bantuinAlert) {
                    window.bantuinAlert({ title: 'Nominal Tidak Valid', text: 'Silakan masukkan nominal harga penawaran yang valid.', icon: 'warning' });
                } else {
                    alert('Silakan masukkan nominal harga penawaran yang valid.');
                }
                return;
            }

            btnSub.disabled = true;
            btnSub.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Mengirim...`;

            const payload = new FormData();
            payload.append('csrf_token', csrfToken);
            payload.append('conversation_id', conversationId);
            payload.append('price', price);
            payload.append('note', note);

            fetch('<?= base_url('chat/negotiate') ?>', {
                method: 'POST',
                body: payload,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(async r => {
                const text = await r.text();
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Non-JSON server response:', text);
                    throw new Error('Format respon server tidak valid.');
                }
            })
            .then(async res => {
                btnSub.disabled = false;
                btnSub.innerHTML = `<i class="bi bi-send-fill"></i> <span>Kirim Penawaran Harga</span>`;
                if (res.success) {
                    const modalEl = document.getElementById('negotiateModal');
                    if (modalEl && typeof bootstrap !== 'undefined') {
                        const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                        modalInstance.hide();
                    }
                    if (window.bantuinAlert) {
                        await window.bantuinAlert({
                            title: 'Tawaran Terkirim!',
                            text: 'Penawaran harga berhasil diajukan ke ruang percakapan.',
                            icon: 'success',
                            isSuccess: true
                        });
                    }
                    location.reload();
                } else {
                    if (window.bantuinAlert) {
                        window.bantuinAlert({ title: 'Gagal', text: res.message || 'Gagal mengajukan penawaran harga.', icon: 'error', isDanger: true });
                    } else {
                        alert(res.message || 'Gagal mengajukan penawaran harga.');
                    }
                }
            })
            .catch(err => {
                console.error('Negotiate error:', err);
                btnSub.disabled = false;
                btnSub.innerHTML = `<i class="bi bi-send-fill"></i> <span>Kirim Penawaran Harga</span>`;
                if (window.bantuinAlert) {
                    window.bantuinAlert({ title: 'Kendala Jaringan', text: err.message || 'Terjadi kesalahan jaringan.', icon: 'error', isDanger: true });
                } else {
                    alert(err.message || 'Terjadi kesalahan jaringan.');
                }
            });
        });

        // Quick price buttons
        document.querySelectorAll('.quick-price-btn').forEach(b => {
            b.addEventListener('click', function() {
                document.getElementById('negoPriceInput').value = this.getAttribute('data-val');
            });
        });
    }

    // Negosiasi Harga: Aksi Terima (Deal) atau Tolak dengan Modern Custom Pop-up Modal
    document.addEventListener('click', async function(e) {
        const btnAccept = e.target.closest('.btn-accept-nego');
        const btnReject = e.target.closest('.btn-reject-nego');

        if (btnAccept || btnReject) {
            if (isPostCompleted) {
                if (window.bantuinAlert) {
                    window.bantuinAlert({ title: 'Postingan Selesai', text: 'Pengerjaan sudah selesai. Kesepakatan harga sudah final.', icon: 'info' });
                }
                return;
            }
            const isAccept = !!btnAccept;
            const btn = isAccept ? btnAccept : btnReject;
            const negoId = btn.getAttribute('data-nego-id');

            // Gunakan Modern Pop-up Dialog BANTUIN
            let confirmed = false;
            if (window.bantuinConfirm) {
                confirmed = await window.bantuinConfirm({
                    title: isAccept ? 'Setujui Kesepakatan Harga (Deal)?' : 'Tolak Penawaran Harga?',
                    html: isAccept 
                        ? `Apakah Anda sepakat dengan penawaran harga ini?<br><span class="text-muted small">Nominal ini akan menjadi <strong>harga resmi kesepakatan</strong> yang dikunci untuk transaksi penyelesaian servis.</span>` 
                        : `Tawaran harga ini akan ditolak.<br><span class="text-muted small">Anda atau rekan chat dapat mengajukan tawaran harga baru berikutnya.</span>`,
                    icon: isAccept ? 'question' : 'warning',
                    confirmBtnText: isAccept ? '<i class="bi bi-handshake-fill me-1"></i> Ya, Deal &amp; Setujui' : '<i class="bi bi-x-circle-fill me-1"></i> Ya, Tolak Tawaran',
                    cancelBtnText: 'Batal',
                    isSuccess: isAccept,
                    isDanger: !isAccept
                });
            } else {
                const confirmMsg = isAccept 
                    ? 'Setujui penawaran harga ini sebagai kesepakatan final (Deal)?'
                    : 'Tolak penawaran harga ini?';
                confirmed = confirm(confirmMsg);
            }

            if (!confirmed) {
                return;
            }

            btn.disabled = true;
            const origHtml = btn.innerHTML;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span>`;

            const payload = new FormData();
            payload.append('csrf_token', csrfToken);
            payload.append('negotiation_id', negoId);
            payload.append('action', isAccept ? 'accept' : 'reject');

            fetch('<?= base_url('chat/negotiate-action') ?>', {
                method: 'POST',
                body: payload,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(async r => {
                const text = await r.text();
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Non-JSON server response:', text);
                    throw new Error('Format respon server tidak valid.');
                }
            })
            .then(async res => {
                if (res.success) {
                    if (typeof Swal !== 'undefined') {
                        await Swal.fire({
                            icon: isAccept ? 'success' : 'info',
                            title: isAccept ? 'Kesepakatan Disetujui (Deal)!' : 'Tawaran Ditolak',
                            text: res.message,
                            timer: 1600,
                            showConfirmButton: false,
                            backdrop: 'rgba(7, 30, 61, 0.45)',
                            customClass: {
                                popup: 'bantuin-swal-popup rounded-4 shadow-lg border-0 p-4'
                            }
                        });
                    }
                    location.reload();
                } else {
                    if (window.bantuinAlert) {
                        window.bantuinAlert({ title: 'Gagal Memproses', text: res.message || 'Gagal memproses kesepakatan harga.', icon: 'error', isDanger: true });
                    } else {
                        alert(res.message || 'Gagal memproses kesepakatan harga.');
                    }
                    btn.disabled = false;
                    btn.innerHTML = origHtml;
                }
            })
            .catch(err => {
                console.error('Negotiate action error:', err);
                if (window.bantuinAlert) {
                    window.bantuinAlert({ title: 'Kendala Jaringan', text: err.message || 'Terjadi kendala jaringan.', icon: 'error', isDanger: true });
                } else {
                    alert(err.message || 'Terjadi kendala jaringan.');
                }
                btn.disabled = false;
                btn.innerHTML = origHtml;
            });
        }
    });

    // Poll every 4 seconds
    setInterval(pollNewMessages, 4000);
});
</script>
