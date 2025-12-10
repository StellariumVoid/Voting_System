<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: create.php");
    exit;
}

// GET POST DATA
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$choices_raw = $_POST['choices'] ?? [];
$start_date = trim($_POST['start_date'] ?? '');
$end_date = trim($_POST['end_date'] ?? '');
$max_votes = trim($_POST['max_votes'] ?? 1);

// Convert choices to array of valid options
$choices = [];

if (is_array($choices_raw)) {
    // Form had fields like choices[]
    foreach ($choices_raw as $c) {
        $t = trim((string)$c);
        if ($t !== '') {
            $choices[] = $t;
        }
    }
} else {
    // Form had textarea with newlines
    $choices_list = preg_split('/\r\n|\r|\n/', $choices_raw);
    foreach ($choices_list as $c) {
        $t = trim((string)$c);
        if ($t !== '') {
            $choices[] = $t;
        }
    }
}


if (count($choices) < 2) {
    header("Location: create.php?error=not_enough_choices");
    exit;
}

// Validate dates
try {
    $sd = new DateTime($start_date);
    $ed = new DateTime($end_date);
    if ($ed <= $sd) {
        header("Location: create.php?error=invalid_dates");
        exit;
    }
} catch (Exception $e) {
    header("Location: create.php?error=invalid_dates");
    exit;
}

if ($max_votes < 1 || $max_votes > count($choices)) {
    header("Location: create.php?error=invalid_max_votes");
    exit;
}

// Create Poll Data
$poll = [
    'id' => bin2hex(random_bytes(8)),
    'title' => $title,
    'description' => $description,
    'choices' => [],
    'start_date' => $sd->format('Y-m-d H:i:s'),
    'end_date' => $ed->format('Y-m-d H:i:s'),
    'max_votes' => (int)$max_votes,
    'created_by' => $_SESSION['username'] ?? $_SESSION['user_id'],
    'created_at' => (new DateTime())->format('Y-m-d H:i:s')

];

// Choices
foreach ($choices as $txt) {
    $poll['choices'][] = [
        'id' => bin2hex(random_bytes(4)),
        'text' => $txt,
        'votes' => 0
    ];
}

// DATA FOLDER SETUP
$dataDir = __DIR__ . DIRECTORY_SEPARATOR . 'data';
$pollsFile = $dataDir . DIRECTORY_SEPARATOR . 'polls.json';

if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}

// LOAD EXISTING POLLS
$polls = [];
if (file_exists($pollsFile)) {
    $decoded = json_decode(file_get_contents($pollsFile), true);
    if (is_array($decoded)) {
        $polls = $decoded;
    }
}

// SAVE
$polls[] = $poll;
if (file_put_contents($pollsFile, json_encode($polls, JSON_PRETTY_PRINT)) === false) {
    header("Location: create.php?error=storage_error");
    exit;
}

header("Location: dashboard.php?success=poll_created");
exit;
?>
