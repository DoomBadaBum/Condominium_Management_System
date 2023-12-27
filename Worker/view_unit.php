<?php
include '../include/connection.php';
session_start();

// Header sidebar
include '../include/header_worker.php'; 
include '../include/sidebar_worker.php';

// Check if the user is authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login_worker.php");
    exit();
}

// Fetch units
$sqlUnits = "SELECT * FROM unit";
$resultUnits = $conn->query($sqlUnits);

if ($resultUnits === false) {
    die("Error executing units query: " . $conn->error);
}

$conn->close();
?>

<!-- HTML content for viewing units -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Units - Worker</title>
</head>
<body>
    <h2>View Units - Worker</h2>
    <?php if ($resultUnits->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>Unit ID</th>
                <th>Unit Number</th>
                <th>Size</th>
                <th>Block Number</th>
                <th>Floor</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $resultUnits->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['unit_id']; ?></td>
                    <td><?php echo $row['unit_number']; ?></td>
                    <td><?php echo $row['size']; ?></td>
                    <td><?php echo $row['block_number']; ?></td>
                    <td><?php echo $row['floor']; ?></td>
                    <td><a href="update_unit.php?unit_id=<?php echo $row['unit_id']; ?>">Update</a>
                    <a href="delete_unit.php?unit_id=<?php echo $row['unit_id']; ?>" onclick="return confirm('Are you sure you want to delete this unit?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No units available.</p>
    <?php endif; ?>
</body>
</html>
