<?php
include '../include/connection.php';
session_start();

// Check if the user is authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login_worker.php");
    exit();
}

// Get form data
$user_id = $_POST['user_id'];
$facility_id = $_POST['facility_id'];
$booking_date = $_POST['booking_date'];
$start_time = $_POST['start_time'];
$end_time = $_POST['end_time'];

// Validate form data (you may want to add more validation)
if (empty($user_id) || empty($facility_id) || empty($booking_date) || empty($start_time) || empty($end_time)) {
    // Redirect with an error message
    header("Location: add_booking_facility.php?error=Please fill in all the fields");
    exit();
}

// Insert the new booking into the database
$sql = "INSERT INTO booking (user_id, facility_id, booking_date, start_time, end_time) 
        VALUES ('$user_id', '$facility_id', '$booking_date', '$start_time', '$end_time')";

if ($conn->query($sql) === TRUE) {
    // Redirect with a success message
    echo '<script>alert("Booking Facility added successfully!"); window.location.href = "view_booking.php";</script>';
    exit();
} else {
    // Redirect with an error message
    header("Location: add_booking_facility.php?error=Error adding booking: " . $conn->error);
    exit();
}

$conn->close();
?>
