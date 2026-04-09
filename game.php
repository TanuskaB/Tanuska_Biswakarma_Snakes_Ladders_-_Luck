<?php
// game.php - The game page
require_once 'config.php';
require_once 'functions.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

// Clear session game when starting a new one (int)
if (isset($_GET['new'])) {
    unset($_SESSION['game']);
}

$game             = get_game_state();
$narrator_message = '';
$winner           = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['start_game'])) {
        $difficulty   = $_POST['difficulty'];
        $players      = (int)($_POST['players'] ?? 2);
        $player1_name = sanitize($_POST['player1_name'] ?? get_logged_user() ?? 'Player 1');
        $player2_name = $players === 1 ? 'CPU' : sanitize($_POST['player2_name'] ?? 'Player 2');
        init_game($difficulty, $players);
        $_SESSION['game']['player_names'] = [
            0 => $player1_name ?: (get_logged_user() ?? 'Player 1'),
            1 => $player2_name ?: 'Player 2'
        ];
        $game = get_game_state();
    } elseif (isset($_POST['roll_dice']) && $game) {
        $current_player = $game['current_player'];
        if (!$game['skipped_turns'][$current_player]) {
            $turn_elapsed = isset($_POST['turn_elapsed']) ? (int)$_POST['turn_elapsed'] : (time() - ($game['turn_start_time'] ?? time()));
            $game['turn_durations'][] = [
                'turn'     => $game['turn_count'],
                'player'   => $current_player,
                'duration' => $turn_elapsed
            ];
            $roll = roll_dice();
            $game['dice_history'][] = ['player' => $current_player, 'roll' => $roll, 'turn' => $game['turn_count'], 'duration' => $turn_elapsed];
            
            $old_pos = $game['positions'][$current_player];
            $move_result = move_player($old_pos, $roll, $game['board']);
            $new_pos = $move_result['pos'];
            $move_type = $move_result['type'];
            
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
                $move_type = $event['type'];
            }
            
            // Check bonus
            $bonus = check_bonus($new_pos);
            if ($bonus) {
                $narrator_message = get_narrator_message($bonus, []);
                $move_type = $bonus;
                if ($bonus == 'extra_roll') {
                    // Allow extra roll, don't switch player
                } elseif ($bonus == 'skip_turn') {
                    $next_player = ($current_player + 1) % count($game['positions']);
                    $game['skipped_turns'][$next_player] = true;
                }
            }

            $game['last_roll']     = $roll;
            $game['last_type']     = $move_type;
            $game['last_narrator'] = $narrator_message;
            
            $game['positions'][$current_player] = $new_pos;
            
            // Check win
            if (check_win($new_pos)) {
                $winner = $current_player;
                $time_played = time() - $game['start_time'];
                update_leaderboard($game['player_names'][$current_player], $time_played, $game['difficulty'], $game['turn_count']);
                $game['winner']      = $current_player;
                $game['time_played'] = $time_played;
            } else {
                if (!$bonus || $bonus != 'extra_roll') {
                    do {
                        $game['current_player'] = ($game['current_player'] + 1) % count($game['positions']);
                    } while ($game['skipped_turns'][$game['current_player']]);
                }
            }
            
            $game['turn_count']++;
            $game['turn_start_time'] = time();
            $_SESSION['game'] = $game;
        } else {
            $game['skipped_turns'][$current_player] = false;
            $game['current_player'] = ($current_player + 1) % count($game['positions']);
            $game['turn_start_time'] = time();
            $_SESSION['game'] = $game;
        }
    }
}

// If no active game show setup form
if (!$game) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Play - Snakes, Ladders &amp; Luck</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
    <div class="container">
        <h1>🐍 Snakes, Ladders &amp; Luck 🪜</h1>

        <nav class="site-nav">
            <a href="index.php">🏠 Home</a>
            <a href="howtoplay.php">📖 How to Play</a>
            <a href="game.php">🎮 Play</a>
            <a href="leaderboard.php">🏆 Leaderboard</a>
            <span class="nav-user">👤 <?php echo htmlspecialchars(get_logged_user()); ?></span>
            <a href="logout.php">🚪 Logout</a>
        </nav>

        <div class="game-setup-wrap">
            <h2>🎮 Set Up Your Game</h2>
            <form action="game.php" method="post">
                
                <div class="form-group">
                    <label for="player1_name">🔴 Player 1 Name:</label>
                    <input type="text"
                            name="player1_name"
                            id="player1_name"
                            value="<?php echo htmlspecialchars(get_logged_user()); ?>"
                            placeholder="Your display name"
                            maxlength="20"
                            required>
                </div>
                

                <div class="form-group">
                    <label>👥 Game Mode:</label>
                    <div class="mode-toggle">
                        <label class="mode-option">
                            <input type="radio" name="players" value="1" id="solo">
                            <span class="mode-btn">🎮 Solo</span>
                        </label>
                        <label class="mode-option">
                            <input type="radio" name="players" value="2" id="multi">
                            <span class="mode-btn">🎮🎮 Multiplayer</span>
                        </label>
                    </div>
                </div>

               
                <div class="form-group">
                    <label for="player2_name">🟢 Player 2 Name: </label>
                    <input type="text"
                            name="player2_name"
                            id="player2_name"
                            placeholder="Enter Player 2 name or leave blank for CPU"
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
        </div>
    </div>
    </body>
    </html>
    <?php
    exit;
}

