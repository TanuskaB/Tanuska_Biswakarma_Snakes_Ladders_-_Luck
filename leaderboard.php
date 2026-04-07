<?php
// leaderboard.php - Leaderboard page
require_once 'config.php';
require_once 'functions.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$leaderboard = $_SESSION['leaderboard'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard - Snakes, Ladders & Luck</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>🏆 Leaderboard 🏆</h1>
        <div class="leaderboard">
            <table>
                <thead>
                    <tr>
                        <th>🥇 Rank</th>
                        <th>👤 Player</th>
                        <th>⏱️ Time</th>
                        <th>⚙️ Difficulty</th>
                        <th>📅 Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($leaderboard)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 20px;">No games completed yet. <a href="index.php">Play now!</a> 🎮</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($leaderboard as $rank => $entry): ?>
                            <tr>
                                <td>
                                    <?php 
                                    if ($rank == 0) echo '🥇';
                                    elseif ($rank == 1) echo '🥈';
                                    elseif ($rank == 2) echo '🥉';
                                    else echo '#' . ($rank + 1);
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($entry['player']); ?></td>
                                <td><strong><?php echo gmdate("H:i:s", $entry['time']); ?></strong></td>
                                <td>
                                    <?php 
                                    $diff = ucfirst($entry['difficulty']);
                                    if ($diff == 'Beginner') echo '🌱 ' . $diff;
                                    elseif ($diff == 'Standard') echo '⭐ ' . $diff;
                                    else echo '🔥 ' . $diff;
                                    ?>
                                </td>
                                <td><?php echo date("M d, Y H:i", $entry['timestamp']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <p style="text-align: center; margin-top: 25px;"><a href="index.php" style="font-size: 1.1em;">🏠 Back to Home</a></p>
    </div>
</body>
</html>