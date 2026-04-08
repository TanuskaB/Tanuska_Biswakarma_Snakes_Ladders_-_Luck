<?php
// login.php - User login
require_once 'config.php';
require_once 'functions.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $errors = validate_login($username, $password);
    
    if (empty($errors)) {
        if (login_user($username, $password)) {
            $_SESSION['user'] = $username;
            header('Location: index.php');
            exit;
        } else {
            $errors[] = 'Invalid username or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Snakes, Ladders & Luck</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>🔓 Welcome Back!</h1>
        <?php if ($errors): ?>
            <div class="error">
                ❌ Login failed:
                <?php foreach ($errors as $error): ?>
                    <p>• <?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form method="post" style="max-width: 400px; margin: 0 auto;">
            <div class="form-group">
                <label for="username">👤 Username:</label>
                <input type="text" name="username" id="username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" placeholder="Your username">
            </div>
            <div class="form-group">
                <label for="password">🔐 Password:</label>
                <input type="password" name="password" id="password" required placeholder="Your password">
            </div>
            <button type="submit" style="width: 100%;">Login 🚀</button>
        </form>
        <p style="text-align: center; margin-top: 20px;">Don't have an account? <a href="register.php">Register now 📝</a></p>
        <p style="text-align: center;"><a href="index.php">Back to Home 🏠</a></p>
    </div>
</body>
</html>