<?php
include '../config.php';

// Redirect non-teachers
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../index.php");
    exit;
}

if (isset($_GET['quiz_id'])) {
    $quiz_id = $_GET['quiz_id'];
    $game_code = substr(md5(uniqid()), 0, 6); // Generate 6-character game code

    try {
        // Create game session
        $stmt = $pdo->prepare("INSERT INTO game_sessions (quiz_id, code) VALUES (?, ?)");
        $stmt->execute([$quiz_id, strtoupper($game_code)]);
        
        header("Location: dashboard.php?game_code=" . $game_code);
    } catch (PDOException $e) {
        die("Error starting battle: " . $e->getMessage());
    }
}
?>