<?php
include '../config.php';

if (!isset($_SESSION)) {
    session_start();
}

// Check for previous phases
if (!isset($_SESSION['reg_account_type']) || !isset($_SESSION['birthdate'])) {
    header('Location: register-phase1.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username'])) {
        // Store full name for next page and username from this page
        $_SESSION['reg_username'] = trim($_POST['username']);
        $_SESSION['full_name'] = ''; // Initialize empty, will be filled in register.php
        $_SESSION['completed_phase3'] = true;

        // Debug log
        error_log("Phase 3 completed - Username: " . $_SESSION['reg_username']);
        
        header("Location: register.php");
        exit;
    }
}

// Debug information
error_log("Session at phase 3: " . print_r($_SESSION, true));

// Add visual debug (remove in production)
if (isset($_SESSION)) {
    echo "<!-- Session Data: ";
    print_r($_SESSION);
    echo " -->";
}

// Debug information (remove in production)
if (isset($_SESSION['error'])) {
    echo '<pre>';
    print_r($_SESSION);
    echo '</pre>';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Username Creation - PSU BrainHive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/loader.css">
    <style>
        :root {
            --psu-blue: <?= PSU_BLUE ?>;
            --psu-yellow: <?= PSU_YELLOW ?>;
            --psu-beige: <?= PSU_BEIGE ?>;
        }

        body {
            background: linear-gradient(rgba(210, 180, 140, 0.1), rgba(0, 87, 184, 0.1)),
                        url('<?= ASSETS_PATH ?>img/psu_background.jpg');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        .logo-container {
            margin-bottom: 30px;
            text-align: center;
        }

        .logo-container img {
            width: 80px;
            margin-bottom: 15px;
        }

        .registration-container {
            max-width: 500px;
            width: 100%;
        }

        .username-form {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 2px solid var(--psu-yellow);
        }

        .progress-steps {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
            gap: 40px;
        }

        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            position: relative;
            border: 2px solid #ddd;
        }

        .step.active {
            background: var(--psu-yellow);
            color: var(--psu-blue);
            border-color: var(--psu-yellow);
        }

        .step.completed {
            background: var(--psu-blue);
            color: white;
            border-color: var(--psu-blue);
        }

        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            right: -40px;
            top: 50%;
            width: 40px;
            height: 2px;
            background: #ddd;
        }

        .step.completed:not(:last-child)::after {
            background: var(--psu-blue);
        }

        .form-control {
            padding: 15px;
            border-radius: 10px;
            border: 2px solid #eee;
            margin-bottom: 20px;
            font-size: 1rem;
        }

        .form-control:focus {
            border-color: var(--psu-yellow);
            box-shadow: 0 0 0 0.25rem rgba(255, 215, 0, 0.25);
        }

        .btn-next, .btn-back {
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-next {
            background: var(--psu-yellow);
            color: var(--psu-blue);
            border: none;
        }

        .btn-next:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.4);
        }

        .btn-back {
            background: transparent;
            border: 2px solid #ddd;
            color: #666;
        }

        .btn-back:hover {
            border-color: var(--psu-blue);
            color: var(--psu-blue);
        }

        .form-text {
            color: #666;
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="loader-container">
        <div class="loader"></div>
        <div class="loader-text">Moving to final step...</div>
    </div>
    <div class="logo-container">
        <img src="<?= ASSETS_PATH ?>img/psu_logo.png" alt="PSU Logo">
        <h2 class="text-white">BrainHive Registration</h2>
    </div>

    <div class="registration-container">
        <div class="progress-steps">
            <div class="step completed">1</div>
            <div class="step completed">2</div>
            <div class="step active">3</div>
        </div>

        <div class="username-form">
            <h3 class="text-center mb-4">Choose a Username</h3>
            <form id="usernameForm" action="register-phase3.php" method="POST" onsubmit="return validateForm()">
                <div class="form-floating">
                    <input type="text" class="form-control" id="username" name="username" autocomplete="off" required minlength="3">
                    <label for="username">Username</label>
                </div>

                <p class="form-text">Your username will be visible to others</p>

                <div class="d-flex justify-content-between">
                    <a href="register-phase2.php" class="btn btn-back">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                    <button type="submit" class="btn btn-next">
                        Continue <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>
            <div class="text-center mt-3">
                <a href="../index.php" class="text-decoration-none">Back to Login</a>
            </div>
        </div>
    </div>

    <script>
        function validateForm() {
            const username = document.getElementById('username').value.trim();
            if (username.length < 3) {
                alert('Username must be at least 3 characters long.');
                return false;
            }
            return true;
        }

        function showLoader() {
            const loader = document.querySelector('.loader-container');
            loader.style.display = 'flex';
            // Force reflow
            loader.offsetHeight;
            loader.classList.add('show');
        }

        document.getElementById('usernameForm').addEventListener('submit', function(e) {
            if (validateForm()) {
                e.preventDefault();
                showLoader();
                // Updated to 3 seconds
                setTimeout(() => {
                    this.submit();
                }, 3000);
            }
        });
    </script>
</body>
</html>
