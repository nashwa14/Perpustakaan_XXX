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
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <div class="login-container">
        <!-- Left Side - Illustration -->
        <div class="login-left">
            <div class="decoration-circle circle-1"></div>
            <!-- <div class="decoration-circle circle-2"></div> -->
            <div class="decoration-circle circle-3"></div>
            
            <div class="illustration-wrapper">
                <div class="illustration-circle">
                    <div class="illustration-content">
                        <!-- Logo Perpustakaan -->
                        <div class="mascot-container">
                            <img src="assets/uploads/logo.png" alt="Logo Perpustakaan" class="mascot-img">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="login-right">
            <div class="login-form-wrapper">
                <a href="index.php" class="back-link">
                    <i class="bi bi-arrow-left"></i>
                    Kembali ke Beranda
                </a>

                <div class="form-header">
                    <div class="form-logo">
                        <img src="assets/uploads/logo.png" alt="Logo" onerror="this.parentElement.style.display='none'">
                    </div>
                    <h1 class="form-title">Selamat Datang!</h1>
                    <p class="form-subtitle">Masuk ke Perpustakaan Yogakarta</p>
                </div>

                <?php if(isset($_SESSION['logout_success'])): ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i>
                    <span>Anda telah berhasil logout. Silakan login kembali.</span>
                    <button type="button" class="btn-close" onclick="this.parentElement.remove()">×</button>
                </div>
                <?php 
                unset($_SESSION['logout_success']); 
                endif; 
                ?>

                <?php if(isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle"></i>
                    <span><?= $error ?></span>
                    <button type="button" class="btn-close" onclick="this.parentElement.remove()">×</button>
                </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Nama Pengguna</label>
                        <div class="input-wrapper">
                            <i class="bi bi-person"></i>
                            <input type="text" 
                                   name="username" 
                                   class="form-control" 
                                   placeholder="Masukkan nama pengguna"
                                   required 
                                   autofocus>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Kata Sandi</label>
                        <div class="input-wrapper">
                            <i class="bi bi-lock"></i>
                            <input type="password" 
                                   name="password" 
                                   id="password"
                                   class="form-control" 
                                   placeholder="Masukkan kata sandi"
                                   required>
                            <i class="bi bi-eye password-toggle" 
                               onclick="togglePassword()"
                               id="toggleIcon"></i>
                        </div>
                    </div>

                    <button type="submit" name="login" class="btn-login">
                        Masuk
                    </button>
                </form>

                <div class="register-link">
                    Belum punya akun? <a href="register.php">Daftar Anggota</a>
                </div>
            </div>
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