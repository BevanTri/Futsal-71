<?php
$pageTitle = 'Beranda';
require_once __DIR__ . '/../includes/header.php';

$fields = getAllFields($pdo);
$testimonials = getTestimonials();
?>

<!-- HERO -->
<section class="hero">
    <div class="container hero-content">
        <div class="hero-badge">
            <span class="dot"></span>
            Buka setiap hari 08:00 — 22:00 WIB
        </div>
        <h1 class="display-hero">Booking Lapangan<br>Futsal Jadi Mudah</h1>
        <p>Pilih lapangan, tentukan waktu, bayar online. Tanpa ribet, tanpa antri, langsung main.</p>
        <div class="hero-actions">
            <a href="<?= BASE_URL ?>frontend/pages/booking.php" class="btn-futsal btn-red">
                <i class="fas fa-calendar-check"></i> Booking Sekarang
            </a>
            <a href="<?= BASE_URL ?>frontend/pages/contact.php" class="btn-futsal btn-outline-futsal">
                <i class="fas fa-info-circle"></i> Info Lengkap
            </a>
        </div>
    </div>
</section>

<!-- FEATURES -->
<section class="section-dark">
    <div class="container text-center">
        <h2 class="section-title">Kenapa Futsal 71?</h2>
        <p class="section-subtitle mx-auto">Tiga alasan kenapa pemain futsal milih kami</p>
        <div class="row g-4 mt-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon-wrap"><i class="fas fa-futbol"></i></div>
                    <h5>Lapangan Berkualitas</h5>
                    <p>Rumput vinyl dan sintetis berkualitas tinggi, dirawat rutin biar nyaman main.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon-wrap"><i class="fas fa-clock"></i></div>
                    <h5>Booking 24/7</h5>
                    <p>Booking kapan aja, di mana aja lewat website. Gak perlu telepon atau datang langsung.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon-wrap"><i class="fas fa-credit-card"></i></div>
                    <h5>Bayar Gampang</h5>
                    <p>Transfer Bank (VA), QRIS, E-Wallet, atau minimarket — pilih yang paling gampang.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FIELDS -->
<section class="section-surface">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Lapangan Kami</h2>
            <p class="section-subtitle mx-auto">Tersedia 3 lapangan dengan tipe berbeda</p>
        </div>
        <div class="row g-4">
            <?php foreach ($fields as $field): ?>
                <div class="col-md-4">
                    <div class="field-card">
                        <div class="field-img-wrap">
                            <img src="<?= BASE_URL . $field['photo_url'] ?>" alt="<?= $field['name'] ?>" loading="lazy">
                            <span class="field-type-badge"><i class="fas fa-futbol"></i> <?= $field['type'] ?></span>
                        </div>
                        <div class="field-body">
                            <h5><?= $field['name'] ?></h5>
                            <p><?= substr($field['description'], 0, 120) ?>...</p>
                            <div class="field-footer">
                                <span class="price-tag"><?= formatRupiah($field['price_per_hour']) ?> <span class="unit">/jam</span></span>
                                <a href="<?= BASE_URL ?>frontend/pages/booking.php?field_id=<?= $field['id'] ?>" class="btn-futsal btn-red" style="padding:0.5rem 1.25rem;font-size:0.85rem">
                                    Booking <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- TESTIMONIALS -->
<section class="section-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Kata Mereka</h2>
            <p class="section-subtitle mx-auto">Pengalaman seru dari pelanggan setia Futsal 71</p>
        </div>

        <div class="row g-4 mb-4">
            <?php foreach ($testimonials as $t): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="testimonial-card h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-placeholder" style="background:linear-gradient(135deg, <?= $t['color'] ?>, var(--f1-bg))">
                                <?= $t['initials'] ?>
                            </div>
                            <div>
                                <h6 class="mb-0" style="font-weight:600;color:#fff"><?= $t['name'] ?></h6>
                                <div class="stars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fa<?= $i <= $t['rating'] ? 's' : 'r' ?> fa-star"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>
                        <blockquote>&ldquo;<?= $t['text'] ?>&rdquo;</blockquote>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<!-- LOCATION -->
<section class="section-surface">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Lokasi</h2>
            <p class="section-subtitle mx-auto">Dateng langsung, main bareng</p>
        </div>
        <div class="row g-5 align-items-center">
            <div class="col-md-6">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.6424912828616!2d106.60741809999999!3d-6.178586899999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69ff219cd963ef%3A0x9264c890b2990c06!2sFutsal%2071!5e0!3m2!1sid!2sid!4v1782882100372!5m2!1sid!2sid" width="100%" height="340" style="border:0;border-radius:var(--radius-sm)" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
            </div>
            <div class="col-md-6">
                <h3 class="section-title" style="font-size:2.2rem">Futsal 71</h3>
                <p style="color:var(--f1-text-secondary);line-height:1.8;font-size:0.95rem">Jl. Aria Santika No.15113, Bugel, Kec. Karawaci, Kota Tangerang</p>
                <div class="d-flex flex-column gap-3 mt-4">
                    <div style="display:flex;align-items:center;gap:0.75rem">
                        <div style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;color:var(--f1-red);flex-shrink:0;background:rgba(225,6,0,0.08);border:1px solid rgba(225,6,0,0.15)"><i class="fas fa-phone"></i></div>
                        <span style="color:var(--f1-text-secondary)">0817-9430-031</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:0.75rem">
                        <div style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;color:var(--f1-red);flex-shrink:0;background:rgba(225,6,0,0.08);border:1px solid rgba(225,6,0,0.15)"><i class="fas fa-clock"></i></div>
                        <span style="color:var(--f1-text-secondary)">08:00 — 22:00 WIB (Setiap Hari)</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:0.75rem">
                        <div style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;color:var(--f1-red);flex-shrink:0;background:rgba(225,6,0,0.08);border:1px solid rgba(225,6,0,0.15)"><i class="fab fa-instagram"></i></div>
                        <span style="color:var(--f1-text-secondary)">@futsal71bugel</span>
                    </div>
                </div>
                <a href="https://wa.me/628179430031" target="_blank" class="btn-futsal btn-red mt-4">
                    <i class="fab fa-whatsapp"></i> Hubungi via WhatsApp
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
