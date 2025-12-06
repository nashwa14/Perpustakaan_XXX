<?php
session_start();
require_once 'config/database.php';
if (!isset($_GET['id'])) header("Location: index.php");
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
$stmt->execute([$id]);
$book = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pinjam'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
    $user_id = $_SESSION['user_id'];
    $durasi = $_POST['durasi'];
    if ($book['stok'] > 0) {
        $sql = "INSERT INTO borrows (user_id, book_id, durasi_hari, status) VALUES (?, ?, ?, 'Pending')";
        $pdo->prepare($sql)->execute([$user_id, $id, $durasi]);
        echo "<script>
            alert('Permintaan peminjaman berhasil dikirim! Tunggu persetujuan Admin.');
            window.location='riwayat.php';
        </script>";
    } else {
        echo "<script>alert('Maaf, stok buku sedang habis.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($book['judul']) ?> - Perpustakaan Yogakarta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>    
    <?php include 'includes/navbar_user.php'; ?>
    <div class="container my-5">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Katalog</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($book['judul']) ?></li>
            </ol>
        </nav>

        <div class="book-detail fade-in-up">
            <div>
                <img src="assets/uploads/<?= htmlspecialchars($book['gambar']) ?>" 
                     alt="<?= htmlspecialchars($book['judul']) ?>"
                     class="book-detail-img">
            </div>
            <div class="book-info">
                <h1><?= htmlspecialchars($book['judul']) ?></h1>
                <h3>
                    <i class="bi bi-person-circle me-2"></i>
                    <?= htmlspecialchars($book['penulis']) ?>
                </h3>                
                <div class="info-row">
                    <i class="bi bi-tag-fill text-secondary me-2"></i>
                    <strong>Kategori:</strong> 
                    <span class="badge bg-secondary ms-2"><?= htmlspecialchars($book['kategori']) ?></span>
                </div>
                <div class="info-row">
                    <i class="bi bi-box-seam text-primary me-2"></i>
                    <strong>Ketersediaan:</strong> 
                    <?php if($book['stok'] > 0): ?>
                        <span class="status-badge status-available ms-2">
                            <i class="bi bi-check-circle me-1"></i>
                            Tersedia (<?= $book['stok'] ?> eksemplar)
                        </span>
                    <?php else: ?>
                        <span class="status-badge status-unavailable ms-2">
                            <i class="bi bi-x-circle me-1"></i>
                            Stok Habis
                        </span>
                    <?php endif; ?>
                </div>
                <hr class="my-4">               
                <h4 class="mb-3">
                    <i class="bi bi-text-paragraph me-2"></i>
                    Deskripsi
                </h4>
                <p style="text-align: justify; line-height: 1.8;">
                    <?= nl2br(htmlspecialchars($book['deskripsi'])) ?>
                </p>
                <hr class="my-4">                

                <?php if ($book['stok'] > 0): ?>
                    <div class="card border-primary">
                        <div class="card-body">
                            <h4 class="card-title mb-4">
                                <i class="bi bi-calendar-check me-2"></i>
                                Form Peminjaman
                            </h4>
                            <form method="POST">
                                <div class="form-group">
                                    <label>
                                        <i class="bi bi-clock me-1"></i>
                                        Durasi Peminjaman
                                    </label>
                                    <select name="durasi" class="form-control" required>
                                        <option value="3">3 Hari</option>
                                        <option value="7" selected>7 Hari</option>
                                        <option value="14">14 Hari</option>
                                    </select>
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Pilih durasi peminjaman yang sesuai dengan kebutuhan Anda
                                    </small>
                                </div>
                                <?php if(isset($_SESSION['user_id'])): ?>
                                    <button type="submit" name="pinjam" class="btn btn-primary w-100 mt-3">
                                        <i class="bi bi-send me-2"></i>
                                        Ajukan Peminjaman
                                    </button>
                                <?php else: ?>
                                    <a href="login.php" class="btn btn-danger w-100 mt-3">
                                        <i class="bi bi-lock me-2"></i>
                                        Login untuk Meminjam
                                    </a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle me-3 fs-4"></i>
                        <div>
                            <strong>Stok Tidak Tersedia</strong><br>
                            Buku ini sedang tidak tersedia. Silakan coba lagi nanti atau pilih buku lain.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Kembali ke Katalog
            </a>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>