<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIMAKU</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/favicon.png') ?>">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 50%, #2563eb 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }
        
        /* Animated Background Icons */
        .bg-icons {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }
        
        .floating-icon {
            position: absolute;
            color: rgba(255, 255, 255, 0.1);
            font-size: 40px;
            animation: float-up 20s linear infinite;
        }
        
        .floating-icon:nth-child(1) { left: 10%; animation-delay: 0s; font-size: 50px; }
        .floating-icon:nth-child(2) { left: 20%; animation-delay: 2s; font-size: 35px; }
        .floating-icon:nth-child(3) { left: 30%; animation-delay: 4s; font-size: 45px; }
        .floating-icon:nth-child(4) { left: 40%; animation-delay: 1s; font-size: 38px; }
        .floating-icon:nth-child(5) { left: 50%; animation-delay: 5s; font-size: 42px; }
        .floating-icon:nth-child(6) { left: 60%; animation-delay: 3s; font-size: 48px; }
        .floating-icon:nth-child(7) { left: 70%; animation-delay: 6s; font-size: 36px; }
        .floating-icon:nth-child(8) { left: 80%; animation-delay: 2.5s; font-size: 44px; }
        .floating-icon:nth-child(9) { left: 90%; animation-delay: 4.5s; font-size: 40px; }
        
        @keyframes float-up {
            0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-100vh) rotate(360deg); opacity: 0; }
        }
        
        /* Login Container */
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 440px;
            padding: 50px 40px;
            position: relative;
            z-index: 10;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        /* Login Header */
        .login-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 35px;
        }
        
        .logo-wrapper {
            width: 80px;
            height: 80px;
            background: #ffffff;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        
        .logo-wrapper img {
            width: 60%;
            height: auto;
            display: block;
        }
        
        .login-header h1 {
            color: #1f2937;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .login-header p {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.5;
        }
        
        /* Alert */
        .alert {
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        
        .alert i {
            font-size: 18px;
            flex-shrink: 0;
        }
        
        .alert-error {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }
        
        .alert-error i { color: #ef4444; }
        
        .alert-success {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            color: #166534;
            border-left: 4px solid #22c55e;
        }
        
        .alert-success i { color: #22c55e; }
        
        /* Form Group */
        .form-group {
            margin-bottom: 24px;
            position: relative;
        }
        
        .form-group label {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 10px;
            color: #374151;
            font-weight: 600;
            font-size: 14px;
        }
        
        .form-group label i {
            font-size: 12px;
            color: #14b8a6;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 16px;
            transition: color 0.3s;
        }
        
        .form-group input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #ffffff;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #14b8a6;
            box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.1);
        }
        
        .form-group input:focus + .input-icon {
            color: #14b8a6;
        }
        
        /* Password Toggle */
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            cursor: pointer;
            font-size: 16px;
            transition: color 0.3s;
        }
        
        .password-toggle:hover { color: #14b8a6; }
        
        /* Button Login */
        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(20, 184, 166, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }
        
        .btn-login:hover::before { left: 100%; }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(20, 184, 166, 0.4);
        }
        
        .btn-login:active { transform: translateY(0); }
        
        .btn-login:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .btn-login i { font-size: 16px; }
        
        /* Loading Spinner */
        .spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            display: none;
        }
        
        @keyframes spin { to { transform: rotate(360deg); } }
        
        .btn-login.loading .spinner { display: block; }
        .btn-login.loading .btn-text { display: none; }
        
        /* Login Footer */
        .login-footer {
            margin-top: 28px;
            text-align: center;
            color: #6b7280;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        
        .login-footer i {
            font-size: 12px;
            color: #14b8a6;
        }
        
        /* Mobile Responsive */
        @media (max-width: 480px) {
            .login-container {
                padding: 40px 30px;
                border-radius: 16px;
            }
            
            .logo-wrapper {
                width: 70px;
                height: 70px;
                margin-bottom: 16px;
            }
            
            .login-header h1 { font-size: 28px; }
            .login-header p { font-size: 13px; }
            
            .form-group input {
                padding: 13px 16px 13px 44px;
                font-size: 14px;
            }
            
            .btn-login {
                padding: 14px;
                font-size: 15px;
            }
            
            .floating-icon { font-size: 30px !important; }
        }
    </style>
</head>
<body>
    <!-- Animated Background Icons -->
    <div class="bg-icons">
        <i class="floating-icon fa-solid fa-wallet"></i>
        <i class="floating-icon fa-solid fa-coins"></i>
        <i class="floating-icon fa-solid fa-money-bill-wave"></i>
        <i class="floating-icon fa-solid fa-hand-holding-dollar"></i>
        <i class="floating-icon fa-solid fa-file-invoice-dollar"></i>
        <i class="floating-icon fa-solid fa-credit-card"></i>
        <i class="floating-icon fa-solid fa-piggy-bank"></i>
        <i class="floating-icon fa-solid fa-chart-line"></i>
        <i class="floating-icon fa-solid fa-receipt"></i>
    </div>
    
    <!-- Login Container -->
    <div class="login-container">
        <div class="login-header">
            <div class="logo-wrapper">
                <img src="<?= base_url('assets/img/logoavatar.png') ?>" alt="Logo SMPIT Wahdatul Ummah">
            </div>
            <h1>LOGIN ADMIN</h1>
            <p>Sistem Informasi Manajemen<br>Keuangan</p>
        </div>
        
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span><?= session()->getFlashdata('error') ?></span>
            </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <span><?= session()->getFlashdata('success') ?></span>
            </div>
        <?php endif; ?>
        
        <form action="<?= base_url('admin/login') ?>" method="POST" id="loginForm">
            <div class="form-group">
                <label for="username">
                    <i class="fa-solid fa-user"></i>
                    Username
                </label>
                <div class="input-wrapper">
                    <input type="text" 
                           id="username" 
                           name="username" 
                           placeholder="Masukkan username"
                           required 
                           autofocus
                           autocomplete="username">
                    <i class="input-icon fa-solid fa-user"></i>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">
                    <i class="fa-solid fa-lock"></i>
                    Password
                </label>
                <div class="input-wrapper">
                    <input type="password" 
                           id="password" 
                           name="password" 
                           placeholder="Masukkan password"
                           required
                           autocomplete="current-password">
                    <i class="input-icon fa-solid fa-lock"></i>
                    <i class="password-toggle fa-solid fa-eye" id="togglePassword"></i>
                </div>
            </div>
            
            <button type="submit" class="btn-login" id="btnLogin">
                <span class="btn-text">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Login
                </span>
                <span class="spinner"></span>
            </button>
        </form>
        
        <div class="login-footer">
            <i class="fa-solid fa-copyright"></i>
            <span><?= date('Y') ?> SMPIT Wahdhatul Ummah</span>
        </div>
    </div>
    
    <script>
        // Password Toggle
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        // Show loading state on submit
        const loginForm = document.getElementById('loginForm');
        const btnLogin = document.getElementById('btnLogin');

        loginForm.addEventListener('submit', function() {
            btnLogin.classList.add('loading');
            btnLogin.disabled = true;
        });

        // Reset button jika kembali ke halaman (misal: back button)
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                btnLogin.classList.remove('loading');
                btnLogin.disabled = false;
            }
        });

        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>