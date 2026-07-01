<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/ipaymu.php';

$rawBody = file_get_contents('php://input');
error_log("iPaymu callback RAW: " . ($rawBody ?: 'EMPTY'));

$data = null;
if (!empty($rawBody)) {
    $data = json_decode($rawBody, true);
}
if (!is_array($data)) {
    $data = $_POST;
}

if (!isset($data['reference_id'])) {
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'ignored', 'message' => 'No reference_id']);
    exit;
}

$booking_code = $data['reference_id'];
$stmt = $pdo->prepare("SELECT b.id, b.status FROM bookings b WHERE b.booking_code = :code");
$stmt->execute(['code' => $booking_code]);
$booking = $stmt->fetch();

if (!$booking) {
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Booking not found']);
    exit;
}

$status_code = (int)($data['status_code'] ?? 0);

if ($status_code === 1) {
    try {
        $pdo->beginTransaction();
        $pdo->prepare("UPDATE payments SET status = 'success', transaction_id = :trx, ipaymu_reference = :ref, paid_at = NOW() WHERE booking_id = :bid")
            ->execute(['trx' => $data['trx_id'] ?? null, 'ref' => $data['sid'] ?? null, 'bid' => $booking['id']]);
        $pdo->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = :id")
            ->execute(['id' => $booking['id']]);
        $pdo->commit();
        error_log("iPaymu callback OK: booking {$booking['id']} confirmed");
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("iPaymu callback DB error: " . $e->getMessage());
    }
} elseif (in_array($status_code, [2, 3], true)) {
    $ps = $status_code === 2 ? 'expired' : 'failed';
    $pdo->prepare("UPDATE payments SET status = :ps WHERE booking_id = :bid")->execute(['ps' => $ps, 'bid' => $booking['id']]);
    $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = :id")->execute(['id' => $booking['id']]);
}

http_response_code(200);
header('Content-Type: application/json');
echo json_encode(['status' => 'ok']);
