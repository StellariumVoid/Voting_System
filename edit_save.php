<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: manage.php");
    exit();
}

// Get POST data
$pollId = $_POST['id'] ?? '';
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$choices_raw = $_POST['choices'] ?? [];
$start_date = trim($_POST['start_date'] ?? '');
$end_date = trim($_POST['end_date'] ?? '');
$max_votes = (int)($_POST['max_votes'] ?? 1);

$pollsFile = __DIR__ . "/data/polls.json";
if (!file_exists($pollsFile)) {
    die("No polls found.");
}

$polls = json_decode(file_get_contents($pollsFile), true);
if (!is_array($polls)) {
    die("Invalid polls data.");
}

$found = false;
foreach ($polls as $index => $p) {
    if ($p['id'] === $pollId) {
        // Ownership check
        if (($p['created_by'] ?? '') !== ($_SESSION['username'] ?? $_SESSION['user_id'])) {
            die("You are not allowed to edit this poll.");
        }

        // Validate choices
        $choices = [];
        foreach ($choices_raw as $c) {
            $t = trim($c);
            if ($t !== '') $choices[] = $t;
        }
        if (count($choices) < 2) {
            die("At least 2 choices are required.");
        }

        // Validate dates
        $sd = new DateTime($start_date);
        $ed = new DateTime($end_date);
        if ($ed <= $sd) {
            die("End date must be after start date.");
        }

        // Update poll
        $polls[$index]['title'] = $title;
        $polls[$index]['description'] = $description;
        $polls[$index]['start_date'] = $sd->format('Y-m-d H:i:s');
        $polls[$index]['end_date'] = $ed->format('Y-m-d H:i:s');
        $polls[$index]['max_votes'] = $max_votes;

        // Update choices (preserve existing votes where possible)
        $oldChoices = $polls[$index]['choices'] ?? [];
        $newChoices = [];

        foreach ($choices as $i => $text) {
            // Keep old ID and votes if exists
            $oldChoice = $oldChoices[$i] ?? null;
            $newChoices[] = [
                'id' => $oldChoice['id'] ?? bin2hex(random_bytes(4)),
                'text' => $text,
                'votes' => $oldChoice['votes'] ?? 0
            ];
        }

        $polls[$index]['choices'] = $newChoices;

        $found = true;
        break;
    }
}

if (!$found) {
    die("Poll not found.");
}

// Save polls
if (file_put_contents($pollsFile, json_encode($polls, JSON_PRETTY_PRINT)) === false) {
    die("Failed to save changes.");
}

$_SESSION['flash_success'] = "Poll updated successfully!";
header("Location: manage.php");
exit();
?>
