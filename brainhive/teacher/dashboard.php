<?php
include '../config.php';

// Check for teacher login and fetch teacher data
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../index.php");
    exit;
}

// Fetch teacher data
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
    <title>Teacher Dashboard - PSU BrainHive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
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
            /* Add masculine theme colors */
            --man-blue: #1a365d;
            --man-gray: #2d3748;
            --man-accent: #4299e1;
            --man-secondary: #2b6cb0;
            --man-highlight: #e53e3e;
            --man-bg-primary: #f7fafc;
            --man-bg-secondary: #edf2f7;
            /* Add pastel theme colors */
            --pastel-violet: #E6E6FA;
            --pastel-white: #FFFFFF;
            --pastel-accent: #9370DB;
            --pastel-secondary: #B19CD9;
            --pastel-highlight: #DCD0FF;
            /* Update pastel theme to Quizlet colors */
            --quizlet-primary: #4257B2;
            --quizlet-secondary: #3CCFCF;
            --quizlet-accent: #FFD500;
            --quizlet-light: #F6F7FB;
            --quizlet-dark: #28314B;
            /* Update professional theme colors */
            --prof-primary: #E6E6FA;    /* Pastel violet */
            --prof-secondary: #F8F8FF;  /* White with slight violet tint */
            --prof-text: #000000;       /* Black text */
            --prof-accent: #D8BFD8;     /* Lighter pastel violet */
            --prof-hover: #DCD0FF;      /* Hover state violet */
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
            position: relative;
            --scrollbar-color: rgba(255, 105, 180, 0.5);
            --scrollbar-hover: rgba(255, 105, 180, 0.8);
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                linear-gradient(45deg, transparent 48%, rgba(255,255,255,0.1) 50%, transparent 52%),
                linear-gradient(-45deg, transparent 48%, rgba(255,255,255,0.1) 50%, transparent 52%);
            background-size: 30px 30px;
            pointer-events: none;
        }

        .navbar,
        .sidebar,
        .col-md-9 {
            position: relative;
            z-index: 1;
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
            transform: translateZ(0);
            box-shadow: var(--modern-shadow);
            padding: 2.5rem 1.5rem;
            position: relative;
            overflow: hidden;
            min-height: calc(100vh - 80px);
            z-index: 10;
            backdrop-filter: blur(10px);
        }

        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="10" cy="10" r="2" fill="white" fill-opacity="0.1"/></svg>');
            background-size: 20px 20px;
            opacity: 0.5;
            z-index: -1;
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

        .welcome-banner {
            background: linear-gradient(135deg, var(--candy-purple), var(--candy-pink));
            padding: 3.5rem 3rem;
            border-radius: 30px;
            position: relative;
            overflow: hidden;
            border: 4px solid white;
            box-shadow: var(--modern-shadow);
            transform-style: preserve-3d;
            transform: perspective(1000px) translateZ(0);
        }

        .welcome-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="10" cy="10" r="2" fill="white" fill-opacity="0.2"/></svg>');
            background-size: 20px 20px;
        }

        .welcome-banner h2 {
            font-size: 2.8rem;
            margin-bottom: 1.2rem;
            font-weight: 700;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .stats-grid {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 2.5rem;
            flex-wrap: nowrap;
        }

        .stat-card {
            flex: 1;
            min-width: 200px;
            background: white;
            border-radius: 25px;
            padding: 2.5rem 2rem;
            margin: 1rem 0;
            border: none;
            position: relative;
            overflow: hidden;
            box-shadow: var(--subtle-shadow);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            transform-style: preserve-3d;
            transform: perspective(1000px) translateZ(0);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: perspective(1000px) translateY(-5px);
            box-shadow: var(--card-hover-shadow);
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(255, 215, 0, 0.1), transparent);
            transform: translateX(-100%);
            transition: 0.5s;
        }

        .stat-card:hover::after {
            transform: translateX(100%);
        }

        .stat-card:nth-child(1)::before { background: var(--candy-pink); }
        .stat-card:nth-child(2)::before { background: var(--candy-blue); }
        .stat-card:nth-child(3)::before { background: var(--candy-yellow); }

        .stat-number {
            font-size: 3.5rem;
            font-weight: 800;
            background: linear-gradient(45deg, var(--candy-yellow), #FFA500);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 1rem 0;
            transition: all 0.3s ease;
        }

        .stat-card:hover .stat-number {
            transform: scale(1.1);
        }

        .stat-card i {
            font-size: 2.5rem;
            color: var(--candy-yellow);
            transition: all 0.3s ease;
            -webkit-text-fill-color: var(--candy-yellow);
        }

        .stat-card:hover i {
            transform: rotate(15deg) scale(1.2);
        }

        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .action-btn {
            background: var(--candy-yellow);
            color: #333;
            font-weight: 600;
            padding: 2rem 1.5rem;
            margin: 0.8rem 0;
            border: none;
            position: relative;
            overflow: hidden;
            z-index: 1;
            box-shadow: var(--subtle-shadow);
            background: rgba(255, 255, 255, 0.95);
            transform-style: preserve-3d;
            transform: perspective(1000px) translateZ(0);
            border-radius: 20px;
            transition: all 0.2s ease;
        }

        .action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, var(--candy-yellow), #FFF3B0);
            opacity: 0;
            z-index: -1;
            transition: all 0.4s ease;
        }

        .action-btn:hover::before {
            opacity: 1;
        }

        .action-btn:hover {
            color: #333;
            transform: perspective(1000px) translateY(-3px);
            box-shadow: var(--card-hover-shadow);
        }

        .action-btn i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            transition: all 0.4s ease;
            color: #333;
        }

        .action-btn:hover i {
            transform: scale(1.2) rotate(5deg);
        }

        .action-btn::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 3px;
            background: var(--candy-gradient);
            transition: all 0.4s ease;
            transform: translateX(-50%);
        }

        .action-btn:hover::after {
            width: 80%;
        }

        .dashboard-card {
            background: white;
            border-radius: 25px;
            border: none;
            overflow: hidden;
            margin-bottom: 1.5rem;
            box-shadow: var(--subtle-shadow);
            transform-style: preserve-3d;
            transform: perspective(1000px) translateZ(0);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .dashboard-card:hover {
            transform: perspective(1000px) translateY(-3px);
            box-shadow: var(--card-hover-shadow);
        }

        .card-header {
            background: var(--candy-gradient) !important;
            padding: 1.5rem;
            border: none;
            color: white !important;
        }

        .quiz-item {
            background: #f8f9fa;
            border-radius: 20px;
            margin: 1rem;
            padding: 2rem !important;
            border: 2px dashed transparent;
            transition: all 0.4s ease;
            background: rgba(255, 255, 255, 0.8);
            transform-style: preserve-3d;
            transform: perspective(1000px) translateZ(0);
            box-shadow: var(--box-3d-shadow),
                        0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .quiz-item:hover {
            border-color: var(--candy-pink);
            background: white;
            transform: perspective(1000px) var(--hover-transform);
            box-shadow: 0 12px 0 rgba(0, 0, 0, 0.1),
                        0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .btn-psu-yellow {
            background: var(--candy-yellow);
            color: #333;
            font-weight: 600;
            padding: 0.8rem 2rem;
            border-radius: 15px;
            transition: all 0.4s ease;
            border: none;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
        }

        .btn-psu-yellow:hover {
            background: var(--candy-pink);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 105, 180, 0.4);
        }

        .btn-psu-yellow::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.2),
                transparent
            );
            transition: 0.5s;
        }

        .btn-psu-yellow:hover::before {
            left: 100%;
        }

        /* Improved scrollbar performance */
        * {
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 105, 180, 0.5) transparent;
        }

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background-color: rgba(255, 105, 180, 0.5);
            border-radius: 3px;
            border: transparent;
        }

        .activity-feed {
            max-height: 500px;
            overflow-y: auto;
            padding: 1.5rem;
        }

        .activity-item {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 1.2rem;
            margin-bottom: 1rem;
            border-left: 4px solid var(--candy-pink);
            transition: all 0.4s ease;
        }

        .activity-item:hover {
            background: white;
            transform: translateX(8px);
            box-shadow: var(--card-shadow);
        }

        /* Modern chart container */
        .chart-container {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            margin: 1.5rem 0;
            border: none;
            box-shadow: var(--box-3d-shadow),
                        0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            transform-style: preserve-3d;
            transform: perspective(1000px) translateZ(0);
            max-height: 400px;
            padding: 1rem;
        }

        .chart-container:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transform: perspective(1000px) var(--hover-transform);
        }

        .btn-group .btn {
            background: var(--candy-yellow);
            color: #333;
            border: none;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-group .btn:hover {
            background: #FFF3B0;
            transform: translateY(-2px);
        }

        .btn-group .btn.active {
            background: #FFA500;
            color: white;
        }

        /* Add GPU acceleration for smoother performance */
        .sidebar, .stat-card, .action-btn, .dashboard-card {
            will-change: transform;
            transform: translateZ(0);
            backface-visibility: hidden;
        }

        /* Update container background for depth effect */
        .container-fluid {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            margin: 1rem;
            width: calc(100% - 2rem);
            box-shadow: var(--box-3d-shadow),
                        0 4px 15px rgba(0, 0, 0, 0.1);
        }

        /* Theme Switcher Button */
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

        .theme-switch i {
            margin-right: 0.5rem;
        }

        /* Masculine theme classes */
        .masculine-theme {
            --candy-pink: var(--prof-primary);
            --candy-blue: var(--prof-secondary);
            --candy-yellow: var(--prof-accent);
            --candy-purple: var(--prof-hover);
            --candy-mint: var(--prof-secondary);
            --candy-gradient: linear-gradient(135deg, var(--prof-primary), var(--prof-secondary));
            --scrollbar-color: var(--prof-accent);
            --scrollbar-hover: var(--prof-hover);
        }

        /* Professional theme specific background */
        .masculine-theme body {
            background-color: var(--prof-secondary);
            background-image: 
                radial-gradient(circle at 100% 0%, var(--prof-primary) 20%, transparent 50%),
                radial-gradient(circle at 0% 100%, var(--prof-accent) 20%, transparent 50%),
                radial-gradient(circle at 50% 50%, var(--prof-hover) 5%, transparent 40%);
        }

        /* Professional theme specific styles */
        .masculine-theme .nav-link,
        .masculine-theme .welcome-banner,
        .masculine-theme .card-header {
            color: var(--prof-text) !important;
        }

        .masculine-theme .nav-link:hover {
            color: var(--prof-text) !important;
            background: var(--prof-hover);
        }

        .masculine-theme .nav-link.active {
            background: var(--prof-secondary);
            color: var(--prof-text) !important;
        }

        .masculine-theme .welcome-banner h2,
        .masculine-theme .card-header h5 {
            color: var(--prof-text) !important;
        }

        .masculine-theme .stat-number {
            background: var(--prof-accent);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: var(--prof-text);
        }

        .masculine-theme .stat-card i {
            color: var(--prof-accent) !important;
            -webkit-text-fill-color: var(--prof-accent);
        }

        /* Update theme button styles to ensure visibility */
        .masculine-theme .theme-switch {
            border-color: var(--prof-text);
            color: var(--prof-text);
        }

        .masculine-theme .theme-switch:hover {
            background: var(--prof-text);
            color: var(--prof-secondary);
        }

        /* Fix theme options visibility in masculine theme */
        .masculine-theme .theme-option {
            color: var(--prof-text);
        }

        .masculine-theme .theme-option:hover {
            background: var(--prof-hover);
        }

        /* Pastel theme classes */
        .pastel-theme {
            --candy-pink: var(--pastel-violet);
            --candy-blue: var(--pastel-white);
            --candy-yellow: var(--pastel-accent);
            --candy-purple: var(--pastel-secondary);
            --candy-gradient: linear-gradient(135deg, var(--pastel-violet), var(--pastel-white));
            --scrollbar-color: rgba(147, 112, 219, 0.5);
            --scrollbar-hover: rgba(147, 112, 219, 0.8);
        }

        /* Quizlet theme classes */
        .quizlet-theme {
            --candy-pink: var(--quizlet-primary);
            --candy-blue: var(--quizlet-secondary);
            --candy-yellow: var(--quizlet-accent);
            --candy-purple: var(--quizlet-dark);
            --candy-mint: var(--quizlet-secondary);
            --candy-gradient: linear-gradient(135deg, var(--quizlet-primary), var(--quizlet-secondary));
            --scrollbar-color: rgba(66, 87, 178, 0.5);
            --scrollbar-hover: rgba(66, 87, 178, 0.8);
        }

        /* Update background for Quizlet theme */
        .quizlet-theme body {
            background-image: 
                radial-gradient(circle at 100% 0%, var(--quizlet-primary) 15%, transparent 50%),
                radial-gradient(circle at 0% 100%, var(--quizlet-secondary) 15%, transparent 50%),
                radial-gradient(circle at 50% 50%, var(--quizlet-accent) 5%, transparent 50%);
            background-color: var(--quizlet-light);
        }

        /* Dynamic scrollbar colors */
        ::-webkit-scrollbar-thumb {
            background-color: var(--scrollbar-color);
            transition: background-color 0.3s ease;
        }

        ::-webkit-scrollbar-thumb:hover {
            background-color: var(--scrollbar-hover);
        }

        /* Theme Switcher Dropdown */
        .theme-dropdown {
            position: relative;
            display: inline-block;
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

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Adjusted Activity Overview size */
        .dashboard-card.activity-overview {
            max-width: 900px;
            margin: 1.5rem auto;
        }

        .dashboard-card.activity-overview .card-body {
            padding: 0;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= BASE_URL?>">
                <img src="<?= ASSETS_PATH?>img/psu_logo.png" width="40" 
                     alt="PSU Logo"
                     class="d-inline-block align-top">
                <span class="ms-2" style="color: var(--psu-blue); font-weight: 600;">
                    PSU BrainHive - Teacher Dashboard
                </span>
            </a>
            <div class="navbar-text">
                <div class="theme-dropdown">
                    <button class="theme-switch" onclick="toggleThemeMenu()">
                        <i class="fas fa-palette"></i> <!-- Changed from fa-paint-brush -->
                        <span>Theme</span>
                    </button>
                    <div class="theme-options" id="themeOptions">
                        <div class="theme-option" onclick="setTheme('original')">
                            <i class="fas fa-star" style="color: var(--candy-pink)"></i> <!-- Changed from fa-heart -->
                            Playful
                        </div>
                        <div class="theme-option" onclick="setTheme('masculine')">
                            <i class="fas fa-briefcase" style="color: var(--prof-primary)"></i> <!-- Changed from fa-square -->
                            Professional
                        </div>
                        <div class="theme-option" onclick="setTheme('quizlet')">
                            <i class="fas fa-crown" style="color: var(--quizlet-primary)"></i> <!-- Changed from fa-graduation-cap -->
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
            <div class="col-md-3 col-xl-2 sidebar p-0">
                <div class="list-group list-group-flush pt-3">
                    <div class="nav-item">
                        <a href="create_quiz.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'create_quiz.php' ? 'active' : '' ?>">
                            <i class="fas fa-plus-square"></i>Create Quiz <!-- Changed from fa-plus-circle -->
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="dashboard.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
                            <i class="fas fa-gamepad"></i>Active Battles <!-- Changed from fa-trophy -->
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-medal"></i>Leaderboards <!-- Changed from fa-chart-line -->
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-user-graduate"></i>Students <!-- Changed from fa-users -->
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-tools"></i>Settings <!-- Changed from fa-cog -->
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-9 col-xl-10 p-4">
                <div class="welcome-banner animate__animated animate__fadeIn">
                    <h2>Welcome back, <?= htmlspecialchars($_SESSION['username'])?>!</h2>
                    <p class="mb-0">Manage your quizzes and monitor active battles from your dashboard.</p>
                </div>

                <div class="action-buttons">
                    <button class="action-btn" onclick="location.href='create_quiz.php'">
                        <i class="fas fa-plus-circle fa-2x mb-2"></i>
                        <div>New Quiz</div>
                    </button>
                    <button class="action-btn" onclick="location.href='reports.php'">
                        <i class="fas fa-chart-bar fa-2x mb-2"></i>
                        <div>Reports</div>
                    </button>
                    <button class="action-btn" onclick="location.href='student_progress.php'">
                        <i class="fas fa-user-graduate fa-2x mb-2"></i>
                        <div>Student Progress</div>
                    </button>
                    <button class="action-btn" onclick="location.href='quiz_templates.php'">
                        <i class="fas fa-copy fa-2x mb-2"></i>
                        <div>Templates</div>
                    </button>
                </div>

                <div class="stats-grid animate__animated animate__fadeInUp">
                    <?php
                    // Fetch statistics
                    $activeQuizzes = $pdo->prepare("SELECT COUNT(*) FROM game_sessions WHERE is_active = 1 AND quiz_id IN (SELECT id FROM quizzes WHERE teacher_id = ?)");
                    $totalQuizzes = $pdo->prepare("SELECT COUNT(*) FROM quizzes WHERE teacher_id = ?");
                    $totalStudents = $pdo->prepare("SELECT COUNT(DISTINCT student_id) FROM battle_participants WHERE quiz_id IN (SELECT id FROM quizzes WHERE teacher_id = ?)");
                    
                    $activeQuizzes->execute([$_SESSION['user_id']]);
                    $totalQuizzes->execute([$_SESSION['user_id']]);
                    $totalStudents->execute([$_SESSION['user_id']]);
                    ?>
                    <div class="stat-card">
                        <i class="fas fa-fire fa-2x" style="color: var(--psu-yellow)"></i>
                        <div class="stat-number"><?= $activeQuizzes->fetchColumn() ?></div>
                        <div>Active Battles</div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-clipboard-list fa-2x" style="color: var(--psu-yellow)"></i>
                        <div class="stat-number"><?= $totalQuizzes->fetchColumn() ?></div>
                        <div>Total Quizzes</div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-users fa-2x" style="color: var(--psu-yellow)"></i>
                        <div class="stat-number"><?= $totalStudents->fetchColumn() ?></div>
                        <div>Total Students</div>
                    </div>
                </div>

                <div class="dashboard-card animate__animated animate__fadeInUp">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-fire me-2"></i>Active Battles</h5>
                    </div>
                    <div class="card-body" id="activeBattles">
                        <?php
                        // Fetch active battles
                        $stmt = $pdo->prepare("SELECT * FROM game_sessions 
                                              WHERE quiz_id IN 
                                              (SELECT id FROM quizzes WHERE teacher_id =?)
                                              AND is_active = 1");
                        $stmt->execute([$_SESSION['user_id']]);
                        $sessions = $stmt->fetchAll();
                        
                        if (empty($sessions)) {
                            echo '<div class="text-center py-4">
                                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No quizzes found. Create your first one!</p>
                                  </div>';
                        }
                      ?>
                    </div>
                </div>

                <div class="dashboard-card activity-overview">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Activity Overview</h5>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-light" onclick="updateChart('week')">Week</button>
                            <button class="btn btn-sm btn-light" onclick="updateChart('month')">Month</button>
                        </div>
                    </div>
                    <canvas id="activityChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://kit.fontawesome.com/your-kit-code.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Enhanced chart configuration
        const ctx = document.getElementById('activityChart').getContext('2d');
        const gradientFill = ctx.createLinearGradient(0, 0, 0, 400);
        gradientFill.addColorStop(0, 'rgba(255, 215, 0, 0.3)');
        gradientFill.addColorStop(1, 'rgba(255, 215, 0, 0)');

        const activityChart = new Chart(ctx, {
            type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Student Participation',
                data: [12, 19, 3, 5, 2, 3, 7],
                borderColor: '#FFD700',
                backgroundColor: gradientFill,
                tension: 0.4,
                fill: true,
                borderWidth: 3,
                pointBackgroundColor: '#FFD700',
                pointBorderColor: 'white',
                pointBorderWidth: 2,
                pointRadius: 6
            }]
        }
    });

    function updateActiveBattles() {
        // Add your battle update logic here
    }

    function updateActivityFeed() {
        // Add your activity feed update logic here
    }

    // Initialize real-time updates
    setInterval(updateActiveBattles, 5000);
    updateActivityFeed();

    // Add smooth animations to cards
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.dashboard-card');
        cards.forEach((card, index) => {
            card.classList.add('animate__animated', 'animate__fadeInUp');
            card.style.animationDelay = `${index * 0.1}s`;
        });
    });

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
        // Remove all theme classes
        document.body.classList.remove('masculine-theme', 'quizlet-theme');
        
        // Add selected theme class
        if (theme !== 'original') {
            document.body.classList.add(`${theme}-theme`);
        }
        
        // Update scrollbar colors based on theme
        const root = document.documentElement;
        if (theme === 'masculine') {
            root.style.setProperty('--scrollbar-base-color', '#D8BFD8');
            root.style.setProperty('--scrollbar-hover-color', '#DCD0FF');
        } else if (theme === 'quizlet') {
            root.style.setProperty('--scrollbar-base-color', '#4257B2');
            root.style.setProperty('--scrollbar-hover-color', '#3CCFCF');
        } else {
            root.style.setProperty('--scrollbar-base-color', '#FF69B4');
            root.style.setProperty('--scrollbar-hover-color', '#DDA0DD');
        }
        
        // Save theme preference
        localStorage.setItem('theme', theme);
        
        // Update chart colors
        const colors = {
            original: '#FFD700',
            masculine: '#D8BFD8',
            quizlet: '#4257B2'
        };
        
        updateChartColors(colors[theme]);
        document.getElementById('themeOptions').classList.remove('show');
    }

    function updateChartColors(theme) {
        const newColor = theme === 'masculine' ? '#D8BFD8' : theme === 'quizlet' ? '#4257B2' : '#FFD700';
        activityChart.data.datasets[0].borderColor = newColor;
        activityChart.data.datasets[0].pointBackgroundColor = newColor;
        activityChart.update();
    }

    // Load saved theme preference
    document.addEventListener('DOMContentLoaded', function() {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            setTheme(savedTheme);
        }
        // ...existing animation code...
    });
    </script>
</body>
</html>