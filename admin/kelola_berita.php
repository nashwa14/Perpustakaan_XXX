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

$berita = $pdo->query("SELECT * FROM news ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Berita - Perpustakaan Yogakarta</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- CSS ADMIN -->
    <link rel="stylesheet" href="../assets/css/style_admin.css">
</head>
<body>
    <?php include '../includes/navbar_admin.php'; ?>

    <div class="container my-5">
        
        <!-- Page Header -->
        <div class="page-header fade-in-up">
            <h1>
                <i class="bi bi-newspaper me-3"></i>
                Kelola Informasi & Berita
            </h1>
            <p>Publikasikan berita dan agenda kegiatan perpustakaan</p>
        </div>

        <?php if(isset($success)): ?>
        <div class="alert alert-success alert-dismissible fade show fade-in-up">
            <i class="bi bi-check-circle me-2"></i>
            <?= $success ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Add News Form -->
        <div class="card mb-4 fade-in-up">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="bi bi-plus-circle me-2"></i>
                    Publikasikan Berita Baru
                </h5>
            </div>
            <div class="card-body p-4">
                <form method="POST">
                    <div class="form-group">
                        <label>
                            <i class="bi bi-bookmark me-1"></i>
                            Judul Berita / Agenda
                        </label>
                        <input type="text" 
                               name="judul" 
                               class="form-control" 
                               placeholder="Contoh: Koleksi Buku Baru Bulan Ini"
                               required>
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Judul yang menarik akan meningkatkan minat baca
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <i class="bi bi-text-paragraph me-1"></i>
                            Isi Berita / Informasi
                        </label>
                        <textarea name="isi" 
                                  class="form-control" 
                                  rows="6" 
                                  placeholder="Tulis informasi lengkap di sini..."
                                  required></textarea>
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Gunakan Enter untuk membuat paragraf baru
                        </small>
                    </div>
                    
                    <button type="submit" name="tambah_berita" class="btn btn-success">
                        <i class="bi bi-send me-2"></i>
                        Terbitkan Sekarang
                    </button>
                </form>
            </div>
        </div>

        <!-- News List -->
        <div class="card fade-in-up">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="bi bi-list-ul me-2"></i>
                    Berita yang Diterbitkan (<?= count($berita) ?>)
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if(count($berita) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Tanggal</th>
                                <th width="30%">Judul</th>
                                <th width="40%">Isi Singkat</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach($berita as $row): 
                            $isi_singkat = substr($row['isi_berita'], 0, 100);
                            if(strlen($row['isi_berita']) > 100) $isi_singkat .= '...';
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        <?= date('d M Y', strtotime($row['tanggal'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($row['judul']) ?></strong>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= htmlspecialchars($isi_singkat) ?>
                                    </small>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info me-1" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#previewModal<?= $row['id'] ?>">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <a href="?hapus=<?= $row['id'] ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Yakin ingin menghapus berita ini?')">
                                        <i class="bi bi-trash"></i>
                                    </a>

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
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 4rem; color: var(--gray-30);"></i>
                    <h5 class="mt-3 text-muted">Belum ada berita</h5>
                    <p class="text-muted">Publikasikan berita pertama Anda di atas</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tips -->
        <div class="card mt-4 fade-in-up border-primary">
            <div class="card-body">
                <h6 class="mb-3">
                    <i class="bi bi-lightbulb-fill text-warning me-2"></i>
                    Tips Menulis Berita
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <ul class="mb-0">
                            <li>Gunakan judul yang jelas dan menarik perhatian</li>
                            <li>Tulis dengan bahasa yang mudah dipahami</li>
                            <li>Sertakan informasi lengkap (5W+1H)</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="mb-0">
                            <li>Periksa kembali sebelum publikasi</li>
                            <li>Update berita secara berkala</li>
                            <li>Sertakan tanggal/waktu jika ada agenda</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>