// Restore winner and narrator from session after reload
if (isset($game['winner'])) $winner = $game['winner'];
if (!$narrator_message && !empty($game['last_narrator'])) {
    $narrator_message = $game['last_narrator'];
}

$player_names = $game['player_names'] ?? ['0' => 'Player 1', '1' => 'Player 2'];
$last_roll    = $game['last_roll'] ?? null;
$dice_faces   = [1 => '⚀', 2 => '⚁', 3 => '⚂', 4 => '⚃', 5 => '⚄', 6 => '⚅'];

// Determine if a popup should show for this roll
$popup_type = $game['last_type'] ?? 'normal';
$popup_msg  = $game['last_narrator'] ?? '';
$big_events = ['snake', 'ladder', 'bonus', 'extra_roll', 'skip_turn', 'mystery_boost', 'warp', 'skip', 'penalty'];
$show_popup = in_array($popup_type, $big_events) && $popup_msg !== '';

if ($winner !== null) {
    $show_popup  = true;
    $popup_type  = 'win';
    $popup_msg   = '🏆 ' . htmlspecialchars($player_names[$winner]) . ' Wins!';
}

$popup_icons = [
    'snake'         => '🐍',
    'ladder'        => '🪜',
    'bonus'         => '⭐',
    'extra_roll'    => '🎲',
    'skip_turn'     => '⏸️',
    'mystery_boost' => '❓',
    'warp'          => '✨',
    'skip'          => '⏸️',
    'penalty'       => '😞',
    'win'           => '🏆',
];
$popup_icon = $popup_icons[$popup_type] ?? '🎉';

