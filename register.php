<?php
session_start();

$usersFile = __DIR__ . '/data/users.json';
$errors = [];

if (!file_exists($usersFile)) {
    if (!is_dir(dirname($usersFile))) {
        mkdir(dirname($usersFile), 0777, true);
    }
    file_put_contents($usersFile, json_encode([]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $gmail = trim($_POST['gmail'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    if (!$username || !$gmail || !$password || !$confirmPassword) {
        $errors[] = "All fields are required.";
    } elseif ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    } elseif (!filter_var($gmail, FILTER_VALIDATE_EMAIL) || substr($gmail, -10) !== '@gmail.com') {
        $errors[] = "Please enter a valid Gmail address.";
    } else {
        $users = json_decode(file_get_contents($usersFile), true);
        if (!is_array($users)) $users = [];

        foreach ($users as $user) {
            if ($user['gmail'] === $gmail) {
                $errors[] = "Gmail is already registered.";
                break;
            }
        }
        if (empty($errors)) {
            $users[] = [
                'username' => $username,
                'gmail' => $gmail,
                'password' => password_hash($password, PASSWORD_DEFAULT)
            ];

            file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
            $_SESSION['success'] = "Registration successful! Please log in.";
            header('Location: login.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChoiceHub Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">

<div class="auth-container">

    <!-- LEFT -->
    <div class="auth-left">
        <h1>ChoiceHub</h1>
        <p>Empowering communities through voting</p>
    </div>

    <!-- RIGHT -->
    <div class="auth-right">
        <h2>Register</h2>

        <?php if (!empty($errors)): ?>
            <div class="error-box"><?= implode('<br>', $errors); ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="gmail" placeholder="Gmail" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button class="auth-btn">Register</button>
        </form>

        <p class="switch-link">Already have an account?
            <a href="login.php">Login here</a>
        </p>
    </div>

</div>

</body>
</html>
