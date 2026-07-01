<?php
$pageTitle = 'Register';
require_once __DIR__ . '/../../backend/includes/functions.php';

if (isLoggedIn()) { header('Location: ' . BASE_URL); exit; }

$error = '';
$success = '';
$csrf_token = generateCSRFToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid request. Please try again.';
    } else {
        $name = sanitize($_POST['name']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if (empty($name) || empty($email) || empty($password)) {
            $error = 'Semua field harus diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Format email tidak valid.';
        } elseif (strlen($password) < 6) {
            $error = 'Password minimal 6 karakter.';
        } elseif ($password !== $confirm_password) {
            $error = 'Password tidak cocok.';
        } else {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);

            if ($stmt->fetch()) {
                $error = 'Email sudah terdaftar.';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (:name, :email, :phone, :password, 'user')");

                if ($stmt->execute([
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'password' => $hashed_password
                ])) {
                    $success = 'Registrasi berhasil! Silakan login.';
                } else {
                    $error = 'Terjadi kesalahan. Silakan coba lagi.';
                }
            }
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="section-light min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="auth-card">
            <div class="card-futsal">
                <h2>Daftar</h2>
                <p class="auth-subtitle">Bikin akun dulu, yuk</p>

                <?php if ($error): ?>
                    <div class="alert-futsal alert-danger mb-4"><?= $error ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert-futsal alert-success mb-4"><?= $success ?></div>
                <?php endif; ?>

                <a href="<?= BASE_URL ?>api/google-callback.php?action=register" class="btn-futsal btn-outline-futsal w-100 justify-content-center mb-3" style="color:var(--f1-text);border-color:var(--f1-border)">
                    <i class="fab fa-google" style="color:#EA4335"></i> Daftar dengan Google
                </a>

                <div class="text-center mb-3" style="color:var(--f1-text-secondary);font-size:0.85rem;position:relative">
                    <span style="background:var(--f1-surface);padding:0 1rem;position:relative;z-index:1">atau daftar dengan email</span>
                    <hr style="position:relative;top:-0.7rem">
                </div>

                <form method="POST" class="form-futsal">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Nomor WhatsApp</label>
                        <input type="tel" class="form-control" id="phone" name="phone" placeholder="081234567890">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <small style="color:var(--f1-text-secondary)">Minimal 6 karakter</small>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>

                    <button type="submit" class="btn-futsal btn-turf w-100 justify-content-center">Daftar</button>
                </form>

                <p class="text-center mt-3" style="font-size:0.88rem;color:var(--f1-text-secondary)">
                    Sudah punya akun? <a href="<?= BASE_URL ?>frontend/pages/login.php" style="color:var(--turf);font-weight:600">Login di sini</a>
                </p>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
