<?php
session_start();
require_once 'config/database.php';

$berita = $pdo->query("SELECT * FROM news ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita & Agenda - Perpustakaan Yogakarta</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar_user.php'; ?>

    <div class="container my-5">
        
        <!-- Page Header -->
        <div class="page-header fade-in-up">
            <h1>
                <i class="bi bi-newspaper me-3"></i>
                Informasi & Kegiatan Terkini
            </h1>
            <p>Dapatkan berita terbaru dan agenda kegiatan perpustakaan</p>
        </div>

        <?php if(count($berita) > 0): ?>
            <div class="row g-4">
                <?php foreach($berita as $row): ?>
                <div class="col-md-12 fade-in-up">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h2 class="card-title mb-0">
                                    <i class="bi bi-pin-angle-fill text-primary me-2"></i>
                                    <?= htmlspecialchars($row['judul']) ?>
                                </h2>
                                <span class="badge bg-secondary">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    <?= date('d M Y', strtotime($row['tanggal'])) ?>
                                </span>
                            </div>
                            <hr>
                            <p class="card-text" style="line-height: 1.8; text-align: justify;">
                                <?= nl2br(htmlspecialchars($row['isi_berita'])) ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state fade-in-up">
                <div class="empty-state-icon">
                    <i class="bi bi-newspaper"></i>
                </div>
                <h3>Belum Ada Berita</h3>
                <p class="text-muted">Informasi terbaru akan segera ditampilkan di sini.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>