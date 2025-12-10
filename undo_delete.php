<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION['deleted_poll'])) {
    $_SESSION['flash_error'] = "No poll to restore.";
    header("Location: manage.php");
    exit();
}

// Check if within 30 seconds
$deletedAt = $_SESSION['deleted_poll']['deleted_at'] ?? 0;
if ((time() - $deletedAt) > 30) {
    unset($_SESSION['deleted_poll']);
    $_SESSION['flash_error'] = "Undo period has expired.";
    header("Location: manage.php");
    exit();
}

$pollsFile = __DIR__ . "/data/polls.json";
$polls = [];

if (file_exists($pollsFile)) {
    $decoded = json_decode(file_get_contents($pollsFile), true);
    if (is_array($decoded)) {
        $polls = $decoded;
    }
}

// Restore poll
$polls[] = $_SESSION['deleted_poll']['poll'];

// Save back
if (file_put_contents($pollsFile, json_encode($polls, JSON_PRETTY_PRINT)) === false) {
    $_SESSION['flash_error'] = "Failed to restore poll.";
    unset($_SESSION['deleted_poll']);
    header("Location: manage.php");
    exit();
}

unset($_SESSION['deleted_poll']);
$_SESSION['flash_success'] = "Poll restored successfully!";
header("Location: manage.php");
exit();
?>
