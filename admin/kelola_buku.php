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

$list_buku = $pdo->query("SELECT * FROM books ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Buku - Perpustakaan Yogakarta</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- CSS ADMIN -->
    <link rel="stylesheet" href="../assets/css/style_admin.css">
</head>

<body>
    <?php include '../includes/navbar_admin.php'; ?>
    <div class="container my-3">
        <!-- Page Header -->
        <div class="page-header fade-in-up">
            <h1>
                <i class="bi bi-book-fill me-3"></i>
                Kelola Koleksi Buku
            </h1>
        </div>

        <?php if (isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show fade-in-up">
                <i class="bi bi-check-circle me-2"></i>
                <?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Add Book Form -->
        <div class="card mb-4 fade-in-up">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-plus-circle me-2"></i>
                    Tambah Buku Baru
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>
                                    <i class="bi bi-bookmark me-1"></i>
                                    Judul Buku
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

                    <button type="submit" name="tambah" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>
                        Simpan Buku
                    </button>
                </form>
            </div>
        </div>

        <!-- Books List -->
        <div class="card fade-in-up">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul me-2"></i>
                        Daftar Koleksi (<?= count($list_buku) ?> Buku)
                    </h5>
                    <span class="badge bg-secondary"><?= array_sum(array_column($list_buku, 'stok')) ?> Total Stok</span>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (count($list_buku) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="10%">Cover</th>
                                    <th width="30%">Judul & Penulis</th>
                                    <th width="15%">Kategori</th>
                                    <th width="10%">Stok</th>
                                    <th width="15%">Ditambahkan</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                foreach ($list_buku as $row):
                                ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <img src="../assets/uploads/<?= htmlspecialchars($row['gambar']) ?>"
                                                class="rounded"
                                                style="width: 50px; height: 70px; object-fit: cover;">
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($row['judul']) ?></strong><br>
                                            <small class="text-muted">
                                                <i class="bi bi-person me-1"></i>
                                                <?= htmlspecialchars($row['penulis']) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <?= htmlspecialchars($row['kategori']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($row['stok'] > 0): ?>
                                                <span class="badge bg-success"><?= $row['stok'] ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Habis</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?= date('d M Y', strtotime($row['created_at'])) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="edit_buku.php?id=<?= $row['id']; ?>" class="btn-edit">
                                                    <i class="bi bi-pencil me-1"></i>Edit
                                                </a>
                                                <a href="?hapus=<?= $row['id']; ?>" 
                                                   class="btn-delete"
                                                   onclick="return confirm('Yakin ingin menghapus buku <?= htmlspecialchars($row['judul']) ?>?')">
                                                    <i class="bi bi-trash me-1"></i>Hapus
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
                        <h5 class="mt-3 text-muted">Belum ada buku</h5>
                        <p class="text-muted">Tambahkan buku pertama Anda di atas</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>