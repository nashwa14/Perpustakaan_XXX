<?php
session_start();
require_once '../config/database.php';
if ($_SESSION['role'] != 'admin') header("Location: ../index.php");
if (isset($_GET['act']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $status = $_GET['act'] == 'approve' ? 'Disetujui' : 'Ditolak';
    $pdo->prepare("UPDATE borrows SET status = ? WHERE id = ?")->execute([$status, $id]);

    if ($status == 'Disetujui') {
        $stmt = $pdo->prepare("SELECT book_id FROM borrows WHERE id = ?");
        $stmt->execute([$id]);
        $book_id = $stmt->fetchColumn();
        $pdo->prepare("UPDATE books SET stok = stok - 1 WHERE id = ?")->execute([$book_id]);
    }
    header("Location: dashboard.php");
}
$pendings = $pdo->query("SELECT p.id, u.nama, b.judul, p.durasi_hari, p.tanggal_pinjam FROM borrows p JOIN users u ON p.user_id = u.id JOIN books b ON p.book_id = b.id WHERE p.status = 'Pending' ORDER BY p.tanggal_pinjam DESC")->fetchAll();
$total_buku = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
$total_anggota = $pdo->query("SELECT COUNT(*) FROM users WHERE role='anggota'")->fetchColumn();
$total_pinjam = $pdo->query("SELECT COUNT(*) FROM borrows WHERE status='Disetujui'")->fetchColumn();
$total_pending = $pdo->query("SELECT COUNT(*) FROM borrows WHERE status='Pending'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Perpustakaan Yogakarta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style_admin.css">
</head>

<body>
    <?php include '../includes/navbar_admin.php'; ?>
    <div class="container my-3">
        <div class="page-header fade-in-up">
            <h1>
                <i class="bi bi-speedometer2 me-3"></i>
                Dashboard Admin
            </h1>
            <p>Selamat datang, <?= htmlspecialchars($_SESSION['nama']) ?>! Silahkan Kelola Perpustakaan Yogakarta.</p>
        </div>

        <div class="row g-2 mb-5 fade-in-up">
            <div class="col-md-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-book-fill" style="font-size: 3rem; color: var(--slate-gray);"></i>
                        </div>
                        <h3 class="mb-1"><?= $total_buku ?></h3>
                        <p class="text-muted mb-0">Total Buku</p>
                        <a href="kelola_buku.php" class="btn btn-sm btn-outline-secondary mt-2">
                            <i class="bi bi-arrow-right me-1"></i>Kelola
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-people-fill" style="font-size: 3rem; color: var(--space-cadet);"></i>
                        </div>
                        <h3 class="mb-1"><?= $total_anggota ?></h3>
                        <p class="text-muted mb-0">Total Anggota</p>
                        <a href="kelola_anggota.php" class="btn btn-sm btn-outline-secondary mt-2">
                            <i class="bi bi-arrow-right me-1"></i>Kelola
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-arrow-left-right" style="font-size: 3rem; color: var(--coffee);"></i>
                        </div>
                        <h3 class="mb-1"><?= $total_pinjam ?></h3>
                        <p class="text-muted mb-0">Sedang Dipinjam</p>
                        <a href="pengembalian.php" class="btn btn-sm btn-outline-secondary mt-2">
                            <i class="bi bi-arrow-right me-1"></i>Lihat
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-hourglass-split" style="font-size: 3rem; color: var(--warning);"></i>
                        </div>
                        <h3 class="mb-1"><?= $total_pending ?></h3>
                        <p class="text-muted mb-0">Menunggu Approval</p>
                        <?php if ($total_pending > 0): ?>
                            <span class="badge bg-warning mt-2">Perlu Tindakan</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card fade-in-up">
            <div class="card-header bg-white py-3">
                <h4 class="mb-0">
                    <i class="bi bi-bell-fill me-2 text-warning"></i>
                    Permintaan Peminjaman Baru
                </h4>
            </div>
            <div class="card-body p-0">
                <?php if (count($pendings) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="25%">Nama Peminjam</th>
                                    <th width="30%">Buku</th>
                                    <th width="15%">Tanggal</th>
                                    <th width="10%">Durasi</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                foreach ($pendings as $row):
                                ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <i class="bi bi-person-circle me-2"></i>
                                            <strong><?= htmlspecialchars($row['nama']) ?></strong>
                                        </td>
                                        <td><?= htmlspecialchars($row['judul']) ?></td>
                                        <td>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar-event me-1"></i>
                                                <?= date('d M Y', strtotime($row['tanggal_pinjam'])) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge" style="background-color: var(--slate-gray);">
                                                <?= $row['durasi_hari'] ?> Hari
                                            </span>

                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-start gap-2">
                                                <a href="?act=approve&id=<?= $row['id'] ?>"
                                                    class="btn btn-sm btn-approve"
                                                    onclick="return confirm('Setujui peminjaman ini?')">
                                                    <i class="bi bi-check-circle"></i>
                                                </a>
                                                <a href="?act=reject&id=<?= $row['id'] ?>"
                                                    class="btn btn-sm btn-reject"
                                                    onclick="return confirm('Tolak peminjaman ini?')">
                                                    <i class="bi bi-x-circle"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 4rem; color: var(--gray-30);"></i>
                        <h5 class="mt-3 text-muted">Tidak ada permintaan pending</h5>
                        <p class="text-muted">Semua permintaan sudah diproses</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="row g- mt-4 fade-in-up">
            <div class="col-md-12">
                <h5 class="mb-3">
                    <i class="bi bi-lightning-fill me-2" style="color: var(--coffee);"></i>
                    Aksi Cepat
                </h5>
            </div>
            <div class="col-md-3">
                <a href="kelola_buku.php" class="btn btn-outline-primary w-100 py-3" style="border-color: var(--coffee); color: var(--coffee);">
                    <i class="bi bi-plus-circle fs-4 d-block mb-2" style="color: var(--coffee);"></i>
                    Tambah Buku Baru
                </a>
            </div>
            <div class="col-md-3">
                <a href="kelola_berita.php" class="btn btn-outline-primary w-100 py-3" style="border-color: var(--space-cadet); color: var(--space-cadet);">
                    <i class="bi bi-newspaper fs-4 d-block mb-2" style="color: var(--space-cadet);"></i>
                    Publikasi Berita
                </a>
            </div>
            <div class="col-md-3">
                <a href="pengembalian.php" class="btn btn-outline-secondary w-100 py-3" style="border-color: var(--slate-gray); color: var(--slate-gray);">
                    <i class="bi bi-arrow-return-left fs-4 d-block mb-2" style="color: var(--slate-gray);"></i>
                    Proses Pengembalian
                </a>
            </div>
            <div class="col-md-3">
                <a href="laporan.php" class="btn btn-outline-secondary w-100 py-3" style="border-color: var(--tan); color: var(--tan);">
                    <i class="bi bi-file-earmark-bar-graph fs-4 d-block mb-2" style="color: var(--tan);"></i>
                    Lihat Laporan
                </a>
            </div>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>