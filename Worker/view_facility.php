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

// Fetch the list of facilities
$facilitySql = "SELECT * FROM facility";
$facilityResult = $conn->query($facilitySql);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Facilities - Worker</title>
</head>
<body>
    <h2>View Facilities - Worker</h2>
    <?php if ($facilityResult->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>Facility ID</th>
                <th>Facility Name</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $facilityResult->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['facility_id']; ?></td>
                    <td><?php echo $row['facility_name']; ?></td>
                    <td>
                        <a href="update_facility.php?facility_id=<?php echo $row['facility_id']; ?>">Update</a>
                        <a href="delete_facility.php?facility_id=<?php echo $row['facility_id']; ?>">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No facilities available.</p>
    <?php endif; ?>
</body>
</html>
