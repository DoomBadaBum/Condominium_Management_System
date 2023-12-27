<?php
include '../include/connection.php';

// Header footer
include '../include/header_worker.php'; 
include '../include/sidebar_worker.php';

// Check if facility_id is provided in the URL for deletion
if (isset($_GET['facility_id'])) {
    $facilityId = $_GET['facility_id'];

    // Check if the facility is associated with any bookings
    $checkBookingsSql = "SELECT * FROM booking WHERE facility_id = $facilityId";
    $bookingsResult = $conn->query($checkBookingsSql);

    if ($bookingsResult->num_rows > 0) {
        echo '<script>alert("Cannot delete the facility as it is associated with bookings."); window.location.href = "view_facility.php";</script>';
        exit();
    }

    // Delete the record from the database
    $deleteFacilitySql = "DELETE FROM facility WHERE facility_id = $facilityId";

    if ($conn->query($deleteFacilitySql) === TRUE) {
        echo '<script>alert("Facility deleted successfully!"); window.location.href = "view_facility.php";</script>';
        exit();
    } else {
        echo "Error deleting facility: " . $conn->error;
    }
}

// Fetch the list of facilities
$facilitySql = "SELECT * FROM facility";
$facilityResult = $conn->query($facilitySql);

// Close the database connection
$conn->close();
?>