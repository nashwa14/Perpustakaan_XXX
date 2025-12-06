<?php
session_start();
require_once 'config/database.php';
if (!isset($_SESSION['user_id'])) header("Location: login.php");

$user_id = $_SESSION['user_id'];
$query = "SELECT b.judul, p.tanggal_pinjam, p.durasi_hari, p.status 
          FROM borrows p 
          JOIN books b ON p.book_id = b.id 
          WHERE p.user_id = ? ORDER BY p.id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$riwayat = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman - Perpustakaan Yogakarta</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar_user.php'; ?>
    
    <div class="container my-5">
        
        <!-- Page Header -->
        <div class="page-header fade-in-up">
            <h1>
                <i class="bi bi-clock-history me-3"></i>
                Riwayat Peminjaman
            </h1>
            <p>Lihat status dan histori peminjaman buku Anda</p>
        </div>

        <?php if(count($riwayat) > 0): ?>
        <div class="table-container fade-in-up">
            <table class="table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="35%">Judul Buku</th>
                        <th width="15%">Tanggal Pinjam</th>
                        <th width="15%">Durasi</th>
                        <th width="15%">Status</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    foreach ($riwayat as $row): 
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <strong><?= htmlspecialchars($row['judul']) ?></strong>
                        </td>
                        <td>
                            <i class="bi bi-calendar-event me-1"></i>
                            <?= date('d M Y', strtotime($row['tanggal_pinjam'])) ?>
                        </td>
                        <td>
                            <i class="bi bi-clock me-1"></i>
                            <?= $row['durasi_hari'] ?> Hari
                        </td>
                        <td>
                            <span class="status-badge status-<?= $row['status'] ?>">
                                <?php
                                $icons = [
                                    'Pending' => 'hourglass-split',
                                    'Disetujui' => 'check-circle',
                                    'Ditolak' => 'x-circle',
                                    'Dikembalikan' => 'arrow-return-left'
                                ];
                                ?>
                                <i class="bi bi-<?= $icons[$row['status']] ?> me-1"></i>
                                <?= $row['status'] ?>
                            </span>
                        </td>
                        <td>
                            <?php if($row['status'] == 'Disetujui'): ?>
                                <button class="btn btn-sm btn-outline-secondary" disabled>
                                    <i class="bi bi-book me-1"></i>
                                    Sedang Dipinjam
                                </button>
                            <?php elseif($row['status'] == 'Dikembalikan'): ?>
                                <button class="btn btn-sm btn-outline-success" disabled>
                                    <i class="bi bi-check-circle me-1"></i>
                                    Selesai
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Statistics -->
        <div class="row g-4 mt-4">
            <?php
            $total = count($riwayat);
            $pending = count(array_filter($riwayat, fn($r) => $r['status'] == 'Pending'));
            $disetujui = count(array_filter($riwayat, fn($r) => $r['status'] == 'Disetujui'));
            $dikembalikan = count(array_filter($riwayat, fn($r) => $r['status'] == 'Dikembalikan'));
            ?>

        <?php else: ?>
        <div class="empty-state fade-in-up">
            <div class="empty-state-icon">
                <i class="bi bi-inbox"></i>
            </div>
            <h3>Belum Ada Riwayat</h3>
            <p class="text-muted">Anda belum pernah melakukan peminjaman buku.</p>
            <a href="index.php" class="btn btn-primary mt-3">
                <i class="bi bi-book me-2"></i>
                Jelajahi Katalog
            </a>
        </div>
        <?php endif; ?>
    </div>
    
    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>