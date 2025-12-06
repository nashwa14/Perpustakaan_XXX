<?php
session_start();
require_once 'config/database.php';
if (!isset($_SESSION['user_id'])) header("Location: login.php");
$id = $_SESSION['user_id'];
$editMode = false;

if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    $pass = $_POST['password'];
    
    if (!empty($pass)) {
        $password = password_hash($pass, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET nama=?, password=? WHERE id=?";
        $pdo->prepare($sql)->execute([$nama, $password, $id]);
    } else {
        $sql = "UPDATE users SET nama=? WHERE id=?";
        $pdo->prepare($sql)->execute([$nama, $id]);
    }
    $_SESSION['nama'] = $nama;
    $success = "Profil berhasil diperbarui!";
}

if (isset($_GET['edit'])) {
    $editMode = true;
}
$user = $pdo->query("SELECT * FROM users WHERE id=$id")->fetch();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Perpustakaan Yogakarta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar_user.php'; ?>
    <div class="container my-5">
        <div class="page-header fade-in-up">
            <h1>
                <i class="bi bi-person-circle me-3"></i>
                Pengaturan Profil
            </h1>
            <p>Kelola informasi akun Anda</p>
        </div>

        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card fade-in-up">
                    <div class="card-body p-4">
                        
                        <?php if(isset($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="bi bi-check-circle me-2"></i>
                            <?= $success ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        <div class="text-center mb-4 pb-4 border-bottom">
                            <div class="mb-3">
                                <i class="bi bi-person-circle" style="font-size: 5rem; color: var(--slate-gray);"></i>
                            </div>
                            <h4 class="mb-1"><?= htmlspecialchars($user['nama']) ?></h4>
                            <p class="text-muted mb-0">
                                <i class="bi bi-at me-1"></i>
                                <?= htmlspecialchars($user['username']) ?>
                            </p>
                            <span class="badge bg-secondary mt-2">
                                <?= ucfirst($user['role']) ?>
                            </span>
                        </div>

                        <?php if (!$editMode): ?>
                        <h5 class="mb-4">
                            <i class="bi bi-info-circle me-2"></i>
                            Informasi Akun
                        </h5>
                        
                        <div class="mb-4">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>
                                        <i class="bi bi-person me-1"></i>
                                        Nama Lengkap
                                    </strong>
                                </div>
                                <div class="col-md-8">
                                    <?= htmlspecialchars($user['nama']) ?>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>
                                        <i class="bi bi-shield-lock me-1"></i>
                                        Username
                                    </strong>
                                </div>
                                <div class="col-md-8">
                                    <?= htmlspecialchars($user['username']) ?>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>
                                        <i class="bi bi-key me-1"></i>
                                        Password
                                    </strong>
                                </div>
                                <div class="col-md-8">
                                    ••••••••
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>
                                        <i class="bi bi-calendar-check me-1"></i>
                                        Bergabung Sejak
                                    </strong>
                                </div>
                                <div class="col-md-8">
                                    <?= date('d M Y', strtotime($user['created_at'])) ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <a href="profil.php?edit=true" class="btn btn-primary flex-fill">
                                <i class="bi bi-pencil-square me-2"></i>
                                Edit Profil
                            </a>
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>
                                Kembali
                            </a>
                        </div>
                        
                        <?php else: ?>
                        <h5 class="mb-4">
                            <i class="bi bi-pencil-square me-2"></i>
                            Edit Informasi
                        </h5>
                        
                        <form method="POST">
                            <div class="form-group">
                                <label>
                                    <i class="bi bi-person me-1"></i>
                                    Nama Lengkap
                                </label>
                                <input type="text" 
                                       name="nama" 
                                       class="form-control" 
                                       value="<?= htmlspecialchars($user['nama']) ?>" 
                                       required>
                            </div>
                            
                            <div class="form-group">
                                <label>
                                    <i class="bi bi-shield-lock me-1"></i>
                                    Username
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       value="<?= htmlspecialchars($user['username']) ?>" 
                                       disabled>
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Username tidak dapat diubah
                                </small>
                            </div>
                            
                            <div class="form-group">
                                <label>
                                    <i class="bi bi-key me-1"></i>
                                    Password Baru
                                </label>
                                <input type="password" 
                                       name="password" 
                                       class="form-control"
                                       placeholder="Kosongkan jika tidak ingin mengganti"
                                       minlength="6">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Minimal 6 karakter untuk keamanan yang lebih baik
                                </small>
                            </div>
                            
                            <div class="form-group">
                                <label>
                                    <i class="bi bi-calendar-check me-1"></i>
                                    Bergabung Sejak
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       value="<?= date('d M Y', strtotime($user['created_at'])) ?>" 
                                       disabled>
                            </div>
                            
                            <div class="d-flex gap-2 mt-4">
                                <button type="submit" name="update" class="btn btn-primary flex-fill">
                                    <i class="bi bi-save me-2"></i>
                                    Simpan Perubahan
                                </button>
                                <a href="profil.php" class="btn btn-danger">
                                    <i class="bi bi-x-circle me-2"></i>
                                    Batalkan
                                </a>
                            </div>
                        </form>
                        <?php endif; ?>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>