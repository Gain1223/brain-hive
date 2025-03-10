<?php
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit;
}

// Update XP when battle completes
if (isset($_SESSION['current_battle'])) {
    $stmt = $pdo->prepare("UPDATE leaderboards SET total_xp = total_xp + ? WHERE user_id = ?");
    $stmt->execute([$_SESSION['current_battle']['xp'], $_SESSION['user_id']]);
    unset($_SESSION['current_battle']);
}

// Fetch leaderboard
$leaderboard = $pdo->query("
    SELECT u.username, l.total_xp 
    FROM leaderboards l 
    JOIN users u ON l.user_id = u.id 
    ORDER BY l.total_xp DESC 
    LIMIT 10
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Leaderboard - PSU BrainHive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4>Leaderboard</h4>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Student</th>
                        <th>XP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leaderboard as $i => $row): ?>
                    <tr>
                        <td><?= $i+1 ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= number_format($row['total_xp']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <a href="../logout.php" class="btn btn-danger">Logout</a>
</body>
</html>