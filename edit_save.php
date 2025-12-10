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

        // Validate & clean choices
        $choices = [];
        foreach ($choices_raw as $c) {
            $t = trim($c);
            if ($t !== '') $choices[] = $t;
        }
        if (count($choices) < 2) {
            die("At least 2 choices are required.");
        }

        // Force max_votes <= choices count
        if ($max_votes > count($choices)) {
            $max_votes = count($choices);
        }
        if ($max_votes < 1) {
            $max_votes = 1;
        }

        // Validate dates
        try {
            $sd = new DateTime($start_date);
            $ed = new DateTime($end_date);
        } catch (Exception $e) {
            die("Invalid date format.");
        }      
        if ($ed <= $sd) {
            die("End date must be after start date.");
        }

        // Save poll edits
        $polls[$index]['title'] = $title;
        $polls[$index]['description'] = $description;
        $polls[$index]['start_date'] = $sd->format('Y-m-d H:i:s');
        $polls[$index]['end_date'] = $ed->format('Y-m-d H:i:s');
        $polls[$index]['max_votes'] = $max_votes;

        // Update choices (preserve votes+id)
        $newChoices = [];
        foreach ($choices as $i => $text) {
            $newChoices[] = [
                'id' => $polls[$index]['choices'][$i]['id'] ?? bin2hex(random_bytes(4)),
                'text' => $text,
                'votes' => $polls[$index]['choices'][$i]['votes'] ?? 0
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

// Save
if (file_put_contents($pollsFile, json_encode($polls, JSON_PRETTY_PRINT), LOCK_EX) === false) {
    die("Failed to save changes.");
}

$_SESSION['flash_success'] = "Poll updated successfully!";
header("Location: manage.php");
exit();
?>
