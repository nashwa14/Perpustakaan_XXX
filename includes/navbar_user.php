<nav class="navbar navbar-expand-lg main-navbar fixed-top">
    <div class="container">

        <!-- Brand + Logo -->
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="assets/uploads/logo.png"
                 alt="Logo Perpustakaan"
                 class="navbar-logo"
                 onerror="this.style.display='none'">
            <span class="fw-semibold ms-2">Perpustakaan Yogakarta</span>
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarUser">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse align-items-center" id="navbarUser">
            <ul class="navbar-nav ms-auto gap-2 align-items-center">
                <!-- menu Kamu -->
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
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'profil.php' ? 'active' : '' ?>" 
                               href="profil.php"
                               title="Profil Saya">
                                <i class="bi bi-person-circle" style="font-size: 1.5rem;"></i>
                            </a>
                        </li>
                    <?php elseif ($_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link nav-admin-btn" href="admin/dashboard.php">
                                <i class="bi bi-speedometer2 me-2"></i>
                                Panel Admin
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a class="nav-link nav-logout" 
                           href="logout.php"
                           onclick="return confirm('Yakin ingin logout?')">
                            <i class="bi bi-box-arrow-right me-2"></i>
                            Logout
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="btn btn-primary nav-login-btn ms-lg-2" href="login.php">
                            <i class="bi bi-box-arrow-in-right me-1"></i>
                            Login
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>