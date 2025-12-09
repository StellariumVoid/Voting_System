<?php
session_start();

$usersFile = __DIR__ . '/data/users.json';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gmail = strtolower(trim($_POST['gmail'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    $users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];
    if (!is_array($users)) $users = [];

    foreach ($users as $user) {
        if (strtolower($user['gmail']) === $gmail && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['gmail'];
            $_SESSION['username'] = $user['username'];
            header('Location: dashboard.php');
            exit;
        }
    }
    $error = "Invalid Gmail or password.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChoiceHub Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">

<div class="auth-container">

    <!-- Left branding -->
    <div class="auth-left">
        <h1>ChoiceHub</h1>
        <p>Your best platform for voting polls</p>
    </div>

    <!-- Right content -->
    <div class="auth-right">
        <h2>Login</h2>
        <?php if (!empty($error)): ?>
            <div class="error-box"><?= $error; ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="email" name="gmail" placeholder="Gmail" required>
            <input type="password" name="password" placeholder="Password" required>
            <button class="auth-btn">Login</button>
        </form>
        <p class="switch-link">Don't have an account?  
            <a href="register.php">Register here</a>
        </p>
    </div>
</div>

</body>
</html>
