<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - Perpustakaan Yogakarta</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <?php include 'includes/navbar_user.php'; ?>

    <div class="container my-5">

        <!-- Page Header -->
        <div class="page-header fade-in-up">
            <h1>
                <i class="bi bi-info-circle me-3"></i>
                Tentang Perpustakaan Yogakarta
            </h1>
            <p>Pusat Informasi dan Pengetahuan untuk Anda</p>
        </div>

        <!-- Main Content -->
        <div class="card fade-in-up">
            <div class="card-body p-5">
                <div class="row mb-5">
                    <div class="col-md-12">
                        <h3 class="mb-4">
                            <i class="bi bi-building me-2 text-primary"></i>
                            Tentang Kami
                        </h3>
                        <p style="text-align: justify; line-height: 1.8; font-size: 1.05rem;">
                            Perpustakaan Yogakarta hadir sebagai pusat informasi dan pengetahuan bagi seluruh karyawan dan anggota.
                            Kami berkomitmen menyediakan koleksi berkualitas untuk menunjang produktivitas dan wawasan.
                            Dengan koleksi buku yang terus diperbarui dan layanan yang profesional, kami siap membantu Anda
                            dalam mengakses informasi dan pengetahuan yang Anda butuhkan.
                        </p>
                    </div>
                </div>

                <hr class="my-5">

                <!-- Information Grid -->
                <div class="row g-4">
                    <!-- Location -->
                    <div class="col-md-4">
                        <div class="text-center p-4 h-100" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); border-radius: 15px;">
                            <div class="mb-3">
                                <i class="bi bi-geo-alt-fill" style="font-size: 3rem; color: var(--slate-gray);"></i>
                            </div>
                            <h4 class="mb-3">Lokasi Kami</h4>
                            <p class="mb-2"><strong>Gedung Utama Lt. 2</strong></p>
                            <p class="mb-2">Jl. Jendral Sudirman No. Kav 10</p>
                            <p class="mb-0">Yogakarta, 55000</p>
                        </div>
                    </div>

                    <!-- Operating Hours -->
                    <div class="col-md-4">
                        <div class="text-center p-4 h-100" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); border-radius: 15px;">
                            <div class="mb-3">
                                <i class="bi bi-clock-fill" style="font-size: 3rem; color: var(--success);"></i>
                            </div>
                            <h4 class="mb-3">Jam Operasional</h4>
                            <div class="d-flex justify-content-between mb-2 px-3">
                                <span>Senin - Jumat</span>
                                <strong>08.00 - 17.00</strong>
                            </div>
                            <div class="d-flex justify-content-between px-3">
                                <span>Sabtu - Minggu</span>
                                <strong class="text-danger">Tutup</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Contact -->
                    <div class="col-md-4">
                        <div class="text-center p-4 h-100" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); border-radius: 15px;">
                            <div class="mb-3">
                                <i class="bi bi-telephone-fill" style="font-size: 3rem; color: var(--coffee);"></i>
                            </div>
                            <h4 class="mb-3">Hubungi Kami</h4>
                            <p class="mb-2">
                                <i class="bi bi-envelope me-2"></i>
                                <strong>perpus@yogakarta.com</strong>
                            </p>
                            <p class="mb-2">
                                <i class="bi bi-telephone me-2"></i>
                                <strong>(0274) 555-0199</strong>
                            </p>
                            <p class="mb-0">
                                <i class="bi bi-whatsapp me-2"></i>
                                <strong>0812-3456-7890</strong>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Services -->
                <div class="mt-5">
                    <h3 class="mb-4 text-center">
                        <i class="bi bi-star-fill me-2 text-warning"></i>
                        Layanan Kami
                    </h3>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-start p-3 rounded" style="background: #f8f9fa;">
                                <i class="bi bi-check-circle-fill text-success me-3 fs-4"></i>
                                <div>
                                    <strong>Peminjaman Buku</strong>
                                    <p class="mb-0 text-muted">Pinjam buku dengan mudah dan cepat</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start p-3 rounded" style="background: #f8f9fa;">
                                <i class="bi bi-check-circle-fill text-success me-3 fs-4"></i>
                                <div>
                                    <strong>Katalog Online</strong>
                                    <p class="mb-0 text-muted">Cari dan telusuri koleksi secara digital</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start p-3 rounded" style="background: #f8f9fa;">
                                <i class="bi bi-check-circle-fill text-success me-3 fs-4"></i>
                                <div>
                                    <strong>Ruang Baca</strong>
                                    <p class="mb-0 text-muted">Fasilitas ruang baca yang nyaman</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start p-3 rounded" style="background: #f8f9fa;">
                                <i class="bi bi-check-circle-fill text-success me-3 fs-4"></i>
                                <div>
                                    <strong>Konsultasi</strong>
                                    <p class="mb-0 text-muted">Bantuan pustakawan profesional</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA -->
        <?php if (!isset($_SESSION['user_id'])): ?>
            <!-- CTA -->
            <div class="text-center mt-5 fade-in-up">
                <h4 class="mb-3">Tertarik untuk Bergabung?</h4>
                <p class="text-muted mb-4">Daftar sekarang dan nikmati akses ke ribuan koleksi buku kami</p>
                <a href="register.php" class="btn btn-primary btn-lg">
                    <i class="bi bi-person-plus me-2"></i>
                    Daftar Sebagai Anggota
                </a>
            </div>
        <?php endif; ?>

    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>