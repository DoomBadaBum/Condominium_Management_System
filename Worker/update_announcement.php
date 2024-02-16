<?php
include '../include/connection.php';
session_start();
date_default_timezone_set('Asia/Kuala_Lumpur'); // Set the timezone to Malaysian time

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

    // Fetch the current media URL from the database
    $fetchMediaUrlSql = "SELECT media_url FROM announcement WHERE announcement_id = ? AND worker_id = ?";
    $stmtFetchMediaUrl = $conn->prepare($fetchMediaUrlSql);
    $stmtFetchMediaUrl->bind_param("ii", $announcementId, $workerId);
    $stmtFetchMediaUrl->execute();
    $result = $stmtFetchMediaUrl->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $mediaUrl = $row['media_url'];

        // Check if the "Remove Media" button is clicked
        if (isset($_POST["remove_media"])) {
            // Remove the media file from the server
            if (!empty($mediaUrl)) {
                $mediaFilePath = '../' . $mediaUrl;
                if (file_exists($mediaFilePath)) {
                    unlink($mediaFilePath);
                }
            }

            // Set the media URL in the database to NULL
            $updateMediaSql = "UPDATE announcement SET media_url = NULL WHERE announcement_id = ? AND worker_id = ?";
            $stmtUpdateMedia = $conn->prepare($updateMediaSql);
            $stmtUpdateMedia->bind_param("ii", $announcementId, $workerId);

            if ($stmtUpdateMedia->execute()) {
                // Media removal successful, provide feedback and redirect
                echo '<script>alert("Media removed successfully!");</script>';
                echo '<script>window.location.href = "announcements.php";</script>';
                exit();
            } else {
                // Display the SQL error
                echo "Error: " . $stmtUpdateMedia->error;
                exit();
            }
        }
    }

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
                                  SET title = ?, content = ?, date = ?, time = ?, media_url = ?
                                  WHERE announcement_id = ? AND worker_id = ?";
        $stmtUpdateAnnouncement = $conn->prepare($updateAnnouncementSql);
        $stmtUpdateAnnouncement->bind_param("ssssssi", $title, $content, $date, $time, $mediaUrl, $announcementId, $workerId);
    } else {
        // Update announcement without changing media URL
        $updateAnnouncementSql = "UPDATE announcement 
                                  SET title = ?, content = ?, date = ?, time = ?
                                  WHERE announcement_id = ? AND worker_id = ?";
        $stmtUpdateAnnouncement = $conn->prepare($updateAnnouncementSql);
        $stmtUpdateAnnouncement->bind_param("ssssii", $title, $content, $date, $time, $announcementId, $workerId);
    }

    if ($stmtUpdateAnnouncement->execute()) {
        // Update successful, redirect to announcements page
        echo '<script>alert("Announcement updated successfully!");</script>';
        echo '<script>window.location.href = "announcements.php";</script>';
        exit();
    } else {
        // Display the SQL error
        echo "Error: " . $stmtUpdateAnnouncement->error;
    }

    // Close prepared statements
    $stmtFetchMediaUrl->close();
    $stmtUpdateAnnouncement->close();
} else {
    // Invalid request method, redirect to announcements page
    echo '<script>alert("Announcement failed to update!");</script>';
    echo '<script>window.location.href = "announcements.php";</script>';
    exit();
}

// Close the database connection
$conn->close();
?>