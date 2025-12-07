<nav class="navbar navbar-expand-lg navbar-light bg-white main-navbar fixed-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="assets/uploads/logo.png"
                 alt="Logo Perpustakaan"
                 class="navbar-logo"
                 style="height: 50px"
                 onerror="this.style.display='none'">
            <span class="fw-semibold ms-2" style="color: #5d4037;">Perpustakaan Yogakarta</span>
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
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle p-0" 
                               href="#" 
                               id="profileDropdown"
                               role="button"
                               data-bs-toggle="dropdown"
                               aria-expanded="false">
                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['nama']) ?>&background=5d4037&color=fff&size=45&bold=true" 
                                     alt="Avatar" 
                                     class="rounded-circle"
                                     style="width: 45px; height: 45px; object-fit: cover; border: 2px solid #e0e0e0;">
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end profile-dropdown shadow-sm" aria-labelledby="profileDropdown">
                                <li class="dropdown-header">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['nama']) ?>&background=5d4037&color=fff&size=50&bold=true" 
                                             alt="Avatar" 
                                             class="rounded-circle"
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                        <div>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($_SESSION['nama']) ?></div>
                                            <small class="text-muted"><?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Anggota' ?></small>
                                        </div>
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="profil.php">
                                        Profil Saya
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" 
                                       href="logout.php"
                                       onclick="return confirm('Yakin ingin logout?')">
                                        Keluar
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php elseif ($_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link nav-admin-btn" href="admin/dashboard.php">
                                Panel Admin
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle p-0" 
                               href="#" 
                               id="profileDropdown"
                               role="button"
                               data-bs-toggle="dropdown"
                               aria-expanded="false">
                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['nama']) ?>&background=5d4037&color=fff&size=45&bold=true" 
                                     alt="Avatar" 
                                     class="rounded-circle"
                                     style="width: 45px; height: 45px; object-fit: cover; border: 2px solid #e0e0e0;">
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end profile-dropdown shadow-sm" aria-labelledby="profileDropdown">
                                <li class="dropdown-header">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['nama']) ?>&background=5d4037&color=fff&size=50&bold=true" 
                                             alt="Avatar" 
                                             class="rounded-circle"
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                        <div>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($_SESSION['nama']) ?></div>
                                            <small class="text-muted">Admin</small>
                                        </div>
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" 
                                       href="logout.php"
                                       onclick="return confirm('Yakin ingin logout?')">
                                        Keluar
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="btn btn-primary nav-login-btn ms-lg-2" href="login.php">
                            Login
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>