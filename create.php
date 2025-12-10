<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create Poll</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-page">

<div class="top-bar">
    <a href="dashboard.php">
        <img src="Assets/login/Choicehub.png" id="dashboard-logo">
    </a>
</div>

<div class="back-btn-container">
    <a href="dashboard.php" class="back-btn">⬅ Back to Dashboard</a>
</div>

<div class="form-wrapper">
    <div class="poll-card">
        <h2>Create New Poll</h2>
        <form method="POST" action="create_save.php" id="pollForm">

            <label class="label">Poll Title</label>
            <input class="input" type="text" name="title" required>

            <label class="label">Description</label>
            <textarea class="textarea" name="description" rows="3" required></textarea>

            <label class="label">Choices</label>
            <div id="choices-wrapper">
                <input class="input" type="text" name="choices[]" placeholder="Choice 1" required>
                <input class="input" type="text" name="choices[]" placeholder="Choice 2" required>
            </div>
            <button type="button" class="btn small" onclick="addChoice()">+ Add Option</button>

            <div class="row">
                <div>
                    <label class="label">Start Date</label>
                    <input class="input" type="date" name="start_date" required>
                </div>
                <div>
                    <label class="label">End Date</label>
                    <input class="input" type="date" name="end_date" required>
                </div>
            </div>

            <label class="label">Max votes per user</label>
            <input class="input" type="number" name="max_votes" min="1" value="1" required>

            <button type="submit" class="btn full">Create Poll</button>
        </form>
    </div>
</div>

<script>
function addChoice() {
    let c = document.getElementById("choices-wrapper");
    let i = document.createElement("input");
    i.type = "text";
    i.name = "choices[]";
    i.placeholder = "Another option";
    i.className = "input";
    i.required = true;
    c.appendChild(i);
}
</script>

</body>
</html>
