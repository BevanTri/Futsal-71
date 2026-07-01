<?php
$pageTitle = 'Login';
require_once __DIR__ . '/../../backend/includes/functions.php';

if (isLoggedIn()) { header('Location: ' . BASE_URL); exit; }

$error = '';
$csrf_token = generateCSRFToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid request. Please try again.';
    } else {
        $email = sanitize($_POST['email']);
        $password = $_POST['password'];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_avatar'] = $user['avatar_url'];

            header('Location: ' . BASE_URL . ($user['role'] === 'admin' ? 'admin/' : 'frontend/pages/index.php'));
            exit;
        } else {
            $error = 'Email atau password salah.';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="section-light min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="auth-card">
            <div class="card-futsal">
                <h2>Login</h2>
                <p class="auth-subtitle">Masuk buat booking lapangan</p>

                <?php if ($error): ?>
                    <div class="alert-futsal alert-danger mb-4"><?= $error ?></div>
                <?php endif; ?>

                <a href="<?= BASE_URL ?>api/google-callback.php?action=login" class="btn-futsal btn-outline-futsal w-100 justify-content-center mb-3" style="color:var(--f1-text);border-color:var(--f1-border)">
                    <i class="fab fa-google" style="color:#EA4335"></i> Login dengan Google
                </a>

                <div class="text-center mb-3" style="color:var(--f1-text-secondary);font-size:0.85rem;position:relative">
                    <span style="background:var(--f1-surface);padding:0 1rem;position:relative;z-index:1">atau login dengan email</span>
                    <hr style="position:relative;top:-0.7rem">
                </div>

                <form method="POST" class="form-futsal">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <button type="submit" class="btn-futsal btn-turf w-100 justify-content-center">Login</button>
                </form>

                <p class="text-center mt-3" style="font-size:0.88rem;color:var(--f1-text-secondary)">
                    Belum punya akun? <a href="<?= BASE_URL ?>frontend/pages/register.php" style="color:var(--turf);font-weight:600">Daftar di sini</a>
                </p>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
