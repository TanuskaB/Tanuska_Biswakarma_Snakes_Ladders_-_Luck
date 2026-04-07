<?php
// register.php - User registration
require_once 'config.php';
require_once 'functions.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    
    $errors = validate_registration($username, $password, $confirm);
    
    if (empty($errors)) {
        if (register_user($username, $password)) {
            $success = 'Registration successful! <a href="login.php">Login here</a>';
        } else {
            $errors[] = 'Username already exists.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Snakes, Ladders & Luck</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>📝 Create Account</h1>
        <?php if ($errors): ?>
            <div class="error">
                ❌ Oops! There were some issues:
                <?php foreach ($errors as $error): ?>
                    <p>• <?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success">
                ✅ <?php echo $success; ?>
            </div>
        <?php else: ?>
            <form method="post" style="max-width: 400px; margin: 0 auto;">
                <div class="form-group">
                    <label for="username">👤 Username:</label>
                    <input type="text" name="username" id="username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" placeholder="Choose a cool username">
                </div>
                <div class="form-group">
                    <label for="password">🔐 Password:</label>
                    <input type="password" name="password" id="password" required placeholder="At least 6 characters">
                </div>
                <div class="form-group">
                    <label for="confirm">🔐 Confirm Password:</label>
                    <input type="password" name="confirm" id="confirm" required placeholder="Type password again">
                </div>
                <button type="submit" style="width: 100%;">Create Account ✨</button>
            </form>
        <?php endif; ?>
        <p style="text-align: center; margin-top: 20px;">Already have an account? <a href="login.php">Login here 🔓</a></p>
        <p style="text-align: center;"><a href="index.php">Back to Home 🏠</a></p>
    </div>
</body>
</html>