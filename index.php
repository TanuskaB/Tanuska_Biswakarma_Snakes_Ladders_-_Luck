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
    <h1>&#x1F40D; Snakes, Ladders &amp; Luck &#x1FA9C;</h1>

    <?php if ($message): ?>
        <p class="success">&#x2705; <?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if (is_logged_in()): ?>

        <nav class="site-nav">
            <a href="index.php">&#x1F3E0; Home</a>
            <a href="howtoplay.php">&#x1F4D6; How to Play</a>
            <a href="game.php">&#x1F3AE; Play</a>
            <a href="leaderboard.php">&#x1F3C6; Leaderboard</a>
            <span class="nav-user">&#x1F464; <?php echo htmlspecialchars(get_logged_user()); ?></span>
            <a href="logout.php">&#x1F6AA; Logout</a>
        </nav>

        <h2>&#x1F3AE; Start New Game</h2>
        <form action="game.php" method="post" style="max-width:440px; margin:0 auto;">

            <div class="form-group">
                <label for="player1_name">&#x1F534; Player 1 Name:</label>
                <input type="text"
                        name="player1_name"
                        id="player1_name"
                        value="<?php echo htmlspecialchars(get_logged_user()); ?>"
                        placeholder="Your display name"
                        maxlength="20"
                        required>
            </div>

            <div class="form-group">
                <label>&#x1F465; Game Mode:</label>
                <div class="mode-toggle">
                    <label class="mode-option">
                        <input type="radio" name="players" value="1" id="solo">
                        <span class="mode-btn">&#x1F3AE; Solo</span>
                    </label>
                    <label class="mode-option">
                        <input type="radio" name="players" value="2" id="multi" checked>
                        <span class="mode-btn">&#x1F3AE;&#x1F3AE; Multiplayer</span>
                    </label>
                </div>
            </div>

            <div class="form-group" id="player2Group">
                <label for="player2_name">&#x1F7E2; Player 2 Name:</label>
                <input type="text"
                        name="player2_name"
                        id="player2_name"
                        placeholder="Enter Player 2 name"
                        maxlength="20">
            </div>

            <div class="form-group">
                <label for="difficulty">&#x1F3AF; Difficulty Level:</label>
                <select name="difficulty" id="difficulty">
                    <option value="beginner">&#x1F331; Beginner (3 snakes, 3 ladders)</option>
                    <option value="standard" selected>&#x2B50; Standard (6 snakes, 5 ladders)</option>
                    <option value="expert">&#x1F525; Expert (9 snakes, 4 ladders)</option>
                </select>
            </div>

            <button type="submit" name="start_game" style="width:100%;">&#x1F680; Start Game!</button>
        </form>

        <script>
            var soloBtn = document.getElementById('solo');
            var multiBtn = document.getElementById('multi');
            var p2Group = document.getElementById('player2Group');
            var p2Input = document.getElementById('player2_name');

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
            updateMode();
        </script>

    <?php endif; ?>

    <!-- How to Play -->
    <div class="how-to-play">
        <h2>&#x1F4D6; How to Play</h2>
        <div class="steps-grid">
            <div class="step-card">
                <span class="step-num">1</span>
                <span class="step-icon">&#x1F4DD;</span>
                <h3>Register &amp; Login</h3>
                <p>Create a free account, then log in to access the game board.</p>
            </div>
            <div class="step-card">
                <span class="step-num">2</span>
                <span class="step-icon">&#x1F3AF;</span>
                <h3>Pick Your Settings</h3>
                <p>Choose Solo or Multiplayer, enter player names, and pick a difficulty level.</p>
            </div>
            <div class="step-card">
                <span class="step-num">3</span>
                <span class="step-icon">&#x1F3B2;</span>
                <h3>Roll the Dice</h3>
                <p>Take turns rolling. Move across the 100-cell board you can climb ladders, dodge snakes!</p>
            </div>
            <div class="step-card">
                <span class="step-num">4</span>
                <span class="step-icon">&#x1F3C6;</span>
                <h3>First to 100 Wins</h3>
                <p>Race your opponent to cell 100. Land exactly or bounce back from the edge!</p>
            </div>
        </div>

        <div class="rules-row">
            <div class="rule-badge snake-rule">&#x1F40D; Snake : slides you DOWN</div>
            <div class="rule-badge ladder-rule">&#x1FA9C; Ladder : shoots you UP</div>
            <div class="rule-badge event-rule">&#x26A1; Event : random twist!</div>
            <div class="rule-badge bonus-rule">&#x2B50; Bonus : lucky reward!</div>
        </div>
    </div>

    <?php if (!is_logged_in()): ?>
        <h2>Let's Get Started! &#x1F3B2;</h2>
        <p style="text-align:center; font-size:1.1em; margin-bottom:25px;">
            Login or Register to play!
        </p>
        <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
            <a href="login.php" class="btn-link">&#x1F513; Login</a>
            <a href="register.php" class="btn-link">&#x1F4DD; Register</a>
        </div>
    <?php endif; ?>

</div>
</body>
</html>