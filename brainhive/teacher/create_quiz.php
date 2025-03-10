<?php
include '../config.php';

// Check for teacher login and fetch teacher data
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../index.php");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'teacher'");
    $stmt->execute([$_SESSION['user_id']]);
    $teacher = $stmt->fetch();
    
    if (!$teacher) {
        session_destroy();
        header("Location: ../index.php");
        exit;
    }
} catch (PDOException $e) {
    error_log("Teacher data fetch error: " . $e->getMessage());
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Quiz - PSU BrainHive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        /* Copy ALL styles from dashboard.php */
        :root {
            --candy-pink: #FF69B4;
            --candy-blue: #87CEEB;
            --candy-yellow: #FFD700;
            --candy-mint: #98FF98;
            --candy-purple: #DDA0DD;
            --candy-orange: #FFA07A;
            --candy-gradient: linear-gradient(135deg, #FFC0CB, #87CEEB);
            --card-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            --pastel-bg: #FFF5F9;
            --header-shadow: 0 4px 20px rgba(255, 105, 180, 0.15);
            --box-3d-shadow: 0 8px 0 rgba(0, 0, 0, 0.1);
            --hover-transform: translateY(-4px) translateZ(0);
            --modern-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.1);
            --subtle-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            --card-hover-shadow: 0 20px 40px -5px rgba(0, 0, 0, 0.15);
            /* Theme colors from dashboard.php */
            --prof-primary: #E6E6FA;
            --prof-secondary: #F8F8FF;
            --prof-text: #000000;
            --prof-accent: #D8BFD8;
            --prof-hover: #DCD0FF;
            --quizlet-primary: #4257B2;
            --quizlet-secondary: #3CCFCF;
            --quizlet-accent: #FFD500;
            --quizlet-light: #F6F7FB;
            --quizlet-dark: #28314B;
        }

        body {
            background-color: #f7f7f7;
            background-image: 
                radial-gradient(circle at 100% 0%, var(--candy-pink) 15%, transparent 50%),
                radial-gradient(circle at 0% 100%, var(--candy-blue) 15%, transparent 50%),
                radial-gradient(circle at 50% 50%, var(--candy-yellow) 5%, transparent 50%),
                linear-gradient(45deg, var(--candy-mint) 0%, transparent 70%);
            background-attachment: fixed;
            background-size: 100% 100%;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            border-bottom: 3px solid var(--candy-pink);
            box-shadow: var(--subtle-shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            position: relative;
            padding: 0.8rem 1.5rem;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 2px 10px rgba(255, 105, 180, 0.1);
            transition: all 0.3s ease;
        }

        .navbar-brand:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 105, 180, 0.2);
        }

        .sidebar {
            background: linear-gradient(160deg, var(--candy-purple) 0%, var(--candy-pink) 100%);
            border-radius: 0 20px 20px 0;
            padding: 2.5rem 1.5rem;
            min-height: calc(100vh - 80px);
            backdrop-filter: blur(10px);
        }

        .nav-item {
            margin: 0.8rem 0;
        }

        .nav-link {
            color: white !important;
            padding: 1.2rem 1.8rem;
            border-radius: 15px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            z-index: 1;
            font-weight: 600;
            letter-spacing: 0.5px;
            border: 2px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            transform-style: preserve-3d;
            transform: perspective(1000px) translateZ(0);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            bottom: 50%;
            background: white;
            opacity: 0;
            z-index: -1;
            transition: all 0.4s ease;
        }

        .nav-link:hover::before {
            top: 0;
            bottom: 0;
            opacity: 1;
        }

        .nav-link:hover {
            color: var(--candy-pink) !important;
            transform: perspective(1000px) translateY(-3px);
            border-color: white;
            box-shadow: var(--modern-shadow);
        }

        .nav-link i {
            transition: all 0.4s ease;
            margin-right: 1rem;
            font-size: 1.2rem;
        }

        .nav-link:hover i {
            transform: scale(1.2) rotate(10deg);
        }

        .nav-link.active {
            background: white;
            color: var(--candy-pink) !important;
            border-color: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .theme-dropdown {
            position: relative;
            display: inline-block;
        }

        .theme-switch {
            background: transparent;
            border: 2px solid var(--candy-pink);
            color: var(--candy-pink);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 1rem;
        }

        .theme-switch:hover {
            background: var(--candy-pink);
            color: white;
            transform: translateY(-2px);
        }

        .theme-options {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 15px;
            box-shadow: var(--modern-shadow);
            padding: 0.5rem;
            z-index: 1000;
            min-width: 200px;
        }

        .theme-options.show {
            display: block;
            animation: fadeIn 0.2s ease;
        }

        .theme-option {
            padding: 0.8rem 1.2rem;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .theme-option:hover {
            background: rgba(0, 0, 0, 0.05);
        }

        .theme-option i {
            margin-right: 0.8rem;
            font-size: 1.1rem;
        }

        .masculine-theme .quiz-section {
            border-color: var(--prof-primary);
        }

        .masculine-theme .section-header {
            color: var(--prof-primary);
        }

        .quizlet-theme .quiz-section {
            border-color: var(--quizlet-primary);
        }

        .quizlet-theme .section-header {
            color: var(--quizlet-primary);
        }

        .masculine-theme .nav-link:hover {
            color: var(--prof-text) !important;
            background: var(--prof-hover);
        }

        .masculine-theme .nav-link.active {
            background: var(--prof-secondary);
            color: var(--prof-text) !important;
        }

        .quizlet-theme .nav-link:hover {
            color: var(--quizlet-dark) !important;
            background: var(--quizlet-secondary);
        }

        .quizlet-theme .nav-link.active {
            background: var(--quizlet-light);
            color: var(--quizlet-primary) !important;
        }

        .create-quiz-container {
            background: white;
            border-radius: 25px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--modern-shadow);
            transform-style: preserve-3d;
            transform: perspective(1000px) translateZ(0);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .quiz-section {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 2px dashed var(--candy-pink);
            transition: all 0.3s ease;
        }

        .quiz-section:hover {
            border-style: solid;
            transform: translateY(-3px);
            box-shadow: var(--subtle-shadow);
        }

        .section-header {
            color: var(--candy-pink);
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-header i {
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= BASE_URL?>">
                <img src="<?= ASSETS_PATH?>img/psu_logo.png" width="40" alt="PSU Logo" class="d-inline-block align-top">
                <span class="ms-2" style="font-weight: 600;">PSU BrainHive - Teacher Dashboard</span>
            </a>
            <div class="navbar-text">
                <div class="theme-dropdown">
                    <button class="theme-switch" onclick="toggleThemeMenu()">
                        <i class="fas fa-palette"></i>
                        <span>Theme</span>
                    </button>
                    <div class="theme-options" id="themeOptions">
                        <div class="theme-option" onclick="setTheme('original')">
                            <i class="fas fa-star" style="color: var(--candy-pink)"></i>
                            Playful
                        </div>
                        <div class="theme-option" onclick="setTheme('masculine')">
                            <i class="fas fa-briefcase" style="color: var(--prof-primary)"></i>
                            Professional
                        </div>
                        <div class="theme-option" onclick="setTheme('quizlet')">
                            <i class="fas fa-crown" style="color: var(--quizlet-primary)"></i>
                            Premium
                        </div>
                    </div>
                </div>
                <span class="me-3"><i class="fas fa-user-circle me-1"></i>@<?= htmlspecialchars($teacher['username']) ?></span>
                <a href="<?= BASE_URL ?>logout.php" class="btn btn-outline-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-xl-2 sidebar p-0">
                <div class="list-group list-group-flush pt-3">
                    <div class="nav-item">
                        <a href="create_quiz.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'create_quiz.php' ? 'active' : '' ?>">
                            <i class="fas fa-plus-square"></i>Create Quiz
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="dashboard.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
                            <i class="fas fa-gamepad"></i>Active Battles
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-medal"></i>Leaderboards
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-user-graduate"></i>Students
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-tools"></i>Settings
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-xl-10 p-4">
                <div class="welcome-banner animate__animated animate__fadeIn">
                    <h2><i class="fas fa-plus-circle me-2"></i>Create New Quiz</h2>
                    <p class="mb-0">Design your quiz and set up battle parameters</p>
                </div>

                <div class="create-quiz-container animate__animated animate__fadeInUp">
                    <div class="quiz-section">
                        <h4 class="section-header">
                            <i class="fas fa-info-circle"></i>
                            Basic Information
                        </h4>
                        <!-- Basic info form placeholder -->
                        <div class="p-3 text-center text-muted">
                            <i class="fas fa-tools fa-3x mb-3"></i>
                            <p>Quiz creation form coming soon...</p>
                        </div>
                    </div>

                    <div class="quiz-section">
                        <h4 class="section-header">
                            <i class="fas fa-cogs"></i>
                            Quiz Settings
                        </h4>
                        <!-- Settings form placeholder -->
                        <div class="p-3 text-center text-muted">
                            <i class="fas fa-sliders-h fa-3x mb-3"></i>
                            <p>Settings configuration coming soon...</p>
                        </div>
                    </div>

                    <div class="quiz-section">
                        <h4 class="section-header">
                            <i class="fas fa-question-circle"></i>
                            Questions
                        </h4>
                        <!-- Questions form placeholder -->
                        <div class="p-3 text-center text-muted">
                            <i class="fas fa-list-ol fa-3x mb-3"></i>
                            <p>Question editor coming soon...</p>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button class="btn btn-light me-2">
                            <i class="fas fa-save me-2"></i>Save Draft
                        </button>
                        <button class="btn btn-psu-yellow">
                            <i class="fas fa-paper-plane me-2"></i>Create Quiz
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Copy theme switching functions from dashboard.php
        function toggleThemeMenu() {
            document.getElementById('themeOptions').classList.toggle('show');
            document.addEventListener('click', closeThemeMenu);
        }

        function closeThemeMenu(e) {
            if (!e.target.closest('.theme-dropdown')) {
                document.getElementById('themeOptions').classList.remove('show');
                document.removeEventListener('click', closeThemeMenu);
            }
        }

        function setTheme(theme) {
            document.body.classList.remove('masculine-theme', 'quizlet-theme');
            if (theme !== 'original') {
                document.body.classList.add(`${theme}-theme`);
            }
            localStorage.setItem('theme', theme);
            document.getElementById('themeOptions').classList.remove('show');
        }

        // Load saved theme preference
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme) {
                setTheme(savedTheme);
            }

            // Add animations
            const sections = document.querySelectorAll('.quiz-section');
            sections.forEach((section, index) => {
                section.style.animationDelay = `${index * 0.1}s`;
                section.classList.add('animate__animated', 'animate__fadeInUp');
            });
        });
    </script>
</body>
</html>
