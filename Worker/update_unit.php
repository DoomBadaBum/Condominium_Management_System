<?php
include '../include/connection.php';
session_start();

// Check if the user is authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login_worker.php");
    exit();
}

// Check if unit_id is provided in the query parameters
if (!isset($_GET["unit_id"])) {
    header("Location: view_unit.php");
    exit();
}

$unitId = $_GET["unit_id"];

// Fetch unit information
$sqlUnit = "SELECT * FROM unit WHERE unit_id = $unitId";
$resultUnit = $conn->query($sqlUnit);

if ($resultUnit === false) {
    die("Error executing unit query: " . $conn->error);
}

if ($resultUnit->num_rows != 1) {
    header("Location: view_unit.php");
    exit();
}

$unit = $resultUnit->fetch_assoc();

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get updated values from the form
    $unitNumber = $_POST["unit_number"];
    $size = $_POST["size"];
    $blockNumber = $_POST["block_number"];
    $floor = $_POST["floor"];

    // Update unit in the database
    $updateUnitSql = "UPDATE unit SET unit_number = '$unitNumber', size = $size, block_number = $blockNumber, floor = $floor WHERE unit_id = $unitId";

    if ($conn->query($updateUnitSql) === TRUE) {
        echo '<script>alert("Unit updated successfully!");</script>';
        echo '<script>window.location.href = "view_unit.php";</script>';
        exit();
    } else {
        // Display the SQL error
        echo "Error: " . $updateUnitSql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Unit</title>
</head>
<body>
    <h2>Update Unit</h2>
    <form action="update_unit.php?unit_id=<?php echo $unit['unit_id']; ?>" method="post">
        <label for="unit_number">Unit Number:</label>
        <input type="text" id="unit_number" name="unit_number" value="<?php echo $unit['unit_number']; ?>" required><br>

        <label for="size">Size:</label>
        <input type="number" id="size" name="size" min="1" max="" value="<?php echo $unit['size']; ?>" required><br>

        <label for="block_number">Block Number:</label>
        <input type="number" id="block_number" name="block_number" value="<?php echo $unit['block_number']; ?>" required><br>

        <label for="floor">Floor:</label>
        <input type="number" id="floor" name="floor" value="<?php echo $unit['floor']; ?>" required><br>

        <input type="submit" value="Update Unit">
    </form>
</body>
</html>
