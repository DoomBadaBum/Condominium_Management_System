<?php
include '../include/connection.php';
session_start();

// Check if the user is authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login_worker.php");
    exit();
}

// Check if the announcement_id is provided in the request
if (isset($_GET['announcement_id'])) {
    $announcementId = $_GET['announcement_id'];

    // Check if the logged-in worker created the announcement and fetch media_url
    $workerId = $_SESSION["user_id"];
    $checkOwnershipSql = "SELECT media_url FROM announcement WHERE announcement_id = $announcementId AND worker_id = $workerId";
    $result = $conn->query($checkOwnershipSql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $mediaUrl = $row['media_url'];

        // Announcement belongs to the logged-in worker, proceed with deletion
        $deleteAnnouncementSql = "DELETE FROM announcement WHERE announcement_id = $announcementId";

        if ($conn->query($deleteAnnouncementSql) === TRUE) {
            // Deletion successful, delete the associated media file
            if (!empty($mediaUrl)) {
                $fileToDelete = '../' . $mediaUrl;
                if (file_exists($fileToDelete)) {
                    unlink($fileToDelete);
                }
            }

            // Redirect to announcements page
            echo '<script>alert("Announcement deleted successfully!");</script>';
            echo '<script>window.location.href = "announcements.php";</script>';
            exit();
        } else {
            // Display the SQL error
            echo "Error: " . $deleteAnnouncementSql . "<br>" . $conn->error;
        }
    } else {
        // Unauthorized access, redirect to announcements page
        header("Location: announcements.php");
        exit();
    }
} else {
    // announcement_id not provided, redirect to announcements page
    header("Location: announcements.php");
    exit();
}

$conn->close();
?>
