<?php
include '../include/connection.php';
session_start();

// Check if the user is authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login_admin.php");
    exit();
}

// Check if an ID is provided in the URL
if (isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];

    // Delete the record from the database
    $sql = "DELETE FROM user WHERE user_id = $userId";

    if ($conn->query($sql) === TRUE) {
        // Redirect to the view page after successful deletion
        echo '<script>alert("Staff deleted successfully!"); window.location.href = "view_worker.php";</script>';
        exit();
    } else {
        echo "Error deleting Staff: " . $conn->error;
    }
} else {
    echo "No ID provided in the URL.";
}

$conn->close();
?>
