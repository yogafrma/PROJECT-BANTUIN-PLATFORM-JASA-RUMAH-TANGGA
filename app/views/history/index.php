<?php
/**
 * Transaction & Service History View
 * Project: BANTUIN — Platform Layanan Jasa Rumah Tangga
 */
$role = $user->role ?? 'customer';
$isCustomer = ($role === 'customer');
$dashboardUrl = base_url($role === 'admin' ? 'admin' : ($role === 'technician' ? 'technician' : 'customer'));
?>

<div class="dashboard-wrapper py-4 animate__animated animate__fadeIn">
    <div class="container">
        
        <!-- Header Banner (Command Center Theme) -->
        <div class="settings-hero-card mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 position-relative z-1">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="settings-hero-pill">
                            <i class="bi bi-clock-history"></i> Riwayat &amp; Pembayaran
                        </span>
                        <span class="text-white-50 small">Log Transaksi Resmi</span>
                    </div>
                    <h2 class="fw-bold mb-1 text-white">Riwayat Transaksi &amp; Layanan</h2>
                    <p class="text-white-50 mb-0 small">
                        Pantau seluruh riwayat pengerjaan, status pembayaran, bukti nomor invoice, dan struk digital resmi BANTUIN.
                    </p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="<?= $dashboardUrl ?>" class="btn btn-outline-light rounded-pill btn-sm px-3 fw-semibold d-inline-flex align-items-center gap-1 shadow-xs">
                        <i class="bi bi-arrow-left"></i> Dashboard
                    </a>
                    <?php if ($isCustomer): ?>
                        <a href="<?= base_url('posts/create') ?>" class="btn btn-primary rounded-pill btn-sm px-3 fw-bold d-inline-flex align-items-center gap-1 shadow-sm">
                            <i class="bi bi-plus-lg"></i> Permintaan Baru
                        </a>
                    <?php else: ?>
                        <a href="<?= base_url('posts') ?>" class="btn btn-primary rounded-pill btn-sm px-3 fw-bold d-inline-flex align-items-center gap-1 shadow-sm">
                            <i class="bi bi-search"></i> Cari Order Servis
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Summary Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 d-flex flex-row align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary flex-shrink-0" style="width: 50px; height: 50px; font-size: 1.4rem;">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <div>
                        <div class="text-muted xs-text fw-bold text-uppercase" style="letter-spacing: 0.5px;">Total Order Servis</div>
                        <div class="h4 fw-bold text-dark mb-0"><?= (int)$stats['total'] ?> Transaksi</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 d-flex flex-row align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success flex-shrink-0" style="width: 50px; height: 50px; font-size: 1.4rem;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted xs-text fw-bold text-uppercase" style="letter-spacing: 0.5px;">Selesai &amp; Lunas</div>
                        <div class="h4 fw-bold text-success mb-0"><?= (int)$stats['completed'] ?> Selesai</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 d-flex flex-row align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning flex-shrink-0" style="width: 50px; height: 50px; font-size: 1.4rem;">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div>
                        <div class="text-muted xs-text fw-bold text-uppercase" style="letter-spacing: 0.5px;">
                            <?= $isCustomer ? 'Total Pengeluaran' : 'Akumulasi Pendapatan' ?>
                        </div>
                        <div class="h4 fw-bold text-dark mb-0">Rp <?= number_format($stats['total_amount'], 0, ',', '.') ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Navigation Tabs -->
        <div class="card border-0 shadow-sm rounded-4 p-2 bg-white mb-4">
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= base_url('history?filter=all') ?>" 
                   class="btn rounded-pill px-3 py-1.5 xs-text fw-bold <?= ($currentFilter === 'all') ? 'btn-primary' : 'btn-light text-muted' ?>">
                    <i class="bi bi-list-check me-1"></i> Semua Riwayat (<?= (int)$stats['total'] ?>)
                </a>
                <a href="<?= base_url('history?filter=completed') ?>" 
                   class="btn rounded-pill px-3 py-1.5 xs-text fw-bold <?= ($currentFilter === 'completed') ? 'btn-success text-white' : 'btn-light text-muted' ?>">
                    <i class="bi bi-check2-circle me-1"></i> Selesai &amp; Lunas (<?= (int)$stats['completed'] ?>)
                </a>
                <a href="<?= base_url('history?filter=in_progress') ?>" 
                   class="btn rounded-pill px-3 py-1.5 xs-text fw-bold <?= ($currentFilter === 'in_progress') ? 'btn-info text-white' : 'btn-light text-muted' ?>">
                    <i class="bi bi-hourglass-split me-1"></i> Sedang Berjalan (<?= (int)$stats['in_progress'] ?>)
                </a>
            </div>
        </div>

        <!-- Transactions List -->
        <?php if (empty($history)): ?>
            <div class="card border-0 shadow-sm rounded-4 text-center py-5 bg-white">
                <div class="card-body p-4">
                    <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center bg-light text-muted" style="width: 70px; height: 70px; font-size: 2rem;">
                        <i class="bi bi-receipt-cutoff opacity-50"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Belum Ada Riwayat Transaksi</h5>
                    <p class="text-muted small mx-auto mb-4" style="max-width: 440px;">
                        <?= $currentFilter !== 'all' ? 'Tidak ada transaksi dengan filter yang dipilih saat ini.' : 'Anda belum memiliki transaksi servis perbaikan yang tercatat di sistem.' ?>
                    </p>
                    <a href="<?= base_url('history') ?>" class="btn btn-outline-secondary rounded-pill px-4 btn-sm fw-semibold">
                        Lihat Semua Riwayat
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($history as $item): ?>
                    <?php
                        $isCompleted = ($item->status === 'Selesai');
                        $hasPayment = !empty($item->payment_id);
                        $finalAmount = (int)($item->paid_amount ?: ($item->agreed_price ?: $item->estimated_min_price));
                        $partnerName = $isCustomer ? ($item->technician_name ?? 'Menunggu Teknisi') : $item->customer_name;
                        $partnerWa = $isCustomer ? $item->technician_whatsapp : $item->customer_whatsapp;
                        $partnerRoleLabel = $isCustomer ? 'Mitra Teknisi' : 'Pelanggan';

                        $badgeClass = 'badge-status-mencari';
                        $statusText = $item->status;
                        if ($isCompleted) {
                            $badgeClass = 'badge-status-selesai';
                            $statusText = $hasPayment ? 'Lunas & Selesai' : 'Selesai';
                        } elseif ($item->status === 'Ada Teknisi') {
                            $badgeClass = 'badge-status-proses';
                            $statusText = 'Sedang Dikerjakan';
                        }
                    ?>
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white d-flex flex-column transition hover-shadow overflow-hidden">
                            
                            <!-- Header Card: Invoice & Status -->
                            <div class="p-3 px-4 border-bottom border-light-subtle d-flex justify-content-between align-items-center bg-light bg-opacity-50">
                                <div>
                                    <span class="text-muted xs-text fw-semibold d-block">Nomor Transaksi / Invoice:</span>
                                    <span class="fw-bold text-dark font-monospace small">
                                        <?= !empty($item->invoice_no) ? e($item->invoice_no) : '#ORDER-' . str_pad((string)$item->id, 4, '0', STR_PAD_LEFT) ?>
                                    </span>
                                </div>
                                <span class="badge <?= $badgeClass ?> rounded-pill px-3 py-1.5 xs-text">
                                    <i class="bi <?= $isCompleted ? 'bi-check-circle-fill' : 'bi-hourglass-split' ?> me-1"></i>
                                    <?= e($statusText) ?>
                                </span>
                            </div>

                            <!-- Body Card: Order Details -->
                            <div class="card-body p-4 flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 xs-text fw-semibold">
                                        <i class="bi <?= e($item->category_icon ?? 'bi-tools') ?> me-1"></i><?= e($item->category_name ?? 'Umum') ?>
                                    </span>
                                    <span class="text-muted xs-text">
                                        <i class="bi bi-clock me-1"></i><?= date('d M Y, H:i', strtotime($item->paid_at ?? $item->created_at)) ?> WIB
                                    </span>
                                </div>

                                <h5 class="fw-bold text-dark mb-3">
                                    <a href="<?= base_url('posts/detail/' . $item->id) ?>" class="text-decoration-none text-dark hover-primary">
                                        <?= e($item->title) ?>
                                    </a>
                                </h5>

                                <!-- Counterparty Info -->
                                <div class="p-3 bg-light rounded-3 d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-circle bg-primary text-white fw-bold" style="width: 38px; height: 38px; font-size: 0.95rem;">
                                            <?= strtoupper(substr($partnerName, 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="xs-text text-muted"><?= $partnerRoleLabel ?>:</div>
                                            <div class="fw-bold text-dark small"><?= e($partnerName) ?></div>
                                        </div>
                                    </div>
                                    <?php if (!empty($partnerWa)): ?>
                                        <a href="https://wa.me/<?= preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $partnerWa)) ?>" 
                                           target="_blank" 
                                           class="btn btn-outline-success btn-sm rounded-pill px-2.5 py-1 xs-text fw-bold d-inline-flex align-items-center gap-1"
                                           title="Chat via WhatsApp">
                                            <i class="bi bi-whatsapp"></i> WA
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <!-- Nominal & Metode Bayar -->
                                <div class="d-flex justify-content-between align-items-end p-3 rounded-3 mb-2" style="background: rgba(0, 120, 212, 0.04); border: 1px solid rgba(0, 120, 212, 0.15);">
                                    <div>
                                        <div class="text-muted xs-text fw-semibold mb-0.5">Biaya Kesepakatan / Pelunasan:</div>
                                        <div class="fw-bold text-success fs-5">
                                            Rp <?= number_format($finalAmount, 0, ',', '.') ?>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <?php if (!empty($item->payment_method)): ?>
                                            <?php if ($item->payment_method === 'qris'): ?>
                                                <span class="badge bg-dark rounded-pill px-2.5 py-1 text-white xs-text">
                                                    <i class="bi bi-qr-code-scan text-warning me-1"></i> QRIS
                                                </span>
                                            <?php elseif ($item->payment_method === 'cash'): ?>
                                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2.5 py-1 xs-text">
                                                    <i class="bi bi-cash-stack me-1"></i> Tunai
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-2.5 py-1 xs-text">
                                                    <i class="bi bi-bank me-1"></i> Transfer
                                                </span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted border rounded-pill px-2.5 py-1 xs-text">
                                                Estimasi
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Review Indicator if rated -->
                                <?php if (!empty($item->review_id)): ?>
                                    <div class="p-2 px-3 rounded-3 bg-warning bg-opacity-10 border border-warning border-opacity-25 d-flex align-items-center justify-content-between xs-text">
                                        <div class="text-warning fw-bold d-flex align-items-center gap-1">
                                            <?php for ($s = 1; $s <= 5; $s++): ?>
                                                <i class="bi <?= ($s <= (int)$item->review_rating) ? 'bi-star-fill' : 'bi-star' ?>"></i>
                                            <?php endfor; ?>
                                            <span class="text-dark ms-1"><?= (int)$item->review_rating ?>/5 Bintang</span>
                                        </div>
                                        <span class="text-muted fst-italic">"<?= e(mb_strimwidth($item->review_comment ?? 'Servis memuaskan', 0, 30, '...')) ?>"</span>
                                    </div>
                                <?php endif; ?>

                            </div>

                            <!-- Footer Card: Action Buttons -->
                            <div class="card-footer bg-white border-top-0 p-4 pt-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div class="d-flex gap-2">
                                    <?php if (!empty($item->conversation_id)): ?>
                                        <a href="<?= base_url('chat/room/' . $item->conversation_id) ?>" class="btn btn-outline-primary rounded-pill btn-sm px-3 fw-semibold">
                                            <i class="bi bi-chat-dots-fill me-1"></i> Chat
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="<?= base_url('posts/detail/' . $item->id) ?>" class="btn btn-primary rounded-pill btn-sm px-3 fw-bold shadow-xs">
                                        <?= $hasPayment ? '<i class="bi bi-receipt me-1"></i> Lihat Struk' : 'Detail Layanan' ?> <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

