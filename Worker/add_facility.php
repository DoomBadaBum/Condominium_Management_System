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

// Fetch the current maximum user_id from the user table
$sqlMaxFacilityId = "SELECT MAX(facility_id) AS max_facility_id FROM facility";
$resultMaxFacilityId = $conn->query($sqlMaxFacilityId);

if ($resultMaxFacilityId === false) {
    die("Error getting max facility_id: " . $conn->error);
}

$maxFacilityIdRow = $resultMaxFacilityId->fetch_assoc();
$maxFacilityId = $maxFacilityIdRow['max_facility_id'];

// Increment the max user_id to get a new user_id
$newFacilityId = $maxFacilityId + 1;

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate form data
    $facilityName = $_POST['facility_name'];

    // Insert the new facility into the database
    $insertFacilitySql = "INSERT INTO facility (facility_id, facility_name) VALUES ($newFacilityId, '$facilityName')";

    if ($conn->query($insertFacilitySql) === TRUE) {
        echo '<script>alert("Facility added successfully!"); window.location.href = "view_facility.php";</script>';
        exit();
    } else {
        echo "Error adding facility: " . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Facility - Worker</title>
</head>
<body>
    <h2>Add Facility - Worker</h2>
    
    <!-- Facility addition form -->
    <form action="add_facility.php" method="post">
        <label for="facility_name">Facility Name:</label>
        <input type="text" id="facility_name" name="facility_name" required><br>

        <input type="submit" value="Add Facility">
    </form>
</body>
</html>
