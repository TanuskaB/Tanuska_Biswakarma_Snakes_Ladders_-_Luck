<?php
// index.php - Main page
require_once 'config.php';
require_once 'functions.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['logout'])) {
        logout();
        $message = 'Logged out successfully.';
    }
}

$leaderboard = get_scores();
$top_entry   = !empty($leaderboard) ? $leaderboard[0] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Snakes, Ladders &amp; Luck</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>🐍 Snakes, Ladders &amp; Luck 🪜</h1>

    <?php if ($message): ?>
        <p class="success">✅ <?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if (is_logged_in()): ?>

        <nav class="site-nav">
            <a href="index.php">🏠 Home</a>
            <a href="howtoplay.php">📖 How to Play</a>
            <a href="game.php">🎮 Play</a>
            <a href="leaderboard.php">🏆 Leaderboard</a>
            <span class="nav-user">👤 <?php echo htmlspecialchars(get_logged_user()); ?></span>
            <a href="logout.php">🚪 Logout</a>
        </nav>

        <!-- Hero welcome -->
        <div class="hero-section">
            <div class="hero-welcome">
                <span class="hero-wave">👋</span>
                <div>
                    <h2 class="hero-greeting">Welcome back, <?php echo htmlspecialchars(get_logged_user()); ?>!</h2>
                    <p class="hero-tagline">Roll. Climb. Dodge. Win.</p>
                </div>
            </div>

            <div class="feature-highlights">
                <div class="feature-chip">🎯 3 Difficulty Levels</div>
                <div class="feature-chip">👥 Solo &amp; Multiplayer</div>
                <div class="feature-chip">⭐ Bonus Tiles</div>
                <div class="feature-chip">🐍 Snakes &amp; Ladders</div>
            </div>

            <?php if ($top_entry): ?>
            <div class="top-score-card">
                <span class="top-score-crown">👑</span>
                <div class="top-score-info">
                    <p class="top-score-label">Current Champion</p>
                    <p class="top-score-name"><?php echo htmlspecialchars($top_entry['player']); ?></p>
                    <p class="top-score-detail">
                        <?php echo gmdate("H:i:s", $top_entry['time']); ?> &nbsp;&middot;&nbsp;
                        <?php echo isset($top_entry['turns']) ? $top_entry['turns'] . ' turns' : ''; ?> &nbsp;&middot;&nbsp;
                        <?php echo ucfirst($top_entry['difficulty']); ?>
                    </p>
                </div>
                <a href="leaderboard.php" class="top-score-link">Can you beat it? 🏆</a>
            </div>
            <?php else: ?>
            <div class="top-score-card">
                <span class="top-score-crown">🏆</span>
                <div class="top-score-info">
                    <p class="top-score-label">No games played yet</p>
                    <p class="top-score-name">Be the first champion!</p>
                </div>
            </div>
            <?php endif; ?>

            <div style="text-align:center; margin-top:22px;">
                <a href="game.php" class="btn-link" style="font-size:1.15rem; padding:14px 40px;">🎮 Play Now</a>
            </div>
        </div>

    <?php else: ?>

        <!-- Logged-out hero -->
        <div class="hero-section hero-loggedout">
            <p class="hero-tagline big">Roll. Climb. Dodge. Win.</p>
            <p class="hero-sub">The classic Snakes &amp; Ladders — now on the web. Pick your difficulty, challenge a friend, and race to cell 100!</p>

            <div class="feature-highlights">
                <div class="feature-chip">🎯 3 Difficulty Levels</div>
                <div class="feature-chip">👥 Solo &amp; Multiplayer</div>
                <div class="feature-chip">⭐ Bonus Tiles</div>
                <div class="feature-chip">🐍 Snakes &amp; Ladders</div>
            </div>

            <?php if ($top_entry): ?>
            <div class="top-score-card">
                <span class="top-score-crown">👑</span>
                <div class="top-score-info">
                    <p class="top-score-label">Current Champion</p>
                    <p class="top-score-name"><?php echo htmlspecialchars($top_entry['player']); ?></p>
                    <p class="top-score-detail">
                        <?php echo gmdate("H:i:s", $top_entry['time']); ?> &nbsp;&middot;&nbsp;
                        <?php echo ucfirst($top_entry['difficulty']); ?>
                    </p>
                </div>
                <span class="top-score-link">Can you beat it?</span>
            </div>
            <?php endif; ?>

            <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap; margin-top:22px;">
                <a href="register.php" class="btn-link">📝 Register</a>
                <a href="login.php" class="btn-link">🔓 Login</a>
            </div>
        </div>

    <?php endif; ?>

</div>
</body>
</html>