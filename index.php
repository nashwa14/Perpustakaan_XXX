<?php
session_start();
require_once 'config/database.php';
$search = $_GET['q'] ?? '';
$query = "SELECT * FROM books WHERE judul LIKE :search OR penulis LIKE :search OR kategori LIKE :search";
$stmt = $pdo->prepare($query);
$stmt->execute(['search' => "%$search%"]);
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
</head>
<body>
    <?php include 'includes/navbar_user.php'; ?>
    <div class="container my-5">
        <div class="search-box fade-in-up">
            <form action="" method="GET" class="search-form">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" 
                           name="q" 
                           class="form-control border-start-0 search-input" 
                           placeholder="Cari judul, penulis, atau kategori..." 
                           value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-search me-2"></i>Cari
                    </button>
                </div>
            </form>
        </div>

        <?php if (!empty($search)): ?>
        <div class="alert alert-info fade-in-up">
            <i class="bi bi-info-circle me-2"></i>
            Menampilkan hasil pencarian untuk: <strong>"<?= htmlspecialchars($search) ?>"</strong>
            <a href="index.php" class="btn btn-sm btn-outline-secondary float-end">
                <i class="bi bi-x-circle me-1"></i>Reset
            </a>
        </div>
        <?php endif; ?>

        <?php if (count($books) > 0): ?>
            <div class="catalog-grid">
                <?php foreach ($books as $book): ?>
                <div class="card fade-in-up">
                    <?php $img = !empty($book['gambar']) ? $book['gambar'] : 'default_cover.jpg'; ?>
                    <div style="overflow: hidden;">
                        <img src="assets/uploads/<?= htmlspecialchars($img) ?>" 
                             alt="<?= htmlspecialchars($book['judul']) ?>"
                             class="card-img">
                    </div>
                    <div class="card-body">
                        <h3 class="card-title"><?= htmlspecialchars($book['judul']) ?></h3>
                        <p class="card-text">
                            <i class="bi bi-person me-1"></i>
                            <?= htmlspecialchars($book['penulis']) ?>
                        </p>
                        <?php if(!empty($book['kategori'])): ?>
                        <p class="card-text">
                            <i class="bi bi-tag me-1"></i>
                            <span class="badge bg-secondary"><?= htmlspecialchars($book['kategori']) ?></span>
                        </p>
                        <?php endif; ?>                        
                        <div class="mb-3">
                            <?php if($book['stok'] > 0): ?>
                                <span class="status-badge status-available">
                                    <i class="bi bi-check-circle me-1"></i>Tersedia (<?= $book['stok'] ?>)
                                </span>
                            <?php else: ?>
                                <span class="status-badge status-unavailable">
                                    <i class="bi bi-x-circle me-1"></i>Stok Habis
                                </span>
                            <?php endif; ?>
                        </div>
                        <a href="detail.php?id=<?= $book['id'] ?>" class="btn btn-primary w-100">
                            <i class="bi bi-eye me-2"></i>Lihat Detail
                        </a>
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
</body>
</html>