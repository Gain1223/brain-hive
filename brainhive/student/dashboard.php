<?php
include '../config.php';

// Check for student login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit;
}

// Fetch student data
try {
    $stmt = $pdo->prepare("SELECT u.*, l.total_xp 
                          FROM users u 
                          LEFT JOIN leaderboards l ON u.id = l.user_id 
                          WHERE u.id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$student) {
        session_destroy();
        header("Location: ../index.php");
        exit;
    }
    
    // Store username in session for easy access
    $_SESSION['username'] = $student['username'];
    
    // Update leaderboard query to use correct column names
    $leaderboard = $pdo->query("
        SELECT u.full_name, u.username, u.avatar, l.total_xp 
        FROM leaderboards l 
        JOIN users u ON l.user_id = u.id 
        ORDER BY l.total_xp DESC 
        LIMIT 10
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

include '../includes/header.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard - BrainHive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --psu-blue: #0057B8;
            --psu-gold: #FFD700;
            --kahoot-purple: #46178F;
            --kahoot-pink: #FF3355;
            --card-bg: rgba(255, 255, 255, 0.95);
            --card-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        body {
            background: 
                linear-gradient(120deg, var(--psu-blue) 0%, var(--kahoot-purple) 100%),
                url('../assets/images/pattern.png');
            background-attachment: fixed;
            min-height: 100vh;
            padding-top: 20px;
            color: #333;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            display: grid;
            gap: 20px;
            grid-template-columns: repeat(12, 1fr);
        }

        .profile-card {
            grid-column: span 12;
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px;
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            transition: all 0.2s ease;
        }

        .avatar-section {
            flex: 0 0 auto;
            width: 100px;
        }

        .avatar-image {
            width: 100px;
            height: 100px;
        }

        .profile-info {
            flex: 1;
        }

        .game-join-card {
            grid-column: span 12;
            background: linear-gradient(135deg, var(--kahoot-purple), var(--psu-blue));
            border-radius: 16px;
            padding: 25px;
            color: white;
            box-shadow: var(--card-shadow);
            position: relative;
            overflow: hidden;
        }

        .game-join-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('../assets/images/kahoot-pattern.svg');
            opacity: 0.1;
            pointer-events: none;
        }

        .game-pin-input {
            background: white;
            border: none;
            border-radius: 12px;
            font-size: 2.5rem;
            font-weight: bold;
            text-align: center;
            letter-spacing: 8px;
            padding: 20px;
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            color: var(--kahoot-purple); /* Make pin text visible */
        }

        .game-pin-input::placeholder {
            color: rgba(70, 23, 143, 0.3); /* Lighter placeholder color */
            font-size: 1.8rem; /* Smaller placeholder text */
        }

        .join-button {
            background: var(--kahoot-pink);
            border: none;
            border-radius: 12px;
            padding: 15px 40px;
            font-size: 1.3rem;
            font-weight: bold;
            color: white;
            margin-top: 20px;
            transition: all 0.2s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .join-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 51, 85, 0.4);
        }

        .quick-actions {
            grid-column: span 12;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }

        .action-item {
            background: var(--card-bg);
            padding: 15px;
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .action-item:hover {
            transform: translateY(-2px);
        }

        .action-icon {
            font-size: 24px;
            color: var(--kahoot-purple);
            margin-bottom: 8px;
        }

        .recent-games {
            grid-column: span 6;
            background: var(--card-bg);
            border-radius: 16px;
            padding: 20px;
        }

        .achievements {
            grid-column: span 6;
            background: var(--card-bg);
            border-radius: 16px;
            padding: 20px;
        }

        .achievement-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 15px;
            padding: 15px;
        }

        .achievement-badge {
            text-align: center;
            padding: 15px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.9);
            transition: transform 0.2s;
            cursor: help;
        }

        .achievement-badge:hover {
            transform: translateY(-3px);
        }

        .achievement-icon {
            font-size: 2rem;
            margin-bottom: 8px;
            background: linear-gradient(135deg, var(--psu-blue), var(--kahoot-purple));
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .badge-locked .achievement-icon {
            background: #999;
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .leaderboard-card {
            grid-column: span 12;
            background: var(--card-bg);
            border-radius: 16px;
            padding: 20px;
            box-shadow: var(--card-shadow);
        }

        .leaderboard-table {
            width: 100%;
            border-spacing: 0;
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        .leaderboard-table tr {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .leaderboard-table td {
            padding: 12px 16px;
        }

        .leaderboard-table td:first-child {
            border-radius: 8px 0 0 8px;
        }

        .leaderboard-table td:last-child {
            border-radius: 0 8px 8px 0;
        }

        .level-badge {
            background: linear-gradient(135deg, var(--psu-blue), var(--kahoot-purple));
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
            margin: 10px 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .xp-progress {
            height: 10px;
            background: rgba(70, 23, 143, 0.1);
            border-radius: 5px;
            margin: 15px 0;
            overflow: hidden;
        }

        .xp-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--psu-blue), var(--kahoot-purple));
            border-radius: 5px;
            transition: width 0.3s ease;
        }

        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.9);
            padding: 15px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--kahoot-purple);
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                grid-template-columns: 1fr;
            }
            .recent-games,
            .achievements {
                grid-column: span 12;
            }
        }

        /* Interactive tooltips */
        .tooltip-custom {
            position: relative;
        }

        .tooltip-custom::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            padding: 8px;
            border-radius: 6px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            font-size: 0.8rem;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s;
        }

        .tooltip-custom:hover::after {
            opacity: 1;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .xp-gain-animation {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: var(--kahoot-purple);
            font-size: 2rem;
            font-weight: bold;
            animation: xpGain 1.5s ease-out forwards;
            pointer-events: none;
        }

        @keyframes xpGain {
            0% { opacity: 0; transform: translate(-50%, -50%) scale(0.5); }
            50% { opacity: 1; transform: translate(-50%, -50%) scale(1.2); }
            100% { opacity: 0; transform: translate(-50%, -100%) scale(1); }
        }

        @keyframes unlock {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            background: var(--psu-gold);
            top: -10px;
            animation: fall 3s linear forwards;
            z-index: 1000;
        }

        @keyframes fall {
            0% { transform: translateY(0) rotate(0deg); }
            100% { transform: translateY(100vh) rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Profile Card -->
        <div class="profile-card">
            <div class="avatar-section">
                <form method="POST" enctype="multipart/form-data">
                    <img src="<?= ASSETS_PATH ?>avatars/<?= htmlspecialchars($student['avatar'] ?? 'default_avatar.png') ?>" 
                         class="avatar-image" alt="Profile Avatar">
                    <div class="mt-3">
                        <input type="file" name="avatar" class="form-control" accept="image/*">
                        <button type="submit" class="btn btn-primary mt-2">Update Avatar</button>
                    </div>
                </form>
            </div>
            <div class="profile-info">
                <h2>Welcome, <?= htmlspecialchars($student['full_name']) ?>!</h2>
                <p class="text-muted">@<?= htmlspecialchars($student['username']) ?></p>
                <div class="text-center">
                    <div class="level-badge">
                        <i class="fas fa-star me-2"></i>Level <?= floor(($student['xp'] ?? 0) / 1000) + 1 ?>
                    </div>
                    <div class="xp-progress">
                        <div class="xp-progress-bar" style="width: <?= (($student['xp'] ?? 0) % 1000) / 10 ?>%"></div>
                    </div>
                    <p class="text-muted">
                        <?= number_format($student['xp'] ?? 0) ?> XP
                        <small>(<?= 1000 - (($student['xp'] ?? 0) % 1000) ?> XP to next level)</small>
                    </p>
                </div>
                <div class="quick-stats">
                    <div class="stat-card">
                        <div class="stat-value"><?= floor(($student['xp'] ?? 0) / 1000) + 1 ?></div>
                        <div class="stat-label">Current Level</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= number_format($student['xp'] ?? 0) ?></div>
                        <div class="stat-label">Total XP</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">
                            <?= 1000 - (($student['xp'] ?? 0) % 1000) ?>
                        </div>
                        <div class="stat-label">XP to Next Level</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <div class="action-item">
                <div class="action-icon"><i class="fas fa-trophy"></i></div>
                <div>Practice Mode</div>
            </div>
            <div class="action-item">
                <div class="action-icon"><i class="fas fa-users"></i></div>
                <div>Create Team</div>
            </div>
            <div class="action-item">
                <div class="action-icon"><i class="fas fa-history"></i></div>
                <div>History</div>
            </div>
            <div class="action-item">
                <div class="action-icon"><i class="fas fa-star"></i></div>
                <div>Achievements</div>
            </div>
        </div>

        <!-- Game Join Card -->
        <div class="game-join-card">
            <h3 class="text-center mb-4">
                <i class="fas fa-gamepad me-2"></i>Join a Battle!
            </h3>
            <form action="game.php" method="POST" class="text-center">
                <div class="mb-3">
                    <label class="text-white mb-2">Enter 6-digit Game PIN</label>
                    <input type="text" 
                           name="game_pin" 
                           class="game-pin-input" 
                           placeholder="000000"
                           maxlength="6"
                           pattern="[0-9]{6}"
                           required>
                </div>
                <button type="submit" class="join-button">
                    <i class="fas fa-play me-2"></i>Join Game
                </button>
            </form>
        </div>

        <!-- Recent Games -->
        <div class="recent-games">
            <h4><i class="fas fa-history me-2"></i>Recent Battles</h4>
            <div class="list-group">
                <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">Math Challenge</h6>
                        <small class="text-muted">2 days ago</small>
                    </div>
                    <span class="badge bg-primary rounded-pill">850 XP</span>
                </a>
                <!-- Add more recent games here -->
            </div>
        </div>

        <!-- Achievements -->
        <div class="achievements">
            <h4><i class="fas fa-medal me-2"></i>Achievements</h4>
            <div class="achievement-grid">
                <div class="achievement-badge tooltip-custom" data-tooltip="Win your first game!">
                    <div class="achievement-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <small>First Victory</small>
                </div>
                <div class="achievement-badge tooltip-custom" data-tooltip="Score 100% in any game">
                    <div class="achievement-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <small>Perfect Score</small>
                </div>
                <div class="achievement-badge badge-locked tooltip-custom" data-tooltip="Win 5 games in a row">
                    <div class="achievement-icon">
                        <i class="fas fa-fire"></i>
                    </div>
                    <small>Hot Streak</small>
                </div>
                <div class="achievement-badge badge-locked tooltip-custom" data-tooltip="Reach Level 10">
                    <div class="achievement-icon">
                        <i class="fas fa-crown"></i>
                    </div>
                    <small>Master</small>
                </div>
                <div class="achievement-badge badge-locked tooltip-custom" data-tooltip="Help 3 other students">
                    <div class="achievement-icon">
                        <i class="fas fa-hands-helping"></i>
                    </div>
                    <small>Helper</small>
                </div>
                <div class="achievement-badge badge-locked tooltip-custom" data-tooltip="Complete all subjects">
                    <div class="achievement-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <small>Scholar</small>
                </div>
            </div>
        </div>

        <!-- Leaderboard Section -->
        <div class="leaderboard-card">
            <h3>Leaderboard</h3>
            <div class="table-responsive">
                <table class="leaderboard-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Player</th>
                            <th>XP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($leaderboard as $index => $player): ?>
                        <tr class="<?= ($player['username'] === $student['username']) ? 'current-user' : '' ?>">
                            <td><?= $index + 1 ?></td>
                            <td>
                                <img src="<?= ASSETS_PATH ?>avatars/<?= htmlspecialchars($player['avatar'] ?? 'default_avatar.png') ?>" 
                                     alt="Avatar" 
                                     style="width: 30px; height: 30px; border-radius: 50%; margin-right: 10px;">
                                <?= htmlspecialchars($player['full_name']) ?>
                                <small class="text-muted">@<?= htmlspecialchars($player['username']) ?></small>
                            </td>
                            <td><?= number_format($player['total_xp']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Removed the parallax effect for simplicity
        // Add minimal hover effect to cards
        document.querySelectorAll('.profile-card, .game-join-card').forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-3px)';
            });
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0)';
            });
        });

        // Add pulse animation to join button when PIN is complete
        document.querySelector('.game-pin-input')?.addEventListener('input', function() {
            const joinButton = document.querySelector('.join-button');
            if (this.value.length === 6) {
                joinButton.style.animation = 'pulse 1.5s infinite';
            } else {
                joinButton.style.animation = 'none';
            }
        });

        // Add tooltip for locked achievements
        const lockedBadges = document.querySelectorAll('.badge-locked');
        lockedBadges.forEach(badge => {
            badge.title = 'Complete required tasks to unlock';
        });

        // Add animation for XP gains
        function animateXPGain(xpAmount) {
            const xpCounter = document.createElement('div');
            xpCounter.className = 'xp-gain-animation';
            xpCounter.textContent = `+${xpAmount} XP`;
            document.body.appendChild(xpCounter);
            setTimeout(() => xpCounter.remove(), 1500);
        }

        // Add PIN input formatting
        document.querySelector('.game-pin-input')?.addEventListener('input', function(e) {
            // Remove non-numeric characters
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Add visual feedback
            if (this.value.length === 6) {
                this.style.border = '2px solid #4CAF50';
                this.style.boxShadow = '0 0 10px rgba(76, 175, 80, 0.3)';
            } else {
                this.style.border = 'none';
                this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.1)';
            }
        });

        // Add achievement unlock animation
        function unlockAchievement(achievementElement) {
            achievementElement.classList.remove('badge-locked');
            achievementElement.style.animation = 'unlock 0.5s ease-out';
            
            // Show celebration effect
            createConfetti();
        }

        // Simple confetti effect
        function createConfetti() {
            for (let i = 0; i < 50; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.animationDelay = Math.random() * 3 + 's';
                document.body.appendChild(confetti);
                setTimeout(() => confetti.remove(), 3000);
            }
        }
    </script>
</body>
</html>
<?php include '../includes/footer.php'; ?>