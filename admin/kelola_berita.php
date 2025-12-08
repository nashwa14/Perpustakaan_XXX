<?php
session_start();
require_once '../config/database.php';
if ($_SESSION['role'] != 'admin') { header("Location: ../index.php"); exit; }

if (isset($_POST['tambah_berita'])) {
    $judul = $_POST['judul'];
    $isi   = $_POST['isi'];
    $sql   = "INSERT INTO news (judul, isi_berita) VALUES (?, ?)";
    $pdo->prepare($sql)->execute([$judul, $isi]);
    $success = "Berita berhasil diterbitkan!";
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $pdo->prepare("DELETE FROM news WHERE id = ?")->execute([$id]);
    $success = "Berita berhasil dihapus!";
}

// Pagination
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get total count
$total_berita = $pdo->query("SELECT COUNT(*) FROM news")->fetchColumn();
$total_pages = ceil($total_berita / $limit);

// Get paginated news
$berita = $pdo->query("SELECT * FROM news ORDER BY created_at DESC LIMIT $limit OFFSET $offset")->fetchAll();

// Stats
$berita_bulan_ini = $pdo->query("SELECT COUNT(*) FROM news WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())")->fetchColumn();
$berita_minggu_ini = $pdo->query("SELECT COUNT(*) FROM news WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURRENT_DATE(), 1)")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Berita - Perpustakaan Yogakarta</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style_admin.css">
</head>
<body>
    <?php include '../includes/navbar_admin.php'; ?>
    <div class="container my-4">
        <!-- Page Header -->
        <div class="welcome-header fade-in-up">
            <h1 class="welcome-title">Kelola Berita & Informasi</h1>
            <p class="welcome-subtitle">Publikasikan berita dan agenda kegiatan perpustakaan</p>
        </div>

        <?php if(isset($success)): ?>
        <div class="alert alert-success alert-dismissible fade show fade-in-up" style="border-left: 4px solid #4caf50; background: #e8f5e9; border-radius: 8px;">
            <i class="bi bi-check-circle me-2"></i>
            <?= $success ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Search, Filter and Add Button -->
        <div class="book-control-card fade-in-up">
            <div class="book-control-row">
                <div class="book-search-wrapper">
                    <i class="bi bi-search book-search-icon"></i>
                    <input type="text" class="book-search-input" placeholder="Cari judul berita..." id="searchInput">
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

                <button class="book-add-btn" data-bs-toggle="modal" data-bs-target="#addNewsModal">
                    <i class="bi bi-plus-circle"></i>
                    <span>Publikasikan Berita Baru</span>
                </button>
            </div>
        </div>

        <!-- News Table -->
        <div class="book-table-card fade-in-up">
            <div class="table-responsive">
                <?php if(count($berita) > 0): ?>
                    <table class="table book-table mb-0" id="newsTable">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="12%">Tanggal</th>
                                <th width="30%">Judul Berita</th>
                                <th width="43%">Isi Singkat</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = $offset + 1;
                            foreach($berita as $row): 
                            $isi_singkat = substr($row['isi_berita'], 0, 100);
                            if(strlen($row['isi_berita']) > 100) $isi_singkat .= '...';
                            ?>
                            <tr data-judul="<?= strtolower(htmlspecialchars($row['judul'])) ?>"
                                data-timestamp="<?= strtotime($row['created_at']) ?>">
                                <td><?= $no++ ?>.</td>
                                <td>
                                    <span class="date-badge">
                                        <?= date('d M Y', strtotime($row['created_at'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="book-title-cell">
                                        <?= htmlspecialchars($row['judul']) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="book-author-cell" style="white-space: normal; line-height: 1.4;">
                                        <?= htmlspecialchars($isi_singkat) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="book-action-buttons">
                                        <button class="book-action-icon view-icon" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#previewModal<?= $row['id'] ?>"
                                                title="Lihat">
                                            <i class="bi bi-eye-fill"></i>
                                        </button>
                                        <a href="?hapus=<?= $row['id'] ?>" 
                                           class="book-action-icon delete-icon" 
                                           onclick="return confirm('Yakin ingin menghapus berita ini?')"
                                           title="Hapus">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                    </div>

                                    <!-- Preview Modal -->
                                    <div class="modal fade" id="previewModal<?= $row['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">
                                                        <i class="bi bi-newspaper me-2"></i>
                                                        <?= htmlspecialchars($row['judul']) ?>
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            <i class="bi bi-calendar-event me-1"></i>
                                                            Diterbitkan: <?= date('d M Y, H:i', strtotime($row['created_at'])) ?> WIB
                                                        </small>
                                                    </div>
                                                    <p style="white-space: pre-line; text-align: justify; line-height: 1.8;">
                                                        <?= htmlspecialchars($row['isi_berita']) ?>
                                                    </p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
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
                        <h5 class="empty-title">Belum ada berita</h5>
                        <p class="empty-subtitle">Publikasikan berita pertama dengan tombol di atas</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    Menampilkan <?= $offset + 1 ?>-<?= min($offset + $limit, $total_berita) ?> dari <?= $total_berita ?>
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

    <!-- Add News Modal -->
    <div class="modal fade" id="addNewsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                <div class="modal-header" style="border-bottom: 1px solid #f0f0f0; padding: 1.5rem 2rem;">
                    <div>
                        <h5 class="modal-title" style="color: #424242; font-weight: 600; font-size: 1.25rem; margin-bottom: 0.25rem;">Publikasikan Berita Baru</h5>
                        <p class="text-muted mb-0" style="font-size: 0.875rem;">Lengkapi informasi berita untuk dipublikasikan</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding: 2rem;">
                    <form action="" method="POST" id="addNewsForm">
                        <div class="form-group">
                            <label>
                                <i class="bi bi-bookmark me-1"></i>
                                Judul Berita / Agenda
                            </label>
                            <input type="text" name="judul" class="form-control" placeholder="Contoh: Koleksi Buku Baru Bulan Ini" required>
                        </div>

                        <div class="form-group">
                            <label>
                                <i class="bi bi-text-paragraph me-1"></i>
                                Isi Berita / Informasi
                            </label>
                            <textarea name="isi" class="form-control" rows="8" placeholder="Tulis informasi lengkap di sini..." required></textarea>
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Gunakan Enter untuk membuat paragraf baru
                            </small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #f0f0f0; padding: 1rem 2rem;">
                    <button type="button" class="btn" style="background: #f5f5f5; color: #616161; border-radius: 8px; padding: 0.6rem 1.5rem;" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah_berita" form="addNewsForm" class="btn" style="background: #6F4D38; color: white; border-radius: 8px; padding: 0.6rem 1.5rem;">
                        Terbitkan Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elements
            const searchInput = document.getElementById('searchInput');
            const newsTable = document.getElementById('newsTable');
            const tableRows = newsTable ? newsTable.querySelectorAll('tbody tr') : [];
            
            // Filter state
            let currentDateFilter = 'all';
            
            // Search functionality
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    applyFilters();
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
            
            function applyFilters() {
                const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
                const now = new Date();
                const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                let visibleCount = 0;
                
                tableRows.forEach(row => {
                    const judul = row.dataset.judul || '';
                    const timestamp = parseInt(row.dataset.timestamp) * 1000;
                    const rowDate = new Date(timestamp);
                    
                    // Search: if empty, all pass; if has value, check if matches
                    const matchSearch = searchTerm === '' || judul.includes(searchTerm);
                    
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
                    
                    // Show row only if ALL active filters match (AND logic)
                    if (matchSearch && matchDate) {
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
        });
    </script>
</body>
</html>