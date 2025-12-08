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
        /* Hover effect untuk tombol */
        .btn-report-preview:hover {
            background: #5a3d2b !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(111, 77, 56, 0.3);
        }
        
        .btn-print-dropdown:hover {
            background: #c9a876 !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(213, 184, 147, 0.4);
        }
        
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            body {
                background: white !important;
                padding: 20px;
            }
            
            .no-print {
                display: none !important;
            }
            
            .book-table-card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
                margin-top: 0 !important;
            }
            
            .book-table {
                font-size: 11px !important;
            }
            
            .book-table th {
                background: #6F4D38 !important;
                color: white !important;
                border: 1px solid #5a3d2b !important;
            }
            
            .book-table td {
                border: 1px solid #ddd !important;
                padding: 6px 8px !important;
            }
            
            .category-badge, .date-badge {
                border: 1px solid currentColor !important;
            }
            
            @page {
                margin: 15mm;
                size: A4 landscape;
            }
        }
    </style>
</head>

<body>
    <div class="no-print">
        <?php include '../includes/navbar_admin.php'; ?>
    </div>

    <div class="container my-4">
        <!-- Page Header -->
        <div class="no-print">
            <div class="welcome-header fade-in-up">
                <h1 class="welcome-title">
                    Laporan Transaksi Peminjaman
                </h1>
                <p class="welcome-subtitle">Laporan data transaksi peminjaman buku perpustakaan</p>
            </div>

            <!-- Stats Cards -->
            <div class="stats-cards-container fade-in-up" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 1.5rem;">
                <div class="metric-card">
                    <div class="metric-header">
                        <span class="metric-label">Total Transaksi</span>
                    </div>
                    <h2 class="metric-value"><?= number_format($total_transaksi) ?></h2>
                    <span class="metric-change metric-change-info">
                        Periode dipilih
                    </span>
                </div>

                <div class="metric-card">
                    <div class="metric-header">
                        <span class="metric-label">Disetujui</span>
                    </div>
                    <h2 class="metric-value"><?= number_format($total_disetujui) ?></h2>
                    <span class="metric-change metric-change-success">
                        Peminjaman berhasil
                    </span>
                </div>

                <div class="metric-card">
                    <div class="metric-header">
                        <span class="metric-label">Pending</span>
                    </div>
                    <h2 class="metric-value"><?= number_format($total_pending) ?></h2>
                    <span class="metric-change" style="color: #f57c00;">
                        Menunggu approval
                    </span>
                </div>

                <div class="metric-card">
                    <div class="metric-header">
                        <span class="metric-label">Ditolak</span>
                    </div>
                    <h2 class="metric-value"><?= number_format($total_ditolak) ?></h2>
                    <span class="metric-change metric-change-negative">
                        Tidak disetujui
                    </span>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="book-control-card fade-in-up" style="margin-bottom: 1.5rem;">
                <div style="display: flex; align-items: flex-end; gap: 1rem; flex-wrap: wrap; width: 100%;">
                    <div style="flex: 1; min-width: 220px;">
                        <label class="form-label" style="font-weight: 500; color: #424242; margin-bottom: 0.5rem; display: flex; align-items: center; font-size: 0.9rem;">
                            <i class="bi bi-calendar-event me-1"></i>
                            Dari Tanggal
                        </label>
                        <input type="date" id="startDate" class="form-control" value="<?= $start_date ?>" onchange="loadReportData()" style="border: 1.5px solid #e0e0e0; border-radius: 8px; padding: 10px 14px; height: 44px;">
                    </div>
                    <div style="flex: 1; min-width: 220px;">
                        <label class="form-label" style="font-weight: 500; color: #424242; margin-bottom: 0.5rem; display: flex; align-items: center; font-size: 0.9rem;">
                            <i class="bi bi-calendar-event me-1"></i>
                            Sampai Tanggal
                        </label>
                        <input type="date" id="endDate" class="form-control" value="<?= $end_date ?>" onchange="loadReportData()" style="border: 1.5px solid #e0e0e0; border-radius: 8px; padding: 10px 14px; height: 44px;">
                    </div>
                    <div style="display: flex; gap: 0.75rem;">
                        <button type="button" onclick="showReportPreview()" class="btn btn-report-preview" style="background: #6F4D38; color: white; border-radius: 8px; padding: 10px 20px; white-space: nowrap; height: 44px; display: flex; align-items: center; transition: all 0.3s ease;">
                            <i class="bi bi-file-earmark-text me-2"></i>
                            Tampilkan Laporan
                        </button>
                        <div class="btn-group" role="group" style="height: 44px;">
                            <button type="button" class="btn dropdown-toggle btn-print-dropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background: #D5B893; color: white; border-radius: 8px; padding: 10px 20px; white-space: nowrap; height: 44px; display: flex; align-items: center; gap: 0.5rem; border: none; transition: all 0.3s ease;">
                                <i class="bi bi-printer"></i>
                                Cetak
                            </button>
                            <ul class="dropdown-menu" style="border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border: 1px solid #e0e0e0; min-width: 180px;">
                                <li>
                                    <a class="dropdown-item" href="#" onclick="printToPDF(); return false;" style="padding: 10px 16px; display: flex; align-items: center; gap: 0.75rem;">
                                        <i class="bi bi-file-pdf" style="color: #d32f2f; font-size: 18px;"></i>
                                        <div>
                                            <div style="font-weight: 500; color: #212121;">Cetak PDF</div>
                                            <div style="font-size: 0.75rem; color: #757575;">Format dokumen PDF</div>
                                        </div>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider" style="margin: 0.25rem 0;"></li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="exportToExcel(); return false;" style="padding: 10px 16px; display: flex; align-items: center; gap: 0.75rem;">
                                        <i class="bi bi-file-earmark-excel" style="color: #217346; font-size: 18px;"></i>
                                        <div>
                                            <div style="font-weight: 500; color: #212121;">Export Excel</div>
                                            <div style="font-size: 0.75rem; color: #757575;">Format spreadsheet XLS</div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Period Info Banner -->
            <div class="fade-in-up" style="background: linear-gradient(135deg, #6F4D38 0%, #8B6347 100%); border-radius: 12px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 2px 8px rgba(111, 77, 56, 0.15);">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="background: rgba(255,255,255,0.2); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-calendar-range" style="color: white; font-size: 24px;"></i>
                        </div>
                        <div>
                            <div style="color: rgba(255,255,255,0.9); font-size: 0.875rem; margin-bottom: 0.25rem;">Periode Laporan</div>
                            <div style="color: white; font-size: 1.125rem; font-weight: 600;" id="periodInfo">
                                <?= date('d F Y', strtotime($start_date)) ?> - <?= date('d F Y', strtotime($end_date)) ?>
                            </div>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="color: rgba(255,255,255,0.9); font-size: 0.875rem; margin-bottom: 0.25rem;">Total Data</div>
                        <div style="color: white; font-size: 1.5rem; font-weight: bold;" id="totalDataInfo"><?= $total_transaksi ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Print Header -->
        <div style="display: none;" class="text-center mb-4" id="printHeader">
            <div style="text-align: center; margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 3px solid #6F4D38;">
                <h2 style="color: #6F4D38; font-weight: bold; margin: 0; font-size: 24px;">
                    PERPUSTAKAAN DIGITAL
                </h2>
                <h3 style="color: #424242; font-weight: 600; margin: 0.5rem 0 0 0; font-size: 20px;">
                    LAPORAN TRANSAKSI PEMINJAMAN BUKU
                </h3>
                <p style="margin: 0.5rem 0 0 0; color: #666; font-size: 14px;">
                    Periode: <strong><?= date('d F Y', strtotime($start_date)) ?></strong> s/d <strong><?= date('d F Y', strtotime($end_date)) ?></strong>
                </p>
                <p style="margin: 0.25rem 0 0 0; color: #666; font-size: 13px;">
                    Tanggal Cetak: <?= date('d F Y, H:i') ?> WIB
                </p>
            </div>
            
            <!-- Summary Stats for Print -->
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; text-align: center; margin-bottom: 1.5rem;">
                <div style="border: 2px solid #6F4D38; padding: 0.75rem; border-radius: 8px;">
                    <div style="font-size: 11px; color: #666; margin-bottom: 0.25rem;">Total Transaksi</div>
                    <div style="font-size: 20px; font-weight: bold; color: #6F4D38;"><?= $total_transaksi ?></div>
                </div>
                <div style="border: 2px solid #4caf50; padding: 0.75rem; border-radius: 8px;">
                    <div style="font-size: 11px; color: #666; margin-bottom: 0.25rem;">Disetujui</div>
                    <div style="font-size: 20px; font-weight: bold; color: #4caf50;"><?= $total_disetujui ?></div>
                </div>
                <div style="border: 2px solid #ff9800; padding: 0.75rem; border-radius: 8px;">
                    <div style="font-size: 11px; color: #666; margin-bottom: 0.25rem;">Pending</div>
                    <div style="font-size: 20px; font-weight: bold; color: #ff9800;"><?= $total_pending ?></div>
                </div>
                <div style="border: 2px solid #f44336; padding: 0.75rem; border-radius: 8px;">
                    <div style="font-size: 11px; color: #666; margin-bottom: 0.25rem;">Ditolak</div>
                    <div style="font-size: 20px; font-weight: bold; color: #f44336;"><?= $total_ditolak ?></div>
                </div>
            </div>
        </div>

        <!-- Report Table -->
        <div class="book-table-card fade-in-up">
            <div class="table-responsive">
                <?php if (count($laporan) > 0): ?>
                    <table class="table book-table mb-0" id="reportTable">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="20%">Peminjam</th>
                                <th width="25%">Judul Buku</th>
                                <th width="15%">Penulis</th>
                                <th width="12%">Tgl Pinjam</th>
                                <th width="10%">Durasi</th>
                                <th width="13%">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            foreach ($laporan as $row):
                                $status_color = [
                                    'Pending' => ['bg' => '#fff8e1', 'text' => '#f57c00'],
                                    'Disetujui' => ['bg' => '#e8f5e9', 'text' => '#2e7d32'],
                                    'Dikembalikan' => ['bg' => '#e3f2fd', 'text' => '#1976d2'],
                                    'Ditolak' => ['bg' => '#ffebee', 'text' => '#c62828']
                                ];
                                $color = $status_color[$row['status']] ?? ['bg' => '#f5f5f5', 'text' => '#616161'];
                            ?>
                                <tr>
                                    <td><?= $no++ ?>.</td>
                                    <td class="book-title-cell"><?= htmlspecialchars($row['nama']) ?></td>
                                    <td class="book-title-cell"><?= htmlspecialchars($row['judul']) ?></td>
                                    <td class="book-author-cell"><?= htmlspecialchars($row['penulis']) ?></td>
                                    <td>
                                        <span class="date-badge">
                                            <?= date('d M Y', strtotime($row['tanggal_pinjam'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span style="background: #f5f5f5; padding: 4px 10px; border-radius: 12px; font-size: 0.8125rem; color: #616161; white-space: nowrap;">
                                            <?= $row['durasi_hari'] ?> hari
                                        </span>
                                    </td>
                                    <td>
                                        <span class="category-badge" style="background: <?= $color['bg'] ?>; color: <?= $color['text'] ?>;">
                                            <?= $row['status'] ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="bi bi-inbox"></i>
                        </div>
                        <h5 class="empty-title">Tidak ada data</h5>
                        <p class="empty-subtitle">Tidak ada transaksi pada periode yang dipilih</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Print Footer -->
        <div style="display: none; margin-top: 3rem; padding-top: 1rem; border-top: 2px solid #e0e0e0;" id="printFooter">
            <div style="display: flex; justify-content: space-between; font-size: 12px; color: #666;">
                <div>
                    <strong>Dicetak oleh:</strong> <?= htmlspecialchars($_SESSION['nama']) ?> (Admin)
                </div>
                <div>
                    <strong>Halaman:</strong> 1 dari 1
                </div>
            </div>
            <div style="text-align: center; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e0e0e0;">
                <p style="margin: 0; font-size: 11px; color: #999;">
                    © <?= date('Y') ?> Perpustakaan Digital - Dokumen ini dicetak secara otomatis
                </p>
            </div>
        </div>
    </div>

    <!-- Modal Preview Laporan -->
    <div class="modal fade" id="reportPreviewModal" tabindex="-1" aria-labelledby="reportPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content" style="background: #f5f5f5;">
                <div class="modal-header" style="background: #6F4D38; color: white; border: none;">
                    <h5 class="modal-title" id="reportPreviewModalLabel">
                        <i class="bi bi-file-earmark-bar-graph me-2"></i>
                        Preview Laporan Transaksi Peminjaman
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding: 2rem;">
                    <!-- Preview Content -->
                    <div id="previewContent" style="background: white; max-width: 1200px; margin: 0 auto; padding: 3rem; box-shadow: 0 4px 20px rgba(0,0,0,0.1); border-radius: 8px;">
                        <!-- Header akan diisi oleh JavaScript -->
                    </div>
                </div>
                <div class="modal-footer" style="background: white; border-top: 2px solid #e0e0e0; padding: 1.25rem 2rem;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="padding: 10px 24px; border-radius: 8px;">
                        <i class="bi bi-arrow-left me-2"></i>
                        Kembali ke Menu
                    </button>
                    <button type="button" onclick="printReport()" class="btn" style="background: #4caf50; color: white; padding: 10px 24px; border-radius: 8px;">
                        <i class="bi bi-printer me-2"></i>
                        Cetak Laporan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Auto-load data saat tanggal berubah
        function loadReportData() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            
            if (startDate && endDate) {
                window.location.href = `laporan.php?start=${startDate}&end=${endDate}`;
            }
        }

        // Tampilkan preview laporan
        function showReportPreview() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            
            // Format tanggal Indonesia
            const startFormatted = formatDateIndo(startDate);
            const endFormatted = formatDateIndo(endDate);
            
            // Get data from page
            const table = document.getElementById('reportTable');
            const totalTransaksi = '<?= $total_transaksi ?>';
            const totalDisetujui = '<?= $total_disetujui ?>';
            const totalPending = '<?= $total_pending ?>';
            const totalDitolak = '<?= $total_ditolak ?>';
            
            // Build preview HTML
            const previewHTML = `
                <div style="text-align: center; margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 3px solid #6F4D38;">
                    <h2 style="color: #6F4D38; font-weight: bold; margin: 0; font-size: 28px;">
                        PERPUSTAKAAN DIGITAL
                    </h2>
                    <h3 style="color: #424242; font-weight: 600; margin: 0.5rem 0 0 0; font-size: 22px;">
                        LAPORAN TRANSAKSI PEMINJAMAN BUKU
                    </h3>
                    <p style="margin: 0.5rem 0 0 0; color: #666; font-size: 15px;">
                        Periode: <strong>${startFormatted}</strong> s/d <strong>${endFormatted}</strong>
                    </p>
                    <p style="margin: 0.25rem 0 0 0; color: #666; font-size: 14px;">
                        Tanggal Cetak: ${getCurrentDateTimeIndo()}
                    </p>
                </div>
                
                <!-- Summary Stats -->
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 2rem;">
                    <div style="border: 2px solid #6F4D38; padding: 1rem; border-radius: 8px; text-align: center;">
                        <div style="font-size: 0.875rem; color: #666; margin-bottom: 0.5rem;">Total Transaksi</div>
                        <div style="font-size: 28px; font-weight: bold; color: #6F4D38;">${totalTransaksi}</div>
                    </div>
                    <div style="border: 2px solid #4caf50; padding: 1rem; border-radius: 8px; text-align: center;">
                        <div style="font-size: 0.875rem; color: #666; margin-bottom: 0.5rem;">Disetujui</div>
                        <div style="font-size: 28px; font-weight: bold; color: #4caf50;">${totalDisetujui}</div>
                    </div>
                    <div style="border: 2px solid #ff9800; padding: 1rem; border-radius: 8px; text-align: center;">
                        <div style="font-size: 0.875rem; color: #666; margin-bottom: 0.5rem;">Pending</div>
                        <div style="font-size: 28px; font-weight: bold; color: #ff9800;">${totalPending}</div>
                    </div>
                    <div style="border: 2px solid #f44336; padding: 1rem; border-radius: 8px; text-align: center;">
                        <div style="font-size: 0.875rem; color: #666; margin-bottom: 0.5rem;">Ditolak</div>
                        <div style="font-size: 28px; font-weight: bold; color: #f44336;">${totalDitolak}</div>
                    </div>
                </div>
                
                <!-- Table -->
                ${table ? table.outerHTML : '<p style="text-align: center; color: #999;">Tidak ada data</p>'}
                
                <!-- Footer -->
                <div style="margin-top: 3rem; padding-top: 1rem; border-top: 2px solid #e0e0e0;">
                    <div style="display: flex; justify-content: space-between; font-size: 12px; color: #666;">
                        <div><strong>Dicetak oleh:</strong> <?= htmlspecialchars($_SESSION['nama']) ?> (Admin)</div>
                        <div><strong>Halaman:</strong> 1 dari 1</div>
                    </div>
                    <div style="text-align: center; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e0e0e0;">
                        <p style="margin: 0; font-size: 11px; color: #999;">
                            © ${new Date().getFullYear()} Perpustakaan Digital - Dokumen ini dicetak secara otomatis
                        </p>
                    </div>
                </div>
            `;
            
            document.getElementById('previewContent').innerHTML = previewHTML;
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('reportPreviewModal'));
            modal.show();
        }

        // Print dari modal preview
        function printReport() {
            const printContents = document.getElementById('previewContent').innerHTML;
            const originalContents = document.body.innerHTML;
            
            document.body.innerHTML = `
                <html>
                <head>
                    <title>Laporan Transaksi Peminjaman</title>
                    <style>
                        @media print {
                            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
                            body { padding: 20px; font-family: Arial, sans-serif; }
                            table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
                            th { background: #6F4D38 !important; color: white !important; padding: 8px; border: 1px solid #5a3d2b; }
                            td { padding: 8px; border: 1px solid #ddd; }
                            @page { margin: 15mm; size: A4 landscape; }
                        }
                    </style>
                </head>
                <body>${printContents}</body>
                </html>
            `;
            
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload();
        }

        // Cetak ke PDF
        function printToPDF() {
            showReportPreview();
            // Tunggu modal muncul, lalu print
            setTimeout(() => {
                printReport();
            }, 500);
        }

        // Format tanggal ke Indonesia
        function formatDateIndo(dateStr) {
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                           'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const date = new Date(dateStr);
            return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`;
        }

        // Get current date time Indonesia
        function getCurrentDateTimeIndo() {
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                           'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const now = new Date();
            const day = now.getDate();
            const month = months[now.getMonth()];
            const year = now.getFullYear();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            return `${day} ${month} ${year}, ${hours}:${minutes} WIB`;
        }

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