<?php
session_start();
require_once '../config/database.php';

// Cek Role Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php"); exit;
}

// Cek apakah ada ID di URL
if (!isset($_GET['id'])) {
    header("Location: kelola_buku.php"); exit;
}

$id = $_GET['id'];

// Ambil data buku lama
$stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
$stmt->execute([$id]);
$buku = $stmt->fetch();

if (!$buku) {
    echo "<script>alert('Buku tidak ditemukan!'); window.location='kelola_buku.php';</script>"; exit;
}

// LOGIKA UPDATE
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
    <title>Edit Buku</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
   <?php include '../includes/navbar_admin.php'; ?>
    
    <div class="container">
        <h2>Edit Data Buku</h2>
        <div class="card" style="padding: 20px;">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Judul Buku</label>
                    <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($buku['judul']) ?>" required>
                </div>
                <div style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex:1">
                        <label>Penulis</label>
                        <input type="text" name="penulis" class="form-control" value="<?= htmlspecialchars($buku['penulis']) ?>" required>
                    </div>
                    <div class="form-group" style="flex:1">
                        <label>Kategori</label>
                        <input type="text" name="kategori" class="form-control" value="<?= htmlspecialchars($buku['kategori']) ?>">
                    </div>
                    <div class="form-group" style="width: 100px;">
                        <label>Stok</label>
                        <input type="number" name="stok" class="form-control" value="<?= $buku['stok'] ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($buku['deskripsi']) ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Ganti Cover (Biarkan kosong jika tidak ingin mengganti)</label>
                    <br>
                    <img src="../assets/uploads/<?= $buku['gambar'] ?>" style="width: 80px; margin-bottom: 10px; border: 1px solid #ddd;">
                    <input type="file" name="gambar" class="form-control">
                </div>
                
                <button type="submit" name="update" class="btn">Simpan Perubahan</button>
                <a href="kelola_buku.php" class="btn btn-danger">Batal</a>
            </form>
        </div>
    </div>
</body>
</html>