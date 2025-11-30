<nav class="navbar navbar-expand-lg main-navbar">
    <div class="container">

        <!-- Brand + Logo -->
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="assets/uploads/logo.png"
                 alt="Logo Perpustakaan"
                 class="navbar-logo me-2">
            <span class="fw-semibold">Perpustakaan Yogakarta</span>
        </a>

        <!-- Toggler mobile -->
        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarUser"
                aria-controls="navbarUser"
                aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu -->
        <div class="collapse navbar-collapse" id="navbarUser">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Katalog</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="berita.php">Berita &amp; Agenda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">Tentang Kami</a>
                </li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] == 'anggota'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="riwayat.php">Riwayat Pinjam</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profil.php">Profil Saya</a>
                        </li>
                    <?php elseif ($_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link nav-admin-btn" href="admin/dashboard.php">
                                Panel Admin
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a class="nav-link nav-logout" href="logout.php">Logout</a>
                    </li>
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
