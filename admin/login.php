<?php
$pageTitle = 'Admin Login';
require_once __DIR__ . '/../backend/includes/functions.php';

if (isAdmin()) { header('Location: ' . BASE_URL . 'admin/'); exit; }

$error = '';
$csrf_token = generateCSRFToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid request.';
    } else {
        $email = sanitize($_POST['email']);
        $password = $_POST['password'];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND role = 'admin'");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_avatar'] = $user['avatar_url'];

            header('Location: ' . BASE_URL . 'admin/');
            exit;
        } else {
            $error = 'Email atau password salah.';
        }
    }
}

require_once __DIR__ . '/../frontend/includes/header.php';
?>

<section class="section-light min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="auth-card" style="max-width:380px">
            <div class="card-futsal">
                <div class="text-center mb-3">
                    <div style="font-family:var(--font-display);font-size:2.5rem;font-weight:700;letter-spacing:0.02em;color:var(--turf)">
                        <i class="fas fa-futbol"></i> Admin
                    </div>
                </div>
                <h2 style="font-size:1.5rem">Login Admin</h2>
                <p class="auth-subtitle">Masuk ke panel admin</p>

                <?php if ($error): ?>
                    <div class="alert-futsal alert-danger mb-4"><?= $error ?></div>
                <?php endif; ?>

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

                    <button type="submit" class="btn-futsal btn-turf w-100 justify-content-center">Login Admin</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../frontend/includes/footer.php'; ?>
