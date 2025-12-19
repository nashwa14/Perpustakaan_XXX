<?php
require_once 'config/database.php';

if (isset($_POST['register'])) {
    $nama = htmlspecialchars($_POST['nama']);
    $username = htmlspecialchars($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'anggota';

    $check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $check->execute([$username]);
    
    if ($check->rowCount() > 0) {
        $error = "Username sudah digunakan!";
    } else {
        $sql = "INSERT INTO users (nama, username, password, role) VALUES (?, ?, ?, ?)";
        if($pdo->prepare($sql)->execute([$nama, $username, $password, $role])) {
            echo "<script>alert('Registrasi Berhasil! Silakan Login.'); window.location='login.php';</script>";
        } else {
            $error = "Terjadi kesalahan sistem.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Perpustakaan Yogakarta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/register.css">
</head>
<body>
    <div class="register-container">
        <!-- Left Side - Illustration -->
        <div class="register-left">
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
        <div class="register-right">
            <div class="register-form-wrapper">

                <div class="form-header">
                    <div class="form-logo">
                        <img src="assets/uploads/logo.png" alt="Logo" onerror="this.parentElement.style.display='none'">
                    </div>
                    <h1 class="form-title">Selamat Datang!</h1>
                    <p class="form-subtitle">Daftar ke Perpustakaan Yogakarta</p>
                </div>

                <?php if(isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle"></i>
                    <span><?= $error ?></span>
                    <button type="button" class="btn-close" onclick="this.parentElement.remove()">×</button>
                </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <div class="input-wrapper">
                            <i class="bi bi-person"></i>
                            <input type="text" 
                                   name="nama" 
                                   class="form-control" 
                                   placeholder="Masukkan nama lengkap"
                                   required 
                                   autofocus>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Nama Pengguna</label>
                        <div class="input-wrapper">
                            <i class="bi bi-at"></i>
                            <input type="text" 
                                   name="username" 
                                   class="form-control" 
                                   placeholder="Masukkan nama pengguna"
                                   required>
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
                                   required
                                   minlength="6">
                            <i class="bi bi-eye password-toggle" 
                               onclick="togglePassword()"
                               id="toggleIcon"></i>
                        </div>
                    </div>
                    
                    <button type="submit" name="register" class="btn-register">
                        Daftar
                    </button>
                </form>

                <div class="login-link">
                    Sudah punya akun? <a href="login.php">Masuk</a>
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