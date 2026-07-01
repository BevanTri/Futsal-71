<?php
$pageTitle = 'Pembayaran';
require_once __DIR__ . '/../../backend/includes/functions.php';
require_once __DIR__ . '/../../backend/config/ipaymu.php';


$callbackData = null;
$rawBody = file_get_contents('php://input');


if (!empty($rawBody)) {
    $callbackData = json_decode($rawBody, true);
}


if (!is_array($callbackData)) {
    $callbackData = $_POST;
    if (!isset($callbackData['reference_id'])) {
        foreach ($_POST as $v) {
            if (is_string($v) && ($d = json_decode($v, true)) && isset($d['reference_id'])) { $callbackData = $d; break; }
        }
    }
}

$isCallback = (
    isset($callbackData['reference_id']) ||
    !empty($_SERVER['HTTP_X_SIGNATURE']) ||
    !empty($_SERVER['HTTP_X_EXTERNAL_ID'])
);

if ($isCallback) {
    header('Content-Type: application/json');
    http_response_code(200);
    error_log("iPaymu callback received: " . $rawBody);

    $booking_code = $callbackData['reference_id'] ?? '';
    if (!$booking_code) { echo json_encode(['status' => 'error', 'message' => 'No reference_id']); exit; }

    $stmt = $pdo->prepare("SELECT b.id, b.status, b.user_id FROM bookings b WHERE b.booking_code = :code");
    $stmt->execute(['code' => $booking_code]);
    $booking = $stmt->fetch();

    if (!$booking) {
        echo json_encode(['status' => 'error', 'message' => 'Booking not found']);
        exit;
    }

    $signature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';
    $expectedSignature = hash_hmac('sha256', strtoupper('POST') . ':' . IPAYMU_VA_NUMBER . ':' . strtolower(hash('sha256', $rawBody)) . ':' . IPAYMU_API_KEY, IPAYMU_API_KEY);
    if ($signature && !hash_equals($expectedSignature, $signature)) {
        error_log("iPaymu callback signature mismatch for booking {$booking['id']}");
    }

    $status_code = (int)($callbackData['status_code'] ?? 0);

    if ($status_code === 1) {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("UPDATE payments SET status = 'success', transaction_id = :trx, ipaymu_reference = :ref, paid_at = NOW() WHERE booking_id = :bid");
            $stmt->execute(['trx' => $callbackData['trx_id'] ?? null, 'ref' => $callbackData['sid'] ?? null, 'bid' => $booking['id']]);
            $stmt = $pdo->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = :id");
            $stmt->execute(['id' => $booking['id']]);
            $pdo->commit();
            error_log("iPaymu callback: booking {$booking['id']} confirmed");
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("iPaymu callback DB error: " . $e->getMessage());
        }
    } elseif (in_array($status_code, [2, 3], true)) {
        $paymentStatus = $status_code === 2 ? 'expired' : 'failed';
        $pdo->prepare("UPDATE payments SET status = :ps WHERE booking_id = :bid")->execute(['ps' => $paymentStatus, 'bid' => $booking['id']]);
        $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = :id")->execute(['id' => $booking['id']]);
    }

    echo json_encode(['status' => 'ok']);
    exit;
}

requireLogin();

$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;

