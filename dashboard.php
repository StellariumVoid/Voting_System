<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ChoiceHub Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-page">
    <div class="top-bar">
        <a href="dashboard.php">
            <img src="Assets/Choicehub.png" alt="Logo" class="logo" id="logo">
        </a>
    </div>
    <div class="user-welcome">
        <h1>Welcome, <?= htmlspecialchars($_SESSION['username']); ?>!</h1>
        <a href="logout.php" class="btn">Logout</a>
    </div>
    <div class="nav">
        <a href="create.php" class="nav-button"><img src="Assets/dashboard/Frame 60.png" alt="Create"></a>
        <a href="vote-inv.php" class="nav-button"><img src="Assets/dashboard/Frame 61.png" alt="Vote"></a>
        <a href="manage.php" class="nav-button"><img src="Assets/dashboard/Frame 57.png" alt="Manage"></a>
        <a href="view.php" class="nav-button"><img src="Assets/dashboard/Frame 58.png" alt="View Results"></a>
    </div>
    <script src="js/script.js"></script>
</body>
</html>
