<?php
require_once 'config.php';
require_once 'functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How to Play - Snakes, Ladders &amp; Luck</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>&#x1F40D; Snakes, Ladders &amp; Luck &#x1FA9C;</h1>

    <nav class="site-nav">
        <a href="index.php">&#x1F3E0; Home</a>
        <a href="howtoplay.php">&#x1F4D6; How to Play</a>
        <?php if (is_logged_in()): ?>
            <a href="game.php">&#x1F3AE; Play</a>
            <a href="leaderboard.php">&#x1F3C6; Leaderboard</a>
            <span class="nav-user">&#x1F464; <?php echo htmlspecialchars(get_logged_user()); ?></span>
            <a href="logout.php">&#x1F6AA; Logout</a>
        <?php else: ?>
            <a href="login.php">&#x1F513; Login</a>
            <a href="register.php">&#x1F4DD; Register</a>
        <?php endif; ?>
    </nav>

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
                <p>Choose Solo or Multiplayer, enter player names, and select a difficulty level.</p>
            </div>
            <div class="step-card">
                <span class="step-num">3</span>
                <span class="step-icon">&#x1F3B2;</span>
                <h3>Roll the Dice</h3>
                <p>Take turns clicking Roll Dice. Your piece moves automatically across the 100-cell board.</p>
            </div>
            <div class="step-card">
                <span class="step-num">4</span>
                <span class="step-icon">&#x1F3C6;</span>
                <h3>First to 100 Wins</h3>
                <p>Race to cell 100. Overshoot and you bounce back by the extra steps!</p>
            </div>
        </div>

        <!-- Rules -->
        <div class="rules-row">
            <div class="rule-badge snake-rule">&#x1F40D; Snake : slides you DOWN</div>
            <div class="rule-badge ladder-rule">&#x1FA9C; Ladder : shoots you UP</div>
            <div class="rule-badge event-rule">&#x26A1; Event : random twist!</div>
            <div class="rule-badge bonus-rule">&#x2B50; Bonus : lucky reward!</div>
        </div>

        <!-- Special tiles -->
        <h3 style="margin-top:25px;">&#x2728; Special Tiles</h3>
        <div class="special-tiles-grid">
            <div class="special-tile">
                <span class="special-icon">&#x1F3B2;</span>
                <strong>Extra Roll</strong>
                <p>Land on cell 25 and roll again immediately!</p>
            </div>
            <div class="special-tile">
                <span class="special-icon">&#x23F8;&#xFE0F;</span>
                <strong>Skip Turn</strong>
                <p>Land on cell 50 and your opponent loses their next turn.</p>
            </div>
            <div class="special-tile">
                <span class="special-icon">&#x2753;</span>
                <strong>Mystery Boost</strong>
                <p>Land on cell 75 for a surprise random effect!</p>
            </div>
        </div>

        <!-- Difficulty -->
        <h3 style="margin-top:25px;">&#x1F3AF; Difficulty Levels</h3>
        <div class="diff-table-wrap">
            <table class="diff-table">
                <thead>
                    <tr>
                        <th>Level</th>
                        <th>Snakes</th>
                        <th>Ladders</th>
                        <th>Best for</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>&#x1F331; Beginner</td>
                        <td>3</td>
                        <td>3</td>
                        <td>First-time players</td>
                    </tr>
                    <tr>
                        <td>&#x2B50; Standard</td>
                        <td>6</td>
                        <td>5</td>
                        <td>Classic experience</td>
                    </tr>
                    <tr>
                        <td>&#x1F525; Expert</td>
                        <td>9</td>
                        <td>4</td>
                        <td>A real challenge!</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="text-align:center; margin-top:28px;">
            <?php if (is_logged_in()): ?>
                <a href="index.php" class="btn-link">&#x1F680; Start Playing!</a>
            <?php else: ?>
                <a href="register.php" class="btn-link">&#x1F4DD; Register to Play</a>
            <?php endif; ?>
        </div>
    </div>

</div>
</body>
</html>