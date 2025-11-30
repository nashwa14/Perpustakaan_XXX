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
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        body {
            background: linear-gradient(135deg, var(--space-cadet) 0%, var(--slate-gray) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .register-container {
            width: 100%;
            max-width: 500px;
            padding: 20px;
        }
        
        .register-card {
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            overflow: hidden;
            animation: fadeInUp 0.6s ease;
        }
        
        .register-header {
            background: linear-gradient(135deg, var(--space-cadet) 0%, var(--slate-gray) 100%);
            color: var(--white);
            padding: 2.5rem 2rem;
            text-align: center;
        }
        
        .register-header img {
            height: 60px;
            margin-bottom: 1rem;
            filter: brightness(1.2);
        }
        
        .register-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .register-body {
            padding: 2.5rem 2rem;
        }
        
        .input-group-icon {
            position: relative;
        }
        
        .input-group-icon i {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-40);
            z-index: 10;
        }
        
        .input-group-icon .form-control {
            padding-left: 3rem;
        }
        
        .password-toggle {
            position: absolute;
            right: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--gray-40);
            z-index: 10;
        }
        
        .password-toggle:hover {
            color: var(--slate-gray);
        }
        
        .divider {
            text-align: center;
            margin: 1.5rem 0;
            position: relative;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: var(--gray-20);
        }
        
        .divider span {
            background: var(--white);
            padding: 0 1rem;
            position: relative;
            color: var(--gray-60);
            font-size: 0.9rem;
        }

        .benefits {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }

        .benefit-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .benefit-item:last-child {
            margin-bottom: 0;
        }

        .benefit-item i {
            color: var(--success);
            margin-right: 0.75rem;
        }
    </style>
</head>
<body>
    
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <img src="assets/uploads/logo.png" alt="Logo" onerror="this.style.display='none'">
                <h2>Daftar Anggota</h2>
                <p>Bergabung dengan Perpustakaan Yogakarta</p>
            </div>
            
            <div class="register-body">
                
                <!-- Benefits -->
                <div class="benefits">
                    <h6 class="mb-3"><strong>Keuntungan Menjadi Anggota:</strong></h6>
                    <div class="benefit-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <small>Akses ke ribuan koleksi buku</small>
                    </div>
                    <div class="benefit-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <small>Peminjaman gratis tanpa biaya</small>
                    </div>
                    <div class="benefit-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <small>Peminjaman mudah tanpa perlu datang</small>
                    </div>
                </div>

                <?php if(isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label>
                            <i class="bi bi-person me-1"></i>
                            Nama Lengkap
                        </label>
                        <div class="input-group-icon">
                            <i class="bi bi-person-circle"></i>
                            <input type="text" 
                                   name="nama" 
                                   class="form-control" 
                                   placeholder="Masukkan nama lengkap"
                                   required 
                                   autofocus>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="bi bi-at me-1"></i>
                            Username
                        </label>
                        <div class="input-group-icon">
                            <i class="bi bi-shield-lock"></i>
                            <input type="text" 
                                   name="username" 
                                   class="form-control" 
                                   placeholder="Pilih username unik"
                                   required>
                        </div>
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Username akan digunakan untuk login
                        </small>
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
                                   placeholder="Buat password (min. 6 karakter)"
                                   required
                                   minlength="6">
                            <i class="bi bi-eye password-toggle" 
                               onclick="togglePassword()"
                               id="toggleIcon"></i>
                        </div>
                    </div>
                    
                    <button type="submit" name="register" class="btn btn-primary w-100 mt-4">
                        <i class="bi bi-person-plus me-2"></i>
                        Daftar Sekarang
                    </button>
                </form>
                
                <div class="divider">
                    <span>atau</span>
                </div>
                
                <div class="text-center">
                    <p class="mb-2">Sudah punya akun?</p>
                    <a href="login.php" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-box-arrow-in-right me-2"></i>
                        Login di Sini
                    </a>
                </div>
                
                <div class="text-center mt-4">
                    <a href="index.php" class="text-decoration-none text-muted">
                        <i class="bi bi-arrow-left me-1"></i>
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <p class="text-white-50 mb-0">
                &copy; <?= date('Y') ?> Perpustakaan Yogakarta
            </p>
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