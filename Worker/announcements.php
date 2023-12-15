<!-- maintenance_announcements_worker.php -->
<?php
include '../include/connection.php';
session_start();

// Header footer
include '../include/header_worker.php'; 
include '../include/sidebar_worker.php';

// Check if the user is authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login_worker.php");
    exit();
}

$userId = $_SESSION["user_id"];
$sql = "SELECT * FROM user WHERE user_id = $userId";
$result = $conn->query($sql);

if ($result === false) {
    die("Error executing user query: " . $conn->error);
}

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
} else {
    $user = null;
}

// Fetch announcements for the worker
$announcementsSql = "SELECT announcement_id, title, content, date, username AS worker_name
                     FROM announcement
                     JOIN user ON announcement.worker_id = user.user_id
                     ORDER BY date DESC";
$announcementsResult = $conn->query($announcementsSql);

if ($announcementsResult === false) {
    die("Error executing announcements query: " . $conn->error);
}

$conn->close();
?>

<!-- HTML content for announcements page -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements - Worker</title>
</head>
<body>
    <h2>Announcements - Worker</h2>
    <?php if ($user): ?>
        <p>Worker: <?php echo $user['username']; ?></p>
        <!-- Display announcements -->
        <?php if ($announcementsResult->num_rows > 0): ?>
            <ul>
                <?php while ($row = $announcementsResult->fetch_assoc()): ?>
                    <li>
                        <h3><?php echo $row['title']; ?></h3>
                        <p><?php echo $row['content']; ?></p>
                        <p>Date: <?php echo $row['date']; ?></p>
                        <p>Posted by: <?php echo $row['worker_name']; ?></p>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No announcements available.</p>
        <?php endif; ?>
        <p><a href="logout_worker.php">Logout</a></p>
    <?php else: ?>
        <p>Error: Worker not found.</p>
    <?php endif; ?>
</body>
</html>
