<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Establish a database connection
include '../include/connection.php';

// Fetch the list of maintenance requests for the current resident
$userId = $_SESSION["user_id"];
$maintenanceRequestsSql = "SELECT maintenance_request.request_id, category.category_name, maintenance_request.description,maintenance_request.location, maintenance_request.request_date , maintenance_request.urgency, maintenance_request.status
                          FROM maintenance_request
                          INNER JOIN category ON maintenance_request.category_id = category.category_id
                          WHERE user_id = (SELECT user_id FROM user WHERE user_id = $userId)";
$maintenanceRequestsResult = $conn->query($maintenanceRequestsSql);

// Fetch categories from the database
$categoriesSql = "SELECT category_id, category_name FROM category";
$categoriesResult = $conn->query($categoriesSql);

// Check if the maintenance request form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoryID = $_POST["category"];
    $location = $_POST["location"];
    $description = $_POST["description"];
    $urgency = $_POST["urgency"];

    // Insert the maintenance request into the database
    $insertRequestSql = "INSERT INTO maintenance_request (user_id, category_id, location, description, urgency, status, request_date)
                        VALUES ((SELECT user_id FROM user WHERE user_id = $userId), $categoryID, '$location', '$description', '$urgency', 'Pending', NOW())";

    if ($conn->query($insertRequestSql) === true) {
        $requestSubmissionMessage = "Maintenance request submitted successfully!";
    } else {
        $requestSubmissionMessage = "Error submitting maintenance request: " . $conn->error;
    }
}

$conn->close();
?>

<?php include '../include/header.php'; ?>

<div class="container">
    <?php include '../include/sidebar.php'; ?>

    <main>
        <h2>Maintenance Requests</h2>

        <!-- Display existing maintenance requests -->
        <?php if ($maintenanceRequestsResult->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Category</th>
                        <th>Location</th>
                        <th>Description</th>
                        <th>Urgency</th>
                        <th>Request Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $maintenanceRequestsResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['request_id']; ?></td>
                            <td><?php echo $row['category_name']; ?></td>
                            <td><?php echo $row['location']; ?></td>
                            <td><?php echo $row['description']; ?></td>
                            <td><?php echo $row['urgency']; ?></td>
                            <td><?php echo $row['request_date']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No maintenance requests submitted.</p>
        <?php endif; ?>

        <!-- Maintenance request form -->
        <h3>Submit Maintenance Request</h3>
        <?php if (isset($requestSubmissionMessage)): ?>
            <p><?php echo $requestSubmissionMessage; ?></p>
        <?php endif; ?>
        <form action="submit_maintenance_request.php" method="post">
            <!-- Dropdown for dynamic categories -->
            <label for="category">Category:</label>
            <?php if ($categoriesResult->num_rows > 0): ?>
                <select id="category" name="category" required>
                    <?php while ($categoryRow = $categoriesResult->fetch_assoc()): ?>
                        <option value="<?php echo $categoryRow['category_id']; ?>"><?php echo $categoryRow['category_name']; ?></option>
                    <?php endwhile; ?>
                </select><br>
            <?php else: ?>
                <p>No categories available. Please contact the administrator.</p>
            <?php endif; ?>

            <label for="location">Location:</label>
            <input type="text" id="location" name="location" required><br>

            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea><br>

            <label for="urgency">Urgency:</label>
            <select id="urgency" name="urgency" required>
                <option value="Low">Low</option>
                <option value="Medium">Medium</option>
                <option value="High">High</option>
            </select><br>

            <input type="submit" value="Submit Request">
        </form>
    </main>
</div>

<?php include '../include/footer.php'; ?>
