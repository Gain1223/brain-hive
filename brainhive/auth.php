<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'login') {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['username'] = $user['username']; // Store username in session
                
                // Redirect based on role
                $redirect = $user['role'] === 'teacher' ? 'teacher/dashboard.php' : 'student/dashboard.php';
                header("Location: $redirect");
                exit;
            } else {
                $_SESSION['error'] = "Invalid email or password!";
                header("Location: index.php");
                exit;
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Database error: " . $e->getMessage();
            header("Location: index.php");
            exit;
        }
    }
}
?>