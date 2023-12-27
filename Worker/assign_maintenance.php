<!-- assign_maintenance.php -->
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

// Fetch pending maintenance requests
$maintenanceRequestsSql = "SELECT maintenance_request.request_id, maintenance_request.description, maintenance_request.status, category.category_name AS category, maintenance_request.location, maintenance_request.urgency, maintenance_request.request_date
                          FROM maintenance_request
                          JOIN category ON maintenance_request.category_id = category.category_id
                          WHERE maintenance_request.Status = 'Pending'
                          ORDER BY maintenance_request.request_date DESC";

$maintenanceRequestsResult = $conn->query($maintenanceRequestsSql);

if ($maintenanceRequestsResult === false) {
    die("Error executing maintenance requests query: " . $conn->error);
}

// Handle form submission for assigning maintenance requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $requestId = $_POST["request_id"];

    // Update the status of the maintenance request to 'In Progress' and record assignment details
    $updateStatusSql = "UPDATE maintenance_request SET status = 'In Progress', assigned_worker_id = $userId, assignment_date = NOW() WHERE request_id = $requestId";

    if ($conn->query($updateStatusSql) === TRUE) {
        echo '<script>alert("Maintenance request assigned successfully!");</script>';
        echo '<script>window.location.href = "view_maintenance_requests.php";</script>';
        exit();
    } else {
        echo "Error updating maintenance request status: " . $conn->error;
    }
}

$conn->close();
?>

<!-- HTML content for assigning maintenance requests page -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Maintenance Requests - Worker</title>
</head>
<body>
    <h2>Assign Maintenance Requests - Worker</h2>
    <?php if ($user): ?>
        <p>Worker: <?php echo $user['username']; ?></p>
        <!-- Display maintenance requests -->
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
                        <th>Action</th>
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
                            <td>
                                <form method="post" action="">
                                    <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
                                    <input type="submit" value="Assign">
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No pending maintenance requests.</p>
        <?php endif; ?>
        <p><a href="logout_worker.php">Logout</a></p>
    <?php else: ?>
        <p>Error: Worker not found.</p>
    <?php endif; ?>
    <!-- Include footer -->
    <?php include '../include/footer_worker.php'; ?>
</body>
</html>
