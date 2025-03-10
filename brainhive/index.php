<?php
include 'config.php';

if (!isset($_SESSION)) {
    session_start();
}

// Check if already logged in
if (isset($_SESSION['user_id'])) {
    $redirect = $_SESSION['role'] === 'teacher' ? 'teacher/dashboard.php' : 'student/dashboard.php';
    header("Location: $redirect");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>PSU BrainHive - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/loader.css">
    <style>
        :root {
            --psu-beige: <?= PSU_BEIGE ?>;
            --psu-yellow: <?= PSU_YELLOW ?>;
            --psu-blue: <?= PSU_BLUE ?>;
        }

        body {
            background: linear-gradient(rgba(210, 180, 140, 0.1), rgba(0, 87, 184, 0.1)),
                        url('<?= ASSETS_PATH ?>img/psu_background.jpg');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            position: relative;
        }

        .login-container {
            margin-top: 5vh;
            position: relative;
            z-index: 2;
        }

        .card {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transform: translateY(0);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: var(--psu-blue);
            border-radius: 20px 20px 0 0 !important;
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                45deg,
                transparent,
                rgba(255, 255, 255, 0.1),
                transparent
            );
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }

        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .form-control {
            padding: 1rem 1rem 1rem 3rem;
            border-radius: 15px;
            border: 2px solid #eee;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .form-control:focus {
            border-color: var(--psu-yellow);
            box-shadow: 0 0 0 0.25rem rgba(255, 215, 0, 0.25);
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            z-index: 10;
            transition: color 0.3s ease;
        }

        .form-control:focus + .input-icon {
            color: var(--psu-blue);
        }

        .btn-login {
            background: var(--psu-yellow);
            color: var(--psu-blue);
            font-weight: 600;
            padding: 1rem;
            border-radius: 15px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.4);
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .welcome-text {
            position: absolute;
            top: 2rem;
            left: 2rem;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
        }

        .social-links {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            display: flex;
            gap: 1rem;
            z-index: 2;
        }

        .social-link {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--psu-blue);
            transition: all 0.3s ease;
        }

        .social-link:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="loader-container">
        <div class="loader"></div>
        <div class="loader-text">Logging in...</div>
    </div>

    <div class="welcome-text">
        <h1>BrainHive</h1>
        <p>Pangasinan State University's Learning Platform</p>
    </div>

    <div id="particles-js" class="particles"></div>

    <div class="container login-container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header text-center">
                        <img src="<?= ASSETS_PATH ?>img/psu_logo.png" width="60" alt="PSU Logo" class="mb-3">
                        <h3 class="text-white mb-0">Engage, authenticate now.</h3>
                        <p class="text-white-50 mb-0">Sign in to continue</p>
                    </div>
                    <div class="card-body p-4">
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?= $_SESSION['error'] ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>

                        <form action="auth.php" method="POST" id="loginForm">
                            <input type="hidden" name="action" value="login">
                            <div class="input-group">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" name="email" class="form-control" 
                                       placeholder="PSU Email" autocomplete="off" required>
                            </div>
                            
                            <div class="input-group">
                                <i class="fas fa-lock input-icon"></i>
                                <div class="position-relative w-100">
                                    <input type="password" name="password" class="form-control" 
                                           placeholder="Password" required>
                                    <i class="fas fa-eye password-toggle" 
                                       style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; z-index: 10;"></i>
                                </div>
                            </div>

                            <div class="remember-me">
                                <input type="checkbox" id="remember" name="remember">
                                <label for="remember">Remember me</label>
                            </div>

                            <button type="submit" class="btn btn-login w-100 mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In
                            </button>

                            <div class="text-center mt-4">
                                <a href="forgot-password.php" class="text-decoration-none me-3">
                                    <i class="fas fa-key me-1"></i>Forgot Password?
                                </a>
                                <a href="signups/register-phase1.php" class="text-decoration-none">
                                    <i class="fas fa-user-plus me-1"></i>Create Account
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="social-links">
        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
    </div>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        particlesJS('particles-js', {
            particles: {
                number: { value: 80 },
                color: { value: '#ffffff' },
                shape: { type: 'circle' },
                opacity: { value: 0.5 },
                size: { value: 3 },
                move: {
                    enable: true,
                    speed: 2
                }
            }
        });

        // Enhanced form interactions
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.closest('.input-group').style.transform = 'scale(1.02)';
            });

            input.addEventListener('blur', function() {
                this.closest('.input-group').style.transform = 'scale(1)';
            });
        });

        document.querySelector('.password-toggle').addEventListener('click', function() {
            const input = this.previousElementSibling;
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const loader = document.querySelector('.loader-container');
            loader.style.display = 'flex';
            loader.offsetHeight;
            loader.classList.add('show');
            
            setTimeout(() => {
                this.submit();
            }, 3000);
        });
    </script>
</body>
</html>