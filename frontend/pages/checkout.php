<?php
$pageTitle = 'Checkout';
require_once __DIR__ . '/../../backend/includes/functions.php';
requireLogin();

$error = '';
$csrf_token = generateCSRFToken();

$field_id = isset($_GET['field_id']) ? (int)$_GET['field_id'] : 0;
$booking_date = isset($_GET['date']) ? $_GET['date'] : '';
$start_time = isset($_GET['start_time']) ? $_GET['start_time'] : '';

if (!$field_id || !$booking_date || !$start_time) {
    header('Location: ' . BASE_URL . 'frontend/pages/booking.php'); exit;
}

$field = getFieldById($pdo, $field_id);
if (!$field) { header('Location: ' . BASE_URL . 'frontend/pages/booking.php'); exit; }

$end_time = date('H:i:s', strtotime($start_time . ' +1 hour'));

if (!isTimeSlotAvailable($pdo, $field_id, $booking_date, $start_time, $end_time)) {
    $error = 'Maaf, jam yang Anda pilih sudah dibooking. Silakan pilih jam lain.';
}

$hours = calculateHours($start_time, $end_time);
$total_price = $field['price_per_hour'] * $hours;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid request.';
    } else {
        $notes = sanitize($_POST['notes']);
        $payment_method = sanitize($_POST['payment_method']);

        if (!isTimeSlotAvailable($pdo, $field_id, $booking_date, $start_time, $end_time)) {
            $error = 'Maaf, jam yang Anda pilih sudah dibooking.';
        } else {
            try {
                $pdo->beginTransaction();

                $booking_code = generateBookingCode();
                $stmt = $pdo->prepare("INSERT INTO bookings (booking_code, user_id, field_id, booking_date, start_time, end_time, total_hours, total_price, status, notes)
                                      VALUES (:booking_code, :user_id, :field_id, :booking_date, :start_time, :end_time, :total_hours, :total_price, 'pending', :notes)");
                $stmt->execute([
                    'booking_code' => $booking_code,
                    'user_id' => $_SESSION['user_id'],
                    'field_id' => $field_id,
                    'booking_date' => $booking_date,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'total_hours' => $hours,
                    'total_price' => $total_price,
                    'notes' => $notes
                ]);

                $booking_id = $pdo->lastInsertId();

                $stmt = $pdo->prepare("INSERT INTO payments (booking_id, payment_method, amount, status)
                                      VALUES (:booking_id, :payment_method, :amount, 'pending')");
                $stmt->execute([
                    'booking_id' => $booking_id,
                    'payment_method' => $payment_method,
                    'amount' => $total_price
                ]);

                $pdo->commit();

                header("Location: " . BASE_URL . "frontend/pages/payment.php?booking_id=$booking_id");
                exit;

            } catch (Exception $e) {
                $pdo->rollBack();
                $error = 'Terjadi kesalahan: ' . $e->getMessage();
            }
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="section-light">
    <div class="container">
        <div class="stepper">
            <div class="step done"><span class="step-num">✓</span> Pilih Jadwal</div>
            <div class="step-line done"></div>
            <div class="step active"><span class="step-num">2</span> Checkout</div>
            <div class="step-line"></div>
            <div class="step"><span class="step-num">3</span> Bayar</div>
        </div>

        <?php if ($error): ?>
            <div class="alert-futsal alert-danger mb-4"><?= $error ?></div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card-futsal">
                    <div class="card-body">
                        <h5 style="font-family:var(--font-display);font-size:1.3rem;font-weight:600;letter-spacing:0.01em">Detail Booking</h5>
                        <hr>
                        <table class="detail-table">
                            <tr><td>Lapangan</td><td><strong><?= $field['name'] ?></strong></td></tr>
                            <tr><td>Tipe</td><td><?= $field['type'] ?></td></tr>
                            <tr><td>Tanggal</td><td><?= formatDate($booking_date) ?></td></tr>
                            <tr><td>Jam</td><td><?= formatTime($start_time) ?> — <?= formatTime($end_time) ?> (<?= $hours ?> jam)</td></tr>
                            <tr><td>Harga/Jam</td><td><?= formatRupiah($field['price_per_hour']) ?></td></tr>
                            <tr><td>Total</td><td><span class="price-tag"><?= formatRupiah($total_price) ?></span></td></tr>
                        </table>

                        <form method="POST" class="form-futsal mt-4">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                            <div class="mb-3">
                                <label for="notes" class="form-label">Catatan <span style="font-weight:400;color:var(--f1-text-muted)">(opsional)</span></label>
                                <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="Misal: minta lapangan yang dekat pintu..."></textarea>
                            </div>

                                            <input type="hidden" name="payment_method" value="redirect">

                            <button type="submit" class="btn-futsal btn-red w-100 justify-content-center" <?= $error ? 'disabled' : '' ?>>
                                <i class="fas fa-credit-card"></i> Lanjut ke Pembayaran
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="info-sidebar">
                    <h5><i class="fas fa-exclamation-circle"></i> Penting</h5>
                    <hr>
                    <ul>
                        <li>Bayar dalam <strong>30 menit</strong></li>
                        <li>Booking otomatis batal kalau gak dibayar</li>
                        <li>Simpan bukti bayar buat referensi</li>
                        <li>Ada kendala? Hubungi kami</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>



<?php require_once __DIR__ . '/../includes/footer.php'; ?>
