<?php
$pageTitle = 'Kelola Booking';
require_once __DIR__ . '/../backend/includes/functions.php';
requireAdmin();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $booking_id = (int)$_POST['booking_id'];
    $new_status = sanitize($_POST['status']);
    $stmt = $pdo->prepare("UPDATE bookings SET status = :status WHERE id = :id");
    $stmt->execute(['status' => $new_status, 'id' => $booking_id]);
    header('Location: ' . BASE_URL . 'admin/bookings.php?success=Status updated');
    exit;
}


if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $sql = "SELECT b.booking_code, u.name as user_name, u.email as user_email, f.name as field_name,
            b.booking_date, b.start_time, b.end_time, b.total_hours, b.total_price,
            b.status as booking_status, p.status as payment_status, p.payment_method
            FROM bookings b
            JOIN users u ON b.user_id = u.id
            JOIN fields f ON b.field_id = f.id
            LEFT JOIN payments p ON b.id = p.booking_id
            ORDER BY b.created_at DESC";

    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll();

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=bookings_' . date('Y-m-d') . '.csv');

    $fh = fopen('php://output', 'w');
    fputs($fh, "\xEF\xBB\xBF"); 
    fputcsv($fh, ['Kode', 'Customer', 'Email', 'Lapangan', 'Tanggal', 'Jam Mulai', 'Jam Selesai', 'Durasi (jam)', 'Total', 'Status Booking', 'Status Bayar', 'Metode Bayar']);
    foreach ($rows as $r) {
        fputcsv($fh, [
            $r['booking_code'], $r['user_name'], $r['user_email'], $r['field_name'],
            $r['booking_date'], $r['start_time'], $r['end_time'], $r['total_hours'],
            $r['total_price'], $r['booking_status'], $r['payment_status'], $r['payment_method']
        ]);
    }
    fclose($fh);
    exit;
}


$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$filter_date = isset($_GET['date']) ? $_GET['date'] : '';

$sql = "SELECT b.*, u.name as user_name, u.email as user_email, f.name as field_name,
        p.status as payment_status, p.payment_method
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN fields f ON b.field_id = f.id
        LEFT JOIN payments p ON b.id = p.booking_id
        WHERE 1=1";

$params = [];
if ($filter_status) { $sql .= " AND b.status = :status"; $params['status'] = $filter_status; }
if ($filter_date) { $sql .= " AND b.booking_date = :date"; $params['date'] = $filter_date; }
$sql .= " ORDER BY b.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll();

$success_msg = isset($_GET['success']) ? sanitize($_GET['success']) : '';

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
                        <a class="nav-link active" href="<?= BASE_URL ?>admin/bookings.php"><i class="fas fa-calendar-check"></i> Booking</a>
                        <a class="nav-link" href="<?= BASE_URL ?>admin/fields.php"><i class="fas fa-futbol"></i> Lapangan</a>
                        <a class="nav-link" href="<?= BASE_URL ?>admin/users.php"><i class="fas fa-users"></i> Users</a>
                        <a class="nav-link" href="<?= BASE_URL ?>frontend/pages/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </nav>
                </div>
            </div>

            <div class="col-lg-10">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 style="font-family:var(--font-display);font-size:2rem;font-weight:600;letter-spacing:0.01em;margin:0">Kelola Booking</h2>
                    <a href="bookings.php?export=csv" class="btn-futsal btn-turf" style="padding:0.5rem 1.25rem;font-size:0.85rem">
                        <i class="fas fa-file-export"></i> Export CSV
                    </a>
                </div>

                <?php if ($success_msg): ?>
                    <div class="alert-futsal alert-success mb-4"><?= $success_msg ?></div>
                <?php endif; ?>

                <!-- Filters -->
                <div class="card-futsal mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label" style="font-weight:600;font-size:0.85rem">Status</label>
                                <select name="status" class="form-control">
                                    <option value="">Semua Status</option>
                                    <option value="pending" <?= $filter_status === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="confirmed" <?= $filter_status === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                    <option value="cancelled" <?= $filter_status === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    <option value="completed" <?= $filter_status === 'completed' ? 'selected' : '' ?>>Completed</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="font-weight:600;font-size:0.85rem">Tanggal</label>
                                <input type="date" name="date" class="form-control" value="<?= $filter_date ?>">
                            </div>
                            <div class="col-md-4 d-flex align-items-end gap-2">
                                <button type="submit" class="btn-futsal btn-turf" style="padding:0.5rem 1.25rem;font-size:0.85rem">Filter</button>
                                <a href="bookings.php" class="btn-futsal btn-outline-futsal" style="padding:0.5rem 1.25rem;font-size:0.85rem;color:var(--f1-text);border-color:var(--f1-border)">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Table -->
                <div class="card-futsal">
                    <div class="card-body">
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
                                        <th>Pembayaran</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($bookings)): ?>
                                        <tr>
                                            <td colspan="9" class="text-center" style="color:var(--f1-text-secondary);padding:2rem">Tidak ada booking</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($bookings as $booking): ?>
                                            <tr>
                                                <td><strong><?= $booking['booking_code'] ?></strong></td>
                                                <td>
                                                    <?= $booking['user_name'] ?><br>
                                                    <small style="color:var(--f1-text-secondary)"><?= $booking['user_email'] ?></small>
                                                </td>
                                                <td><?= $booking['field_name'] ?></td>
                                                <td><?= formatDate($booking['booking_date']) ?></td>
                                                <td><?= formatTime($booking['start_time']) ?> — <?= formatTime($booking['end_time']) ?></td>
                                                <td><?= formatRupiah($booking['total_price']) ?></td>
                                                <td>
                                                    <span class="badge-futsal <?= $booking['payment_status'] === 'success' ? 'badge-confirmed' : ($booking['payment_status'] === 'pending' ? 'badge-pending' : 'badge-cancelled') ?>">
                                                        <?= $booking['payment_status'] === 'success' ? 'Lunas' : ucfirst($booking['payment_status']) ?>
                                                    </span>
                                                    <br><small style="color:var(--f1-text-secondary)"><?= ucfirst($booking['payment_method']) ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge-futsal <?= $booking['status'] === 'pending' ? 'badge-pending' : ($booking['status'] === 'confirmed' ? 'badge-confirmed' : ($booking['status'] === 'cancelled' ? 'badge-cancelled' : 'badge-completed')) ?>">
                                                        <?= ucfirst($booking['status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn-futsal btn-turf" style="padding:0.3rem 0.75rem;font-size:0.8rem" data-bs-toggle="modal" data-bs-target="#editModal<?= $booking['id'] ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Edit Modals (di luar table biar HTML valid) -->
<?php foreach ($bookings as $booking): ?>
<div class="modal fade modal-futsal" id="editModal<?= $booking['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Update Status Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                    <p><strong>Kode:</strong> <?= $booking['booking_code'] ?></p>
                    <p><strong>Customer:</strong> <?= $booking['user_name'] ?></p>
                    <div class="mb-3">
                        <label class="form-label">Status Baru</label>
                        <select name="status" class="form-control" required>
                            <option value="pending" <?= $booking['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="confirmed" <?= $booking['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                            <option value="cancelled" <?= $booking['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            <option value="completed" <?= $booking['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-futsal btn-outline-futsal" style="padding:0.4rem 1rem;font-size:0.85rem;color:var(--f1-text);border-color:var(--f1-border)" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="update_status" class="btn-futsal btn-turf" style="padding:0.4rem 1rem;font-size:0.85rem">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php require_once __DIR__ . '/../frontend/includes/footer.php'; ?>
