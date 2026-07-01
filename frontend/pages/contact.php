<?php
$pageTitle = 'Kontak';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="section-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Hubungi Kami</h2>
            <p class="section-subtitle mx-auto">Ada pertanyaan? Langsung aja chat atau dateng</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card-futsal">
                    <div class="card-body">
                        <h5 style="font-family:var(--font-display);font-size:1.3rem;font-weight:600;letter-spacing:0.01em">Informasi Kontak</h5>
                        <hr>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex gap-3">
                                <div style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;color:var(--f1-red);flex-shrink:0;background:rgba(225,6,0,0.08);border:1px solid rgba(225,6,0,0.15)">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <strong>Alamat</strong><br>
                                    <span style="color:var(--f1-text-secondary);font-size:0.9rem">Jl. Aria Santika No.15113, Bugel, Karawaci, Tangerang</span>
                                </div>
                            </div>
                            <div class="d-flex gap-3">
                                <div style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;color:var(--f1-red);flex-shrink:0;background:rgba(225,6,0,0.08);border:1px solid rgba(225,6,0,0.15)">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div>
                                    <strong>Telepon</strong><br>
                                    <span style="color:var(--f1-text-secondary);font-size:0.9rem">0817-9430-031</span>
                                </div>
                            </div>
                            <div class="d-flex gap-3">
                                <div style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;color:var(--f1-red);flex-shrink:0;background:rgba(225,6,0,0.08);border:1px solid rgba(225,6,0,0.15)">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <strong>Jam Operasional</strong><br>
                                    <span style="color:var(--f1-text-secondary);font-size:0.9rem">08:00 — 22:00 WIB (Setiap Hari)</span>
                                </div>
                            </div>
                            <div class="d-flex gap-3">
                                <div style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;color:var(--f1-red);flex-shrink:0;background:rgba(225,6,0,0.08);border:1px solid rgba(225,6,0,0.15)">
                                    <i class="fab fa-instagram"></i>
                                </div>
                                <div>
                                    <strong>Instagram</strong><br>
                                    <span style="color:var(--f1-text-secondary);font-size:0.9rem">@futsal71bugel</span>
                                </div>
                            </div>
                        </div>

                        <a href="https://wa.me/628179430031" target="_blank" class="btn-futsal btn-red w-100 justify-content-center mt-4">
                            <i class="fab fa-whatsapp"></i> Chat via WhatsApp
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card-futsal">
                    <div class="card-body p-0">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.6424912828616!2d106.60741809999999!3d-6.178586899999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69ff219cd963ef%3A0x9264c890b2990c06!2sFutsal%2071!5e0!3m2!1sid!2sid!4v1782882100372!5m2!1sid!2sid"
                                width="100%" height="400" style="border:0;display:block" allowfullscreen="" loading="lazy"
                                referrerpolicy="strict-origin-when-cross-origin"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
