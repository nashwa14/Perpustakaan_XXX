<?php
session_start();
require_once 'config/database.php';

// Ambil parameter filter
$search = $_GET['q'] ?? '';
$kategori = $_GET['kategori'] ?? '';

// Ambil semua kategori yang tersedia
$kategoris = $pdo->query("SELECT DISTINCT kategori FROM books WHERE kategori IS NOT NULL AND kategori != '' ORDER BY kategori")->fetchAll(PDO::FETCH_COLUMN);

// Build query dengan filter
$query = "SELECT * FROM books WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (judul LIKE :search OR penulis LIKE :search OR kategori LIKE :search)";
    $params['search'] = "%$search%";
}

if (!empty($kategori)) {
    $query .= " AND kategori = :kategori";
    $params['kategori'] = $kategori;
}

$query .= " ORDER BY judul ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$books = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Perpustakaan Yogakarta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/navbar_style.css">
    <style>
        :root {
            --coffee: #6F4D38;
            --soft-ivory: #f8f4ef;
            --soft-border: #e7dfd7;
        }
        .book-card {
            border: 1px solid var(--soft-border);
            border-radius: 16px;
            background: linear-gradient(160deg, #fff, var(--soft-ivory));
            box-shadow: 0 14px 40px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: all 0.28s ease;
            display: flex;
            flex-direction: column;
        }
        .book-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 48px rgba(0,0,0,0.12);
        }
        .book-cover {
            position: relative;
            aspect-ratio: 3 / 4;
            background: linear-gradient(135deg, #f2e9e1, #fdfbf8);
            overflow: hidden;
        }
        .book-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.35s ease;
        }
        .book-card:hover .book-cover img {
            transform: scale(1.05);
        }
        .book-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: rgba(111,77,56,0.9);
            color: #fff;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            max-width: 80%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .book-availability {
            position: absolute;
            bottom: 12px;
            right: 12px;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.85rem;
            background: rgba(255,255,255,0.92);
            color: #2f3b45;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        .book-availability.unavailable {
            background: rgba(255, 241, 241, 0.95);
            color: #c0392b;
        }
        .book-body {
            padding: 16px 18px 18px;
            display: flex;
            flex-direction: column;
            flex: 1;
            gap: 6px;
        }
        .book-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #2f1f16;
            margin-bottom: 4px;
            line-height: 1.35;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .book-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #6c757d;
            font-size: 0.92rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .book-meta + .book-meta { margin-top: -2px; }
        .book-stats {
            margin-top: 6px;
            color: #2f3b45;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.95rem;
        }
        .book-footer {
            margin-top: auto;
        }
        .book-footer .status-badge {
            font-size: 0.9rem;
        }
        .btn-outline-coffee {
            border-color: var(--coffee);
            color: var(--coffee);
        }
        .btn-outline-coffee:hover {
            background: var(--coffee);
            color: #fff;
        }
        .btn-coffee {
            background: var(--coffee);
            color: #fff;
            border: 1px solid var(--coffee);
        }
        .btn-coffee:hover {
            background: #5a3e2e;
            border-color: #5a3e2e;
            color: #fff;
        }
        .btn-coffee:focus {
            box-shadow: 0 0 0 0.2rem rgba(111,77,56,0.25);
        }
        @media (max-width: 576px) {
            .book-body { padding: 14px 16px 16px; }
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar_user.php'; ?>
    <div class="container my-5">
        <div class="mb-4 fade-in-up">
            <h3 class="fw-bold mb-3" style="color: #6F4D38;">Jelajahi Koleksi Kami</h3>
            <p class="lead text-muted">Temukan buku favorit Anda berikutnya dari ribuan koleksi kami</p>
        </div>
        
        <!-- Ganti seluruh bagian search-box dengan kode ini: -->
<div class="search-section fade-in-up">
    
    <div class="search-card">
        <form action="" method="GET" class="search-form">
            <div class="row g-3 align-items-end">
                <!-- Search Input -->
                <div class="col-md-6">
                    <label class="form-label label-text">Cari Buku</label>
                    <div class="input-group search-input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" 
                               name="q" 
                               class="form-control search-input" 
                               placeholder="Judul, penulis, atau kata kunci..." 
                               value="<?= htmlspecialchars($search) ?>">
                        <?php if(!empty($search)): ?>
                        <button type="button" class="btn btn-clear-search" onclick="clearSearch()">
                            <i class="bi bi-x"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Category Filter -->
                <div class="col-md-4">
                    <label class="form-label label-text">Filter Kategori</label>
                    <div class="input-group category-input-group">
                        <span class="input-group-text">
                            <i class="bi bi-tag text-muted"></i>
                        </span>
                        <select name="kategori" class="form-select category-select">
                            <option value="">Semua Kategori</option>
                            <?php foreach ($kategoris as $kat): ?>
                                <option value="<?= htmlspecialchars($kat) ?>" 
                                    <?= $kategori === $kat ? 'selected' : '' ?>
                                    class="category-option">
                                    <?= htmlspecialchars($kat) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="col-md-2">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-search">
                            <i class="bi bi-search me-2"></i>Cari
                        </button>
                        <?php if (!empty($search) || !empty($kategori)): ?>
                        <a href="index.php" class="btn btn-reset">
                            <i class="bi bi-arrow-clockwise me-1"></i>Reset
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if (!empty($search) || !empty($kategori)): ?>
<!-- Active Filters Badge -->
<div class="active-filters fade-in-up">
    <div class="filter-badges">
        <span class="filter-label">
            <i class="bi bi-funnel-fill me-2"></i>Filter Aktif:
        </span>
        <?php if (!empty($search)): ?>
        <span class="filter-badge">
            Pencarian: "<?= htmlspecialchars($search) ?>"
            <a href="index.php?kategori=<?= urlencode($kategori) ?>" class="badge-remove">
                <i class="bi bi-x"></i>
            </a>
        </span>
        <?php endif; ?>
        
        <?php if (!empty($kategori)): ?>
        <span class="filter-badge">
            Kategori: <?= htmlspecialchars($kategori) ?>
            <a href="index.php?q=<?= urlencode($search) ?>" class="badge-remove">
                <i class="bi bi-x"></i>
            </a>
        </span>
        <?php endif; ?>
        
        <a href="index.php" class="btn-clear-all">
            <i class="bi bi-x-circle me-1"></i>Hapus Semua
        </a>
    </div>
    
    <div class="filter-results mt-2">
        <span class="text-muted">
            <i class="bi bi-book me-1"></i>
            Ditemukan <strong><?= count($books) ?></strong> buku
        </span>
    </div>
</div>
<?php endif; ?>

<script>
function clearSearch() {
    document.querySelector('.search-input').value = '';
    document.querySelector('.search-form').submit();
}
</script>

        <?php if (!empty($search) || !empty($kategori)): ?>
        <div class="alert alert-info fade-in-up mt-3">
            <i class="bi bi-funnel me-2"></i>
            <strong>Filter Aktif:</strong>
            <?php if (!empty($search)): ?>
                Pencarian: <strong>"<?= htmlspecialchars($search) ?>"</strong>
            <?php endif; ?>
            <?php if (!empty($kategori)): ?>
                <?= !empty($search) ? ' | ' : '' ?>Kategori: <strong><?= htmlspecialchars($kategori) ?></strong>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if (count($books) > 0): ?>
            <div class="catalog-grid">
                <?php foreach ($books as $book): ?>
                <?php $img = !empty($book['gambar']) ? $book['gambar'] : 'default_cover.jpg'; ?>
                <div class="book-card fade-in-up">
                    <div class="book-cover">
                        <img src="assets/uploads/<?= htmlspecialchars($img) ?>" 
                             alt="<?= htmlspecialchars($book['judul']) ?>">
                        <?php if(!empty($book['kategori'])): ?>
                        <div class="book-badge">
                            <i class="bi bi-tag"></i>
                            <span><?= htmlspecialchars($book['kategori']) ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="book-availability <?= $book['stok'] > 0 ? '' : 'unavailable' ?>">
                            <i class="bi <?= $book['stok'] > 0 ? 'bi-check-circle' : 'bi-x-circle' ?>"></i>
                            <?= $book['stok'] > 0 ? 'Tersedia ('.$book['stok'].')' : 'Stok Habis' ?>
                        </div>
                    </div>
                    <div class="book-body">
                        <div class="book-title"><?= htmlspecialchars($book['judul']) ?></div>
                        <div class="book-meta">
                            <i class="bi bi-person"></i>
                            <span><?= htmlspecialchars($book['penulis']) ?></span>
                        </div>
                        <?php if(!empty($book['penerbit'])): ?>
                        <div class="book-meta">
                            <i class="bi bi-building"></i>
                            <span><?= htmlspecialchars($book['penerbit']) ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="book-footer">
                            <a href="detail.php?id=<?= $book['id'] ?>" class="btn btn-coffee btn-sm px-3 w-100">
                                <i class="bi bi-eye me-1"></i>Detail
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state fade-in-up">
                <div class="empty-state-icon">
                    <i class="bi bi-inbox"></i>
                </div>
                <h3>Buku tidak ditemukan</h3>
                <p class="text-muted">Coba gunakan kata kunci lain atau telusuri katalog lengkap kami.</p>
                <?php if(!empty($search)): ?>
                <a href="index.php" class="btn btn-outline-secondary mt-3">
                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Katalog
                </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }, index * 100);
                }
            });
        }, observerOptions);
        document.querySelectorAll('.card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.5s ease';
            observer.observe(card);
        });
    </script>
    <script>
