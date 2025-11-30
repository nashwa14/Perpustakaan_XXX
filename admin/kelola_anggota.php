<?php
session_start();
require_once '../config/database.php';
if ($_SESSION['role'] != 'admin') header("Location: ../index.php");

if (isset($_GET['hapus'])) {
    $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$_GET['hapus']]);
    $success = "Anggota berhasil dihapus!";
}

$users = $pdo->query("SELECT * FROM users WHERE role='anggota' ORDER BY created_at DESC")->fetchAll();

// Get statistics
$total_anggota = count($users);
$anggota_baru = $pdo->query("SELECT COUNT(*) FROM users WHERE role='anggota' AND DATE(created_at) >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Anggota - Perpustakaan Yogakarta</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar_admin.php'; ?>
    
    <div class="container my-5">
        
        <!-- Page Header -->
        <div class="page-header fade-in-up">
            <h1>
                <i class="bi bi-people-fill me-3"></i>
                Kelola Anggota Perpustakaan
            </h1>
            <p>Kelola data anggota dan keanggotaan perpustakaan</p>
        </div>

        <?php if(isset($success)): ?>
        <div class="alert alert-success alert-dismissible fade show fade-in-up">
            <i class="bi bi-check-circle me-2"></i>
            <?= $success ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="row g-4 mb-4 fade-in-up">
            <div class="col-md-6">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="bi bi-people" style="font-size: 3rem; color: var(--success);"></i>
                        <h2 class="mt-3 mb-1"><?= $total_anggota ?></h2>
                        <p class="text-muted mb-0">Total Anggota Terdaftar</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="bi bi-person-plus" style="font-size: 3rem; color: var(--coffee);"></i>
                        <h2 class="mt-3 mb-1"><?= $anggota_baru ?></h2>
                        <p class="text-muted mb-0">Anggota Baru (30 Hari Terakhir)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Members List -->
        <div class="card fade-in-up">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul me-2"></i>
                        Daftar Anggota
                    </h5>
                    <div class="input-group" style="width: 300px;">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" 
                               class="form-control" 
                               id="searchInput"
                               placeholder="Cari nama atau username...">
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if(count($users) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="membersTable">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">ID</th>
                                <th width="25%">Nama Lengkap</th>
                                <th width="20%">Username</th>
                                <th width="20%">Terdaftar Sejak</th>
                                <th width="10%">Status</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach($users as $row): 
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <span class="badge bg-secondary">#<?= $row['id'] ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" 
                                             style="width: 40px; height: 40px; font-weight: bold;">
                                            <?= strtoupper(substr($row['nama'], 0, 1)) ?>
                                        </div>
                                        <strong><?= htmlspecialchars($row['nama']) ?></strong>
                                    </div>
                                </td>
                                <td>
                                    <i class="bi bi-at me-1"></i>
                                    <?= htmlspecialchars($row['username']) ?>
                                </td>
                                <td>
                                    <i class="bi bi-calendar-event me-1"></i>
                                    <?= date('d M Y', strtotime($row['created_at'])) ?>
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Aktif
                                    </span>
                                </td>
                                <td>
                                    <a href="?hapus=<?= $row['id'] ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Yakin ingin menghapus anggota <?= htmlspecialchars($row['nama']) ?>?\n\nPeringatan: Semua data peminjaman terkait akan hilang!')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 4rem; color: var(--gray-30);"></i>
                    <h5 class="mt-3 text-muted">Belum ada anggota</h5>
                    <p class="text-muted">Anggota baru akan muncul di sini setelah registrasi</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Info Box -->
        <div class="card mt-4 fade-in-up border-info">
            <div class="card-body">
                <h6 class="mb-3">
                    <i class="bi bi-info-circle-fill text-info me-2"></i>
                    Informasi
                </h6>
                <ul class="mb-0">
                    <li class="mb-2">Anggota baru dapat mendaftar melalui halaman registrasi di website</li>
                    <li class="mb-2">Penghapusan anggota bersifat permanen dan akan menghapus semua riwayat peminjaman</li>
                    <li>Pastikan tidak ada peminjaman aktif sebelum menghapus anggota</li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#membersTable tbody tr');
            
            tableRows.forEach(row => {
                const nama = row.cells[2].textContent.toLowerCase();
                const username = row.cells[3].textContent.toLowerCase();
                
                if (nama.includes(searchValue) || username.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>