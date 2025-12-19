<nav class="navbar navbar-expand-lg navbar-light bg-white main-navbar fixed-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="assets/uploads/logo.png"
                 alt="Logo Perpustakaan"
                 class="navbar-logo"
                 onerror="this.style.display='none'">
            <span class="fw-semibold ms-2 brand-text">Perpustakaan Yogyakarta</span>
        </a>
        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarUser">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarUser">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" 
                       href="index.php">
                        Katalog
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'berita.php' ? 'active' : '' ?>" 
                       href="berita.php">
                        Berita &amp; Agenda
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : '' ?>" 
                       href="about.php">
                        Tentang Kami
                    </a>
                </li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] == 'anggota'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'riwayat.php' ? 'active' : '' ?>" 
                               href="riwayat.php">
                                Riwayat
                            </a>
                        </li>
                        <li class="nav-item dropdown ms-2">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" 
                               href="#" 
                               id="profileDropdown"
                               role="button"
                               data-bs-toggle="dropdown"
                               aria-expanded="false">
                                  <div class="position-relative">
                                     <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['nama']) ?>&background=5d4037&color=fff&size=45&bold=true" 
                                         alt="Avatar" 
                                         class="rounded-circle avatar-profile avatar-sm">
                                     <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle status-dot"></span>
                                </div>
                                <span class="ms-2 d-none d-lg-inline profile-name"><?= htmlspecialchars(explode(' ', $_SESSION['nama'])[0]) ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end profile-dropdown shadow-lg" aria-labelledby="profileDropdown">
                                <li class="dropdown-header p-3" style="background: linear-gradient(135deg, #5d4037 0%, #7a5c4e 100%);">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['nama']) ?>&background=5d4037&color=fff&size=60&bold=true" 
                                            alt="Avatar" 
                                            class="rounded-circle avatar-lg">
                                        <div>
                                            <div class="fw-bold text-white mb-1"><?= htmlspecialchars($_SESSION['nama']) ?></div>
                                            <div class="badge bg-light text-dark"><?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Anggota' ?></div>
                                        </div>
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider my-2"></li>
                                <li>
                                    <a class="dropdown-item py-2" href="profil.php">
                                        <i class="bi bi-person me-2 nav-icon"></i> Profil Saya
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="riwayat.php">
                                        <i class="bi bi-clock-history me-2 nav-icon"></i> Riwayat Peminjaman
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-2"></li>
                                <li>
                                    <a class="dropdown-item py-2 text-danger" 
                                       href="logout.php"
                                       onclick="return confirm('Yakin ingin logout?')">
                                        <i class="bi bi-box-arrow-right me-2 nav-icon"></i> Keluar
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php elseif ($_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item">
                            <a class="btn btn-admin-panel" href="admin/dashboard.php">
                                <i class="bi bi-speedometer2 me-1 nav-icon"></i> Panel Admin
                            </a>
                        </li>
                        <li class="nav-item dropdown ms-2">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" 
                               href="#" 
                               id="profileDropdown"
                               role="button"
                               data-bs-toggle="dropdown"
                               aria-expanded="false">
                                  <div class="position-relative">
                                     <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['nama']) ?>&background=5d4037&color=fff&size=45&bold=true" 
                                         alt="Avatar" 
                                         class="rounded-circle avatar-profile avatar-sm">
                                     <span class="position-absolute bottom-0 end-0 bg-warning border border-2 border-white rounded-circle status-dot"></span>
                                </div>
                                <span class="ms-2 d-none d-lg-inline profile-name"><?= htmlspecialchars(explode(' ', $_SESSION['nama'])[0]) ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end profile-dropdown shadow-lg" aria-labelledby="profileDropdown">
                                <li class="dropdown-header p-3" style="background: linear-gradient(135deg, #5d4037 0%, #7a5c4e 100%);">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['nama']) ?>&background=5d4037&color=fff&size=60&bold=true" 
                                            alt="Avatar" 
                                            class="rounded-circle avatar-lg">
                                        <div>
                                            <div class="fw-bold text-white mb-1"><?= htmlspecialchars($_SESSION['nama']) ?></div>
                                            <div class="badge bg-warning text-dark">Admin</div>
                                        </div>
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider my-2"></li>
                                <li>
                                    <a class="dropdown-item py-2" href="admin/dashboard.php">
                                        <i class="bi bi-speedometer2 me-2 nav-icon"></i> Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="admin/kelola_buku.php">
                                        <i class="bi bi-book me-2 nav-icon"></i> Kelola Buku
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="admin/kelola_anggota.php">
                                        <i class="bi bi-people me-2 nav-icon"></i> Kelola Anggota
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-2"></li>
                                <li>
                                    <a class="dropdown-item py-2 text-danger" 
                                       href="logout.php"
                                       onclick="return confirm('Yakin ingin logout?')">
                                        <i class="bi bi-box-arrow-right me-2 nav-icon"></i> Keluar
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php else: ?>
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-login-custom" href="login.php">
                            <i class="bi bi-box-arrow-in-right me-2 nav-icon"></i> Login
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>