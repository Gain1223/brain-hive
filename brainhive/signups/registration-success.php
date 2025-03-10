<?php
include 'config.php';

if (!isset($_SESSION)) {
    session_start();
}

// Verify registration was completed
if (!isset($_SESSION['username'])) {
    header('Location: register.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registration Success - PSU BrainHive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

        .success-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 2px solid var(--psu-yellow);
            max-width: 500px;
            width: 100%;
            text-align: center;
            margin-top: 30px;
        }

        .logo-container {
            margin-bottom: 30px;
            text-align: center;
        }

        .logo-container img {
            width: 80px;
            margin-bottom: 15px;
        }

        .success-icon {
            color: #28a745;
            font-size: 4rem;
            margin-bottom: 20px;
        }

        .btn-login {
            background: var(--psu-yellow);
            color: var(--psu-blue);
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.4);
        }
    </style>
</head>
<body>
    <div class="logo-container">
        <img src="<?= ASSETS_PATH ?>img/psu_logo.png" alt="PSU Logo">
        <h2 class="text-white">BrainHive Registration</h2>
    </div>

    <div class="success-container">
        <i class="fas fa-check-circle success-icon"></i>
        <h2>Registration Successful!</h2>
        <p class="lead mb-4">Welcome to BrainHive, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
        <a href="login.php" class="btn btn-login">
            Proceed to Login <i class="fas fa-sign-in-alt ms-2"></i>
        </a>
    </div>
</body>
</html>
