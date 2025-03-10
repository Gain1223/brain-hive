<?php
include '../config.php';

if (!isset($_SESSION)) {
    session_start();
}

// Check phases completion
if (!isset($_SESSION['reg_account_type']) || 
    !isset($_SESSION['birthdate']) || 
    !isset($_SESSION['reg_username']) || 
    !isset($_SESSION['completed_phase3'])) {
    header('Location: register-phase1.php');
    exit;
}

// Initialize variables
$username = isset($_SESSION['reg_username']) ? $_SESSION['reg_username'] : '';
$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : '';

// Initialize error variable
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from form
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $username = $_SESSION['reg_username']; // Use username from phase 3
    
    try {
        // Check for existing user
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$email, $username]);
        
        if ($stmt->rowCount() > 0) {
            $error = "Email or username already registered!";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            
            // Insert using the correct fields
            $stmt = $pdo->prepare("INSERT INTO users (full_name, username, email, password, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$full_name, $username, $email, $hashedPassword, $_SESSION['reg_account_type']]);
            
            if ($stmt->rowCount() > 0) {
                $new_user_id = $pdo->lastInsertId();
                $pdo->prepare("INSERT INTO leaderboards (user_id, total_xp) VALUES (?, 0)")
                   ->execute([$new_user_id]);
                
                // Clear registration sessions
                unset($_SESSION['reg_account_type'], $_SESSION['birthdate'], 
                      $_SESSION['reg_username'], $_SESSION['completed_phase3'],
                      $_SESSION['full_name']);
                
                header("Location: ../index.php?registered=1");
                exit;
            }
        }
    } catch (PDOException $e) {
        $error = "Registration failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - PSU BrainHive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/loader.css">
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
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: white !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .container {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 1rem 0;
            min-height: calc(100vh - 76px); /* Adjusted for navbar */
        }

        .card {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            margin: auto;
        }

        .card-header {
            background: linear-gradient(135deg, var(--psu-blue), #003d82);
            color: white;
            padding: 1.5rem;
            text-align: center;
            border-bottom: none;
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

        .card-header h3 {
            margin: 0;
            font-size: 1.75rem;
            font-weight: 600;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .card-header p {
            margin: 0.5rem 0 0;
            font-size: 1rem;
            opacity: 0.9;
        }

        .card-body {
            padding: 1.5rem;
        }

        .form-control, .form-select {
            border-radius: 8px;
            padding: 12px;
            border: 1px solid #ddd;
            margin-bottom: 15px;
        }

        .btn-register {
            background: var(--psu-yellow);
            color: var(--psu-blue);
            border: none;
            padding: 12px;
            font-weight: bold;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-register:hover {
            background: var(--psu-blue);
            color: white;
            transform: translateY(-2px);
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .text-decoration-none {
            color: var(--psu-blue);
            transition: color 0.3s ease;
        }

        .text-decoration-none:hover {
            color: var(--psu-yellow);
        }

        .social-login {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-bottom: 1rem; /* Reduced margin */
        }

        .social-btn {
            padding: 10px 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .social-btn:hover {
            background-color: #f8f9fa;
        }

        .divider {
            text-align: center;
            margin: 1rem 0; /* Reduced margin */
            position: relative;
        }

        .divider:before,
        .divider:after {
            content: "";
            position: absolute;
            top: 50%;
            width: 45%;
            height: 1px;
            background-color: #ddd;
        }

        .divider:before {
            left: 0;
        }

        .divider:after {
            right: 0;
        }

        .form-group {
            position: relative;
            margin-bottom: 0.75rem;
        }

        .form-control {
            padding: 1rem 1rem 1rem 2.5rem;
            border-radius: 15px;
            border: 2px solid #eee;
            transition: all 0.3s ease;
            font-size: 1rem;
            height: calc(2.5rem + 2px); /* Consistent height */
            padding: 0.5rem 1rem 0.5rem 2.5rem; /* Adjusted padding */
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
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
            z-index: 10;
            transition: color 0.3s;
        }

        .password-toggle:hover {
            color: var(--psu-blue);
        }

        .card {
            margin-top: 2rem;
            margin-bottom: 2rem;
        }

        .card-body {
            padding: 2rem;
        }

        @keyframes shine {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }
    </style>
</head>
<body>
    <div class="loader-container">
        <div class="loader"></div>
        <div class="loader-text">Creating your account...</div>
    </div>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= BASE_URL ?>">
                <img src="<?= ASSETS_PATH ?>img/psu_logo.png" width="40" 
                     alt="PSU Logo"
                     onerror="this.style.display='none'">
                <span class="ms-2" style="color: var(--psu-blue);">PSU BrainHive</span>
            </a>
        </div>
    </nav>

    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    <?php endif; ?>

    <div class="container">
        <div class="row justify-content-center w-100">
            <div class="col-md-6 col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <img src="<?= ASSETS_PATH ?>img/psu_logo.png" width="60" alt="PSU Logo" class="mb-2">
                        <h3>Join BrainHive</h3>
                        <p>Where Knowledge Meets Adventure!</p>
                        <div class="header-accent"></div>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <div class="social-login">
                            <button type="button" class="social-btn">
                                <img src="https://img.icons8.com/color/24/000000/google-logo.png"/>
                                Google
                            </button>
                            <button type="button" class="social-btn">
                                <img src="https://img.icons8.com/color/24/000000/facebook-new.png"/>
                                Facebook
                            </button>
                        </div>
                        
                        <div class="divider">
                            <span>or</span>
                        </div>

                        <form action="register.php" method="POST" autocomplete="off">
                            <div class="form-group">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" 
                                       name="full_name" 
                                       class="form-control" 
                                       placeholder="Full Name" 
                                       required>
                            </div>
                            <div class="form-group">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" 
                                       name="email" 
                                       class="form-control" 
                                       placeholder="PSU Email" 
                                       required>
                            </div>
                            <div class="form-group">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" 
                                       name="password" 
                                       class="form-control" 
                                       placeholder="Password" 
                                       required>
                                <i class="fas fa-eye password-toggle"></i>
                            </div>
                            <div class="form-group">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" 
                                       name="confirm_password" 
                                       class="form-control" 
                                       placeholder="Confirm Password" 
                                       required>
                                <i class="fas fa-eye password-toggle"></i>
                            </div>
                            <button type="submit" class="btn btn-register w-100 mb-3">Register</button>
                            <div class="text-center">
                                <a href="../index.php" class="text-decoration-none">Already have an account? Login</a>
                            </div>
                        </form>

                        <script>
                            document.querySelectorAll('.password-toggle').forEach(icon => {
                                icon.addEventListener('click', function() {
                                    const input = this.previousElementSibling;
                                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                                    input.setAttribute('type', type);
                                    this.classList.toggle('fa-eye');
                                    this.classList.toggle('fa-eye-slash');
                                });
                            });

                            document.querySelectorAll('.form-control').forEach(input => {
                                input.addEventListener('focus', function() {
                                    this.style.transform = 'scale(1.02)';
                                });

                                input.addEventListener('blur', function() {
                                    this.style.transform = 'scale(1)';
                                });
                            });

                            document.querySelector('form').addEventListener('submit', function(e) {
                                const password = document.querySelector('input[name="password"]').value;
                                const confirm = document.querySelector('input[name="confirm_password"]').value;
                                
                                if (password === confirm) {
                                    e.preventDefault();
                                    const loader = document.querySelector('.loader-container');
                                    loader.style.display = 'flex';
                                    loader.offsetHeight;
                                    loader.classList.add('show');
                                    
                                    setTimeout(() => {
                                        this.submit();
                                    }, 5000);
                                } else {
                                    e.preventDefault();
                                    alert('Passwords do not match!');
                                }
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>