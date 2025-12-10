<?php
session_start();

// Check poll id exists in URL
if (!isset($_GET['id'])) {
    die("No poll selected.");
}

$poll_id = $_GET['id'];

// Load polls file
$pollsFile = __DIR__ . "/data/polls.json";
if (!file_exists($pollsFile)) {
    die("No polls found.");
}

$polls = json_decode(file_get_contents($pollsFile), true);

// Find this poll
$poll = null;
foreach ($polls as $p) {
    if ($p['id'] == $poll_id) {
        $poll = $p;
        break;
    }
}

if (!$poll) {
    die("Poll not found.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($poll['title']); ?> - Vote</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-page">

    <div class="top-bar">
        <a href="dashboard.php">
            <img src="Assets/login/Choicehub.png" alt="Logo" id="dashboard-logo">
        </a>
    </div>

    <div class="dashboard-container">
        <div class="dashboard-content">

            <h1><?= htmlspecialchars($poll['title']); ?></h1>
            <p><?= htmlspecialchars($poll['description']); ?></p>

            <form method="POST" action="vote_save.php">
                <input type="hidden" name="poll_id" value="<?= $poll['id']; ?>">

                <?php foreach ($poll['choices'] as $choice): ?>
                    <div>
                        <label>
                            <input type="radio" name="choice_id" value="<?= $choice['id']; ?>">
                            <?= htmlspecialchars($choice['text']); ?>
                        </label>
                    </div>
                <?php endforeach; ?>

                <button type="submit" class="btn">Submit Vote</button>

            </form>

        </div>
    </div>

</body>
</html>
