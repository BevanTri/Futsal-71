<?php
$pageTitle = 'Kelola User';
require_once __DIR__ . '/../backend/includes/functions.php';
requireAdmin();

$stmt = $pdo->query("SELECT u.*,
                     (SELECT COUNT(*) FROM bookings WHERE user_id = u.id) as total_bookings,
                     (SELECT SUM(total_price) FROM bookings b JOIN payments p ON b.id = p.booking_id WHERE b.user_id = u.id AND p.status = 'success') as total_spent
                     FROM users u WHERE role = 'user' ORDER BY created_at DESC");
$users = $stmt->fetchAll();

require_once __DIR__ . '/../frontend/includes/header.php';
?>

<section class="section-light" style="padding-top:2rem">
    <div class="container-fluid">
        <div class="row g-4">
            <div class="col-lg-2">
                <div class="admin-sidebar">
                    <div class="sidebar-header">
                        <h5><i class="fas fa-futbol"></i> Admin</h5>
                        <div class="sub">Futsal 71</div>
                    </div>
                    <nav>
                        <a class="nav-link" href="<?= BASE_URL ?>admin/"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                        <a class="nav-link" href="<?= BASE_URL ?>admin/bookings.php"><i class="fas fa-calendar-check"></i> Booking</a>
                        <a class="nav-link" href="<?= BASE_URL ?>admin/fields.php"><i class="fas fa-futbol"></i> Lapangan</a>
                        <a class="nav-link active" href="<?= BASE_URL ?>admin/users.php"><i class="fas fa-users"></i> Users</a>
                        <a class="nav-link" href="<?= BASE_URL ?>frontend/pages/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </nav>
                </div>
            </div>

            <div class="col-lg-10">
                <h2 style="font-family:var(--font-display);font-size:2rem;font-weight:600;letter-spacing:0.01em">User</h2>

                <div class="card-futsal mt-3">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table-futsal">
                                <thead>
                                    <tr>
                                        <th>Avatar</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Login Via</th>
                                        <th>Total Booking</th>
                                        <th>Total Spending</th>
                                        <th>Terdaftar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td>
                                                <?php if (!empty($user['avatar_url'])): ?>
                                                    <img src="<?= $user['avatar_url'] ?>" alt="" class="rounded-circle" width="40" height="40">
                                                <?php else: ?>
                                                    <div style="width:40px;height:40px;border-radius:50%;background:var(--turf);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.9rem">
                                                        <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td><strong><?= $user['name'] ?></strong></td>
                                            <td><?= $user['email'] ?></td>
                                            <td><?= $user['phone'] ?: '-' ?></td>
                                            <td>
                                                <?php if (!empty($user['google_id'])): ?>
                                                    <span class="badge-futsal" style="background:#FEE2E2;color:#991B1B"><i class="fab fa-google"></i> Google</span>
                                                <?php else: ?>
                                                    <span class="badge-futsal" style="background:#F1F5F9;color:var(--f1-text-secondary)">Email</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $user['total_bookings'] ?> booking</td>
                                            <td><?= formatRupiah($user['total_spent'] ?? 0) ?></td>
                                            <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../frontend/includes/footer.php'; ?>
