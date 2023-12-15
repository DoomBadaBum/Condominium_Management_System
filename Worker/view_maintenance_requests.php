<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login_worker.php");
    exit();
}

include '../include/connection.php';
// Header footer
include '../include/header_worker.php'; 
include '../include/sidebar_worker.php';

$userId = $_SESSION["user_id"];
$sql = "SELECT * FROM user WHERE user_id = $userId";
$result = $conn->query($sql);

if ($result === false) {
    die("Error executing user query: " . $conn->error);
}

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
} else {
    $user = null;
}

// Fetch all maintenance requests
$maintenanceRequestsSql = "SELECT maintenance_request.request_id, maintenance_request.description, maintenance_request.status, 
                            category.category_name AS category, maintenance_request.location, maintenance_request.urgency, 
                            maintenance_request.request_date, maintenance_request.completion_date
                            FROM maintenance_request
                            JOIN category ON maintenance_request.category_id = category.category_id
                            ORDER BY maintenance_request.request_date DESC";

$maintenanceRequestsResult = $conn->query($maintenanceRequestsSql);

if ($maintenanceRequestsResult === false) {
    die("Error executing maintenance requests query: " . $conn->error);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Maintenance Requests - Worker</title>
</head>
<body>
    <h2>All Maintenance Requests - Worker</h2>
    <?php if ($user): ?>
        <p>Worker: <?php echo $user['username']; ?></p>

        <!-- Display all maintenance requests -->
        <?php if ($maintenanceRequestsResult->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Category</th>
                        <th>Location</th>
                        <th>Urgency</th>
                        <th>Request Date</th>
                        <th>Completion Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $maintenanceRequestsResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['request_id']; ?></td>
                            <td><?php echo $row['description']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                            <td><?php echo $row['category']; ?></td>
                            <td><?php echo $row['location']; ?></td>
                            <td><?php echo $row['urgency']; ?></td>
                            <td><?php echo $row['request_date']; ?></td>
                            <td><?php echo $row['completion_date']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No maintenance requests found.</p>
        <?php endif; ?>

        <p><a href="logout_worker.php">Logout</a></p>
    <?php else: ?>
        <p>Error: Worker not found.</p>
    <?php endif; ?>

    <!-- Include footer -->
    <?php include '../include/footer_worker.php'; ?>
</body>
</html>
