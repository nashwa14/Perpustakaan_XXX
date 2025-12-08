<?php
session_start();
require_once '../config/database.php';
if ($_SESSION['role'] != 'admin') header("Location: ../index.php");
if (isset($_GET['hapus'])) {
    $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$_GET['hapus']]);
    $success = "Anggota berhasil dihapus!";
}

// Tambahkan kolom status jika belum ada
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'aktif'");
} catch(PDOException $e) {
    // Column might already exist
}

// Pagination
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Statistik anggota
$anggota_baru_30hari = $pdo->query("SELECT COUNT(*) FROM users WHERE role='anggota' AND DATE(created_at) >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn();
$total_anggota_terdaftar = $pdo->query("SELECT COUNT(*) FROM users WHERE role='anggota'")->fetchColumn();
$anggota_aktif = $pdo->query("SELECT COUNT(*) FROM users WHERE role='anggota' AND status='aktif'")->fetchColumn();
$anggota_nonaktif = $pdo->query("SELECT COUNT(*) FROM users WHERE role='anggota' AND status='nonaktif'")->fetchColumn();

// Get paginated users
$total_pages = ceil($total_anggota_terdaftar / $limit);
$users = $pdo->query("SELECT * FROM users WHERE role='anggota' ORDER BY created_at DESC LIMIT $limit OFFSET $offset")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Anggota - Perpustakaan Yogakarta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style_admin.css">
</head>
<body>
    <?php include '../includes/navbar_admin.php'; ?>  
    <div class="container my-4">
        <!-- Page Header -->
        <div class="welcome-header fade-in-up">
            <h1 class="welcome-title">Kelola Anggota Perpustakaan</h1>
            <p class="welcome-subtitle">Kelola data anggota dan keanggotaan perpustakaan</p>
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
                    <span class="metric-label">Anggota Baru</span>
                </div>
                <h2 class="metric-value"><?= number_format($anggota_baru_30hari) ?></h2>
                <span class="metric-change metric-change-positive">
                    +<?= $anggota_baru_30hari ?> dalam 30 hari terakhir
                </span>
            </div>

            <div class="metric-card">
                <div class="metric-header">
                    <span class="metric-label">Anggota Terdaftar</span>
                </div>
                <h2 class="metric-value"><?= number_format($total_anggota_terdaftar) ?></h2>
                <span class="metric-change metric-change-success">
                    +<?= $anggota_baru_30hari ?> dalam 30 hari terakhir
                </span>
            </div>

            <div class="metric-card">
                <div class="metric-header">
                    <span class="metric-label">Anggota Aktif</span>
                </div>
                <h2 class="metric-value"><?= number_format($anggota_aktif) ?></h2>
                <span class="metric-change metric-change-primary">
                    Status aktif saat ini
                </span>
            </div>

            <div class="metric-card">
                <div class="metric-header">
                    <span class="metric-label">Anggota Non-Aktif</span>
                </div>
                <h2 class="metric-value"><?= number_format($anggota_nonaktif) ?></h2>
                <span class="metric-change metric-change-negative">
                    Status non-aktif saat ini
                </span>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="book-control-card fade-in-up">
            <div class="book-control-row">
                <div class="book-search-wrapper">
                    <i class="bi bi-search book-search-icon"></i>
                    <input type="text" class="book-search-input" placeholder="Cari nama atau username..." id="searchInput">
                </div>
                
                <div class="dropdown">
                    <button class="book-filter-btn" type="button" id="dateFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <span id="dateFilterText">Tanggal</span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <ul class="dropdown-menu book-filter-menu" aria-labelledby="dateFilterDropdown">
                        <li><a class="dropdown-item date-filter-option active" href="#" data-filter="all">Semua</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item date-filter-option" href="#" data-filter="today">Hari Ini</a></li>
                        <li><a class="dropdown-item date-filter-option" href="#" data-filter="week">7 Hari</a></li>
                        <li><a class="dropdown-item date-filter-option" href="#" data-filter="month">30 Hari</a></li>
                        <li><a class="dropdown-item date-filter-option" href="#" data-filter="year">Tahun Ini</a></li>
                    </ul>
                </div>
                
                <div class="dropdown">
                    <button class="book-filter-btn" type="button" id="statusFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <span id="statusFilterText">Status</span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <ul class="dropdown-menu book-filter-menu" aria-labelledby="statusFilterDropdown">
                        <li><a class="dropdown-item status-filter-option active" href="#" data-filter="all">Semua</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item status-filter-option" href="#" data-filter="aktif">Aktif</a></li>
                        <li><a class="dropdown-item status-filter-option" href="#" data-filter="nonaktif">Non-Aktif</a></li>
                        <li><a class="dropdown-item status-filter-option" href="#" data-filter="pending">Pending</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Members Table -->
        <div class="book-table-card fade-in-up">
            <div class="table-responsive">
                <?php if(count($users) > 0): ?>
                    <table class="table book-table mb-0" id="membersTable">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="30%">Nama Lengkap</th>
                                <th width="20%">Username</th>
                                <th width="20%">Tanggal Terdaftar</th>
                                <th width="15%">Status</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach($users as $row): 
                            $status = isset($row['status']) ? strtolower($row['status']) : 'aktif';
                            ?>
                            <tr data-nama="<?= strtolower(htmlspecialchars($row['nama'])) ?>"
                                data-username="<?= strtolower(htmlspecialchars($row['username'])) ?>"
                                data-timestamp="<?= strtotime($row['created_at']) ?>"
                                data-status="<?= $status ?>">
                                <td><?= $no++ ?>.</td>
                                <td class="book-title-cell"><?= htmlspecialchars($row['nama']) ?></td>
                                <td class="book-author-cell"><?= htmlspecialchars($row['username']) ?></td>
                                <td class="book-author-cell"><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                                <td>
                                    <?php 
                                    $statusColor = [
                                        'aktif' => ['bg' => '#e8f5e9', 'text' => '#2e7d32'],
                                        'nonaktif' => ['bg' => '#ffebee', 'text' => '#c62828'],
                                        'pending' => ['bg' => '#fff8e1', 'text' => '#f57c00']
                                    ];
                                    $color = $statusColor[$status] ?? $statusColor['aktif'];
                                    ?>
                                    <span class="category-badge" style="background: <?= $color['bg'] ?>; color: <?= $color['text'] ?>;">
                                        <?= ucfirst($status) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="book-action-buttons">
                                        <a href="?hapus=<?= $row['id'] ?>" 
                                           class="book-action-icon delete-icon"
                                           onclick="return confirm('Yakin ingin menghapus anggota <?= htmlspecialchars($row['nama']) ?>?\n\nPeringatan: Semua data peminjaman terkait akan hilang!')"
                                           title="Hapus">
                                            <i class="bi bi-trash-fill"></i>
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
                        <h5 class="empty-title">Belum ada anggota</h5>
                        <p class="empty-subtitle">Anggota baru akan muncul di sini setelah registrasi</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    Menampilkan <?= $offset + 1 ?>-<?= min($offset + $limit, $total_anggota_terdaftar) ?> dari <?= $total_anggota_terdaftar ?>
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
            const membersTable = document.getElementById('membersTable');
            const tableRows = membersTable ? membersTable.querySelectorAll('tbody tr') : [];
            
            // Filter states
            let currentDateFilter = 'all';
            let currentStatusFilter = 'all';
            
            // Search functionality
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    applyFilters();
                });
            }
            
            function applyFilters() {
                const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
                const now = new Date();
                const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                let visibleCount = 0;
                
                tableRows.forEach(row => {
                    const nama = row.dataset.nama || '';
                    const username = row.dataset.username || '';
                    const timestamp = parseInt(row.dataset.timestamp) * 1000;
                    const rowDate = new Date(timestamp);
                    const status = row.dataset.status || '';
                    
                    // Search: if empty, all pass; if has value, check if matches
                    const matchSearch = searchTerm === '' || nama.includes(searchTerm) || username.includes(searchTerm);
                    
                    // Date filter: if 'all', all pass; if specific, check date range
                    let matchDate = true;
                    if (currentDateFilter !== 'all') {
                        if (currentDateFilter === 'today') {
                            matchDate = rowDate >= today;
                        } else if (currentDateFilter === 'week') {
                            const weekAgo = new Date(today);
                            weekAgo.setDate(weekAgo.getDate() - 7);
                            matchDate = rowDate >= weekAgo;
                        } else if (currentDateFilter === 'month') {
                            const monthAgo = new Date(today);
                            monthAgo.setDate(monthAgo.getDate() - 30);
                            matchDate = rowDate >= monthAgo;
                        } else if (currentDateFilter === 'year') {
                            const yearStart = new Date(now.getFullYear(), 0, 1);
                            matchDate = rowDate >= yearStart;
                        }
                    }
                    
                    // Status: if 'all', all pass; if specific, must match
                    const matchStatus = currentStatusFilter === 'all' || status === currentStatusFilter;
                    
                    // Show row only if ALL active filters match (AND logic)
                    if (matchSearch && matchDate && matchStatus) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                updateTableNumbers();
                console.log(`Showing ${visibleCount} results`);
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
        
            
            // Date filter
            const dateFilterOptions = document.querySelectorAll('.date-filter-option');
            const dateFilterText = document.getElementById('dateFilterText');
            
            dateFilterOptions.forEach(option => {
                option.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    dateFilterOptions.forEach(opt => opt.classList.remove('active'));
                    this.classList.add('active');
                    
                    currentDateFilter = this.dataset.filter;
                    if (dateFilterText) {
                        const dateFilterNames = {
                            'all': 'Tanggal',
                            'today': 'Hari Ini',
                            'week': '7 Hari',
                            'month': '30 Hari',
                            'year': 'Tahun Ini'
                        };
                        dateFilterText.textContent = dateFilterNames[currentDateFilter] || 'Tanggal';
                    }
                    
                    applyFilters();
                });
            });
            
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
                        const statusFilterNames = {
                            'all': 'Status',
                            'aktif': 'Aktif',
                            'nonaktif': 'Non-Aktif',
                            'pending': 'Pending'
                        };
                        statusFilterText.textContent = statusFilterNames[currentStatusFilter] || 'Status';
                    }
                    
                    applyFilters();
                });
            });
        });
    </script>
</body>
</html>