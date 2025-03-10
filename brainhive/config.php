<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database Configuration
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('DB_NAME')) define('DB_NAME', 'brainhive');

// Absolute Paths
if (!defined('BASE_URL')) define('BASE_URL', '/brainhive/');
if (!defined('ASSETS_PATH')) define('ASSETS_PATH', BASE_URL . 'assets/');
if (!defined('SIGNUPS_PATH')) define('SIGNUPS_PATH', BASE_URL . 'signups/');

// PSU Branding Colors
if (!defined('PSU_YELLOW')) define('PSU_YELLOW', '#FFD700');
if (!defined('PSU_BLUE')) define('PSU_BLUE', '#0057B8');
if (!defined('PSU_BEIGE')) define('PSU_BEIGE', '#D2B48C');
if (!defined('PSU_BLUE_RGB')) define('PSU_BLUE_RGB', '0, 87, 184');
if (!defined('PSU_YELLOW_RGB')) define('PSU_YELLOW_RGB', '255, 215, 0');

// Database Connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Session Management
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure proper session handling
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Add session timeout check
$session_timeout = 1800; // 30 minutes
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
    session_unset();
    session_destroy();
    header("Location: " . BASE_URL . "?timeout=1");
    exit;
}
$_SESSION['last_activity'] = time();

// Google OAuth configuration (if needed in the future)
if (!defined('GOOGLE_CLIENT_ID')) define('GOOGLE_CLIENT_ID', '123456789-your-client-id.apps.googleusercontent.com'); // Replace with your client ID
if (!defined('GOOGLE_CLIENT_SECRET')) define('GOOGLE_CLIENT_SECRET', 'GOCSPX-your-client-secret'); // Replace with your client secret
if (!defined('GOOGLE_REDIRECT_URI')) define('GOOGLE_REDIRECT_URI', 'http://localhost/brainhive/google-callback.php');
?>