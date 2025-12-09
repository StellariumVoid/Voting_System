<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$poll_id = $_GET['id'] ?? '';

// Load polls
$pollsFile = __DIR__ . "/data/polls.json";
$polls = json_decode(file_get_contents($pollsFile), true);

$poll = null;
foreach ($polls as $p) {
    if ($p['id'] === $poll_id) {
        $poll = $p;
        break;
    }
}

if (!$poll) {
    die("Poll not found.");
}

if ($poll['created_by'] !== $_SESSION['username']) {
    die("You are not allowed to view this page.");
}
?>

<h2>Invite users to: <?= htmlspecialchars($poll['title']) ?></h2>

<?php if (isset($_GET['success'])): ?>
<p style="color: green">User invited successfully!</p>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
<p style="color: red">Error: <?= htmlspecialchars($_GET['error']) ?></p>
<?php endif; ?>

<form action="invite_save.php" method="POST">
    <input type="hidden" name="poll_id" value="<?= $poll_id ?>">

    <label>Enter username:</label>
    <input type="text" name="invite_user" required>

    <button type="submit">Invite User</button>
</form>

<hr>

<h3>Already Invited:</h3>
<ul>
<?php foreach ($poll['invited'] as $inv): ?>
    <li><?= htmlspecialchars($inv['username']) ?></li>
<?php endforeach; ?>
</ul>
