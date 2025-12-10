<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$pollId = $_GET['id'] ?? '';
if ($pollId === '') {
    $_SESSION['flash_error'] = "No poll selected.";
    header("Location: manage.php");
    exit();
}

$pollsFile = __DIR__ . "/data/polls.json";
if (!file_exists($pollsFile)) {
    $_SESSION['flash_error'] = "No polls found.";
    header("Location: manage.php");
    exit();
}

$polls = json_decode(file_get_contents($pollsFile), true);
if (!is_array($polls)) {
    $_SESSION['flash_error'] = "Invalid polls data.";
    header("Location: manage.php");
    exit();
}

$pollFound = false;
foreach ($polls as $index => $p) {
    if ($p['id'] === $pollId) {
        $owner = $p['created_by'] ?? '';
        if ($owner !== ($_SESSION['username'] ?? $_SESSION['user_id'])) {
            $_SESSION['flash_error'] = "You are not allowed to delete this poll.";
            header("Location: manage.php");
            exit();
        }

        // Save deleted poll with timestamp for time-limited undo
        $_SESSION['deleted_poll'] = [
            'poll' => $p,
            'deleted_at' => time()
        ];

        unset($polls[$index]);
        $pollFound = true;
        break;
    }
}

if (!$pollFound) {
    $_SESSION['flash_error'] = "Poll not found.";
    header("Location: manage.php");
    exit();
}

$polls = array_values($polls);
if (file_put_contents($pollsFile, json_encode($polls, JSON_PRETTY_PRINT)) === false) {
    $_SESSION['flash_error'] = "Failed to save changes.";
    header("Location: manage.php");
    exit();
}

// Flash message with Undo
$_SESSION['flash_success'] = "Poll deleted successfully! <a href='undo_delete.php'>Undo</a>";
header("Location: manage.php");
exit();
?>
