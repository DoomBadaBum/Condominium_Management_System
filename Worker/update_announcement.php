<?php
include '../include/connection.php';
session_start();

// Check if the user is authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login_worker.php");
    exit();
}

// Perform the announcement update when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $workerId = $_SESSION["user_id"];
    $announcementId = $_POST["announcement_id"];
    $title = $_POST["title"];
    $content = $_POST["content"];
    $time = date("H:i:s"); // Current time
    $date = date("Y-m-d"); // Current date

    // Handle file upload
    $mediaFile = $_FILES['media'];

    // Check if a file was uploaded
    if ($mediaFile['error'] == 0) {
        // Generate a unique filename to avoid overwriting existing files
        $uniqueFilename = uniqid() . '_' . $mediaFile['name'];

        // Move the uploaded file to a designated directory
        $uploadPath = '../media_uploads/' . $uniqueFilename;
        move_uploaded_file($mediaFile['tmp_name'], $uploadPath);

        // Store the file reference (e.g., file path or URL) in the database
        $mediaUrl = 'media_uploads/' . $uniqueFilename;

        // Update announcement with new media URL
        $updateAnnouncementSql = "UPDATE announcement 
                                  SET title = '$title', content = '$content', date = '$date', time = '$time', media_url = '$mediaUrl'
                                  WHERE announcement_id = $announcementId AND worker_id = $workerId";
    } else {
        // Update announcement without changing media URL
        $updateAnnouncementSql = "UPDATE announcement 
                                  SET title = '$title', content = '$content', date = '$date', time = '$time'
                                  WHERE announcement_id = $announcementId AND worker_id = $workerId";
    }

    if ($conn->query($updateAnnouncementSql) === TRUE) {
        // Update successful, redirect to announcements page
        echo '<script>alert("Announcement updated successfully!");</script>';
        echo '<script>window.location.href = "announcements.php";</script>';
        exit();
    } else {
        // Display the SQL error
        echo "Error: " . $updateAnnouncementSql . "<br>" . $conn->error;
    }
} else {
    // Invalid request method, redirect to announcements page
    echo '<script>alert("Announcement failed to update!");</script>';
    echo '<script>window.location.href = "announcements.php";</script>';
    exit();
}

$conn->close();
?>
