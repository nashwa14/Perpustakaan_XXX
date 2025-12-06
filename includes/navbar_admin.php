<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a href="dashboard.php" class="navbar-brand d-flex align-items-center">
            <img src="../assets/uploads/logo.png"
                alt="Logo Perpustakaan"
                class="me-2"
                style="height: 40px"
                onerror="this.style.display='none'">
            <span class="fw-semibold">Perpustakaan Yogakarta</span>
        </a>
        <button class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarAdmin"
            aria-controls="navbarAdmin"
            aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarAdmin">
            <div class="nav-menu d-flex align-items-center gap-3 ms-auto ms-lg-4">
                <a href="kelola_buku.php"
                    class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'kelola_buku.php' || basename($_SERVER['PHP_SELF']) == 'edit_buku.php' ? 'active' : '' ?>">
                    Kelola Buku
                </a>
                <a href="kelola_berita.php"
                    class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'kelola_berita.php' ? 'active' : '' ?>">
                    Kelola Berita
                </a>
                <a href="kelola_anggota.php"
                    class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'kelola_anggota.php' ? 'active' : '' ?>">
                    Kelola Anggota
                </a>
                <a href="pengembalian.php"
                    class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'pengembalian.php' ? 'active' : '' ?>">
                    Pengembalian
                </a>
                <a href="laporan.php"
                    class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'laporan.php' ? 'active' : '' ?>">
                    Laporan
                </a>
                <a href="../index.php"
                    class="nav-link"
                    target="_blank">
                    <i class="bi bi-box-arrow-up-right me-1"></i>
                    Lihat Website
                </a>
                <a href="../logout.php"
                    class="nav-link nav-link-logout d-flex align-items-center px-3 py-2" onclick="return confirm('Yakin ingin logout dari panel admin?')">
                    <i class="bi bi-box-arrow-right me-1"></i>
                    Logout
                </a>
            </div>
        </div>
    </div>
</nav>