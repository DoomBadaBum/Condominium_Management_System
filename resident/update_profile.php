<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Establish a database connection
include '../include/connection.php';

// Get updated profile information from the form
$userId = $_SESSION["user_id"];
$fullname = $_POST['fullname'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$emergencyContact = $_POST['emergency_contact'];

// Update the user's profile
$sql = "UPDATE User 
        SET fullname = '$fullname', 
            email = '$email', 
            phone_number = '$phone', 
            emergency_contact = '$emergencyContact' 
        WHERE user_id = $userId";

if ($conn->query($sql) === TRUE) {
    echo '<script>alert("Profile updated successfully!");</script>';
    echo '<script>window.location.href = "profile.php";</script>';
} else {
    // Handle error
    echo '<script>alert("Failed to update Profile!");</script>';
    echo '<script>window.location.href = "profile.php";</script>';
}

// Close the database connection
$conn->close();
?>
