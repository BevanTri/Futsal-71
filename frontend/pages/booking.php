<?php
$pageTitle = 'Booking Lapangan';
require_once __DIR__ . '/../../backend/includes/functions.php';
requireLogin();

$fields = getAllFields($pdo);
$selected_field = isset($_GET['field_id']) ? (int)$_GET['field_id'] : 0;
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

$booked_slots = [];
if ($selected_field && $selected_date) {
    $stmt = $pdo->prepare("SELECT start_time, end_time FROM bookings
                           WHERE field_id = :field_id AND booking_date = :booking_date
                           AND status IN ('pending', 'confirmed')");
    $stmt->execute(['field_id' => $selected_field, 'booking_date' => $selected_date]);
    $booked_slots = $stmt->fetchAll();
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="section-light">
    <div class="container">
        <div class="stepper">
            <div class="step active"><span class="step-num">1</span> Pilih Jadwal</div>
            <div class="step-line"></div>
            <div class="step"><span class="step-num">2</span> Checkout</div>
            <div class="step-line"></div>
            <div class="step"><span class="step-num">3</span> Bayar</div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card-futsal">
                    <div class="card-body">
                        <form method="GET" class="form-futsal">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="field_id" class="form-label">Pilih Lapangan</label>
                                    <select name="field_id" id="field_id" class="form-select" required>
                                        <option value="">— Pilih Lapangan —</option>
                                        <?php foreach ($fields as $field): ?>
                                            <option value="<?= $field['id'] ?>" <?= $selected_field == $field['id'] ? 'selected' : '' ?>>
                                                <?= $field['name'] ?> — <?= formatRupiah($field['price_per_hour']) ?>/jam
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="date" class="form-label">Tanggal</label>
                                    <input type="date" name="date" id="date" class="form-control"
                                           value="<?= $selected_date ?>"
                                           min="<?= date('Y-m-d') ?>" required>
                                </div>
                            </div>
                            <button type="submit" class="btn-futsal btn-turf mt-3">
                                <i class="fas fa-search"></i> Cek Ketersediaan
                            </button>
                        </form>

                        <?php if ($selected_field && $selected_date): ?>
                            <hr class="my-4">
                            <h5 style="font-family:var(--font-display);font-size:1.3rem;font-weight:600;letter-spacing:0.01em">Jam Tersedia</h5>
                            <div class="slot-grid mt-3">
                                <?php
                                $hours = ['08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00'];
                                foreach ($hours as $hour):
                                    $is_booked = false;
                                    foreach ($booked_slots as $slot) {
                                        if (strtotime($hour) >= strtotime($slot['start_time']) && strtotime($hour) < strtotime($slot['end_time'])) {
                                            $is_booked = true; break;
                                        }
                                    }
                                ?>
                                    <a href="<?= BASE_URL ?>frontend/pages/checkout.php?field_id=<?= $selected_field ?>&date=<?= $selected_date ?>&start_time=<?= $hour ?>"
                                       class="slot-btn <?= $is_booked ? 'booked' : '' ?>"
                                       <?= $is_booked ? 'tabindex="-1" aria-disabled="true"' : '' ?>>
                                        <?= $hour ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                            <div class="slot-legend">
                                <span><span class="swatch available"></span> Tersedia</span>
                                <span><span class="swatch booked"></span> Dibooking</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="info-sidebar">
                    <h5><i class="fas fa-info-circle"></i> Info Booking</h5>
                    <hr>
                    <p><strong>Jam Operasional:</strong><br>08:00 — 22:00 WIB</p>
                    <p><strong>Ketentuan:</strong></p>
                    <ul>
                        <li>Booking minimal 1 jam</li>
                        <li>Bayar dalam 30 menit</li>
                        <li>Booking gak dibayar otomatis batal</li>
                    </ul>
                    <hr>
                    <p>Butuh bantuan?<br>
                    <a href="https://wa.me/628179430031" target="_blank" class="btn-futsal btn-turf" style="padding:0.5rem 1rem;font-size:0.85rem;margin-top:0.5rem">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
