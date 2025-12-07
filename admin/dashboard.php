<?php
session_start();
require_once '../config/database.php';
if ($_SESSION['role'] != 'admin') header("Location: ../index.php");
if (isset($_GET['act']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $status = $_GET['act'] == 'approve' ? 'Disetujui' : 'Ditolak';
    $pdo->prepare("UPDATE borrows SET status = ? WHERE id = ?")->execute([$status, $id]);

    if ($status == 'Disetujui') {
        $stmt = $pdo->prepare("SELECT book_id FROM borrows WHERE id = ?");
        $stmt->execute([$id]);
        $book_id = $stmt->fetchColumn();
        $pdo->prepare("UPDATE books SET stok = stok - 1 WHERE id = ?")->execute([$book_id]);
    }
    header("Location: dashboard.php");
}
$pendings = $pdo->query("SELECT p.id, u.nama, b.judul, p.durasi_hari, p.tanggal_pinjam FROM borrows p JOIN users u ON p.user_id = u.id JOIN books b ON p.book_id = b.id WHERE p.status = 'Pending' ORDER BY p.tanggal_pinjam DESC")->fetchAll();

// Statistik utama
$total_buku = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
$total_anggota = $pdo->query("SELECT COUNT(*) FROM users WHERE role='anggota'")->fetchColumn();
$total_pinjam = $pdo->query("SELECT COUNT(*) FROM borrows WHERE status='Disetujui'")->fetchColumn();
$total_pending = $pdo->query("SELECT COUNT(*) FROM borrows WHERE status='Pending'")->fetchColumn();

// Statistik bulan ini
$bulan_ini = date('Y-m');

$buku_bulan_ini = $pdo->query("SELECT COUNT(*) FROM books WHERE DATE_FORMAT(created_at, '%Y-%m') = '$bulan_ini'")->fetchColumn();
$anggota_bulan_ini = $pdo->query("SELECT COUNT(*) FROM users WHERE role='anggota' AND DATE_FORMAT(created_at, '%Y-%m') = '$bulan_ini'")->fetchColumn();
$pinjam_bulan_ini = $pdo->query("SELECT COUNT(*) FROM borrows WHERE status='Disetujui' AND DATE_FORMAT(tanggal_pinjam, '%Y-%m') = '$bulan_ini'")->fetchColumn();

// Buku terlambat dikembalikan
$total_overdue = $pdo->query("SELECT COUNT(*) FROM borrows WHERE status='Disetujui' AND DATE_ADD(tanggal_pinjam, INTERVAL durasi_hari DAY) < CURDATE()")->fetchColumn();
$overdue_bulan_ini = $pdo->query("SELECT COUNT(*) FROM borrows WHERE status='Disetujui' AND DATE_ADD(tanggal_pinjam, INTERVAL durasi_hari DAY) < CURDATE() AND DATE_FORMAT(tanggal_pinjam, '%Y-%m') = '$bulan_ini'")->fetchColumn();

// Data untuk grafik Loan Activity (6 bulan terakhir)
$loan_activity_data = [];
$loan_activity_labels = [];
for ($i = 5; $i >= 0; $i--) {
    $bulan = date('Y-m', strtotime("-$i month"));
    $bulan_label = date('M', strtotime("-$i month"));
    $count = $pdo->query("SELECT COUNT(*) FROM borrows WHERE DATE_FORMAT(tanggal_pinjam, '%Y-%m') = '$bulan'")->fetchColumn();
    $loan_activity_labels[] = $bulan_label;
    $loan_activity_data[] = $count;
}

// Data untuk grafik Book Category
$kategori_buku = $pdo->query("SELECT kategori, COUNT(*) as total FROM books GROUP BY kategori")->fetchAll();
$category_labels = [];
$category_data = [];
$total_all_books = array_sum(array_column($kategori_buku, 'total'));
foreach ($kategori_buku as $kat) {
    $category_labels[] = $kat['kategori'];
    $category_data[] = $kat['total'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Perpustakaan Yogakarta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style_admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

</head>

<body>
    <?php include '../includes/navbar_admin.php'; ?>
    <div class="container my-4">
        <!-- Welcome Header -->
        <div class="welcome-header fade-in-up">
            <h1 class="welcome-title">Selamat Datang Kembali, Admin!</h1>
            <p class="welcome-subtitle">Berikut adalah ringkasan status perpustakaan saat ini.</p>
        </div>

        <!-- Stats Cards -->
        <div class="stats-cards-container fade-in-up mb-4">
            <div class="metric-card">
                <div class="metric-header">
                    <span class="metric-label">Total Buku</span>
                </div>
                <h2 class="metric-value"><?= number_format($total_buku) ?></h2>
                <span class="metric-change metric-change-positive">
                    +<?= $buku_bulan_ini ?> buku bulan ini
                </span>
            </div>

            <div class="metric-card">
                <div class="metric-header">
                    <span class="metric-label">Total Anggota</span>
                </div>
                <h2 class="metric-value"><?= number_format($total_anggota) ?></h2>
                <span class="metric-change metric-change-positive">
                    +<?= $anggota_bulan_ini ?> anggota bulan ini
                </span>
            </div>

            <div class="metric-card">
                <div class="metric-header">
                    <span class="metric-label">Sedang Dipinjam</span>
                </div>
                <h2 class="metric-value"><?= number_format($total_pinjam) ?></h2>
                <span class="metric-change metric-change-info">
                    <?= $pinjam_bulan_ini ?> peminjaman bulan ini
                </span>
            </div>

            <div class="metric-card">
                <div class="metric-header">
                    <span class="metric-label">Buku Terlambat</span>
                </div>
                <h2 class="metric-value"><?= number_format($total_overdue) ?></h2>
                <span class="metric-change metric-change-negative">
                    +<?= $overdue_bulan_ini ?> keterlambatan bulan ini
                </span>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row g-3 fade-in-up mt-3">
            <!-- Loan Activity Chart -->
            <div class="col-6">
                <div class="chart-card">
                    <div class="chart-header">
                        <h5 class="chart-title">Aktivitas Peminjaman</h5>
                        <p class="chart-subtitle">6 Bulan Terakhir</p>
                    </div>
                    <div class="chart-body">
                        <canvas id="loanActivityChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Book Category Chart -->
            <div class="col-6">
                <div class="chart-card">
                    <div class="chart-header">
                        <h5 class="chart-title">Kategori Buku</h5>
                        <p class="chart-subtitle">Distribusi</p>
                    </div>
                    <div class="chart-body">
                        <canvas id="bookCategoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Loans Table -->
        <div class="dashboard-table-card fade-in-up mt-3">
            <div class="table-card-header">
                <div class="d-flex align-items-center">
                    <div class="table-icon-badge">
                        <i class="bi bi-bell-fill"></i>
                    </div>
                    <div>
                        <h4 class="table-card-title">Permintaan Peminjaman Baru</h4>
                        <p class="table-card-subtitle">Kelola permintaan peminjaman dari anggota</p>
                    </div>
                </div>
            </div>
            <div class="table-card-body">
                <?php if (count($pendings) > 0): ?>
                    <div class="table-responsive">
                        <table class="table dashboard-table">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="25%">Nama Peminjam</th>
                                    <th width="30%">Buku</th>
                                    <th width="15%">Tanggal</th>
                                    <th width="10%">Durasi</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                foreach ($pendings as $row):
                                ?>
                                    <tr>
                                        <td class="table-number"><?= $no++ ?></td>
                                        <td class="user-info"><?= htmlspecialchars($row['nama']) ?></td>
                                        <td class="book-title"><?= htmlspecialchars($row['judul']) ?></td>
                                        <td class="date-badge"><?= date('d M Y', strtotime($row['tanggal_pinjam'])) ?></td>
                                        <td>
                                            <span class="duration-badge">
                                                <?= $row['durasi_hari'] ?> Hari
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="?act=approve&id=<?= $row['id'] ?>"
                                                    class="action-btn action-btn-approve"
                                                    onclick="return confirm('Setujui peminjaman ini?')"
                                                    title="Setujui">
                                                    <i class="bi bi-check-circle-fill"></i>
                                                </a>
                                                <a href="?act=reject&id=<?= $row['id'] ?>"
                                                    class="action-btn action-btn-reject"
                                                    onclick="return confirm('Tolak peminjaman ini?')"
                                                    title="Tolak">
                                                    <i class="bi bi-x-circle-fill"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="bi bi-inbox"></i>
                        </div>
                        <h5 class="empty-title">Tidak ada permintaan pending</h5>
                        <p class="empty-subtitle">Semua permintaan sudah diproses</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Loan Activity Chart
        const loanActivityCtx = document.getElementById('loanActivityChart').getContext('2d');
        new Chart(loanActivityCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($loan_activity_labels) ?>,
                datasets: [{
                    label: 'Peminjaman',
                    data: <?= json_encode($loan_activity_data) ?>,
                    backgroundColor: '#D5B893',
                    borderRadius: 8,
                    barThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: '#9e9e9e',
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            color: '#f5f5f5',
                            drawBorder: false
                        }
                    },
                    x: {
                        ticks: {
                            color: '#9e9e9e',
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Book Category Chart
        const bookCategoryCtx = document.getElementById('bookCategoryChart').getContext('2d');
        new Chart(bookCategoryCtx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($category_labels) ?>,
                datasets: [{
                    data: <?= json_encode($category_data) ?>,
                    backgroundColor: [
                        '#6F4D38',
                        '#D5B893',
                        '#9e9e9e',
                        '#e0e0e0',
                        '#bdbdbd'
                    ],
                    borderWidth: 0,
                    cutout: '70%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.2,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                size: 11
                            },
                            color: '#757575'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>