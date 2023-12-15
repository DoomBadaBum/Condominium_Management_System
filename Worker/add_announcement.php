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

// Perform the announcement insertion when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $workerId = $_SESSION["user_id"];
    $title = $_POST["title"];
    $content = $_POST["content"];
    $date = date("Y-m-d"); // Current date

    $insertAnnouncementSql = "INSERT INTO announcement (worker_id, title, content, date)
                              VALUES ($workerId, '$title', '$content', '$date')";

    if ($conn->query($insertAnnouncementSql) === TRUE) {
        header("Location: maintenance_announcements_worker.php");
    } else {
        // Display the SQL error
        echo "Error: " . $insertAnnouncementSql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!-- HTML content for the announcement form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Announcement</title>
</head>
<body>
    <h2>Add Announcement</h2>
    <form action="add_announcement.php" method="post">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required><br>

        <label for="content">Content:</label>
        <textarea id="content" name="content" required></textarea><br>

        <input type="submit" value="Submit Announcement">
    </form>
    
    <p><a href="logout_worker.php">Logout</a></p>
    <?php include '../include/footer_worker.php'; ?>
</body>
</html>
