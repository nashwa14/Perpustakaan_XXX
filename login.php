<?php
session_start();
require_once 'config/database.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['nama'] = $user['nama'];
        
        if ($user['role'] == 'admin') header("Location: admin/dashboard.php");
        else header("Location: index.php");
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Perpustakaan Yogakarta</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body class="auth-body">
    
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <img src="assets/uploads/logo.png" alt="Logo" onerror="this.style.display='none'">
                <h2>Selamat Datang</h2>
                <p>Login ke Perpustakaan Yogakarta</p>
            </div>
            
            <div class="auth-body-content">
                <?php if(isset($_SESSION['logout_success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle me-2"></i>
                    Anda telah berhasil logout. Silakan login kembali.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php 
                unset($_SESSION['logout_success']); 
                endif; 
                ?>
                
                <?php if(isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label>
                            <i class="bi bi-person me-1"></i>
                            Username
                        </label>
                        <div class="input-group-icon">
                            <i class="bi bi-person-circle"></i>
                            <input type="text" 
                                   name="username" 
                                   class="form-control" 
                                   placeholder="Masukkan username"
                                   required 
                                   autofocus>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <i class="bi bi-lock me-1"></i>
                            Password
                        </label>
                        <div class="input-group-icon">
                            <input type="password" 
                                   name="password" 
                                   id="password"
                                   class="form-control" 
                                   placeholder="Masukkan password"
                                   required>
                            <i class="bi bi-eye password-toggle" 
                               onclick="togglePassword()"
                               id="toggleIcon"></i>
                        </div>
                    </div>
                    
                    <button type="submit" name="login" class="btn btn-primary w-100 mt-4">
                        <i class="bi bi-box-arrow-in-right me-2"></i>
                        Masuk Sekarang
                    </button>
                </form>
                
                <div class="auth-divider">
                    <span>atau</span>
                </div>
                
                <div class="text-center">
                    <p class="mb-2">Belum punya akun?</p>
                    <a href="register.php" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-person-plus me-2"></i>
                        Daftar Sebagai Anggota
                    </a>
                </div>
                
                <div class="text-center mt-4">
                    <a href="index.php" class="auth-back-link">
                        <i class="bi bi-arrow-left me-1"></i>
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
        
        <div class="auth-footer">
            <p>&copy; <?= date('Y') ?> Perpustakaan Yogakarta</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
        }
    </script>
</body>
</html>