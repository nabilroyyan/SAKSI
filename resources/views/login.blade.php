<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Login - SMKN 1 Sumenep</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="SMKN 1 Sumenep Login System" name="description" />
    <link rel="shortcut icon" href="{{ asset('') }}assets/images/logo-smk1.png">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary: #0A3981;
            --secondary: #1da9b4;
            --accent: #f59e0b;
            --light: #f8fafc;
            --dark: #0f172a;
            --gray: #64748b;
            --light-gray: #e2e8f0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f7fa;
            color: var(--dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px 0;
        }

        .auth-container {
            max-width: 1200px;
            margin: 0 auto;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            background-color: white;
        }

        /* Left Side - School Info */
        .auth-side {
            background: linear-gradient(135deg, var(--primary) 0%, #1e3a8a 100%);
            color: white;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
            min-height: 400px;
        }

        .auth-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiPjxkZWZzPjxwYXR0ZXJuIGlkPSJwYXR0ZXJuIiB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHBhdHRlcm5Vbml0cz0idXNlclNwYWNlT25Vc2UiIHBhdHRlcm5UcmFuc2Zvcm09InJvdGF0ZSg0NSkiPjxyZWN0IHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCIgZmlsbD0icmdiYSgyNTUsMjU1LDI1NSwwLjA1KSIvPjwvcGF0dGVybj48L2RlZnM+PHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0idXJsKCNwYXR0ZXJuKSIvPjwvc3ZnPg==');
            opacity: 0.3;
        }

        .auth-side-content {
            position: relative;
            z-index: 1;
        }

        .school-logo {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .school-logo img {
            width: 40px;
            margin-right: 12px;
        }

        .school-logo-text {
            font-weight: 700;
            font-size: 1.3rem;
        }

        .auth-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .auth-subtitle {
            font-size: 0.95rem;
            opacity: 0.9;
            margin-bottom: 1.5rem;
        }

        .auth-features {
            margin-top: 1.5rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.8rem;
        }

        .feature-icon {
            width: 36px;
            height: 36px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .feature-text {
            font-size: 0.9rem;
        }

        /* Right Side - Login Form */
        .auth-form {
            padding: 2rem;
        }

        .form-header {
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .form-logo {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 0.8rem;
        }

        .form-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .form-subtitle {
            color: var(--gray);
            font-size: 0.9rem;
        }

        .form-control {
            height: 46px;
            border-radius: 8px;
            border: 1px solid var(--light-gray);
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(10, 57, 129, 0.15);
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--dark);
            font-size: 0.95rem;
        }

        .form-text {
            font-size: 0.82rem;
            color: var(--gray);
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            padding: 0.55rem 1.3rem;
            font-weight: 500;
            font-size: 0.95rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #092e6b;
            border-color: #092e6b;
            transform: translateY(-2px);
        }

        .btn-outline-primary {
            border-radius: 8px;
            padding: 0.55rem 1.3rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            transform: translateY(-2px);
        }

        .alert {
            border-radius: 8px;
            font-size: 0.9rem;
            padding: 0.75rem 1rem;
        }

        .invalid-feedback {
            font-size: 0.82rem;
        }

        .input-group-text {
            background-color: var(--light-gray);
            border-color: var(--light-gray);
        }

        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        /* User Type Selector */
        .user-type-selector {
            display: flex;
            justify-content: center;
            margin-bottom: 1.5rem;
            border-radius: 8px;
            overflow: hidden;
            background-color: var(--light-gray);
            padding: 4px;
        }

        .user-type-btn {
            flex: 1;
            text-align: center;
            padding: 0.5rem;
            border: none;
            background: transparent;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .user-type-btn.active {
            background-color: white;
            color: var(--primary);
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Responsive Adjustments */
        @media (min-width: 768px) {
            .auth-side {
                padding: 3rem;
                min-height: auto;
            }
            
            .auth-form {
                padding: 3rem;
            }
            
            .school-logo img {
                width: 50px;
            }
            
            .school-logo-text {
                font-size: 1.5rem;
            }
            
            .auth-title {
                font-size: 2rem;
            }
            
            .auth-subtitle {
                font-size: 1rem;
            }
            
            .form-logo {
                font-size: 1.8rem;
            }
            
            .form-title {
                font-size: 1.5rem;
            }
        }

        @media (min-width: 992px) {
            .auth-container {
                display: flex;
            }
            
            .auth-side {
                width: 40%;
            }
            
            .auth-form {
                width: 60%;
            }
        }

        @media (max-width: 575.98px) {
            body {
                padding: 10px 0;
            }
            
            .auth-container {
                border-radius: 10px;
            }
            
            .auth-side, .auth-form {
                padding: 1.5rem;
            }
            
            .school-logo {
                margin-bottom: 1rem;
            }
            
            .auth-title {
                font-size: 1.3rem;
            }
            
            .form-logo {
                font-size: 1.3rem;
                margin-bottom: 0.5rem;
            }
            
            .form-title {
                font-size: 1.1rem;
            }
            
            .btn-primary, .btn-outline-primary {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid px-3">
        <div class="row justify-content-center mx-0">
            <div class="col-12 p-0">
                <div class="auth-container">
                    <!-- Left Side - School Info -->
                    <div class="auth-side">
                        <div class="auth-side-content">
                            <div class="school-logo">
                                <img src="{{ asset('assets/images/logo-smk1.png') }}" alt="SMKN 1 Sumenep Logo">
                                <div class="school-logo-text">SMKN 1 SUMENEP</div>
                            </div>
                            <h1 class="auth-title">Sistem Login Terpadu</h1>
                            <p class="auth-subtitle">Selamat datang di website Absensi dan Pelanggaran SMKN 1 Sumenep</p>
                            
  
                        </div>
                    </div>
                    
                    <!-- Right Side - Login Form -->
                    <div class="auth-form">
                        <div class="form-header">
                            <div class="form-logo">SMKN 1 SUMENEP</div>
                            <h2 class="form-title">Masuk ke Akun Anda</h2>
                            <p class="form-subtitle">Silakan masukkan email dan kata sandi Anda</p>
                        </div>
                        
                        <!-- Alert Messages -->
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        
                     
                        
                        <form method="POST" action="/login_action" class="forms-sample">
                            @csrf
                            <input type="hidden" name="user_type" id="user_type" value="student">
                            
                            <!-- Email Field -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Alamat Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           name="email" id="email" placeholder="contoh@email.com" 
                                           value="{{ old('email') }}" required>
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Password Field -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Kata Sandi</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           name="password" id="password" placeholder="Masukkan kata sandi" required>
                                    <button class="input-group-text toggle-password" type="button">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Submit Buttons -->
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                                <button type="submit" class="btn btn-primary order-md-1">
                                    <i class="fas fa-sign-in-alt me-2"></i> Masuk
                                </button>
                            </div>
                            

                           
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Script -->
    <script>
        // Auto focus on first input field
        document.addEventListener('DOMContentLoaded', function() {
            const firstInput = document.querySelector('input');
            if (firstInput) {
                firstInput.focus();
            }
            
            // Toggle password visibility
            const togglePassword = document.querySelector('.toggle-password');
            const password = document.getElementById('password');
            
            if (togglePassword && password) {
                togglePassword.addEventListener('click', function() {
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
                });
            }
            
            // User type selector functionality (if enabled)
            const userTypeBtns = document.querySelectorAll('.user-type-btn');
            const userTypeInput = document.getElementById('user_type');
            
            if (userTypeBtns.length && userTypeInput) {
                userTypeBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        userTypeBtns.forEach(b => b.classList.remove('active'));
                        this.classList.add('active');
                        userTypeInput.value = this.dataset.type;
                    });
                });
            }
        });
    </script>
</body>
</html>