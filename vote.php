<?php

session_start();

$poll_id = $_GET['id'];
$current_user_id = $_SESSION['user_id'];

$pollsFile = __DIR__ . "/data/polls.json";
$polls = json_decode(file_get_contents($pollsFile), true);

foreach ($polls as $poll) {
    if ($poll['id'] === $poll_id) {

        $allowed = false;

        // Check if invited
        foreach ($poll['invited'] as $inv) {
            if ($inv['user_id'] === $current_user_id) {
                $allowed = true;
                break;
            }
        }

        // OR poll owner (creator) can vote too
        if ($poll['created_by'] === $_SESSION['username']) {
            $allowed = true;
        }

        if (!$allowed) {
            die("You are not allowed to vote on this poll.");
        }

        // Vote code continues...
    }
}

?>