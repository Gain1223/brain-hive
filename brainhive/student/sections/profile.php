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
        <h2>Welcome, <?= htmlspecialchars($student['username']) ?>!</h2>
        <div class="level-badge">
            <i class="fas fa-star me-2"></i>Level <?= floor(($student['xp'] ?? 0) / 1000) + 1 ?>
        </div>
        <div class="xp-progress">
            <div class="xp-progress-bar" style="width: <?= (($student['xp'] ?? 0) % 1000) / 10 ?>%"></div>
        </div>
        <div class="quick-stats">
            <!-- Stats cards here -->
        </div>
    </div>
</div>
