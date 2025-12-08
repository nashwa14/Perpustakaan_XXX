<?php
session_start();
require_once '../config/database.php';
if ($_SESSION['role'] != 'admin') header("Location: ../index.php");
if (isset($_GET['kembali'])) {
    $id_pinjam = $_GET['kembali'];
    $id_buku   = $_GET['book_id'];

    $pdo->prepare("UPDATE borrows SET status='Dikembalikan', tanggal_kembali=CURDATE() WHERE id=?")->execute([$id_pinjam]);
    $pdo->prepare("UPDATE books SET stok = stok + 1 WHERE id=?")->execute([$id_buku]);
    
    $success = "Buku berhasil dikembalikan!";
}
// Pagination
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get total count
$total_dipinjam = $pdo->query("SELECT COUNT(*) FROM borrows WHERE status='Disetujui'")->fetchColumn();
$total_pages = ceil($total_dipinjam / $limit);

$query = "SELECT p.id, p.book_id, u.nama, b.judul, b.penulis, p.tanggal_pinjam, p.durasi_hari,
          DATEDIFF(CURDATE(), p.tanggal_pinjam) as hari_pinjam
          FROM borrows p 
          JOIN users u ON p.user_id = u.id 
          JOIN books b ON p.book_id = b.id 
          WHERE p.status = 'Disetujui'
          ORDER BY p.tanggal_pinjam ASC
          LIMIT $limit OFFSET $offset";
$list = $pdo->query($query)->fetchAll();

// Tambahkan kolom tanggal_kembali jika belum ada
try {
    $pdo->exec("ALTER TABLE borrows ADD COLUMN IF NOT EXISTS tanggal_kembali DATE");
} catch(PDOException $e) {
    // Column might already exist
}

$dikembalikan_hari_ini = $pdo->query("SELECT COUNT(*) FROM borrows WHERE status='Dikembalikan' AND DATE(tanggal_kembali) = CURDATE()")->fetchColumn();

