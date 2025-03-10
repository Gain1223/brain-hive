<?php
include '../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PSU BrainHive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --psu-blue: <?= PSU_BLUE ?>;
            --psu-yellow: <?= PSU_YELLOW ?>;
            --psu-beige: <?= PSU_BEIGE ?>;
        }
        .psu-navbar {
            background: var(--psu-blue) !important;
            padding: 0.8rem 1rem;
        }
        .navbar-logo {
            filter: brightness(0) invert(1);
            transition: transform 0.3s ease;
        }
        .btn-psu-logout {
            color: var(--psu-yellow);
            border: 2px solid var(--psu-yellow);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg psu-navbar">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="<?= BASE_URL ?>">
                <img src="<?= ASSETS_PATH ?>img/psu_logo.png" 
                     width="40" 
                     class="navbar-logo"
                     alt="PSU Logo">
                <span class="ms-2">PSU BrainHive</span>
            </a>
            <?php if (isset($_SESSION['user_id'])) : ?>
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar-sm">
                        <img src="<?= ASSETS_PATH ?>avatars/<?= htmlspecialchars($_SESSION['avatar'] ?? 'default_avatar.png') ?>" 
                             class="rounded-circle"
                             width="36"
                             alt="Profile">
                    </div>
                    <span class="text-white"><?= htmlspecialchars($_SESSION['username'] ?? '') ?></span>
                    <a href="<?= BASE_URL ?>logout.php" class="btn btn-psu-logout btn-sm">
                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </nav>
</body>
</html>