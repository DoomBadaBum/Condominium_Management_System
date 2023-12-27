<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Establish a database connection
include '../include/connection.php';

// Get maintenance request information from the form
$userId = $_SESSION["user_id"];
$categoryId = $_POST['category'];
$location = $_POST['location'];
$description = $_POST['description'];
$urgency = $_POST['urgency'];

// Set the current date in the format compatible with MySQL (YYYY-MM-DD)
$requestDate = date('Y-m-d');

// Insert the maintenance request into the database
$insertRequestSql = "INSERT INTO maintenance_request (user_id, category_id, location, description, urgency, status, request_date)
                    VALUES (
                        (SELECT user_id FROM user WHERE user_id = $userId),
                        $categoryId,
                        '$location',
                        '$description',
                        '$urgency',
                        'Pending',
                        '$requestDate'
                    )";

if ($conn->query($insertRequestSql) === TRUE) {
    // Use an absolute or root-relative URL for redirection
    header("Location: maintenance_requests.php?success=1");
    exit();
} else {
    // Display the SQL error
    echo "Error: " . $insertRequestSql . "<br>" . $conn->error;
}

// Close the database connection
$conn->close();
?>
