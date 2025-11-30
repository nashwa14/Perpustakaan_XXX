<nav class="navbar">
    <div class="container">
        <a href="dashboard.php" class="navbar-brand">
            <img src="../assets/uploads/logo.png" alt="Logo Perpustakaan" onerror="this.style.display='none'">
            <span>Perpustakaan Yogakarta</span>
        </a>
        
        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <i class="bi bi-list text-dark fs-2"></i>
        </button>

        <!-- Navigation Menu -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="nav-menu ms-auto">
                <a href="dashboard.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
                    Dashboard
                </a>
                <a href="kelola_buku.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'kelola_buku.php' || basename($_SERVER['PHP_SELF']) == 'edit_buku.php' ? 'active' : '' ?>">
                    Kelola Buku
                </a>
                <a href="kelola_berita.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'kelola_berita.php' ? 'active' : '' ?>">
                    Kelola Berita
                </a>
                <a href="kelola_anggota.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'kelola_anggota.php' ? 'active' : '' ?>">
                    Kelola Anggota
                </a>
                <a href="pengembalian.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'pengembalian.php' ? 'active' : '' ?>">
                    Pengembalian
                </a>
                <a href="laporan.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'laporan.php' ? 'active' : '' ?>">
                    Laporan
                </a>
                <span class="nav-divider"></span>
                <a href="../index.php" class="nav-link" target="_blank">
                    Lihat Website
                </a>
                <a href="../logout.php" class="nav-link nav-link-logout">
                    Logout
                </a>
            </div>
        </div>
    </div>
</nav>

<style>
/* Admin Navbar Styles */
.navbar {
    background: var(--tan) !important;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.navbar-brand span {
    color: var(--black-90);
    font-weight: 600;
}

.nav-link {
    color: var(--black-90) !important;
    font-weight: 500;
    padding: 0.5rem 1.25rem !important;
    border-radius: 0 !important;
    background: transparent !important;
    transition: all 0.3s ease;
}

.nav-link:hover {
    background: rgba(0,0,0,0.05) !important;
    color: var(--coffee) !important;
}

.nav-link.active {
    background: rgba(0,0,0,0.1) !important;
    color: var(--coffee) !important;
    font-weight: 600;
}

.nav-link-logout {
    color: var(--error) !important;
    font-weight: 600;
}

.nav-link-logout:hover {
    background: rgba(192, 57, 43, 0.1) !important;
}

.nav-divider {
    width: 1px;
    height: 30px;
    background: rgba(0,0,0,0.15);
    margin: 0 0.5rem;
}

.navbar-toggler {
    border: none;
    background: transparent;
    padding: 0.5rem;
}

.navbar-toggler:focus {
    box-shadow: none;
}

@media (max-width: 991px) {
    .navbar-collapse {
        margin-top: 1rem;
        background: rgba(255,255,255,0.95);
        padding: 1rem;
        border-radius: 10px;
    }
    
    .nav-menu {
        flex-direction: column;
        width: 100%;
        gap: 0.25rem;
    }
    
    .nav-link {
        width: 100%;
        text-align: left;
        padding: 0.75rem 1rem !important;
    }
    
    .nav-divider {
        width: 100%;
        height: 1px;
        margin: 0.5rem 0;
    }
}
</style>