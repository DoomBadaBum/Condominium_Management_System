<?php
include '../include/connection.php';

// Header footer
include '../include/header_worker.php'; 
include '../include/sidebar_worker.php';
session_start();

// Check if the user is authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login_worker.php");
    exit();
}

// Fetch bookings
$sqlBookings = "SELECT b.booking_id, u.username, f.facility_name, b.booking_date, b.start_time, b.end_time
                FROM booking b
                JOIN user u ON b.user_id = u.user_id
                JOIN facility f ON b.facility_id = f.facility_id";
$resultBookings = $conn->query($sqlBookings);

if ($resultBookings === false) {
    die("Error executing bookings query: " . $conn->error);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bookings - Worker</title>
</head>
<body>
    <h2>View Bookings - Worker</h2>
    <?php if ($resultBookings->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>Booking ID</th>
                <th>User</th>
                <th>Facility</th>
                <th>Booking Date</th>
                <th>Start Time</th>
                <th>End Time</th>
            </tr>
            <?php while ($row = $resultBookings->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['booking_id']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['facility_name']; ?></td>
                    <td><?php echo $row['booking_date']; ?></td>
                    <td><?php echo $row['start_time']; ?></td>
                    <td><?php echo $row['end_time']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No bookings available.</p>
    <?php endif; ?>
</body>
</html>
