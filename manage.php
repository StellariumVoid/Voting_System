<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
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

// Only show polls created by this user
$userPolls = array_filter($polls, function($p) {
    return $p['created_by'] === ($_SESSION['username'] ?? $_SESSION['user_id']);
});
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Manage Polls</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-page">

<div class="top-bar">
    <a href="dashboard.php">
        <img src="Assets/login/Choicehub.png" alt="Logo" id="dashboard-logo">
    </a>
</div>

<div class="dashboard-container">
    <div class="dashboard-content">

        <div class="back-btn-container">
            <a href="dashboard.php" class="back-btn">⬅ Back to Dashboard</a>
        </div>

        <h1>Your Polls</h1>

        <!-- Flash Messages -->
        <?php
        if (isset($_SESSION['flash_success'])) {
            echo '<div class="flash-message success">' . $_SESSION['flash_success'] . '</div>';
            unset($_SESSION['flash_success']);
        }
        if (isset($_SESSION['flash_error'])) {
            echo '<div class="flash-message error">' . $_SESSION['flash_error'] . '</div>';
            unset($_SESSION['flash_error']);
        }
        ?>

        <div class="poll-list">
            <table border="1" cellpadding="6">
                <tr>
                    <th>Title</th>
                    <th>Share</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>

                <?php foreach ($userPolls as $poll): ?>
                <tr>
                    <td><?= htmlspecialchars($poll['title']); ?></td>
                    <td>
                        <button onclick="copyLink('poll.php?id=<?= $poll['id']; ?>')">
                            Share Link
                        </button>
                    </td>
                    <td>
                        <a href="edit.php?id=<?= $poll['id']; ?>">Edit</a>
                    </td>
                    <td>
                        <?php if ($poll['created_by'] === ($_SESSION['username'] ?? $_SESSION['user_id'])): ?>
                            <a href="delete.php?id=<?= $poll['id']; ?>"
                               onclick="return confirm('Are you sure you want to delete this poll?');">
                               Delete
                            </a>
                        <?php else: ?>
                            <span style="color: gray; cursor: not-allowed;" title="You can't delete this poll">Delete</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

    </div>
</div>

<script>
function copyLink(link) {
    navigator.clipboard.writeText(link).then(function() {
        alert("Poll link copied to clipboard!");
    });
}


// Flash message fade out & Undo removal
window.addEventListener('DOMContentLoaded', () => {
    const flashMessages = document.querySelectorAll('.flash-message');
    flashMessages.forEach(msg => {
        // Fade out after 3 seconds
        setTimeout(() => {
            msg.style.transition = "opacity 0.5s";
            msg.style.opacity = 0;
        }, 3000);

        // Remove the element after 3.5 seconds
        setTimeout(() => {
            msg.remove();
        }, 3500);

        // If there's an Undo link, remove it after 30 seconds
        const undoLink = msg.querySelector('a[href="undo_delete.php"]');
        if (undoLink) {
            setTimeout(() => {
                undoLink.remove();
            }, 30000); // 30 seconds
        }
    });
});
</script>


</body>
</html>
