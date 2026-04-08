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
    <title>Snakes, Ladders & Luck</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Snakes, Ladders & Luck</h1>
        <?php if ($message): ?>
            <p class="success">✅ <?php echo $message; ?></p>
        <?php endif; ?>
        
        <?php if (is_logged_in()): ?>
            <h2>👋 Welcome, <?php echo htmlspecialchars(get_logged_user()); ?>!</h2>
            
            <div style="text-align: center; margin-bottom: 25px;">
                <form method="post" style="display: inline;">
                    <button type="submit" name="logout" style="padding: 10px 20px; font-size: 14px;">🚪 Logout</button>
                </form>
            </div>
            
            <h2>🎮 Start New Game</h2>
            <form action="game.php" method="post" style="max-width: 400px; margin: 0 auto;">
                <div class="form-group">
                    <label for="difficulty">🎯 Difficulty Level:</label>
                    <select name="difficulty" id="difficulty">
                        <option value="beginner">🌱 Beginner (3 snakes, 3 ladders)</option>
                        <option value="standard" selected>⭐ Standard (6 snakes, 5 ladders)</option>
                        <option value="expert">🔥 Expert (9 snakes, 4 ladders)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="players">👥 Number of Players:</label>
                    <select name="players" id="players">
                        <option value="1">🎮 1 Player (Solo)</option>
                        <option value="2" selected>🎮🎮 2 Players (Multiplayer)</option>
                    </select>
                </div>
                <button type="submit" name="start_game">Start Game! 🚀</button>
            </form>
            
            <div style="text-align: center; margin-top: 30px;">
                <h3 style="margin-bottom: 15px;">📊 Game Stats</h3>
                <a href="leaderboard.php" style="font-size: 1.1em; margin: 10px;">📈 View Leaderboard →</a>
            </div>
        <?php else: ?>
            <h2 style="font-size: 1.5em; margin-bottom: 20px;">Let's Get Started! 🎲</h2>
            <p style="text-align: center; font-size: 1.1em; margin-bottom: 25px;">Join the fun! Login or Register to play with friends.</p>
            <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                <a href="login.php" style="padding: 15px 30px; font-size: 1.1em; background: linear-gradient(135deg, #00d4ff, #667eea); color: white; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.2);"><strong>🔓 Login</strong></a>
                <a href="register.php" style="padding: 15px 30px; font-size: 1.1em; background: linear-gradient(135deg, #51cf66, #40c057); color: white; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.2);"><strong>📝 Register</strong></a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>