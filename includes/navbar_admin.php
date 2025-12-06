<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a href="dashboard.php" class="navbar-brand">
            <img src="../assets/uploads/logo.png" alt="Logo Perpustakaan" onerror="this.style.display='none'">
            <span>Perpustakaan Yogakarta</span>
        </a>
        
        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler" 
                type="button" 
                data-bs-toggle="collapse" 
                data-bs-target="#navbarAdmin"
                aria-controls="navbarAdmin"
                aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Menu -->
        <div class="collapse navbar-collapse" id="navbarAdmin">
            <div class="nav-menu ms-auto">
                
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
                
                <span class="nav-divider"></span>
                
                <a href="../index.php" 
                   class="nav-link" 
                   target="_blank"
                   title="Buka halaman website">
                    <i class="bi bi-box-arrow-up-right me-1"></i>
                    Lihat Website
                </a>
                
                <a href="../logout.php" 
                   class="nav-link nav-link-logout"
                   onclick="return confirm('Yakin ingin logout dari panel admin?')">
                    <i class="bi bi-box-arrow-right me-1"></i>
                    Logout
                </a>
            </div>
        </div>
    </div>
</nav>