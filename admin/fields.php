<?php
$pageTitle = 'Kelola Lapangan';
require_once __DIR__ . '/../backend/includes/functions.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $field_id = isset($_POST['field_id']) ? (int)$_POST['field_id'] : 0;
    $name = sanitize($_POST['name']);
    $type = sanitize($_POST['type']);
    $price = (float)$_POST['price'];
    $description = sanitize($_POST['description']);
    $facilities = sanitize($_POST['facilities']);
    $status = sanitize($_POST['status']);
    $photo_url = sanitize($_POST['photo_url']);

    if ($field_id) {
        $stmt = $pdo->prepare("UPDATE fields SET name = :name, type = :type, price_per_hour = :price,
                               description = :description, facilities = :facilities, status = :status, photo_url = :photo
                               WHERE id = :id");
        $stmt->execute([
            'name' => $name, 'type' => $type, 'price' => $price,
            'description' => $description, 'facilities' => $facilities,
            'status' => $status, 'photo' => $photo_url, 'id' => $field_id
        ]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO fields (name, type, price_per_hour, description, facilities, status, photo_url)
                               VALUES (:name, :type, :price, :description, :facilities, :status, :photo)");
        $stmt->execute([
            'name' => $name, 'type' => $type, 'price' => $price,
            'description' => $description, 'facilities' => $facilities,
            'status' => $status, 'photo' => $photo_url
        ]);
    }

    header('Location: ' . BASE_URL . 'admin/fields.php?success=Lapangan berhasil disimpan');
    exit;
}

if (isset($_GET['delete'])) {
    $field_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM fields WHERE id = :id");
    $stmt->execute(['id' => $field_id]);
    header('Location: ' . BASE_URL . 'admin/fields.php?success=Lapangan berhasil dihapus');
    exit;
}

$stmt = $pdo->query("SELECT * FROM fields ORDER BY name");
$fields = $stmt->fetchAll();

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
                        <a class="nav-link active" href="<?= BASE_URL ?>admin/fields.php"><i class="fas fa-futbol"></i> Lapangan</a>
                        <a class="nav-link" href="<?= BASE_URL ?>admin/users.php"><i class="fas fa-users"></i> Users</a>
                        <a class="nav-link" href="<?= BASE_URL ?>frontend/pages/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </nav>
                </div>
            </div>

            <div class="col-lg-10">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 style="font-family:var(--font-display);font-size:2rem;font-weight:600;letter-spacing:0.01em;margin:0">Kelola Lapangan</h2>
                    <button type="button" class="btn-futsal btn-turf" style="padding:0.5rem 1.25rem;font-size:0.85rem" data-bs-toggle="modal" data-bs-target="#addFieldModal">
                        <i class="fas fa-plus"></i> Tambah Lapangan
                    </button>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert-futsal alert-success mb-4"><?= sanitize($_GET['success']) ?></div>
                <?php endif; ?>

                <div class="card-futsal">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table-futsal">
                                <thead>
                                    <tr>
                                        <th>Foto</th>
                                        <th>Nama</th>
                                        <th>Tipe</th>
                                        <th>Harga/Jam</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($fields as $field): ?>
                                        <tr>
                                            <td>
                                                <img src="<?= $field['photo_url'] ?>" alt="<?= $field['name'] ?>"
                                                     style="width:80px;height:60px;object-fit:cover;border-radius:var(--radius-sm)">
                                            </td>
                                            <td><strong><?= $field['name'] ?></strong></td>
                                            <td><?= $field['type'] ?></td>
                                            <td><?= formatRupiah($field['price_per_hour']) ?></td>
                                            <td>
                                                <?php if ($field['status'] === 'active'): ?>
                                                    <span class="badge-futsal badge-confirmed">Active</span>
                                                <?php else: ?>
                                                    <span class="badge-futsal badge-cancelled">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button type="button" class="btn-futsal btn-turf" style="padding:0.3rem 0.75rem;font-size:0.8rem"
                                                        data-bs-toggle="modal" data-bs-target="#editFieldModal<?= $field['id'] ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="fields.php?delete=<?= $field['id'] ?>"
                                                   class="btn-futsal" style="padding:0.3rem 0.75rem;font-size:0.8rem;background:#FEE2E2;color:#991B1B"
                                                   data-confirm="Yakin ingin menghapus lapangan ini?">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>

                                        <!-- Edit Modal -->
                                        <div class="modal fade modal-futsal" id="editFieldModal<?= $field['id'] ?>" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <form method="POST">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Lapangan</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="field_id" value="<?= $field['id'] ?>">
                                                            <div class="mb-3">
                                                                <label class="form-label">Nama Lapangan</label>
                                                                <input type="text" name="name" class="form-control" value="<?= $field['name'] ?>" required>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label">Tipe</label>
                                                                    <select name="type" class="form-control" required>
                                                                        <option value="Vinyl" <?= $field['type'] === 'Vinyl' ? 'selected' : '' ?>>Vinyl</option>
                                                                        <option value="Rumput Sintetis" <?= $field['type'] === 'Rumput Sintetis' ? 'selected' : '' ?>>Rumput Sintetis</option>
                                                                        <option value="Parquet" <?= $field['type'] === 'Parquet' ? 'selected' : '' ?>>Parquet</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label">Harga per Jam</label>
                                                                    <input type="number" name="price" class="form-control" value="<?= $field['price_per_hour'] ?>" required>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Deskripsi</label>
                                                                <textarea name="description" class="form-control" rows="3"><?= $field['description'] ?></textarea>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Fasilitas</label>
                                                                <textarea name="facilities" class="form-control" rows="2"><?= $field['facilities'] ?></textarea>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">URL Foto</label>
                                                                <input type="text" name="photo_url" class="form-control" value="<?= $field['photo_url'] ?>">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Status</label>
                                                                <select name="status" class="form-control" required>
                                                                    <option value="active" <?= $field['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                                                    <option value="inactive" <?= $field['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn-futsal btn-outline-futsal" style="padding:0.4rem 1rem;font-size:0.85rem;color:var(--f1-text);border-color:var(--f1-border)" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn-futsal btn-turf" style="padding:0.4rem 1rem;font-size:0.85rem">Simpan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
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

<!-- Add Modal -->
<div class="modal fade modal-futsal" id="addFieldModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Lapangan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lapangan</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipe</label>
                            <select name="type" class="form-control" required>
                                <option value="Vinyl">Vinyl</option>
                                <option value="Rumput Sintetis">Rumput Sintetis</option>
                                <option value="Parquet">Parquet</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga per Jam</label>
                            <input type="number" name="price" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fasilitas</label>
                        <textarea name="facilities" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL Foto</label>
                        <input type="text" name="photo_url" class="form-control" placeholder="https://example.com/photo.jpg">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-futsal btn-outline-futsal" style="padding:0.4rem 1rem;font-size:0.85rem;color:var(--f1-text);border-color:var(--f1-border)" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-futsal btn-turf" style="padding:0.4rem 1rem;font-size:0.85rem">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../frontend/includes/footer.php'; ?>
