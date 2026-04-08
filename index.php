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
        <p class="success">✅ <?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <?php if (is_logged_in()): ?>

        <nav class="site-nav">
            <span class="nav-user">👤 <?= htmlspecialchars(get_logged_user()) ?></span>
            <a href="leaderboard.php">🏆 Leaderboard</a>
            <a href="logout.php">🚪 Logout</a>
        </nav>

        <h2>🎮 Start New Game</h2>
        <form action="game.php" method="post" style="max-width:440px; margin:0 auto;">

            <!-- Player 1 name -->
            <div class="form-group">
                <label for="player1_name">🔴 Player 1 Name:</label>
                <input type="text"
                        name="player1_name"
                        id="player1_name"
                        value="<?= htmlspecialchars(get_logged_user()) ?>"
                        placeholder="Your display name"
                        maxlength="20"
                        required>
            </div>

            <!-- Solo or Multiplayer toggle -->
            <div class="form-group">
                <label>👥 Game Mode:</label>
                <div class="mode-toggle">
                    <label class="mode-option">
                        <input type="radio" name="players" value="1" id="solo">
                        <span class="mode-btn">🎮 Solo</span>
                    </label>
                    <label class="mode-option">
                        <input type="radio" name="players" value="2" id="multi" checked>
                        <span class="mode-btn">🎮🎮 Multiplayer</span>
                    </label>
                </div>
            </div>

            <!-- Player 2 name -->
            <div class="form-group" id="player2Group">
                <label for="player2_name">🟢 Player 2 Name:</label>
                <input type="text"
                        name="player2_name"
                        id="player2_name"
                        placeholder='Enter Player 2 name'
                        maxlength="20">
            </div>

            <div class="form-group">
                <label for="difficulty">🎯 Difficulty Level:</label>
                <select name="difficulty" id="difficulty">
                    <option value="beginner">🌱 Beginner (3 snakes, 3 ladders)</option>
                    <option value="standard" selected>⭐ Standard (6 snakes, 5 ladders)</option>
                    <option value="expert">🔥 Expert (9 snakes, 4 ladders)</option>
                </select>
            </div>

            <button type="submit" name="start_game" style="width:100%;">🚀 Start Game!</button>
        </form>

        <script>
            // Show or hide Player 2 name field based on mode selection
            const soloBtn  = document.getElementById('solo');
            const multiBtn = document.getElementById('multi');
            const p2Group  = document.getElementById('player2Group');
            const p2Input  = document.getElementById('player2_name');

            function updateMode() {
                if (soloBtn.checked) {
                    p2Group.style.display = 'none';
                    p2Input.value = 'CPU';
                } else {
                    p2Group.style.display = 'block';
                    if (p2Input.value === 'CPU') p2Input.value = '';
                }
            }

            soloBtn.addEventListener('change', updateMode);
            multiBtn.addEventListener('change', updateMode);
            updateMode(); // run on load
        </script>

    <?php else: ?>

        <h2>Let's Get Started! 🎲</h2>
        <p style="text-align:center; font-size:1.1em; margin-bottom:25px;">
            Login or Register to play!
        </p>
        <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
            <a href="login.php" class="btn-link">🔓 Login</a>
            <a href="register.php" class="btn-link">📝 Register</a>
        </div>

    <?php endif; ?>
</div>
</body>
</html>