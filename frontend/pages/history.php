<?php
$pageTitle = 'Riwayat Booking';
require_once __DIR__ . '/../../backend/includes/functions.php';
requireLogin();

$stmt = $pdo->prepare("SELECT b.*, f.name as field_name, f.type as field_type, f.photo_url,
                       p.status as payment_status, p.payment_method, p.transaction_id
                       FROM bookings b
                       JOIN fields f ON b.field_id = f.id
                       LEFT JOIN payments p ON b.id = p.booking_id
                       WHERE b.user_id = :user_id
                       ORDER BY b.created_at DESC");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$bookings = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<section class="section-light">
    <div class="container">
        <h2 class="section-title">Riwayat Booking</h2>
        <p class="section-subtitle mb-5">Semua booking kamu dalam satu tempat</p>

        <?php if (empty($bookings)): ?>
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-calendar-times"></i></div>
                <h4>Belum Ada Booking</h4>
                <p>Kamu belum pernah booking lapangan. Yuk booking sekarang dan rasakan serunya main di Futsal 71!</p>
                <a href="<?= BASE_URL ?>frontend/pages/booking.php" class="btn-futsal btn-red">
                    <i class="fas fa-calendar-plus"></i> Booking Sekarang
                </a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($bookings as $booking): ?>
                    <div class="col-12">
                        <div class="history-card">
                            <img src="<?= $booking['photo_url'] ?>" alt="<?= $booking['field_name'] ?>" class="history-img" loading="lazy">
                            <div class="history-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h5><?= $booking['field_name'] ?></h5>
                                    <div class="d-flex gap-1">
                                        <span class="badge-futsal <?= $booking['status'] === 'confirmed' ? 'badge-confirmed' : ($booking['status'] === 'pending' ? 'badge-pending' : ($booking['status'] === 'cancelled' ? 'badge-cancelled' : 'badge-completed')) ?>">
                                            <?= ucfirst($booking['status']) ?>
                                        </span>
                                    </div>
                                </div>
                                <span class="badge-futsal" style="background:rgba(225,6,0,0.08);color:var(--turf);font-size:0.65rem"><?= $booking['field_type'] ?></span>
                                <div class="history-meta">
                                    <span><i class="fas fa-calendar"></i> <?= formatDate($booking['booking_date']) ?></span>
                                    <span><i class="fas fa-clock"></i> <?= formatTime($booking['start_time']) ?> — <?= formatTime($booking['end_time']) ?></span>
                                    <span><i class="fas fa-money-bill"></i> <?= formatRupiah($booking['total_price']) ?></span>
                                    <span><i class="fas fa-credit-card"></i> <?= ucfirst($booking['payment_method']) ?></span>
                                </div>
                                <div class="history-actions">
                                    <?php if ($booking['payment_status'] === 'pending' && $booking['status'] === 'pending'): ?>
                                        <a href="<?= BASE_URL ?>frontend/pages/payment.php?booking_id=<?= $booking['id'] ?>" class="btn-futsal btn-red" style="padding:0.4rem 1rem;font-size:0.82rem">
                                            <i class="fas fa-credit-card"></i> Bayar Sekarang
                                        </a>
                                    <?php endif; ?>
                                    <button type="button" class="btn-futsal btn-outline-futsal" style="padding:0.4rem 1rem;font-size:0.82rem;color:var(--f1-text);border-color:var(--f1-border)" data-bs-toggle="modal" data-bs-target="#detailModal<?= $booking['id'] ?>">
                                        <i class="fas fa-info-circle"></i> Detail
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Modal -->
                    <div class="modal fade modal-futsal" id="detailModal<?= $booking['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Detail Booking</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <table class="detail-table">
                                        <tr><td>Kode Booking</td><td><strong><?= $booking['booking_code'] ?></strong></td></tr>
                                        <tr><td>Lapangan</td><td><?= $booking['field_name'] ?></td></tr>
                                        <tr><td>Tanggal</td><td><?= formatDate($booking['booking_date']) ?></td></tr>
                                        <tr><td>Jam</td><td><?= formatTime($booking['start_time']) ?> — <?= formatTime($booking['end_time']) ?></td></tr>
                                        <tr><td>Total</td><td><?= formatRupiah($booking['total_price']) ?></td></tr>
                                        <tr><td>Status Booking</td><td><?= ucfirst($booking['status']) ?></td></tr>
                                        <tr><td>Status Pembayaran</td><td><?= ucfirst($booking['payment_status']) ?></td></tr>
                                        <?php if ($booking['transaction_id']): ?>
                                        <tr><td>Transaction ID</td><td><?= $booking['transaction_id'] ?></td></tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn-futsal btn-outline-futsal" style="padding:0.4rem 1rem;font-size:0.85rem;color:var(--f1-text);border-color:var(--f1-border)" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
