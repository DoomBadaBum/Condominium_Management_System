<!-- maintenance_announcements_worker.php -->
<?php
include '../include/connection.php';
session_start();

// Header sidebar
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
$announcementsSql = "SELECT announcement_id, title, content, DATE_FORMAT(date, '%M %e, %Y') AS formatted_date, TIME_FORMAT(time, '%h:%i %p') AS formatted_time, media_url, username AS worker_name
                     FROM announcement
                     JOIN user ON announcement.worker_id = user.user_id
                     ORDER BY date DESC, time DESC";
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
                        <p><?php echo $row['media_url']; ?></p>
                        <p>Date: <?php echo $row['formatted_date']; ?> at <?php echo $row['formatted_time']; ?></p>
                        <p>Posted by: <?php echo $row['worker_name']; ?></p>
                        <?php if (!empty($row['media_url'])): ?>
                            <?php $mediaExtension = pathinfo($row['media_url'], PATHINFO_EXTENSION); ?>
                            <?php if (in_array($mediaExtension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                <img src="../<?php echo $row['media_url']; ?>" alt="Image">
                            <?php elseif (in_array($mediaExtension, ['mp4', 'webm', 'ogg'])): ?>
                                <video width="320" height="240" controls>
                                    <source src="../<?php echo $row['media_url']; ?>" type="video/<?php echo $mediaExtension; ?>">
                                    Your browser does not support the video tag.
                                </video>
                            <?php endif; ?>
                        <?php endif; ?>
                        <p><?php echo $row['content']; ?></p>
                        <!-- Add a "Delete" link with confirmation -->
                        <a href="edit_announcement.php?announcement_id=<?php echo $row['announcement_id']; ?>">Update</a>
                        <a href="delete_announcement.php?announcement_id=<?php echo $row['announcement_id']; ?>" onclick="return confirm('Are you sure you want to delete this announcement? (Yes/No)')">Delete</a>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No announcements available.</p>
        <?php endif; ?>
    <?php else: ?>
        <p>Error: Worker not found.</p>
    <?php endif; ?>
</body>
</html>
