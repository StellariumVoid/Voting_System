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
<body class="login-page">
    <div class="top-bar"></div>
    <div class="container">
        <img src="Assets/login/Choicehub.png" alt="Logo" class="logo">
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <h2>Login</h2>
        <form method="post">
            <input type="email" name="gmail" placeholder="Gmail" required>
            <input type="password" name="password" placeholder="Password" required>
            <button class="btn" type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
    <script src="js/script.js"></script>
</body>
</html>
