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
    <title>Login - Perpustakaan XXX</title>
    
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
        
        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }
        
        .login-card {
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            overflow: hidden;
            animation: fadeInUp 0.6s ease;
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--space-cadet) 0%, var(--slate-gray) 100%);
            color: var(--white);
            padding: 2.5rem 2rem;
            text-align: center;
        }
        
        .login-header img {
            height: 60px;
            margin-bottom: 1rem;
            filter: brightness(1.2);
        }
        
        .login-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            margin: 0;
            opacity: 0.9;
        }
        
        .login-body {
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
    </style>
</head>
<body>
    
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <img src="assets/uploads/logo.png" alt="Logo" onerror="this.style.display='none'">
                <h2>Selamat Datang</h2>
                <p>Login ke Perpustakaan XXX</p>
            </div>
            
            <div class="login-body">
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
                
                <div class="divider">
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
                    <a href="index.php" class="text-decoration-none text-muted">
                        <i class="bi bi-arrow-left me-1"></i>
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <p class="text-white-50 mb-0">
                &copy; <?= date('Y') ?> Perpustakaan XXX
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