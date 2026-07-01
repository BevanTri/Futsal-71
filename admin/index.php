<?php
$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../backend/includes/functions.php';
requireAdmin();

$today = date('Y-m-d');
$current_month = date('Y-m');

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM bookings WHERE DATE(booking_date) = :today");
$stmt->execute(['today' => $today]);
$bookings_today = $stmt->fetch()['count'];

$stmt = $pdo->prepare("SELECT SUM(total_price) as revenue FROM bookings b
                       JOIN payments p ON b.id = p.booking_id
                       WHERE DATE(b.booking_date) = :today AND p.status = 'success'");
$stmt->execute(['today' => $today]);
$revenue_today = $stmt->fetch()['revenue'] ?? 0;

$stmt = $pdo->prepare("SELECT SUM(total_price) as revenue FROM bookings b
                       JOIN payments p ON b.id = p.booking_id
                       WHERE MONTH(b.booking_date) = MONTH(CURRENT_DATE())
                       AND YEAR(b.booking_date) = YEAR(CURRENT_DATE())
                       AND p.status = 'success'");
$stmt->execute();
$revenue_month = $stmt->fetch()['revenue'] ?? 0;

$stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'");
$total_users = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT b.*, u.name as user_name, f.name as field_name, p.status as payment_status
                     FROM bookings b
                     JOIN users u ON b.user_id = u.id
                     JOIN fields f ON b.field_id = f.id
                     LEFT JOIN payments p ON b.id = p.booking_id
                     ORDER BY b.created_at DESC LIMIT 10");
$recent_bookings = $stmt->fetchAll();

require_once __DIR__ . '/../frontend/includes/header.php';
?>

<section class="section-light" style="padding-top:2rem">
    <div class="container-fluid">
        <div class="row g-4">
            <!-- Sidebar -->
            <div class="col-lg-2">
                <div class="admin-sidebar">
                    <div class="sidebar-header">
                        <h5><i class="fas fa-futbol"></i> Admin</h5>
                        <div class="sub">Futsal 71</div>
                    </div>
                    <nav>
                        <a class="nav-link active" href="<?= BASE_URL ?>admin/"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                        <a class="nav-link" href="<?= BASE_URL ?>admin/bookings.php"><i class="fas fa-calendar-check"></i> Booking</a>
                        <a class="nav-link" href="<?= BASE_URL ?>admin/fields.php"><i class="fas fa-futbol"></i> Lapangan</a>
                        <a class="nav-link" href="<?= BASE_URL ?>admin/users.php"><i class="fas fa-users"></i> Users</a>
                        <a class="nav-link" href="<?= BASE_URL ?>frontend/pages/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </nav>
                </div>
            </div>

            <!-- Main -->
            <div class="col-lg-10">
                <h2 style="font-family:var(--font-display);font-size:2rem;font-weight:600;letter-spacing:0.01em">Dashboard</h2>

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="stat-card stat-turf">
                            <div class="stat-label">Booking Hari Ini</div>
                            <div class="stat-value"><?= $bookings_today ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card stat-ball">
                            <div class="stat-label">Pendapatan Hari Ini</div>
                            <div class="stat-value"><?= formatRupiah($revenue_today) ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card stat-pitch">
                            <div class="stat-label">Pendapatan Bulan Ini</div>
                            <div class="stat-value"><?= formatRupiah($revenue_month) ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card stat-info">
                            <div class="stat-label">Total Users</div>
                            <div class="stat-value"><?= $total_users ?></div>
                        </div>
                    </div>
                </div>

                <div class="card-futsal">
                    <div class="card-body">
                        <h5 style="font-family:var(--font-display);font-size:1.2rem;font-weight:600;letter-spacing:0.01em">Booking Terbaru</h5>
                        <hr>
                        <div class="table-responsive">
                            <table class="table-futsal">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Customer</th>
                                        <th>Lapangan</th>
                                        <th>Tanggal</th>
                                        <th>Jam</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recent_bookings)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center" style="color:var(--f1-text-secondary);padding:2rem">Belum ada booking</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recent_bookings as $booking): ?>
                                            <tr>
                                                <td><strong><?= $booking['booking_code'] ?></strong></td>
                                                <td><?= $booking['user_name'] ?></td>
                                                <td><?= $booking['field_name'] ?></td>
                                                <td><?= formatDate($booking['booking_date']) ?></td>
                                                <td><?= formatTime($booking['start_time']) ?> — <?= formatTime($booking['end_time']) ?></td>
                                                <td><?= formatRupiah($booking['total_price']) ?></td>
                                                <td>
                                                    <span class="badge-futsal <?= $booking['status'] === 'pending' ? 'badge-pending' : ($booking['status'] === 'confirmed' ? 'badge-confirmed' : ($booking['status'] === 'cancelled' ? 'badge-cancelled' : 'badge-completed')) ?>">
                                                        <?= ucfirst($booking['status']) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="bookings.php" class="btn-futsal btn-turf mt-3" style="padding:0.5rem 1.25rem;font-size:0.85rem">
                            Lihat Semua Booking <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../frontend/includes/footer.php'; ?>
