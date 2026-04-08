<?php
// logout.php - Logout page
require_once 'config.php';
require_once 'functions.php';

// Clear session data
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logged Out - Snakes, Ladders &amp; Luck</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>🐍 Snakes, Ladders &amp; Luck 🪜</h1>

    <div class="logout-box">
        <span class="logout-icon">👋</span>
        <h2>You've been logged out!</h2>
        <p>Thanks for playing — hope to see you back soon!</p>
        <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap; margin-top:20px;">
            <a href="login.php" class="btn-link">🔓 Log In Again</a>
            <a href="register.php" class="btn-link">📝 Register</a>
        </div>
    </div>
</div>
</body>
</html>