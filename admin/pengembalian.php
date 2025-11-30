<?php
session_start();
require_once '../config/database.php';
if ($_SESSION['role'] != 'admin') header("Location: ../index.php");

if (isset($_GET['kembali'])) {
    $id_pinjam = $_GET['kembali'];
    $id_buku   = $_GET['book_id'];

    $pdo->prepare("UPDATE borrows SET status='Dikembalikan' WHERE id=?")->execute([$id_pinjam]);
    $pdo->prepare("UPDATE books SET stok = stok + 1 WHERE id=?")->execute([$id_buku]);
    
    $success = "Buku berhasil dikembalikan!";
}

$query = "SELECT p.id, p.book_id, u.nama, b.judul, b.penulis, p.tanggal_pinjam, p.durasi_hari,
          DATEDIFF(CURDATE(), p.tanggal_pinjam) as hari_pinjam
          FROM borrows p 
          JOIN users u ON p.user_id = u.id 
          JOIN books b ON p.book_id = b.id 
          WHERE p.status = 'Disetujui'
          ORDER BY p.tanggal_pinjam ASC";
$list = $pdo->query($query)->fetchAll();

$total_dipinjam = count($list);
$terlambat = count(array_filter($list, fn($item) => $item['hari_pinjam'] > $item['durasi_hari']));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengembalian Buku - Perpustakaan Yogakarta</title>
    
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
                <i class="bi bi-arrow-return-left me-3"></i>
                Pengembalian Buku
            </h1>
            <p>Kelola proses pengembalian buku yang sedang dipinjam</p>
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
                        <i class="bi bi-book" style="font-size: 3rem; color: var(--coffee);"></i>
                        <h2 class="mt-3 mb-1"><?= $total_dipinjam ?></h2>
                        <p class="text-muted mb-0">Buku Sedang Dipinjam</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="bi bi-exclamation-triangle" style="font-size: 3rem; color: var(--error);"></i>
                        <h2 class="mt-3 mb-1"><?= $terlambat ?></h2>
                        <p class="text-muted mb-0">Peminjaman Terlambat</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Borrows List -->
        <div class="card fade-in-up">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="bi bi-clock-history me-2"></i>
                    Transaksi Peminjaman Aktif
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if(count($list) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="20%">Peminjam</th>
                                <th width="25%">Buku</th>
                                <th width="12%">Tgl Pinjam</th>
                                <th width="10%">Durasi</th>
                                <th width="10%">Hari Ke-</th>
                                <th width="10%">Status</th>
                                <th width="8%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach($list as $row): 
                            $is_late = $row['hari_pinjam'] > $row['durasi_hari'];
                            $sisa_hari = $row['durasi_hari'] - $row['hari_pinjam'];
                            ?>
                            <tr class="<?= $is_late ? 'table-danger' : '' ?>">
                                <td><?= $no++ ?></td>
                                <td>
                                    <i class="bi bi-person-circle me-2"></i>
                                    <strong><?= htmlspecialchars($row['nama']) ?></strong>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($row['judul']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($row['penulis']) ?></small>
                                </td>
                                <td>
                                    <small>
                                        <i class="bi bi-calendar-event me-1"></i>
                                        <?= date('d M Y', strtotime($row['tanggal_pinjam'])) ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?= $row['durasi_hari'] ?> hari
                                    </span>
                                </td>
                                <td>
                                    <strong class="<?= $is_late ? 'text-danger' : '' ?>">
                                        Hari ke-<?= $row['hari_pinjam'] ?>
                                    </strong>
                                </td>
                                <td>
                                    <?php if($is_late): ?>
                                        <span class="badge bg-danger">
                                            <i class="bi bi-exclamation-triangle me-1"></i>
                                            Terlambat <?= abs($sisa_hari) ?> hari
                                        </span>
                                    <?php elseif($sisa_hari <= 2): ?>
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-clock me-1"></i>
                                            Segera habis
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Normal
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?kembali=<?= $row['id'] ?>&book_id=<?= $row['book_id'] ?>" 
                                       class="btn btn-sm btn-primary"
                                       onclick="return confirm('Konfirmasi pengembalian buku:\n\n<?= htmlspecialchars($row['judul']) ?>\nPeminjam: <?= htmlspecialchars($row['nama']) ?>')">
                                        <i class="bi bi-check-lg"></i>
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
                    <h5 class="mt-3 text-muted">Tidak ada peminjaman aktif</h5>
                    <p class="text-muted">Semua buku sudah dikembalikan</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Info Box -->
        <div class="row g-3 mt-4">
            <div class="col-md-4">
                <div class="card border-success fade-in-up">
                    <div class="card-body">
                        <h6 class="text-success mb-3">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            Status Normal
                        </h6>
                        <p class="mb-0 small">Peminjaman dalam batas waktu yang ditentukan</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-warning fade-in-up">
                    <div class="card-body">
                        <h6 class="text-warning mb-3">
                            <i class="bi bi-clock-fill me-2"></i>
                            Segera Habis
                        </h6>
                        <p class="mb-0 small">Peminjaman akan habis dalam 1-2 hari</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-danger fade-in-up">
                    <div class="card-body">
                        <h6 class="text-danger mb-3">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            Terlambat
                        </h6>
                        <p class="mb-0 small">Peminjaman melewati batas waktu yang ditentukan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>