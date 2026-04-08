<?php
// functions.php - Utility functions for the game

require_once 'config.php';

// Roll dice
function roll_dice() {
    return rand(1, 6);
}

// Move player position
function move_player($current_pos, $roll, $board) {
    $new_pos = $current_pos + $roll;

    if ($new_pos > 100) {
        $new_pos = 100 - ($new_pos - 100); // Bounce back if over 100
    }

    $type   = 'normal';
    $landed = $new_pos;
    $to     = $new_pos;

    // Check snakes and ladders
    if (isset($board['snakes'][$new_pos])) {
        $to   = $board['snakes'][$new_pos];
        $type = 'snake';
    } elseif (isset($board['ladders'][$new_pos])) {
        $to   = $board['ladders'][$new_pos];
        $type = 'ladder';
    }

    return ['pos' => $to, 'type' => $type, 'from' => $landed];
}

// Check for events
function check_event($pos, $turn_number) {
    global $event_cells;
    return isset($event_cells[$pos]) ? $event_cells[$pos] : null;
}

// Apply event
function apply_event($event, &$pos) {
    switch ($event['type']) {
        case 'bonus':
        case 'penalty':
            $pos += $event['move'];
            break;
        case 'warp':
            $pos = min(100, $pos + $event['move']);
            break;
        case 'skip':
            // Handle skip in game logic
            break;
    }
    return $event;
}

// Check bonus tile
function check_bonus($pos) {
    global $bonus_tiles;
    return isset($bonus_tiles[$pos]) ? $bonus_tiles[$pos] : null;
}

// Apply bonus
function apply_bonus($bonus_type) {
    // Return effect
    return $bonus_type;
}

// Generate narrator message
function get_narrator_message($type, $data) {
    global $narrator_templates;
    $template = $narrator_templates[$type] ?? "Something happened!";
    return str_replace(array_keys($data), array_values($data), $template);
}

// Check win
function check_win($pos) {
    return $pos >= 100;
}

// Initialize game session
function init_game($difficulty, $player2_name = 'Player 2', $players = 2) {
    global $boards;

    if (is_int($player2_name)) {
        $players      = $player2_name;
        $player2_name = 'Player 2';
    }

    $players = max(1, (int)$players);

    $player_names = [
        0 => get_logged_user() ?? 'Player 1',
        1 => $player2_name ?: 'Player 2'
    ];

    $_SESSION['game'] = [
        'difficulty'     => $difficulty,
        'board'          => $boards[$difficulty],
        'positions'      => array_fill(0, $players, 1),
        'player_names'   => $player_names,
        'current_player' => 0,
        'turn_count'     => 0,
        'dice_history'   => [],
        'events_log'     => [],
        'last_event'     => null,
        'last_type'      => 'normal',
        'last_narrator'  => '',
        'last_roll'      => null,
        'start_time'     => time(),
        'skipped_turns'  => array_fill(0, $players, false)
    ];
}

// Get current game state
function get_game_state() {
    return $_SESSION['game'] ?? null;
}

// Update leaderboard
function update_leaderboard($player_name, $time_seconds, $difficulty) {
    if (!isset($_SESSION['leaderboard'])) {
        $_SESSION['leaderboard'] = [];
    }
    $_SESSION['leaderboard'][] = [
        'player'     => htmlspecialchars($player_name, ENT_QUOTES, 'UTF-8'),
        'time'       => $time_seconds,
        'difficulty' => $difficulty,
        'timestamp'  => time()
    ];
    // Sort by fastest time
    usort($_SESSION['leaderboard'], fn($a, $b) => $a['time'] <=> $b['time']);
    // Keep top 10
    $_SESSION['leaderboard'] = array_slice($_SESSION['leaderboard'], 0, 10);
}

// Get leaderboard scores
function get_scores() {
    return $_SESSION['leaderboard'] ?? [];
}

// Authentication functions
function register_user($username, $password) {
    if (!isset($_SESSION['users'])) {
        $_SESSION['users'] = [];
    }
    if (isset($_SESSION['users'][$username])) {
        return false; // User exists
    }
    $_SESSION['users'][$username] = password_hash($password, PASSWORD_DEFAULT);
    return true;
}

function login_user($username, $password) {
    if (!isset($_SESSION['users'][$username])) {
        return false;
    }
    return password_verify($password, $_SESSION['users'][$username]);
}

function is_logged_in() {
    return isset($_SESSION['user']);
}

function logout() {
    unset($_SESSION['user']);
}

function get_logged_user() {
    return $_SESSION['user'] ?? null;
}

// Sanitize input
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Validate form
function validate_registration($username, $password, $confirm) {
    $errors = [];
    if (empty($username) || strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters.";
    }
    if (empty($password) || strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }
    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }
    return $errors;
}

function validate_login($username, $password) {
    $errors = [];
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }
    return $errors;
}
?>