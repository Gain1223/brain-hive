<?php
include '../config.php';

if (!isset($_SESSION)) {
    session_start();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['account_type'])) {
        $_SESSION['reg_account_type'] = $_POST['account_type'];
        header('Location: register-phase2.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - PSU BrainHive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --psu-blue: <?= PSU_BLUE ?>;
            --psu-yellow: <?= PSU_YELLOW ?>;
            --psu-beige: <?= PSU_BEIGE ?>;
        }

        body {
            background: linear-gradient(rgba(0, 87, 184, 0.1), rgba(210, 180, 140, 0.1)),
                        url('<?= ASSETS_PATH ?>img/psu_background.jpg');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 3px solid var(--psu-yellow);
            padding: 15px 0;
            margin-bottom: 40px;
        }

        .registration-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0 20px;
            text-align: center; /* Added to center all content */
        }

        .welcome-box {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            margin-bottom: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 2px solid var(--psu-yellow);
            max-width: 600px;
            width: 100%;
        }

        .welcome-box h2 {
            color: var(--psu-blue);
            margin-bottom: 10px;
        }

        .account-types {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            max-width: 1200px;
            width: 100%;
            padding: 0 20px;
        }

        .account-type {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .account-type:hover {
            transform: translateY(-10px);
            border-color: var(--psu-yellow);
        }

        .account-type.selected {
            background: var(--psu-blue);
            color: white;
            border-color: var(--psu-yellow);
        }

        .account-type.selected .account-icon {
            color: var(--psu-yellow);
        }

        .account-icon {
            font-size: 48px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .professional { color: #FF6B6B; }
        .teacher { color: #4ECDC4; }
        .student { color: #45B7D1; }
        .family { color: #96CEB4; }

        .btn-next {
            background: var(--psu-yellow);
            color: var(--psu-blue);
            padding: 15px 40px;
            border-radius: 30px;
            border: none;
            font-weight: 600;
            margin-top: 40px;
            transition: all 0.3s ease;
            display: none;
            font-size: 1.1rem;
            margin-left: auto;
            margin-right: auto; /* Center the button */
        }

        .bottom-links {
            width: 100%;
            text-align: center;
            margin-top: 40px;
        }

        .login-link {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 15px 30px;
            border-radius: 30px;
            text-decoration: none;
            color: var(--psu-blue);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin: 20px auto; /* Center the link */
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .pulse {
            animation: pulse 1s ease infinite;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="d-flex align-items-center">
                        <img src="<?= ASSETS_PATH ?>img/psu_logo.png" width="50" alt="PSU Logo">
                        <h3 class="ms-3 mb-0">BrainHive</h3>
                    </div>
                </div>
                <div class="col-auto ms-auto">
                    <select class="form-select form-select-sm">
                        <option value="en">English</option>
                        <option value="th">ภาษาไทย</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="registration-container">
        <div class="welcome-box">
            <h2>Join BrainHive</h2>
            <p class="text-muted mb-0">Choose your account type to get started</p>
        </div>

        <form id="accountTypeForm" action="register-phase1.php" method="POST">
            <div class="account-types">
                <div class="account-type" data-type="professional">
                    <i class="fas fa-briefcase account-icon professional"></i>
                    <h4>Professional</h4>
                    <p class="text-muted mb-0">For industry experts</p>
                </div>
                <div class="account-type" data-type="teacher">
                    <i class="fas fa-chalkboard-teacher account-icon teacher"></i>
                    <h4>Teacher</h4>
                    <p class="text-muted mb-0">For educators</p>
                </div>
                <div class="account-type" data-type="student">
                    <i class="fas fa-user-graduate account-icon student"></i>
                    <h4>Student</h4>
                    <p class="text-muted mb-0">For learners</p>
                </div>
                <div class="account-type" data-type="family">
                    <i class="fas fa-users account-icon family"></i>
                    <h4>Family & Friends</h4>
                    <p class="text-muted mb-0">For casual learning</p>
                </div>
            </div>

            <button type="button" class="btn btn-next" id="btnNext" disabled>
                Continue <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </form>

        <div class="bottom-links">
            <a href="../index.php" class="login-link">
                <i class="fas fa-sign-in-alt"></i>
                Already have an account? Log in
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const accountTypes = document.querySelectorAll('.account-type');
            const btnNext = document.getElementById('btnNext');
            let selectedType = null;

            accountTypes.forEach(type => {
                type.addEventListener('click', function() {
                    accountTypes.forEach(t => t.classList.remove('selected'));
                    this.classList.add('selected');
                    selectedType = this.dataset.type;
                    
                    btnNext.style.display = 'inline-block';
                    btnNext.disabled = false;
                    btnNext.classList.add('pulse');
                });
            });

            btnNext.addEventListener('click', function() {
                if (selectedType) {
                    const form = document.getElementById('accountTypeForm');
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'account_type';
                    input.value = selectedType;
                    form.appendChild(input);
                    form.submit();
                }
            });
        });
    </script>
</body>
</html>