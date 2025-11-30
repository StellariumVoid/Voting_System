<?php

session_start();

$usersFile = __DIR__ . '/../data/users.json';
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
    }  elseif (!filter_var($gmail, FILTER_VALIDATE_EMAIL) || substr($gmail, -10) !== '@gmail.com') {
        $errors[] = "Please enter a valid Gmail address.";
    } else {
        $users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];
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
<body class="register-page">
    <div class="top-bar"></div>

    <!-- Logo wrapper -->
    <div class="logo-wrapper">
        <img src="Assets/login/Choicehub.png" alt="Logo" class="logo">
    </div>

    <!-- Form container -->
    <div class="container">
        <h2>Register</h2>
        <?php if (!empty($errors)): ?>
            <div class="error"><?php echo implode('<br>', $errors); ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="gmail" placeholder="Gmail" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button class="btn" type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="index.php">Login</a></p>
    </div>

    <script src="js/script.js"></script>
</body>
</html>