// Real-time search suggestions (jika ingin implementasi autocomplete)
const searchInput = document.querySelector('.search-input');
const categorySelect = document.querySelector('.category-select');

// Clear individual filters
document.querySelectorAll('.badge-remove').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const url = new URL(window.location.href);
        const param = this.parentElement.textContent.includes('Pencarian') ? 'q' : 'kategori';
        url.searchParams.delete(param);
        window.location.href = url.toString();
    });
});

// Add loading state on form submit
const searchForm = document.querySelector('.search-form');
searchForm.addEventListener('submit', function() {
    const searchCard = document.querySelector('.search-card');
    searchCard.classList.add('filter-loading');
});

// Category select enhancement
if(categorySelect) {
    categorySelect.addEventListener('change', function() {
        // Add visual feedback
        this.style.borderColor = 'var(--coffee)';
        this.style.boxShadow = '0 0 0 3px rgba(111, 77, 56, 0.2)';
        
        // Auto-submit on category change (optional)
        setTimeout(() => {
            if(this.value !== '<?= $kategori ?>') {
                searchForm.submit();
            }
        }, 300);
    });
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Focus search on Ctrl/Cmd + K
    if((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        searchInput.focus();
    }
    
    // Reset filters on Escape
    if(e.key === 'Escape' && (searchInput.value || categorySelect.value)) {
        window.location.href = 'index.php';
    }
});

// Animate filter badges
document.querySelectorAll('.filter-badge').forEach((badge, index) => {
    badge.style.animationDelay = `${index * 0.1}s`;
    badge.classList.add('animate__animated', 'animate__fadeInUp');
});

// Update results count dynamically
function updateResultsCount(count) {
    const countElement = document.querySelector('.filter-results strong');
    if(countElement) {
        countElement.textContent = count;
    }
}
</script>
</body>
</html>