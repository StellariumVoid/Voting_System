<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$pollId = $_GET['id'] ?? '';
if ($pollId === '') {
    die("No poll selected.");
}

// Load polls
$pollsFile = __DIR__ . "/data/polls.json";
if (!file_exists($pollsFile)) {
    die("No polls found.");
}

$polls = json_decode(file_get_contents($pollsFile), true);
if (!is_array($polls)) {
    die("Invalid polls data.");
}

// Find the poll
$poll = null;
foreach ($polls as $p) {
    if ($p['id'] === $pollId) {
        // Ownership check
        $owner = $p['created_by'] ?? '';
        if ($owner !== ($_SESSION['username'] ?? $_SESSION['user_id'])) {
            die("You are not allowed to edit this poll.");
        }

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
    <title>Edit Poll - <?= htmlspecialchars($poll['title']); ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        .choice-wrapper { display: flex; align-items: center; margin-bottom: 5px; }
        .choice-wrapper input { flex: 1; }
        .remove-choice { margin-left: 5px; cursor: pointer; color: red; }
    </style>
</head>
<body class="dashboard-page">

<div class="top-bar">
    <a href="dashboard.php">
        <img src="Assets/login/Choicehub.png" id="dashboard-logo">
    </a>
</div>

<div class="back-btn-container">
    <a href="manage.php" class="back-btn">⬅ Back to Manage Polls</a>
</div>

<div class="form-wrapper">
    <div class="poll-card">
        <h2>Edit Poll</h2>
        <form method="POST" action="edit_save.php" id="editForm">

            <input type="hidden" name="id" value="<?= htmlspecialchars($poll['id']); ?>">

            <label class="label">Poll Title</label>
            <input class="input" type="text" name="title" value="<?= htmlspecialchars($poll['title']); ?>" required>

            <label class="label">Description</label>
            <textarea class="textarea" name="description" rows="3" required><?= htmlspecialchars($poll['description']); ?></textarea>

            <label class="label">Choices</label>
            <div id="choices-wrapper">
                <?php foreach ($poll['choices'] as $choice): ?>
                    <div class="choice-wrapper">
                        <input class="input" type="text" name="choices[]" value="<?= htmlspecialchars($choice['text']); ?>" required>
                        <span class="remove-choice" onclick="removeChoice(this)">✖</span>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn small" onclick="addChoice()">+ Add Option</button>

            <div class="row">
                <div>
                    <label class="label">Start Date</label>
                    <input class="input" type="date" name="start_date" value="<?= date('Y-m-d', strtotime($poll['start_date'])); ?>" required>
                </div>
                <div>
                    <label class="label">End Date</label>
                    <input class="input" type="date" name="end_date" value="<?= date('Y-m-d', strtotime($poll['end_date'])); ?>" required>
                </div>
            </div>

            <div class="field" style="margin-top:10px;">
        <label class="label">Max votes per user</label>
        <input class="input" type="number" name="max_votes" id="max_votes" min="1" value="<?= $poll['max_votes']; ?>" required>
        <span id="maxVotesDisplay" style="font-size:13px;color:#444;">
        (Max allowed: <?= count($poll['choices']); ?>)
        </span>
        </div>

            <button type="submit" class="btn full">Save Changes</button>
        </form>
    </div>
</div>

<script>
function addChoice() {
    let c = document.getElementById("choices-wrapper");
    let div = document.createElement("div");
    div.className = "choice-wrapper";

    let i = document.createElement("input");
    i.type = "text";
    i.name = "choices[]";
    i.placeholder = "Another option";
    i.className = "input";
    i.required = true;
    i.addEventListener('input', updateMaxVotes);

    let removeBtn = document.createElement("span");
    removeBtn.className = "remove-choice";
    removeBtn.innerText = "✖";
    removeBtn.onclick = function() { removeChoice(removeBtn); }

    div.appendChild(i);
    div.appendChild(removeBtn);
    c.appendChild(div);

    updateMaxVotes();
}

function removeChoice(elem) {
    let wrapper = elem.parentElement;
    wrapper.remove();
    updateMaxVotes();
}

function updateMaxVotes() {
    const choicesCount = document.querySelectorAll('#choices-wrapper .choice-wrapper').length;
    const maxVotesInput = document.querySelector('input[name="max_votes"]');
    maxVotesInput.max = choicesCount;

    // Adjust value if current max_votes exceeds new number of choices
    if (parseInt(maxVotesInput.value) > choicesCount) {
        maxVotesInput.value = choicesCount;
    }
}

// Attach input listeners to existing choices
document.querySelectorAll('#choices-wrapper input[name="choices[]"]').forEach(input => {
    input.addEventListener('input', updateMaxVotes);
});

// Initialize max votes
updateMaxVotes();
</script>


</body>
</html>
