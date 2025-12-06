<?php
session_start();
require_once '../config/database.php';
if ($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}
$start_date = $_GET['start'] ?? date('Y-m-01');
$end_date   = $_GET['end'] ?? date('Y-m-d');
$query = "SELECT p.id, u.nama, b.judul, b.penulis, p.tanggal_pinjam, p.durasi_hari, p.status 
          FROM borrows p 
          JOIN users u ON p.user_id = u.id 
          JOIN books b ON p.book_id = b.id 
          WHERE p.tanggal_pinjam BETWEEN ? AND ?
          ORDER BY p.tanggal_pinjam DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$start_date, $end_date]);
$laporan = $stmt->fetchAll();
$total_transaksi = count($laporan);
$total_disetujui = count(array_filter($laporan, fn($r) => $r['status'] == 'Disetujui' || $r['status'] == 'Dikembalikan'));
$total_pending = count(array_filter($laporan, fn($r) => $r['status'] == 'Pending'));
$total_ditolak = count(array_filter($laporan, fn($r) => $r['status'] == 'Ditolak'));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Perpustakaan - Perpustakaan Yogakarta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style_admin.css">

    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            .card {
                box-shadow: none;
                border: 1px solid #ddd;
            }

            body {
                background: white;
            }

            .navbar {
                display: none;
            }

            thead {
                background: #333 !important;
                color: white !important;
            }
        }
    </style>
</head>

<body>
    <div class="no-print">
        <?php include '../includes/navbar_admin.php'; ?>
    </div>

    <div class="container my-5">

        <!-- Page Header -->
        <div class="page-header fade-in-up no-print">
            <h1>
                <i class="bi bi-file-earmark-bar-graph me-3"></i>
                Laporan Transaksi Peminjaman
            </h1>
            <p>Lihat dan cetak laporan transaksi perpustakaan</p>
        </div>

        <!-- Filter Section -->
        <div class="card mb-4 fade-in-up no-print">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-funnel me-2"></i>
                    Filter Laporan
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">
                            <i class="bi bi-calendar-event me-1"></i>
                            Dari Tanggal
                        </label>
                        <input type="date"
                            name="start"
                            class="form-control"
                            value="<?= $start_date ?>"
                            required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">
                            <i class="bi bi-calendar-event me-1"></i>
                            Sampai Tanggal
                        </label>
                        <input type="date"
                            name="end"
                            class="form-control"
                            value="<?= $end_date ?>"
                            required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-2"></i>
                            Tampilkan Laporan
                        </button>
                    </div>
                </form>

                <div class="mt-3">
                    <button onclick="window.print()" class="btn btn-success">
                        <i class="bi bi-printer me-2"></i>
                        Cetak / Download PDF
                    </button>
                    <button onclick="exportToExcel()" class="btn btn-secondary">
                        <i class="bi bi-file-earmark-excel me-2"></i>
                        Export ke Excel
                    </button>
                </div>
            </div>
        </div>

        <div class="row g-2 mb-4 fade-in-up">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-collection" style="font-size: 2.5rem; color: var(--slate-gray);"></i>
                        <h3 class="mt-2 mb-1"><?= $total_transaksi ?></h3>
                        <p class="text-muted mb-0 small">Total Transaksi</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-check-circle" style="font-size: 2.5rem; color: var(--success);"></i>
                        <h3 class="mt-2 mb-1"><?= $total_disetujui ?></h3>
                        <p class="text-muted mb-0 small">Disetujui</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-hourglass-split" style="font-size: 2.5rem; color: var(--warning);"></i>
                        <h3 class="mt-2 mb-1"><?= $total_pending ?></h3>
                        <p class="text-muted mb-0 small">Pending</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-x-circle" style="font-size: 2.5rem; color: var(--error);"></i>
                        <h3 class="mt-2 mb-1"><?= $total_ditolak ?></h3>
                        <p class="text-muted mb-0 small">Ditolak</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Header (Print Only) -->
        <div style="display: none;" class="text-center mb-4" id="printHeader">
            <h3>Perpustakaan Yogakarta</h3>
            <h5>Laporan Transaksi Peminjaman</h5>
            <p>Periode: <?= date('d M Y', strtotime($start_date)) ?> - <?= date('d M Y', strtotime($end_date)) ?></p>
            <hr>
        </div>

        <!-- Report Table -->
        <div class="card fade-in-up">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="bi bi-table me-2"></i>
                    Detail Transaksi
                    <span class="badge bg-secondary float-end">
                        <?= date('d M Y', strtotime($start_date)) ?> - <?= date('d M Y', strtotime($end_date)) ?>
                    </span>
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if (count($laporan) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0" id="reportTable">
                            <thead class="table-dark">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="20%">Peminjam</th>
                                    <th width="25%">Buku</th>
                                    <th width="15%">Tgl Pinjam</th>
                                    <th width="10%">Durasi</th>
                                    <th width="15%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                foreach ($laporan as $row):
                                ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($row['nama']) ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($row['judul']) ?></strong><br>
                                            <small class="text-muted"><?= htmlspecialchars($row['penulis']) ?></small>
                                        </td>
                                        <td><?= date('d M Y', strtotime($row['tanggal_pinjam'])) ?></td>
                                        <td><?= $row['durasi_hari'] ?> Hari</td>
                                        <td>
                                            <span class="status-badge status-<?= $row['status'] ?>">
                                                <?= $row['status'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 4rem; color: var(--gray-30);"></i>
                        <h5 class="mt-3 text-muted">Tidak ada data</h5>
                        <p class="text-muted">Tidak ada transaksi pada periode yang dipilih</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Print Footer -->
        <div style="display: none; margin-top: 50px;" id="printFooter">
            <div class="row">
                <div class="col-6">
                    <p>Dicetak pada: <?= date('d M Y H:i') ?> WIB</p>
                </div>
                <div class="col-6 text-end">
                    <p>Admin: <?= htmlspecialchars($_SESSION['nama']) ?></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Show print headers/footers when printing
        window.addEventListener('beforeprint', function() {
            document.getElementById('printHeader').style.display = 'block';
            document.getElementById('printFooter').style.display = 'block';
        });

        window.addEventListener('afterprint', function() {
            document.getElementById('printHeader').style.display = 'none';
            document.getElementById('printFooter').style.display = 'none';
        });

        // Export to Excel
        function exportToExcel() {
            const table = document.getElementById('reportTable');
            let html = table.outerHTML;
            const url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'Laporan_Perpustakaan_<?= date('Y-m-d') ?>.xls';
            link.click();
        }
    </script>
</body>

</html>