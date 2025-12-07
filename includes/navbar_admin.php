<nav class="navbar navbar-expand-lg navbar-light bg-white">
    <div class="container">
        <a href="dashboard.php" class="navbar-brand d-flex align-items-center">
            <img src="../assets/uploads/logo.png"
                alt="Logo Perpustakaan"
                class="me-2"
                style="height: 50px"
                onerror="this.style.display='none'">
            <span class="fw-semibold" style="color: #5d4037;">Perpustakaan Yogakarta</span>
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
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
                        Beranda
                    </a>
                </li>
                <li class="nav-item">
                    <a href="kelola_buku.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'kelola_buku.php' || basename($_SERVER['PHP_SELF']) == 'edit_buku.php' ? 'active' : '' ?>">
                        Buku
                    </a>
                </li>
                <li class="nav-item">
                    <a href="kelola_anggota.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'kelola_anggota.php' ? 'active' : '' ?>">
                        Anggota
                    </a>
                </li>
                <li class="nav-item">
                    <a href="pengembalian.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'pengembalian.php' ? 'active' : '' ?>">
                        Pengembalian
                    </a>
                </li>
                <li class="nav-item">
                    <a href="kelola_berita.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'kelola_berita.php' ? 'active' : '' ?>">
                        Berita
                    </a>
                </li>
                <li class="nav-item">
                    <a href="laporan.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'laporan.php' ? 'active' : '' ?>">
                        Laporan
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle p-0" 
                       href="#" 
                       id="adminProfileDropdown"
                       role="button"
                       data-bs-toggle="dropdown"
                       aria-expanded="false">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['nama']) ?>&background=5d4037&color=fff&size=45&bold=true" 
                             alt="Avatar" 
                             class="rounded-circle"
                             style="width: 45px; height: 45px; object-fit: cover; border: 2px solid #e0e0e0;">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end profile-dropdown-admin shadow-sm" aria-labelledby="adminProfileDropdown">
                        <li class="dropdown-header">
                            <div class="d-flex align-items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['nama']) ?>&background=5d4037&color=fff&size=50&bold=true" 
                                     alt="Avatar" 
                                     class="rounded-circle"
                                     style="width: 50px; height: 50px; object-fit: cover;">
                                <div>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($_SESSION['nama']) ?></div>
                                    <small class="text-muted">Administrator</small>
                                </div>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="dashboard.php">
                                Profil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item text-danger" 
                               href="../logout.php"
                               onclick="return confirm('Yakin ingin logout dari panel admin?')">
                                Keluar
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>