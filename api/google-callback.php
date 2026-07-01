<?php
require_once __DIR__ . '/../backend/includes/functions.php';
require_once __DIR__ . '/../backend/config/google.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'login';


if (!isset($_GET['code'])) {
    $auth_url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
        'client_id' => GOOGLE_CLIENT_ID,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'response_type' => 'code',
        'scope' => GOOGLE_SCOPES,
        'access_type' => 'online',
        'prompt' => 'consent'
    ]);
    
    header('Location: ' . $auth_url);
    exit;
}


$code = $_GET['code'];

$token_url = 'https://oauth2.googleapis.com/token';
$token_params = [
    'code' => $code,
    'client_id' => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'grant_type' => 'authorization_code'
];

$ch = curl_init($token_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_params));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$token_response = curl_exec($ch);
curl_close($ch);

$token_data = json_decode($token_response, true);

if (!isset($token_data['access_token'])) {
    die('Error: Could not get access token');
}


$userinfo_url = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . $token_data['access_token'];

$ch = curl_init($userinfo_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$userinfo_response = curl_exec($ch);
curl_close($ch);

$google_user = json_decode($userinfo_response, true);

if (!$google_user || !isset($google_user['email'])) {
    die('Error: Could not get user info');
}


$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute(['email' => $google_user['email']]);
$user = $stmt->fetch();

if ($user) {
    
    if (empty($user['google_id'])) {
        $stmt = $pdo->prepare("UPDATE users SET google_id = :google_id, avatar_url = :avatar WHERE id = :id");
        $stmt->execute([
            'google_id' => $google_user['id'],
            'avatar' => $google_user['picture'],
            'id' => $user['id']
        ]);
    }
    
    
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_avatar'] = $user['avatar_url'] ?: $google_user['picture'];
} else {
    
    $stmt = $pdo->prepare("INSERT INTO users (name, email, google_id, avatar_url, role) VALUES (:name, :email, :google_id, :avatar, 'user')");
    $stmt->execute([
        'name' => $google_user['name'],
        'email' => $google_user['email'],
        'google_id' => $google_user['id'],
        'avatar' => $google_user['picture']
    ]);
    
    $user_id = $pdo->lastInsertId();
    
    
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $google_user['name'];
    $_SESSION['user_email'] = $google_user['email'];
    $_SESSION['user_role'] = 'user';
    $_SESSION['user_avatar'] = $google_user['picture'];
}


if ($_SESSION['user_role'] === 'admin') {
    header('Location: ' . BASE_URL . 'admin/');
} else {
    header('Location: ' . BASE_URL . 'frontend/pages/index.php');
}
exit;
?>