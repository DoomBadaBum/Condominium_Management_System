<?php
include '../include/connection.php';

// Header sidebar
include '../include/header_worker.php'; 
include '../include/sidebar_worker.php';

session_start();

// Check if the user is authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login_worker.php");
    exit();
}

// Perform unit insertion when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $unitNumber = mysqli_real_escape_string($conn, $_POST["unit_number"]);
    $size = floatval($_POST["size"]);
    $blockNumber = intval($_POST["block_number"]);
    $floor = intval($_POST["floor"]);

    // Insert unit into the database
    $insertUnitSql = "INSERT INTO unit (unit_number, size, block_number, floor)
                      VALUES ('$unitNumber', $size, $blockNumber, $floor)";

    if ($conn->query($insertUnitSql) === TRUE) {
        echo '<script>alert("Unit added successfully!");</script>';
    } else {
        // Display the SQL error
        echo "Error: " . $insertUnitSql . "<br>" . $conn->error;
    }
}

$conn->close();
?>


<!-- HTML content for the add unit form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Unit</title>
</head>
<body>
    <h2>Add Unit</h2>
    <form action="add_unit.php" method="post">
        <label for="unit_number">Unit Number:</label>
        <input type="text" id="unit_number" name="unit_number" required><br>

        <label for="size">Size:</label>
        <input type="number" id="size" name="size" required><br>

        <label for="block_number">Block Number:</label>
        <input type="number" id="block_number" name="block_number" required><br>

        <label for="floor">Floor:</label>
        <input type="number" id="floor" name="floor" required><br>

        <input type="submit" value="Add Unit">
    </form>
</body>
</html>
