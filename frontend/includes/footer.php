<footer class="footer-futsal">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4">
                <a href="<?= BASE_URL ?>frontend/pages/index.php" style="display:inline-block">
                    <img src="<?= BASE_URL ?>photo/logo.png" alt="Futsal 71" style="height:48px;width:auto">
                </a>
                <p style="max-width:320px">Lapangan futsal terbaik di Tangerang dengan fasilitas lengkap dan harga terjangkau. Booking online, main langsung.</p>
                <div class="d-flex gap-2 mt-3">
                    <a href="https://instagram.com/futsal71bugel" target="_blank" class="social-link" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://wa.me/628179430031" target="_blank" class="social-link" aria-label="WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-4">
                <h5>Kontak</h5>
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Jl. Aria Santika No.15113, Bugel, Karawaci, Tangerang</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <span>0817-9430-031</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-clock"></i>
                    <span>08:00 — 22:00 WIB</span>
                </div>
            </div>
            <div class="col-lg-4">
                <h5>Menu</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="<?= BASE_URL ?>frontend/pages/index.php">Beranda</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL ?>frontend/pages/booking.php">Booking</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL ?>frontend/pages/contact.php">Kontak</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li class="mb-2"><a href="<?= BASE_URL ?>frontend/pages/history.php">Riwayat Booking</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <hr class="footer-divider">
        <div class="text-center">
            <p class="mb-0" style="color:var(--f1-text-muted);font-size:0.85rem">&copy; <?= date('Y') ?> Futsal 71. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script src="<?= BASE_URL ?>assets/main.js"></script>
</body>
</html>