$stmt = $pdo->prepare("SELECT b.*, f.name as field_name, f.type as field_type, p.payment_method, p.status as payment_status, p.transaction_id, p.payment_url
                       FROM bookings b
                       JOIN fields f ON b.field_id = f.id
                       LEFT JOIN payments p ON b.id = p.booking_id
                       WHERE b.id = :booking_id AND b.user_id = :user_id");
$stmt->execute(['booking_id' => $booking_id, 'user_id' => $_SESSION['user_id']]);
$booking = $stmt->fetch();

if (!$booking) { header('Location: ' . BASE_URL . 'frontend/pages/history.php'); exit; }

if ($booking['payment_status'] === 'success') {
    $success_message = 'Pembayaran berhasil! Booking kamu udah dikonfirmasi.';
}

$payment_url = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $booking['payment_status'] === 'pending') {
    require_once __DIR__ . '/../../backend/includes/ipaymu.php';

    $result = createIpaymuPayment(
        $booking['booking_code'],
        $booking['total_price'],
        $booking['payment_method'],
        $_SESSION['user_name'],
        $_SESSION['user_email']
    );

    if ($result['success']) {
        $stmt = $pdo->prepare("UPDATE payments SET transaction_id = :transaction_id, ipaymu_reference = :reference, payment_url = :url
                               WHERE booking_id = :booking_id");
        $stmt->execute([
            'transaction_id' => $result['transaction_id'],
            'reference' => $result['reference'],
            'url' => $result['payment_url'],
            'booking_id' => $booking_id
        ]);
        $payment_url = $result['payment_url'];
    } else {
        $error = 'Gagal bikin pembayaran: ' . $result['message'];
        if (isset($result['raw'])) {
            $error .= ' | Detail: ' . print_r($result['raw'], true);
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
            <div class="step done"><span class="step-num">✓</span> Checkout</div>
            <div class="step-line done"></div>
            <div class="step active"><span class="step-num">3</span> Bayar</div>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert-futsal alert-success d-flex align-items-center gap-2">
                <i class="fas fa-check-circle"></i> <?= $success_message ?>
            </div>
            <a href="<?= BASE_URL ?>frontend/pages/history.php" class="btn-futsal btn-red mt-3">
                <i class="fas fa-list"></i> Lihat Riwayat Booking
            </a>
        <?php else: ?>

            <?php if (isset($error)): ?>
                <div class="alert-futsal alert-danger mb-4"><?= $error ?></div>
            <?php endif; ?>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card-futsal">
                        <div class="card-body">
                            <h5 style="font-family:var(--font-display);font-size:1.3rem;font-weight:600;letter-spacing:0.01em">Detail Pembayaran</h5>
                            <hr>
                            <table class="detail-table">
                                <tr><td>Kode Booking</td><td><strong><?= $booking['booking_code'] ?></strong></td></tr>
                                <tr><td>Lapangan</td><td><?= $booking['field_name'] ?> (<?= $booking['field_type'] ?>)</td></tr>
                                <tr><td>Tanggal</td><td><?= formatDate($booking['booking_date']) ?></td></tr>
                                <tr><td>Jam</td><td><?= formatTime($booking['start_time']) ?> — <?= formatTime($booking['end_time']) ?></td></tr>
                                <tr><td>Metode</td><td><?= ucfirst($booking['payment_method']) ?></td></tr>
                                <tr><td>Total Bayar</td><td><span class="price-tag" style="font-size:2rem"><?= formatRupiah($booking['total_price']) ?></span></td></tr>
                                <tr>
                                    <td>Status</td>
                                    <td>
                                        <?php if ($booking['payment_status'] === 'pending'): ?>
                                            <span class="badge-futsal badge-pending">Menunggu Pembayaran</span>
                                        <?php elseif ($booking['payment_status'] === 'success'): ?>
                                            <span class="badge-futsal badge-confirmed">Lunas</span>
                                        <?php elseif ($booking['payment_status'] === 'expired'): ?>
                                            <span class="badge-futsal badge-cancelled">Kadaluarsa</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>

                            <?php if ($booking['payment_status'] === 'pending'): ?>
                                <?php if ($payment_url): ?>
                                    <div class="alert-futsal alert-info mt-4">
                                        <p>Klik tombol di bawah buat lanjut bayar:</p>
                                        <a href="<?= $payment_url ?>" target="_blank" class="btn-futsal btn-red">
                                            <i class="fas fa-credit-card"></i> Bayar Sekarang
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <form method="POST" class="mt-4">
                                        <button type="submit" class="btn-futsal btn-red w-100 justify-content-center">
                                            <i class="fas fa-credit-card"></i> Mulai Pembayaran
                                        </button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="info-sidebar">
                        <h5><i class="fas fa-exclamation-circle"></i> Info Pembayaran</h5>
                        <hr>
                        <ul>
                            <li>Bayar dalam <strong>30 menit</strong></li>
                            <li>Booking batal otomatis kalo lewat</li>
                            <li>Status update otomatis setelah bayar</li>
                            <li>Simpan bukti bayar</li>
                        </ul>
                        <hr>
                        <p>Butuh bantuan? <a href="https://wa.me/628179430031" style="color:var(--f1-red)">Hubungi kami</a></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
