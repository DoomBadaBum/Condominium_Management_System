<?php
include '../include/connection.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the booking_id is set in the URL
if (!isset($_GET['booking_id'])) {
    header("Location: view_bookings.php");
    exit();
}

$bookingId = $_GET['booking_id'];

// Delete the booking from the database
$sqlDeleteBooking = "DELETE FROM booking WHERE booking_id = $bookingId";

if ($conn->query($sqlDeleteBooking) === TRUE) {
    echo '<script>alert("Booking Facility deleted successfully!"); window.location.href = "view_booking.php";</script>';
    exit();
} else {
    echo "Error deleting booking: " . $conn->error;
}

// Close the database connection
$conn->close();
?>
