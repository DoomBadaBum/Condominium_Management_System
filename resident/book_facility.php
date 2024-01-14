<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Set the timezone to Kuala Lumpur
date_default_timezone_set('Asia/Kuala_Lumpur');

// Establish a database connection
include '../include/connection.php';

// Get booking information from the form
$userId = $_SESSION["user_id"];
$facilityId = $_POST['facility'];
$bookingDate = $_POST['booking_date'];
$startTime = $_POST['start_time'];
$endTime = $_POST['end_time'];

// Check if the booking date is in the past or today with a past time
if (isBookingDateTimeInPast($bookingDate, $startTime)) {
    $conn->close();
    echo '<script>alert("Cannot make a booking for a past date or time. Please choose a future date and time."); window.location.href = "facility_booking.php";</script>';
    exit();
}

// Check if the facility is available for the given time slot
$availabilityCheckSql = "SELECT * FROM booking 
                         WHERE facility_id = $facilityId 
                         AND booking_date = '$bookingDate' 
                         AND (
                             (start_time <= '$startTime' AND end_time > '$startTime') 
                             OR 
                             (start_time < '$endTime' AND end_time >= '$endTime')
                             OR
                             (start_time >= '$startTime' AND end_time <= '$endTime')
                         ) ORDER BY booking_date DESC";

$availabilityResult = $conn->query($availabilityCheckSql);

if ($availabilityResult->num_rows > 0) {
    // Facility is not available for the given time slot
    $conn->close();
    echo '<script>alert("Facility is not available for the selected time slot. Please choose another time."); window.location.href = "facility_booking.php";</script>';
    exit();
}

// Insert the booking into the database
$insertBookingSql = "INSERT INTO booking (user_id, facility_id, booking_date, start_time, end_time)
                    VALUES (
                        (SELECT user_id FROM user WHERE user_id = $userId), 
                        $facilityId, 
                        '$bookingDate', 
                        '$startTime', 
                        '$endTime'
                    )";

if ($conn->query($insertBookingSql) === TRUE) {
    $conn->close();
    echo '<script>alert("Booking successful!"); window.location.href = "facility_booking.php";</script>';
    exit();
}

if (isset($errorMessage)) {
    echo '<p style="color: red;">' . $errorMessage . '</p>';
}

// Function to check if the booking date is in the past or today with a past time
function isBookingDateTimeInPast($bookingDate, $startTime) {
    $currentDateTime = date('Y-m-d H:i:s');
    $bookingDateTime = $bookingDate . ' ' . $startTime;
    
    return ($bookingDateTime <= $currentDateTime);
}
?>
