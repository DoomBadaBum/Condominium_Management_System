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

// Perform the announcement insertion when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $workerId = $_SESSION["user_id"];
    $title = $_POST["title"];
    $content = $_POST["content"];
    $time = date("H:i:s"); // Current time
    $date = date("Y-m-d"); // Current date

    // Handle file upload
    $mediaFile = $_FILES['media'];

    // Check if a file was uploaded
    echo "Upload Error Code: " . $mediaFile['error']; // Debugging statement

    if ($mediaFile['error'] == 0) {
        // Generate a unique filename to avoid overwriting existing files
        $uniqueFilename = uniqid() . '_' . $mediaFile['name'];

        // Move the uploaded file to a designated directory
        $uploadPath = '../media_uploads/' . $uniqueFilename;
        move_uploaded_file($mediaFile['tmp_name'], $uploadPath);

        // Store the file reference (e.g., file path or URL) in the database
        $mediaUrl = 'media_uploads/' . $uniqueFilename;

        // Insert announcement with media URL into the database
        $insertAnnouncementSql = "INSERT INTO announcement (worker_id, title, content, date, time, media_url)
                                  VALUES ($workerId, '$title', '$content', '$date', '$time','$mediaUrl')";

        if ($conn->query($insertAnnouncementSql) === TRUE) {
            echo '<script>alert("Announcement created successfully!");</script>';
            echo '<script>window.location.href = "announcements.php";</script>';
            exit(); // Add this line to prevent further execution
        } else {
            // Display the SQL error
            echo "Error: " . $insertAnnouncementSql . "<br>" . $conn->error;
        }
    } else {
        // Handle the case where no file was uploaded
        // Insert announcement without media URL into the database
        $insertAnnouncementSql = "INSERT INTO announcement (worker_id, title, content, date, time)
                                  VALUES ($workerId, '$title', '$content', '$date', '$time')";

        if ($conn->query($insertAnnouncementSql) === TRUE) {
            echo '<script>alert("Announcement created successfully!");</script>';
            echo '<script>window.location.href = "announcements.php";</script>';
            exit(); // Add this line to prevent further execution
        } else {
            // Display the SQL error
            echo "Error: " . $insertAnnouncementSql . "<br>" . $conn->error;
        }
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
    <form action="add_announcement.php" method="post" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required><br>

        <label for="content">Content:</label>
        <textarea id="content" name="content" required></textarea><br>

        <label for="media">Attach Media:</label>
        <input type="file" id="media" name="media"><br>

        <input type="submit" value="Submit Announcement">
    </form>

    <p><a href="logout_worker.php">Logout</a></p>
    <?php include '../include/footer_worker.php'; ?>
</body>
</html>
