<?php
require_once 'config.php';

// Start the session if not already started
if (!isset($_SESSION)) {
    session_start();
}

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Destroy the session
session_destroy();

// Redirect to login page with absolute path
header("Location: " . BASE_URL);
exit;
?>
