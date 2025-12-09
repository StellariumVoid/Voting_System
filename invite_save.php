<?php
session_start();

$poll_id = $_POST['poll_id'] ?? '';
$invite_username = trim($_POST['invite_user'] ?? '');

// Validate
if ($invite_username === '') {
    header("Location: invite.php?id=$poll_id&error=empty_username");
    exit;
}

// Load users to verify existence
$usersFile = __DIR__ . "/data/users.json";
$users = json_decode(file_get_contents($usersFile), true);

$invited_user = null;
foreach ($users as $u) {
    if ($u['username'] === $invite_username) {
        $invited_user = [
            "user_id" => $u['id'],
            "username" => $u['username']
        ];
        break;
    }
}

if ($invited_user === null) {
    header("Location: invite.php?id=$poll_id&error=user_not_found");
    exit;
}

// Load polls
$pollsFile = __DIR__ . "/data/polls.json";
$polls = json_decode(file_get_contents($pollsFile), true);

foreach ($polls as &$poll) {
    if ($poll['id'] === $poll_id) {

        // Only the owner can invite
        if ($poll['created_by'] !== $_SESSION['username']) {
            header("Location: dashboard.php?error=not_allowed");
            exit;
        }

        // Prevent duplicate invite
        foreach ($poll['invited'] as $i) {
            if ($i['user_id'] === $invited_user['user_id']) {
                header("Location: invite.php?id=$poll_id&error=already_invited");
                exit;
            }
        }

        // Add to invited list
        $poll['invited'][] = $invited_user;

        // Save
        file_put_contents($pollsFile, json_encode($polls, JSON_PRETTY_PRINT));

        header("Location: invite.php?id=$poll_id&success=1");
        exit;
    }
}

header("Location: dashboard.php?error=poll_not_found");
exit;
?>
