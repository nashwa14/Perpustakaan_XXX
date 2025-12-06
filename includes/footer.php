<footer>
    <div class="container">
        <div class="footer-content">
            <!-- About Section -->
            <div class="footer-section">
                <h3>
                    <i class="bi bi-building me-2"></i>
                    Perpustakaan Yogakarta
                </h3>
                <p>Pusat informasi dan pengetahuan untuk mendukung produktivitas dan wawasan seluruh anggota.</p>
                <div class="social-links mt-3">
                    <a href="#" class="me-3" title="Facebook">
                        <i class="bi bi-facebook fs-4"></i>
                    </a>
                    <a href="#" class="me-3" title="Twitter">
                        <i class="bi bi-twitter fs-4"></i>
                    </a>
                    <a href="#" class="me-3" title="Instagram">
                        <i class="bi bi-instagram fs-4"></i>
                    </a>
                    <a href="#" title="LinkedIn">
                        <i class="bi bi-linkedin fs-4"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="footer-section">
                <h3>
                    <i class="bi bi-link-45deg me-2"></i>
                    Tautan Cepat
                </h3>
                <a href="index.php">
                    <i class="bi bi-chevron-right me-1"></i>Katalog Buku
                </a>
                <a href="berita.php">
                    <i class="bi bi-chevron-right me-1"></i>Berita & Agenda
                </a>
                <a href="about.php">
                    <i class="bi bi-chevron-right me-1"></i>Tentang Kami
                </a>
                <?php if(isset($_SESSION['user_id'])): ?>
                <a href="riwayat.php">
                    <i class="bi bi-chevron-right me-1"></i>Riwayat Peminjaman
                </a>
                <?php endif; ?>
            </div>

            <!-- Contact Info -->
            <div class="footer-section">
                <h3>
                    <i class="bi bi-envelope me-2"></i>
                    Kontak Kami
                </h3>
                <p>
                    <i class="bi bi-geo-alt me-2"></i>
                    Gedung Utama Lt. 2<br>
                    Jl. Jendral Sudirman No. Kav 10<br>
                    Yogakarta, 55000
                </p>
                <p>
                    <i class="bi bi-telephone me-2"></i>
                    (0274) 555-0199
                </p>
                <p>
                    <i class="bi bi-envelope-at me-2"></i>
                    perpus@yogakarta.com
                </p>
            </div>

            <!-- Operating Hours -->
            <div class="footer-section">
                <h3>
                    <i class="bi bi-clock me-2"></i>
                    Jam Operasional
                </h3>
                <div class="d-flex justify-content-between mb-2">
                    <span>Senin - Jumat</span>
                    <strong>08.00 - 17.00</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Sabtu - Minggu</span>
                    <strong style="color: #f1c40f;">Tutup</strong>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <p class="mb-0">
                &copy; <?= date('Y') ?> Perpustakaan Yogakarta. Hak Cipta Dilindungi.
            </p>
        </div>
    </div>
</footer>