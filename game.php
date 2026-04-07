<?php
// game.php - The game page
require_once 'config.php';
require_once 'functions.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$game = get_game_state();
$narrator_message = '';
$winner = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['start_game'])) {
        $difficulty = $_POST['difficulty'];
        $players = (int)$_POST['players'];
        init_game($difficulty, $players);
        $game = get_game_state();
    } elseif (isset($_POST['roll_dice']) && $game) {
        $current_player = $game['current_player'];
        if (!$game['skipped_turns'][$current_player]) {
            $roll = roll_dice();
            $game['dice_history'][] = ['player' => $current_player, 'roll' => $roll, 'turn' => $game['turn_count']];
            
            $old_pos = $game['positions'][$current_player];
            $move_result = move_player($old_pos, $roll, $game['board']);
            $new_pos = $move_result['pos'];
            
            if ($move_result['type'] != 'normal') {
                $narrator_message = get_narrator_message($move_result['type'], ['{to}' => $new_pos]);
                $game['events_log'][] = ['turn' => $game['turn_count'], 'player' => $current_player, 'type' => $move_result['type'], 'from' => $move_result['from'], 'to' => $new_pos];
            }
            
            // Check for events
            $event = check_event($new_pos, $game['turn_count']);
            if ($event) {
                apply_event($event, $new_pos);
                $game['events_log'][] = ['turn' => $game['turn_count'], 'player' => $current_player, 'event' => $event, 'pos' => $new_pos];
                $game['last_event'] = $event;
                $narrator_message = get_narrator_message($event['type'], ['{to}' => $new_pos, '{move}' => abs($event['move']), '{msg}' => $event['msg']]);
            }
            
            // Check bonus
            $bonus = check_bonus($new_pos);
            if ($bonus) {
                $narrator_message = get_narrator_message($bonus, []);
                // Apply bonus effects
                if ($bonus == 'extra_roll') {
                    // Allow extra roll, don't switch player
                } elseif ($bonus == 'skip_turn') {
                    $next_player = ($current_player + 1) % count($game['positions']);
                    $game['skipped_turns'][$next_player] = true;
                }
            }
            
            $game['positions'][$current_player] = $new_pos;
            
            // Check win
            if (check_win($new_pos)) {
                $winner = $current_player;
                $time_played = time() - $game['start_time'];
                update_leaderboard(get_logged_user(), $time_played, $game['difficulty']);
            } else {
                // Switch player if not extra roll
                if (!$bonus || $bonus != 'extra_roll') {
                    do {
                        $game['current_player'] = ($game['current_player'] + 1) % count($game['positions']);
                    } while ($game['skipped_turns'][$game['current_player']]);
                }
            }
            
            $game['turn_count']++;
            $_SESSION['game'] = $game;
        } else {
            $game['skipped_turns'][$current_player] = false;
            $game['current_player'] = ($current_player + 1) % count($game['positions']);
            $_SESSION['game'] = $game;
        }
    }
}

if (!$game) {
    header('Location: index.php');
    exit;
}

// Generate board HTML
function generate_board($game) {
    $board_html = '<div class="board">';
    for ($i = 100; $i >= 1; $i--) {
        $cell_class = 'cell';
        if (isset($game['board']['snakes'][$i])) {
            $cell_class .= ' snake';
        } elseif (isset($game['board']['ladders'][$i])) {
            $cell_class .= ' ladder';
        }
        if (isset($GLOBALS['event_cells'][$i])) {
            $cell_class .= ' event';
        }
        if (isset($GLOBALS['bonus_tiles'][$i])) {
            $cell_class .= ' bonus';
        }
        $active = ($i == $game['positions'][$game['current_player']]) ? ' active' : '';
        $cell_class .= $active;
        
        $tokens = '';
        foreach ($game['positions'] as $p => $pos) {
            if ($pos == $i) {
                $tokens .= '<div class="token player' . ($p + 1) . '"></div>';
            }
        }
        
        $board_html .= "<div class=\"$cell_class\">$i$tokens</div>";
    }
    $board_html .= '</div>';
    return $board_html;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game - Snakes, Ladders & Luck</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Snakes, Ladders & Luck</h1>
        
        <?php if ($winner !== null): ?>
            <h2>🎉 Player <?php echo $winner + 1; ?> Wins! 🎉</h2>
            <p><a href="leaderboard.php">📊 View Leaderboard</a></p>
            <p><a href="index.php">🎮 Play Again</a></p>
        <?php else: ?>
            <div class="game-info">
                <div>👤 Player's Turn: <?php echo $game['current_player'] + 1; ?></div>
                <div>📍 Turn: <?php echo $game['turn_count']; ?></div>
                <div>⚙️ Difficulty: <?php echo ucfirst($game['difficulty']); ?></div>
            </div>
            
            <div class="game-container">
                <div class="game-board-wrapper">
                    <?php echo generate_board($game); ?>
                </div>
                
                <div class="players-info">
                    <?php foreach ($game['positions'] as $p => $pos): ?>
                        <div class="player-card player<?php echo $p + 1; ?><?php echo ($p == $game['current_player']) ? ' active' : ''; ?>">
                            <div class="player-name">🎯 Player <?php echo $p + 1; ?></div>
                            <div class="player-position"><?php echo $pos; ?>/100</div>
                            <div class="player-status">
                                <?php if ($p == $game['current_player']): ?>
                                    <strong>▶️ Your Turn!</strong>
                                <?php elseif ($game['skipped_turns'][$p]): ?>
                                    ⏸️ Skipped
                                <?php else: ?>
                                    ⏳ Waiting
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="game-controls">
                <form method="post">
                    <button type="submit" name="roll_dice">🎲 Roll Dice!</button>
                </form>
            </div>
            
            <?php if ($narrator_message): ?>
                <div class="narrator">
                    <h3>🎭 Narrator</h3>
                    <p><?php echo $narrator_message; ?></p>
                </div>
            <?php endif; ?>
            
            <h3>🎰 Dice History</h3>
            <div class="dice-history">
                <ul>
                    <?php foreach (array_reverse($game['dice_history']) as $history): ?>
                        <li>🎲 Player <?php echo $history['player'] + 1; ?> rolled <strong><?php echo $history['roll']; ?></strong> (Turn <?php echo $history['turn']; ?>)</li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <h3>⚡ Event Log</h3>
            <div class="event-log">
                <ul>
                    <?php foreach ($game['events_log'] as $log): ?>
                        <li>Turn <?php echo $log['turn']; ?> - Player <?php echo $log['player'] + 1; ?>: 
                            <?php if (isset($log['type'])): ?>
                                <?php echo ucfirst($log['type']); ?> from <?php echo $log['from']; ?> to <?php echo $log['to']; ?>
                            <?php elseif (isset($log['event'])): ?>
                                <?php echo $log['event']['msg']; ?> at <?php echo $log['pos']; ?>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <p><a href="index.php">Back to Home</a></p>
    </div>
</body>
</html>