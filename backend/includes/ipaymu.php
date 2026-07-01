<?php
require_once __DIR__ . '/../config/ipaymu.php';
require_once __DIR__ . '/../../config.php';

function createIpaymuPayment($order_id, $amount, $payment_method, $customer_name, $customer_email) {
    global $pdo;
    $booking_id = getBookingIdByCode($order_id);
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    
    if (strpos($host, 'ngrok') !== false) { $protocol = 'https'; }
    $fullBase = $protocol . '://' . $host . BASE_URL;
    $returnUrl = $fullBase . 'frontend/pages/payment.php?booking_id=' . $booking_id;
    $notifyUrl = $fullBase . 'backend/ipaymu-callback.php';

    $body = [
        'product' => ['Booking Lapangan Futsal 71'],
        'qty' => ['1'],
        'price' => [(string)$amount],
        'returnUrl' => $returnUrl,
        'cancelUrl' => $returnUrl,
        'notifyUrl' => $notifyUrl,
        'referenceId' => $order_id
    ];

    $jsonBody = json_encode($body, JSON_UNESCAPED_SLASHES);
    $requestBody = strtolower(hash('sha256', $jsonBody));
    $stringToSign = strtoupper('POST') . ':' . IPAYMU_VA_NUMBER . ':' . $requestBody . ':' . IPAYMU_API_KEY;
    $signature = hash_hmac('sha256', $stringToSign, IPAYMU_API_KEY);
    $timestamp = date('YmdHis');
    $url = IPAYMU_BASE_URL . 'v2/payment';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json',
        'va: ' . IPAYMU_VA_NUMBER,
        'signature: ' . $signature,
        'timestamp: ' . $timestamp
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    error_log('iPaymu URL: ' . $url);
    error_log('iPaymu Body: ' . $jsonBody);
    error_log('iPaymu StringToSign: ' . $stringToSign);
    error_log('iPaymu Signature: ' . $signature);
    error_log('iPaymu Timestamp: ' . $timestamp);
    error_log('iPaymu Response: ' . ($response ?: 'EMPTY'));
    error_log('iPaymu HTTP: ' . $httpCode);

    if ($curlError) {
        return ['success' => false, 'message' => 'CURL error: ' . $curlError];
    }

    if (!$response) {
        return ['success' => false, 'message' => 'Empty response from iPaymu (HTTP ' . $httpCode . ')'];
    }

    $result = json_decode($response, true);
    if (!$result) {
        return ['success' => false, 'message' => 'Invalid JSON response: ' . substr($response, 0, 200)];
    }

    if ($httpCode === 200 && ($result['Status'] === 0 || $result['Status'] === 200)) {
        $data = $result['Data'] ?? $result['data'] ?? [];
        return [
            'success' => true,
            'transaction_id' => $data['SessionID'] ?? $data['sessionId'] ?? '',
            'reference' => $data['TrxID'] ?? $data['trxId'] ?? '',
            'payment_url' => $data['Url'] ?? $data['UrlPayment'] ?? $data['urlPayment'] ?? ''
        ];
    } else {
        $msg = $result['Keterangan'] ?? $result['Message'] ?? $result['Massage'] ?? $result['message'] ?? 'HTTP ' . $httpCode;
        return ['success' => false, 'message' => $msg, 'raw' => $result];
    }
}

function getBookingIdByCode($booking_code) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM bookings WHERE booking_code = :code");
    $stmt->execute(['code' => $booking_code]);
    $result = $stmt->fetch();
    return $result ? $result['id'] : 0;
}
