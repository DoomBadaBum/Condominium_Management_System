<?php
include '../include/connection.php';
session_start();

// Check if the user is authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login_worker.php");
    exit();
}

// Check if unit_id is provided in the URL
if (isset($_GET['unit_id'])) {
    $unitId = $_GET['unit_id'];

    // Check if there are residents assigned to the unit
    $checkResidentsSql = "SELECT COUNT(*) as resident_count FROM user WHERE unit_id = $unitId AND role_id = 2"; // Assuming role_id 2 corresponds to the "Resident" role
    $resultResidents = $conn->query($checkResidentsSql);

    if ($resultResidents === false) {
        die("Error checking residents: " . $conn->error);
    }

    $rowResidents = $resultResidents->fetch_assoc();
    $residentCount = $rowResidents['resident_count'];

    if ($residentCount > 0) {
        // Residents are assigned, display an alert
        echo '<script>alert("Cannot delete unit. Residents are currently assigned to this unit."); window.location.href = "view_unit.php";</script>';
        exit();
    }

    // Delete the record from the database
    $deleteUnitSql = "DELETE FROM unit WHERE unit_id = $unitId";

    if ($conn->query($deleteUnitSql) === TRUE) {
        // Redirect to the view page after successful deletion
        echo '<script>alert("Unit deleted successfully!"); window.location.href = "view_unit.php";</script>';
        exit();
    } else {
        echo "Error deleting unit: " . $conn->error;
    }
} else {
    echo "No ID provided in the URL.";
}

$conn->close();
?>
