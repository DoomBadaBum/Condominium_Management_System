<?php
include '../include/connection.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $bookingId = $_POST['booking_id'];
    $residentId = $_POST['user_id'];
    $facilityId = $_POST['facility_id'];
    $bookingDate = $_POST['booking_date'];
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'];

    // Update booking in the database
    $sqlUpdateBooking = "UPDATE booking SET user_id = $residentId, facility_id = $facilityId, booking_date = '$bookingDate', start_time = '$startTime', end_time = '$endTime' WHERE booking_id = $bookingId";

    if ($conn->query($sqlUpdateBooking) === TRUE) {
        echo '<script>alert("Booking Facility updated successfully!"); window.location.href = "view_booking.php";</script>';
        exit();
    } else {
        echo "Error updating booking: " . $conn->error;
    }

    // Close the database connection
    $conn->close();
} else {
    // If the form is not submitted, redirect to the view_bookings.php page
    header("Location: view_booking.php");
    exit();
}
?>
