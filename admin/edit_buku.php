<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}
if (!isset($_GET['id'])) {
    header("Location: kelola_buku.php");
    exit;
}
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
$stmt->execute([$id]);
$buku = $stmt->fetch();

if (!$buku) {
    echo "<script>alert('Buku tidak ditemukan!'); window.location='kelola_buku.php';</script>";
    exit;
}

if (isset($_POST['update'])) {
    $judul = $_POST['judul'];
    $penulis = $_POST['penulis'];
    $kategori = $_POST['kategori'];
    $deskripsi = $_POST['deskripsi'];
    $stok = $_POST['stok'];

    if (!empty($_FILES['gambar']['name'])) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . "." . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], "../assets/uploads/" . $filename);
        $sql = "UPDATE books SET judul=?, penulis=?, kategori=?, deskripsi=?, stok=?, gambar=? WHERE id=?";
        $pdo->prepare($sql)->execute([$judul, $penulis, $kategori, $deskripsi, $stok, $filename, $id]);
    } else {
        $sql = "UPDATE books SET judul=?, penulis=?, kategori=?, deskripsi=?, stok=? WHERE id=?";
        $pdo->prepare($sql)->execute([$judul, $penulis, $kategori, $deskripsi, $stok, $id]);
    }
    echo "<script>alert('Data Buku Berhasil Diperbarui!'); window.location='kelola_buku.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku - Perpustakaan Yogakarta</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style_admin.css">
</head>

<body>
    <?php include '../includes/navbar_admin.php'; ?>
    <div class="container my-4">
        <div class="page-header fade-in-up">
            <h1>
                <i class="bi bi-pencil-square me-3"></i>
                Edit Data Buku
            </h1>
            <p>Perbarui informasi buku yang sudah ada</p>
        </div>

        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="kelola_buku.php">Kelola Buku</a></li>
                <li class="breadcrumb-item active">Edit Buku</li>
            </ol>
        </nav>

        <div class="card fade-in-up">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="bi bi-pencil-fill me-2"></i>
                    Form Edit Buku
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="" method="POST" enctype="multipart/form-data">

                    <div class="form-group">
                        <label>
                            <i class="bi bi-bookmark me-1"></i>
                            Judul Buku
                        </label>
                        <input type="text"
                            name="judul"
                            class="form-control"
                            value="<?= htmlspecialchars($buku['judul']) ?>"
                            required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>
                                    <i class="bi bi-person me-1"></i>
                                    Penulis
                                </label>
                                <input type="text"
                                    name="penulis"
                                    class="form-control"
                                    value="<?= htmlspecialchars($buku['penulis']) ?>"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>
                                    <i class="bi bi-tag me-1"></i>
                                    Kategori
                                </label>
                                <input type="text"
                                    name="kategori"
                                    class="form-control"
                                    value="<?= htmlspecialchars($buku['kategori']) ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>
                                    <i class="bi bi-box-seam me-1"></i>
                                    Stok
                                </label>
                                <input type="number"
                                    name="stok"
                                    class="form-control"
                                    value="<?= $buku['stok'] ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="bi bi-text-paragraph me-1"></i>
                            Deskripsi
                        </label>
                        <textarea name="deskripsi"
                            class="form-control"
                            rows="4"><?= htmlspecialchars($buku['deskripsi']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="bi bi-image me-1"></i>
                            Ganti Cover Buku
                        </label>
                        <div class="mb-3">
                            <p class="text-muted mb-2">Cover saat ini:</p>
                            <img src="../assets/uploads/<?= $buku['gambar'] ?>"
                                style="width: 120px; height: 160px; object-fit: cover; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        </div>
                        <input type="file"
                            name="gambar"
                            class="form-control"
                            accept="image/*">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Biarkan kosong jika tidak ingin mengganti cover
                        </small>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" name="update" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>
                            Simpan Perubahan
                        </button>
                        <a href="kelola_buku.php" class="btn btn-danger">
                            <i class="bi bi-x-circle me-2"></i>
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>