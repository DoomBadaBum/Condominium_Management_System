<?php
include '../include/connection.php'; // Assuming this file includes your database connection

session_start();

// Check if the user is authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login_worker.php");
    exit();
}

// Check if facility_id is provided in the URL
if (!isset($_GET['facility_id'])) {
    header("Location: view_facility.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $facilityId = $_POST['facility_id'];
    $newFacilityName = $_POST['facility_name'];

    // Update facility in the database
    $updateFacilitySql = "UPDATE facility SET facility_name = '$newFacilityName' WHERE facility_id = $facilityId";

    if ($conn->query($updateFacilitySql) === TRUE) {
        echo '<script>alert("Facility updated successfully!"); window.location.href = "view_facility.php";</script>';
        exit();
    } else {
        echo "Error updating facility: " . $conn->error;
    }
}

// Fetch facility information
$facilityId = $_GET['facility_id'];
$facilitySql = "SELECT * FROM facility WHERE facility_id = $facilityId";
$facilityResult = $conn->query($facilitySql);

// Check if the facility exists
if ($facilityResult->num_rows != 1) {
    header("Location: view_facility.php");
    exit();
}

$facility = $facilityResult->fetch_assoc();

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Facility - Worker</title>
</head>
<body>
    <h2>Update Facility - Worker</h2>
    <form action="" method="post">
        <input type="hidden" name="facility_id" value="<?php echo $facility['facility_id']; ?>">
        
        <label for="facility_name">Facility Name:</label>
        <input type="text" id="facility_name" name="facility_name" value="<?php echo $facility['facility_name']; ?>" required><br>

        <input type="submit" value="Update Facility">
    </form>
</body>
</html>