function generate_board($game) {
    global $event_cells, $bonus_tiles;

    $rows = [];
    for ($row = 0; $row < 10; $row++) {
        $cells = [];
        for ($col = 0; $col < 10; $col++) {
            $num = ($row % 2 === 0)
                ? $row * 10 + $col + 1
                : $row * 10 + (10 - $col);
            $cells[] = $num;
        }
        array_unshift($rows, $cells);
    }

    $board_html = '<div class="board">';
    foreach ($rows as $row_cells) {
        foreach ($row_cells as $i) {
            $cell_class = 'cell';
            $token_type = '';

            if (isset($game['board']['snakes'][$i])) {
                $cell_class .= ' snake';
                $token_type  = 'snake';
            } elseif (isset($game['board']['ladders'][$i])) {
                $cell_class .= ' ladder';
                $token_type  = 'ladder';
            }
            if (isset($event_cells[$i]))  $cell_class .= ' event';
            if (isset($bonus_tiles[$i]))  $cell_class .= ' bonus';

            $active = ($i == $game['positions'][$game['current_player']]) ? ' active' : '';
            $cell_class .= $active;

            $tokens = '';
            foreach ($game['positions'] as $p => $pos) {
                if ($pos == $i) {
                    $tokens .= '<div class="token player' . ($p + 1) . ' ' . $token_type . '-token"></div>';
                }
            }

            $board_html .= "<div class=\"{$cell_class}\" data-cell=\"{$i}\">";
            $board_html .= "<span class=\"cell-num\">{$i}</span>";
            $board_html .= $tokens;
            $board_html .= "</div>";
        }
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
    <title>Game - Snakes, Ladders &amp; Luck</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php if ($show_popup && $winner === null): ?>
<div class="event-popup popup-<?= htmlspecialchars($popup_type) ?>" id="eventPopup">
    <div class="popup-inner">
        <span class="popup-icon"><?= $popup_icon ?></span>
        <p class="popup-msg"><?= htmlspecialchars($popup_msg) ?></p>
    </div>
</div>
<?php endif; ?>

    <div class="container">
        <h1>Snakes, Ladders &amp; Luck</h1>

        <?php if ($winner !== null): ?>
        <!-- Confetti -->
        <div class="confetti-wrap" aria-hidden="true">
            <?php
            $colors = ['#667eea','#ff6b9d','#51cf66','#ffd93d','#00d4ff','#ff6b6b','#764ba2','#ff9d3d','#a9e34b'];
            for ($c = 1; $c <= 18; $c++):
                $color = $colors[($c - 1) % count($colors)];
                $left  = rand(2, 98);
                $delay = round(($c * 0.18), 2);
                $dur   = round(1.8 + ($c % 5) * 0.25, 2);
                $size  = rand(8, 16);
                $shape = $c % 3 === 0 ? 'border-radius:50%' : ($c % 3 === 1 ? 'border-radius:2px;transform:rotate(45deg)' : '');
            ?>
            <div class="confetti-piece" style="
                left:<?= $left ?>%;
                background:<?= $color ?>;
                width:<?= $size ?>px;
                height:<?= $size ?>px;
                animation-delay:<?= $delay ?>s;
                animation-duration:<?= $dur ?>s;
                <?= $shape ?>
            "></div>
            <?php endfor; ?>
        </div>
        <?php endif; ?>

        <nav class="site-nav">
            <a href="index.php">🏠 Home</a>
            <a href="howtoplay.php">📖 How to Play</a>
            <a href="leaderboard.php">🏆 Leaderboard</a>
            <span class="nav-user">👤 <?= htmlspecialchars(get_logged_user()) ?></span>
            <a href="logout.php">🚪 Logout</a>
        </nav>
        
        <?php if ($winner !== null): ?>
            <!-- Win Screen and Adventure Recap -->
            <div class="winner-banner">
                <h2>🎉 <?= htmlspecialchars($player_names[$winner]) ?> Wins! 🎉</h2>
                <p>Time: <strong><?= gmdate("H:i:s", $game['time_played'] ?? 0) ?></strong>
                    &nbsp;|&nbsp; Turns: <strong><?= $game['turn_count'] ?></strong>
                    &nbsp;|&nbsp; Difficulty: <strong><?= ucfirst($game['difficulty']) ?></strong></p>
            </div>

            <!-- Adventure Recap -->
            <div class="adventure-recap">
                <h3>📜 Adventure Recap</h3>
                <?php if (!empty($game['events_log'])): ?>
                <p class="recap-subtitle">Every twist and turn of your journey:</p>
                <ul class="recap-list">
                    <?php foreach ($game['events_log'] as $log): ?>
                    <li>
                        <span class="recap-turn">Turn <?= $log['turn'] ?></span>
                        <span class="recap-player"><?= htmlspecialchars($player_names[$log['player']]) ?></span>
                        <?php if (isset($log['type'])): ?>
                            <?php if ($log['type'] === 'snake'): ?>
                                🐍 Hit a snake at <?= $log['from'] ?> &rarr; slid down to <?= $log['to'] ?>
                            <?php elseif ($log['type'] === 'ladder'): ?>
                                🪜 Climbed a ladder at <?= $log['from'] ?> &rarr; shot up to <?= $log['to'] ?>
                            <?php endif; ?>
                        <?php elseif (isset($log['event'])): ?>
                            ⚡ <?= htmlspecialchars($log['event']['msg']) ?> at cell <?= $log['pos'] ?>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <p>A clean run — no snakes, ladders, or events triggered!</p>
                <?php endif; ?>
            </div>

            <p style="text-align:center; margin-top:20px;">
                <a href="leaderboard.php">📊 View Leaderboard</a>
                &nbsp;&nbsp;
                <a href="game.php?new=1">🎮 Play Again</a>
            </p>

        <?php else: ?>
            <!-- Active Game -->
            <div class="game-info">
                <div>👤 <?= htmlspecialchars($player_names[$game['current_player']]) ?>'s Turn</div>
                <div>📍 Turn: <?php echo $game['turn_count']; ?></div>
                <div>⏱️ Turn Time: <span id="turnTimer">0s</span></div>
                <div>⚙️ Difficulty: <?php echo ucfirst($game['difficulty']); ?></div>
            </div>
            
            <div class="symbols-guide">
                <h3>🎯 Game Symbols Guide</h3>
                <div class="symbols-list">
                    <div class="symbol-item">
                        <span class="symbol">🐍</span>
                        <span class="description">Snake - Slide down to a lower number!</span>
                    </div>
                    <div class="symbol-item">
                        <span class="symbol">🪜</span>
                        <span class="description">Ladder - Climb up to a higher number!</span>
                    </div>
                    <div class="symbol-item">
                        <span class="symbol">⚡</span>
                        <span class="description">Event - Special random events that can help or hinder!</span>
                    </div>
                    <div class="symbol-item">
                        <span class="symbol">⭐</span>
                        <span class="description">Bonus - Lucky tiles that give extra rolls or special advantages!</span>
                    </div>
                    <div class="symbol-item">
                        <span class="symbol">🎯</span>
                        <span class="description">Player Token - Shows where each player is on the board!</span>
                    </div>
                </div>
            </div>
            
            <div class="game-container">
                <div class="game-board-wrapper">
                    <?php echo generate_board($game); ?>
                </div>
                
                <div class="players-info">
                    <?php foreach ($game['positions'] as $p => $pos): ?>
                        <div class="player-card player<?php echo $p + 1; ?><?php echo ($p == $game['current_player']) ? ' active' : ''; ?>">
                            <div class="player-name">
                                <?= $p === 0 ? '🔴' : '🟢' ?>
                                <?= htmlspecialchars($player_names[$p]) ?>
                            </div>
                            <div class="player-position"><?php echo $pos; ?>/100</div>
                            <div class="progress-wrap">
                                <div class="progress-bar player<?= $p + 1 ?>-bar"
                                        style="width:<?= $pos ?>%"></div>
                            </div>
                            <div class="progress-label"><?= $pos ?>% to finish</div>
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

                    <div class="dice-panel">
                        <div class="dice-face <?= $last_roll ? 'rolled' : '' ?>">
                            <?= $last_roll ? $dice_faces[$last_roll] : '🎲' ?>
                        </div>
                        <?php if ($last_roll): ?>
                            <p class="dice-label">Last roll: <strong><?= $last_roll ?></strong></p>
                        <?php else: ?>
                            <p class="dice-label">Roll to start!</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="game-controls">
                <form method="post" id="rollForm">
                    <input type="hidden" name="turn_elapsed" id="turnElapsedInput" value="0">
                    <button type="submit" name="roll_dice"
                            class="roll-btn <?= htmlspecialchars($game['last_type'] ?? '') ?>">
                        🎲 Roll Dice!
                    </button>
                </form>
            </div>
            
            <?php if ($narrator_message): ?>
                <div class="narrator narrator-<?= htmlspecialchars($game['last_type'] ?? 'normal') ?>">
                    <h3>🎭 Narrator</h3>
                    <p><?php echo htmlspecialchars($narrator_message); ?></p>
                </div>
            <?php endif; ?>
            
            <h3>🎰 Dice History</h3>
            <div class="dice-history">
                <ul>
                    <?php foreach (array_reverse($game['dice_history']) as $history): ?>
                        <li>🎲 <?= htmlspecialchars($player_names[$history['player']]) ?> rolled
                            <strong><?php echo $history['roll']; ?></strong>
                            (Turn <?php echo $history['turn']; ?>)
                            <?php if (isset($history['duration'])): ?>
                                — <em><?php echo $history['duration']; ?>s</em>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <h3>⚡ Event Log</h3>
            <div class="event-log">
                <ul>
                    <?php foreach ($game['events_log'] as $log): ?>
                        <li>Turn <?php echo $log['turn']; ?> - <?= htmlspecialchars($player_names[$log['player']]) ?>: 
                            <?php if (isset($log['type'])): ?>
                                <?php echo ucfirst($log['type']); ?> from <?php echo $log['from']; ?> to <?php echo $log['to']; ?>
                            <?php elseif (isset($log['event'])): ?>
                                <?php echo htmlspecialchars($log['event']['msg']); ?> at <?php echo $log['pos']; ?>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <p><a href="index.php">Back to Home</a></p>
    </div>
    <script>
        (function() {
            const timerElement = document.getElementById('turnTimer');
            const elapsedInput = document.getElementById('turnElapsedInput');
            const rollForm = document.getElementById('rollForm');
            if (!timerElement || !elapsedInput || !rollForm) return;

            let elapsed = 0;
            timerElement.textContent = '0s';
            elapsedInput.value = '0';

            const intervalId = setInterval(() => {
                elapsed += 1;
                timerElement.textContent = elapsed + 's';
                elapsedInput.value = elapsed;
            }, 1000);

            rollForm.addEventListener('submit', function() {
                elapsedInput.value = elapsed;
                clearInterval(intervalId);
            });
        })();
    </script>
</body>
</html>