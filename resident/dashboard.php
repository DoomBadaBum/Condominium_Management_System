<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Establish a database connection
include '../include/connection.php';

// Fetch the current user's username
$userId = $_SESSION["user_id"];
$sql = "SELECT username FROM user WHERE user_id = $userId";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    $username = $user["username"];
} else {
    // Handle error (username not found)
    $username = "Unknown";
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

// Close the database connection
$conn->close();
?>

<?php include '../include/header.php'; ?>

<div class="container">
    <?php include '../include/sidebar.php'; ?>

    <main>
        <h2>Welcome to the Dashboard, <?php echo $username; ?>!</h2>
        <h2>Announcements</h2>
    <?php if ($user): ?>
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
    </main>
</div>

<?php include '../include/footer.php'; ?>
