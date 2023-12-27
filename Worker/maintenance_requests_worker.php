<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login_worker.php");
    exit();
}

include '../include/connection.php';
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

// Handle form submission for completing maintenance requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $requestId = $_POST["request_id"];
    $completionDate = $_POST["completion_date"];
    $status = $_POST["status"];

    // Update the status and completion details of the maintenance request
    $updateCompletionSql = "UPDATE maintenance_request 
                            SET status = '$status', completion_date = '$completionDate'
                            WHERE request_id = $requestId";

    if ($conn->query($updateCompletionSql) === TRUE) {
        echo '<script>alert("Maintenance request updated successfully!");</script>';
        echo '<script>window.location.href = "view_maintenance_requests.php";</script>';
        exit();
    } else {
        echo "Error updating maintenance request completion details: " . $conn->error;
    }
}

// Fetch maintenance requests for the worker
$maintenanceRequestsSql = "SELECT maintenance_request.request_id, maintenance_request.description, maintenance_request.status, category.category_name AS category, maintenance_request.location, maintenance_request.urgency, maintenance_request.request_date, maintenance_request.completion_date
                          FROM maintenance_request
                          JOIN category ON maintenance_request.category_id = category.category_id
                          WHERE maintenance_request.Status IN ('Pending', 'In Progress')
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
    <title>Maintenance Requests - Worker</title>
</head>
<body>
    <h2>Maintenance Requests - Worker</h2>
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
                                <?php if ($row['status'] == 'In Progress'): ?>
                                    <form method="post" action="">
                                        <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
                                        <label for="completion_date">Completion Date:</label>
                                        <input type="datetime-local" name="completion_date" required><br>
                                        <label for="status">Status:</label>
                                        <select name="status" required>
                                            <option value="Completed">Completed</option>
                                            <option value="Not Completed">Not Completed</option>
                                        </select><br>
                                        <input type="submit" value="Update Progress">
                                    </form>
                                <?php endif; ?>
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
