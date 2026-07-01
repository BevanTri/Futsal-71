<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../../config.php';


function isLoggedIn() {
    return isset($_SESSION['user_id']);
}


function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}


function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . 'frontend/pages/login.php');
        exit;
    }
}


function requireAdmin() {
    if (!isAdmin()) {
        header('Location: ' . BASE_URL . 'frontend/pages/login.php');
        exit;
    }
}


function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}


function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}


function generateBookingCode() {
    return 'F71-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
}


function formatRupiah($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}


function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}


function formatTime($time) {
    return date('H:i', strtotime($time));
}


function sendEmailNotification($to, $subject, $message) {
    
    
    
    error_log("Email to: $to, Subject: $subject, Message: $message");
    return true;
}


function calculateHours($startTime, $endTime) {
    $start = strtotime($startTime);
    $end = strtotime($endTime);
    $diff = $end - $start;
    return $diff / 3600; 
}


function isTimeSlotAvailable($pdo, $fieldId, $date, $startTime, $endTime, $excludeBookingId = null) {
    $sql = "SELECT COUNT(*) as count FROM bookings 
            WHERE field_id = :field_id 
            AND booking_date = :booking_date 
            AND status IN ('pending', 'confirmed')
            AND (
                (start_time < :end_time AND end_time > :start_time)
            )";
    
    if ($excludeBookingId) {
        $sql .= " AND id != :exclude_id";
    }
    
    $stmt = $pdo->prepare($sql);
    $params = [
        'field_id' => $fieldId,
        'booking_date' => $date,
        'start_time' => $startTime,
        'end_time' => $endTime
    ];
    
    if ($excludeBookingId) {
        $params['exclude_id'] = $excludeBookingId;
    }
    
    $stmt->execute($params);
    $result = $stmt->fetch();
    
    return $result['count'] == 0;
}


function getUserById($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    return $stmt->fetch();
}


function getFieldById($pdo, $fieldId) {
    $stmt = $pdo->prepare("SELECT * FROM fields WHERE id = :id");
    $stmt->execute(['id' => $fieldId]);
    return $stmt->fetch();
}


function getAllFields($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM fields WHERE status = 'active' ORDER BY name");
    $stmt->execute();
    return $stmt->fetchAll();
}


function getTestimonials() {
    return [
        [
            'name' => 'Rudi Hartono',
            'rating' => 5,
            'text' => 'Lapangan Rumput Premium nya top abis! Karetnya tebel, gak licin, cocok buat main bareng temen-temen. Toilet dan mushola juga bersih. Pasti balik lagi!',
            'initials' => 'RH',
            'color' => 'var(--turf)'
        ],
        [
            'name' => 'Sari Dewi Kusuma',
            'rating' => 5,
            'text' => 'Booking lewat websitemu gampang banget, tinggal pilih jam, bayar online, pas dateng tinggal main aja. Gak perlu telepon-teleponan lagi. Makin sering main jadinya!',
            'initials' => 'SD',
            'color' => 'var(--f1-red)'
        ],
        [
            'name' => 'Bambang Pamungkas',
            'rating' => 4,
            'text' => 'Lapangan Reguler standar kompetisi. Tempat parkir agak sempit kalo rame, tapi lapangannya worth it. Lampu buat malem terang benerang, mantap!',
            'initials' => 'BP',
            'color' => 'var(--f1-bg)'
        ],
        [
            'name' => 'Dian Ayu Lestari',
            'rating' => 5,
            'text' => 'First time main futsal, ternyata seru! Staffnya ramah banget ngajarin aturan main dan minjemin sepatu. Cocok buat pemula yang pengen nyoba futsal.',
            'initials' => 'DA',
            'color' => 'var(--turf-dark)'
        ],
        [
            'name' => 'Fajar Nugraha',
            'rating' => 5,
            'text' => 'Dari kosan tinggal jalan kaki, lokasi strategis di pusat kota. Harga booking sewajarnya, apalagi kalo bagi rata bertiga-tiga. Top markotop!',
            'initials' => 'FN',
            'color' => 'var(--f1-red)'
        ],
    ];
}


function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>