// Get all active borrows for statistics
$all_borrows = $pdo->query("SELECT DATEDIFF(CURDATE(), tanggal_pinjam) as hari_pinjam, durasi_hari FROM borrows WHERE status='Disetujui'")->fetchAll();
$terlambat = count(array_filter($all_borrows, fn($item) => $item['hari_pinjam'] > $item['durasi_hari']));
$segera_jatuh_tempo = count(array_filter($all_borrows, function($item) {
    $sisa_hari = $item['durasi_hari'] - $item['hari_pinjam'];
    return $sisa_hari >= 0 && $sisa_hari <= 2;
}));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengembalian Buku - Perpustakaan Yogakarta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style_admin.css">
</head>
<body>
    <?php include '../includes/navbar_admin.php'; ?>
    <div class="container my-4">
        <!-- Page Header -->
        <div class="welcome-header fade-in-up">
            <h1 class="welcome-title">Pengembalian Buku</h1>
            <p class="welcome-subtitle">Kelola proses pengembalian buku yang sedang dipinjam</p>
        </div>

        <?php if(isset($success)): ?>
        <div class="alert alert-success alert-dismissible fade show fade-in-up" style="border-left: 4px solid #4caf50; background: #e8f5e9; border-radius: 8px;">
            <i class="bi bi-check-circle me-2"></i>
            <?= $success ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="stats-cards-container fade-in-up mb-4" style="grid-template-columns: repeat(4, 1fr);">
            <div class="metric-card">
                <div class="metric-header">
                    <span class="metric-label">Buku Sedang Dipinjam</span>
                </div>
                <h2 class="metric-value"><?= number_format($total_dipinjam) ?></h2>
                <span class="metric-change metric-change-info">
                    Total peminjaman aktif
                </span>
            </div>

            <div class="metric-card">
                <div class="metric-header">
                    <span class="metric-label">Dikembalikan Hari Ini</span>
                </div>
                <h2 class="metric-value"><?= number_format($dikembalikan_hari_ini) ?></h2>
                <span class="metric-change metric-change-success">
                    Pengembalian hari ini
                </span>
            </div>

            <div class="metric-card">
                <div class="metric-header">
                    <span class="metric-label">Peminjaman Terlambat</span>
                </div>
                <h2 class="metric-value"><?= number_format($terlambat) ?></h2>
                <span class="metric-change metric-change-negative">
                    Melewati batas waktu
                </span>
            </div>

            <div class="metric-card">
                <div class="metric-header">
                    <span class="metric-label">Segera Jatuh Tempo</span>
                </div>
                <h2 class="metric-value"><?= number_format($segera_jatuh_tempo) ?></h2>
                <span class="metric-change" style="color: #f57c00;">
                    1-2 hari lagi
                </span>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="book-control-card fade-in-up">
            <div class="book-control-row">
                <div class="book-search-wrapper">
                    <i class="bi bi-search book-search-icon"></i>
                    <input type="text" class="book-search-input" placeholder="Cari nama peminjam atau judul buku..." id="searchInput">
                </div>
                
                <div class="dropdown">
                    <button class="book-filter-btn" type="button" id="statusFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <span id="statusFilterText">Status</span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <ul class="dropdown-menu book-filter-menu" aria-labelledby="statusFilterDropdown">
                        <li><a class="dropdown-item status-filter-option active" href="#" data-filter="all">Semua</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item status-filter-option" href="#" data-filter="normal">Normal</a></li>
                        <li><a class="dropdown-item status-filter-option" href="#" data-filter="segera">Segera Jatuh Tempo</a></li>
                        <li><a class="dropdown-item status-filter-option" href="#" data-filter="terlambat">Terlambat</a></li>
                    </ul>
                </div>
                
                <div class="dropdown">
                    <button class="book-filter-btn" type="button" id="durationFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <span id="durationFilterText">Durasi</span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <ul class="dropdown-menu book-filter-menu" aria-labelledby="durationFilterDropdown">
                        <li><a class="dropdown-item duration-filter-option active" href="#" data-filter="all">Semua</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item duration-filter-option" href="#" data-filter="3">3 Hari</a></li>
                        <li><a class="dropdown-item duration-filter-option" href="#" data-filter="7">7 Hari</a></li>
                        <li><a class="dropdown-item duration-filter-option" href="#" data-filter="14">14 Hari</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="book-table-card fade-in-up">
            <div class="table-responsive" style="overflow-x: auto;">
                <?php if(count($list) > 0): ?>
                    <table class="table book-table mb-0" id="borrowTable" style="table-layout: fixed; min-width: 1100px;">
                        <thead>
                            <tr>
                                <th style="width: 45px; padding: 14px 8px;">No</th>
                                <th style="width: 140px; padding: 14px 10px;">Peminjam</th>
                                <th style="width: 280px; padding: 14px 10px;">Judul Buku</th>
                                <th style="width: 100px; padding: 14px 10px;">Tgl Pinjam</th>
                                <th style="width: 85px; padding: 14px 10px;">Durasi</th>
                                <th style="width: 110px; padding: 14px 10px;">Batas Kembali</th>
                                <th style="width: 150px; padding: 14px 10px;">Status</th>
                                <th style="width: 70px; padding: 14px 8px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach($list as $row): 
                            $is_late = $row['hari_pinjam'] > $row['durasi_hari'];
                            $sisa_hari = $row['durasi_hari'] - $row['hari_pinjam'];
                            $batas_kembali = date('d M Y', strtotime($row['tanggal_pinjam'] . ' + ' . $row['durasi_hari'] . ' days'));
                            $status_class = $is_late ? 'terlambat' : ($sisa_hari <= 2 ? 'segera' : 'normal');
                            ?>
                            <tr data-nama="<?= strtolower(htmlspecialchars($row['nama'])) ?>" 
                                data-judul="<?= strtolower(htmlspecialchars($row['judul'])) ?>" 
                                data-status="<?= $status_class ?>" 
                                data-durasi="<?= $row['durasi_hari'] ?>">
                                <td style="padding: 12px 8px;"><?= $no++ ?>.</td>
                                <td style="padding: 12px 10px;"><?= htmlspecialchars($row['nama']) ?></td>
                                <td style="padding: 12px 10px;">
                                    <div style="font-weight: 500; color: #212121; margin-bottom: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($row['judul']) ?></div>
                                    <small style="color: #9e9e9e;"><?= htmlspecialchars($row['penulis']) ?></small>
                                </td>
                                <td style="padding: 12px 10px;"><?= date('d M Y', strtotime($row['tanggal_pinjam'])) ?></td>
                                <td style="padding: 12px 10px;">
                                    <span style="background: #f5f5f5; padding: 4px 10px; border-radius: 12px; font-size: 0.8125rem; color: #616161; white-space: nowrap;">
                                        <?= $row['durasi_hari'] ?> hari
                                    </span>
                                </td>
                                <td style="padding: 12px 10px;">
                                    <div style="font-weight: 500; color: <?= $is_late ? '#c62828' : '#616161' ?>; font-size: 0.875rem;">
                                        <?= $batas_kembali ?>
                                    </div>
                                </td>
                                <td style="padding: 12px 10px;">
                                    <?php if($is_late): ?>
                                        <span style="background: #ffebee; color: #c62828; padding: 6px 12px; border-radius: 12px; font-size: 0.8125rem; font-weight: 500;">
                                            Terlambat <?= abs($sisa_hari) ?> hari
                                        </span>
                                    <?php elseif($sisa_hari <= 2): ?>
                                        <span style="background: #fff8e1; color: #f57c00; padding: 6px 12px; border-radius: 12px; font-size: 0.8125rem; font-weight: 500;">
                                            Sisa <?= $sisa_hari ?> hari
                                        </span>
                                    <?php else: ?>
                                        <span style="background: #e8f5e9; color: #2e7d32; padding: 6px 12px; border-radius: 12px; font-size: 0.8125rem; font-weight: 500;">
                                            Sisa <?= $sisa_hari ?> hari
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 12px 8px; text-align: center;">
                                    <div style="display: flex; justify-content: center;">
                                        <a href="?kembali=<?= $row['id'] ?>&book_id=<?= $row['book_id'] ?>" 
                                           class="book-action-icon edit-icon"
                                           title="Terima Pengembalian"
                                           onclick="return confirm('Konfirmasi pengembalian buku:\n\n<?= htmlspecialchars($row['judul']) ?>\nPeminjam: <?= htmlspecialchars($row['nama']) ?>')">
                                            <i class="bi bi-check-lg"></i>
                                        </a>
                                    </div>
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
                        <h5 class="empty-title">Tidak ada peminjaman aktif</h5>
                        <p class="empty-subtitle">Semua buku sudah dikembalikan</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    Menampilkan <?= $offset + 1 ?>-<?= min($offset + $limit, $total_dipinjam) ?> dari <?= $total_dipinjam ?>
                </div>
                <div class="pagination-controls">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>" class="pagination-btn pagination-arrow">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    <?php else: ?>
                        <span class="pagination-btn pagination-arrow disabled">
                            <i class="bi bi-chevron-left"></i>
                        </span>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == 1 || $i == $total_pages || ($i >= $page - 1 && $i <= $page + 1)): ?>
                            <a href="?page=<?= $i ?>" class="pagination-btn <?= $i == $page ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php elseif ($i == $page - 2 || $i == $page + 2): ?>
                            <span class="pagination-dots">...</span>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?= $page + 1 ?>" class="pagination-btn pagination-arrow">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    <?php else: ?>
                        <span class="pagination-btn pagination-arrow disabled">
                            <i class="bi bi-chevron-right"></i>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elements
            const searchInput = document.getElementById('searchInput');
            const borrowTable = document.getElementById('borrowTable');
            const tableRows = borrowTable ? borrowTable.querySelectorAll('tbody tr') : [];
            
            // Filter states
            let currentStatusFilter = 'all';
            let currentDurationFilter = 'all';
            
            // Search functionality
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    applyFilters();
                });
            }
            
            // Status filter
            const statusFilterOptions = document.querySelectorAll('.status-filter-option');
            const statusFilterText = document.getElementById('statusFilterText');
            
            statusFilterOptions.forEach(option => {
                option.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    statusFilterOptions.forEach(opt => opt.classList.remove('active'));
                    this.classList.add('active');
                    
                    currentStatusFilter = this.dataset.filter;
                    if (statusFilterText) {
                        statusFilterText.textContent = this.textContent;
                    }
                    
                    applyFilters();
                });
            });
            
            // Duration filter
            const durationFilterOptions = document.querySelectorAll('.duration-filter-option');
            const durationFilterText = document.getElementById('durationFilterText');
            
            durationFilterOptions.forEach(option => {
                option.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    durationFilterOptions.forEach(opt => opt.classList.remove('active'));
                    this.classList.add('active');
                    
                    currentDurationFilter = this.dataset.filter;
                    if (durationFilterText) {
                        durationFilterText.textContent = currentDurationFilter === 'all' ? 'Durasi' : this.textContent;
                    }
                    
                    applyFilters();
                });
            });
            
            function applyFilters() {
                const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
                let visibleCount = 0;
                
                tableRows.forEach(row => {
                    const nama = row.dataset.nama || '';
                    const judul = row.dataset.judul || '';
                    const status = row.dataset.status || '';
                    const durasi = row.dataset.durasi || '';
                    
                    // Check each filter condition independently
                    // Search: if empty, all pass; if has value, check if matches
                    const matchSearch = searchTerm === '' || nama.includes(searchTerm) || judul.includes(searchTerm);
                    
                    // Status: if 'all', all pass; if specific, must match
                    const matchStatus = currentStatusFilter === 'all' || status === currentStatusFilter;
                    
                    // Duration: if 'all', all pass; if specific, must match
                    const matchDuration = currentDurationFilter === 'all' || durasi === currentDurationFilter;
                    
                    // Show row only if ALL active filters match (AND logic)
                    if (matchSearch && matchStatus && matchDuration) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                updateTableNumbers();
                
                // Update visibility message
                const paginationInfo = document.querySelector('.pagination-info');
                if (paginationInfo && visibleCount === 0) {
                    console.log('No results found');
                } else {
                    console.log(`Showing ${visibleCount} results`);
                }
            }
            
            function updateTableNumbers() {
                let visibleIndex = 1;
                tableRows.forEach(row => {
                    if (row.style.display !== 'none') {
                        const firstCell = row.querySelector('td:first-child');
                        if (firstCell) {
                            firstCell.textContent = visibleIndex + '.';
                            visibleIndex++;
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>