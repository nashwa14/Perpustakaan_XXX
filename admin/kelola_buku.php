<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

if (isset($_POST['tambah'])) {
    $judul = $_POST['judul'];
    $penulis = $_POST['penulis'];
    $kategori = $_POST['kategori'];
    $deskripsi = $_POST['deskripsi'];
    $stok = $_POST['stok'];
    $gambar = 'default_cover.jpg';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . "." . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], "../assets/uploads/" . $filename);
        $gambar = $filename;
    }
    $sql = "INSERT INTO books (judul, penulis, kategori, deskripsi, stok, gambar) VALUES (?, ?, ?, ?, ?, ?)";
    $pdo->prepare($sql)->execute([$judul, $penulis, $kategori, $deskripsi, $stok, $gambar]);
    $success = "Buku berhasil ditambahkan!";
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $pdo->prepare("DELETE FROM books WHERE id = ?")->execute([$id]);
    $success = "Buku berhasil dihapus!";
}

// Pagination
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total_books = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
$total_pages = ceil($total_books / $limit);

$list_buku = $pdo->query("SELECT * FROM books ORDER BY id DESC LIMIT $limit OFFSET $offset")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Buku - Perpustakaan Yogakarta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style_admin.css">
</head>

<body>
    <?php include '../includes/navbar_admin.php'; ?>
    <div class="container my-4">
        <!-- Page Header -->
        <div class="welcome-header fade-in-up">
            <h1 class="welcome-title">Pengelolaan Koleksi Buku</h1>
            <p class="welcome-subtitle">Kelola koleksi buku perpustakaan dengan mudah</p>
        </div>

        <?php if (isset($success)): ?>
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
                    <input type="text" class="book-search-input" placeholder="Cari..." id="searchInput">
                </div>
                
                <div class="dropdown">
                    <button class="book-filter-btn" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <span id="filterText">Kategori</span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <ul class="dropdown-menu book-filter-menu" aria-labelledby="filterDropdown">
                        <li><a class="dropdown-item filter-option active" href="#" data-filter="all">Semua Kategori</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <?php
                        // Ambil semua kategori dan split untuk mendapatkan kategori individual
                        $all_books_categories = $pdo->query("SELECT kategori FROM books")->fetchAll();
                        $unique_categories = [];
                        
                        foreach ($all_books_categories as $book) {
                            // Split kategori dengan koma dan trim whitespace
                            $cats = array_map('trim', explode(',', $book['kategori']));
                            foreach ($cats as $cat) {
                                if (!empty($cat) && !in_array(strtolower($cat), array_map('strtolower', $unique_categories))) {
                                    $unique_categories[] = $cat;
                                }
                            }
                        }
                        
                        // Sort categories
                        sort($unique_categories, SORT_STRING | SORT_FLAG_CASE);
                        
                        foreach ($unique_categories as $cat):
                        ?>
                        <li><a class="dropdown-item filter-option" href="#" data-filter="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <button class="book-add-btn" data-bs-toggle="modal" data-bs-target="#addBookModal">
                    <i class="bi bi-plus-lg"></i>
                    <span>Tambah Buku Baru</span>
                </button>
            </div>
        </div>

        <!-- Modal Tambah Buku -->
        <div class="modal fade" id="addBookModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                    <div class="modal-header" style="border-bottom: 1px solid #f0f0f0; padding: 1.5rem 2rem;">
                        <div>
                            <h5 class="modal-title" style="color: #424242; font-weight: 600; font-size: 1.25rem; margin-bottom: 0.25rem;">Tambah Buku Baru</h5>
                            <p class="text-muted mb-0" style="font-size: 0.875rem;">Lengkapi informasi buku untuk menambahkan ke koleksi</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="padding: 2rem;">
                        <form action="" method="POST" enctype="multipart/form-data" id="addBookForm">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>
                                    <i class="bi bi-bookmark me-1"></i>
                                    Buku
                                </label>
                                <input type="text" name="judul" class="form-control" placeholder="Masukkan judul buku" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>
                                    <i class="bi bi-box-seam me-1"></i>
                                    Stok
                                </label>
                                <input type="number" name="stok" class="form-control" value="1" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>
                                    <i class="bi bi-person me-1"></i>
                                    Penulis
                                </label>
                                <input type="text" name="penulis" class="form-control" placeholder="Nama penulis" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>
                                    <i class="bi bi-tag me-1"></i>
                                    Kategori
                                </label>
                                <input type="text" name="kategori" class="form-control" placeholder="Contoh: Fiksi, Novel, dll">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="bi bi-text-paragraph me-1"></i>
                            Deskripsi
                        </label>
                        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi singkat tentang buku"></textarea>
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="bi bi-image me-1"></i>
                            Cover Buku
                        </label>
                        <input type="file" name="gambar" class="form-control" accept="image/*">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Format: JPG, PNG. Ukuran maksimal 2MB
                        </small>
                    </div>

                        </form>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #f0f0f0; padding: 1rem 2rem;">
                        <button type="button" class="btn" style="background: #f5f5f5; color: #616161; border-radius: 8px; padding: 0.6rem 1.5rem;" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah" form="addBookForm" class="btn" style="background: #6F4D38; color: white; border-radius: 8px; padding: 0.6rem 1.5rem;">
                            Simpan Buku
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="book-table-card fade-in-up">
            <div class="table-responsive">
                <?php if (count($list_buku) > 0): ?>
                    <table class="table book-table mb-0">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">Sampul</th>
                                <th width="25%">Judul Buku</th>
                                <th width="20%">Penulis</th>
                                <th width="15%">Kategori</th>
                                <th width="10%">Stok</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            foreach ($list_buku as $row):
                            ?>
                                <tr>
                                    <td><?= $no++ ?>.</td>
                                    <td>
                                        <img src="../assets/uploads/<?= htmlspecialchars($row['gambar']) ?>" 
                                             alt="Cover" 
                                             class="book-cover">
                                    </td>
                                    <td class="book-title-cell"><?= htmlspecialchars($row['judul']) ?></td>
                                    <td class="book-author-cell"><?= htmlspecialchars($row['penulis']) ?></td>
                                    <td>
                                        <div class="category-badges-wrapper">
                                            <?php
                                            $categories = array_map('trim', explode(',', $row['kategori']));
                                            foreach ($categories as $cat):
                                                if (!empty($cat)):
                                            ?>
                                                <span class="category-badge"><?= htmlspecialchars($cat) ?></span>
                                            <?php
                                                endif;
                                            endforeach;
                                            ?>
                                        </div>
                                    </td>
                                    <td class="book-stock-cell"><?= $row['stok'] ?></td>
                                    <td>
                                        <div class="book-action-buttons">
                                            <a href="edit_buku.php?id=<?= $row['id']; ?>" 
                                               class="book-action-icon edit-icon"
                                               title="Edit">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                            <a href="?hapus=<?= $row['id']; ?>" 
                                               class="book-action-icon delete-icon"
                                               onclick="return confirm('Yakin ingin menghapus buku <?= htmlspecialchars($row['judul']) ?>?')"
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
                        <h5 class="empty-title">Belum ada buku</h5>
                        <p class="empty-subtitle">Tambahkan buku pertama Anda menggunakan form di atas</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    Menampilkan <?= $offset + 1 ?>-<?= min($offset + $limit, $total_books) ?> dari <?= $total_books ?>
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
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .dropdown-menu {
            z-index: 1050 !important;
            position: absolute !important;
        }
        .dropdown-item.active,
        .dropdown-item:active {
            background-color: #6F4D38 !important;
            color: white !important;
        }
        .dropdown-item:hover {
            background-color: #f9f5f2;
            color: #6F4D38;
        }
        .dropdown-item.active:hover {
            background-color: #5d4037 !important;
            color: white !important;
        }
        .dropdown-item {
            transition: all 0.2s ease;
        }
    </style>
    <script>
        // Filter functionality
        let currentFilter = 'all';
        document.querySelectorAll('.filter-option').forEach(option => {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                currentFilter = this.dataset.filter;
                
                // Remove active class from all options
                document.querySelectorAll('.filter-option').forEach(opt => opt.classList.remove('active'));
                // Add active class to clicked option
                this.classList.add('active');
                
                applyFilters();
                
                // Update button text to show selected category
                const filterText = document.getElementById('filterText');
                if (currentFilter === 'all') {
                    filterText.textContent = 'Kategori';
                } else {
                    filterText.textContent = currentFilter;
                }
            });
        });

        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', applyFilters);
        
        // Apply filters function
        function applyFilters() {
            const searchValue = document.getElementById('searchInput').value.toLowerCase();
            const tableRows = document.querySelectorAll('.book-table tbody tr');
            
            tableRows.forEach(row => {
                const judul = row.cells[2].textContent.toLowerCase();
                const penulis = row.cells[3].textContent.toLowerCase();
                
                // Get all category badges from the category cell
                const categoryCell = row.cells[4];
                const categoryBadges = categoryCell.querySelectorAll('.category-badge');
                const bookCategories = Array.from(categoryBadges).map(badge => 
                    badge.textContent.trim().toLowerCase()
                );
                
                const matchesSearch = judul.includes(searchValue) || penulis.includes(searchValue);
                const matchesFilter = currentFilter === 'all' || bookCategories.includes(currentFilter.toLowerCase());
                
                if (matchesSearch && matchesFilter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>