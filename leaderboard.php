<?php
// leaderboard.php - Leaderboard page
require_once 'config.php';
require_once 'functions.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$leaderboard = get_scores();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard - Snakes, Ladders &amp; Luck</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>🏆 Leaderboard 🏆</h1>

    <nav class="site-nav">
        <a href="index.php">&#x1F3E0; Home</a>
        <a href="howtoplay.php">&#x1F4D6; How to Play</a>
        <a href="game.php">&#x1F3AE; Play</a>
        <span class="nav-user">&#x1F464; <?= htmlspecialchars(get_logged_user()) ?></span>
        <a href="logout.php">&#x1F6AA; Logout</a>
    </nav>

    <div class="leaderboard">
        <table>
            <thead>
                <tr>
                    <th>🥇 Rank</th>
                    <th>👤 Player Name</th>
                    <th>⏱️ Time</th>
                    <th>🎲 Turns</th>
                    <th>⚙️ Difficulty</th>
                    <th>📅 Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($leaderboard)): ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding:20px;">
                        No games completed yet. <a href="index.php">Play now!</a> 🎮
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($leaderboard as $rank => $entry): ?>
                <tr>
                    <td>
                        <?php
                            if ($rank == 0)      echo '🥇';
                            elseif ($rank == 1)  echo '🥈';
                            elseif ($rank == 2)  echo '🥉';
                            else                 echo '#' . ($rank + 1);
                        ?>
                    </td>
                    <td><?= htmlspecialchars($entry['player']) ?></td>
                    <td><strong><?= gmdate("H:i:s", $entry['time']) ?></strong></td>
                    <td><?= isset($entry['turns']) ? $entry['turns'] : '—' ?></td>
                    <td>
                        <?php
                            $diff = ucfirst($entry['difficulty']);
                            if ($diff === 'Beginner')    echo '🌱 ' . $diff;
                            elseif ($diff === 'Standard') echo '⭐ ' . $diff;
                            else                          echo '🔥 ' . $diff;
                        ?>
                    </td>
                    <td><?= date("M d, Y H:i", $entry['timestamp']) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <p style="text-align:center; margin-top:25px;">
        <a href="index.php">🏠 Back to Home</a>
    </p>
</div>
</body>
</html>