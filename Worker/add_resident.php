<?php
include '../include/connection.php';
// Header sidebar
include '../include/header_worker.php'; 
include '../include/sidebar_worker.php';
session_start();

// Check if the user is authenticated and has admin privileges
if (!isset($_SESSION["user_id"])) {
    header("Location: login_admin.php");
    exit();
}

// Fetch the current maximum user_id from the user table
$sqlMaxUserId = "SELECT MAX(user_id) AS max_user_id FROM user";
$resultMaxUserId = $conn->query($sqlMaxUserId);

if ($resultMaxUserId === false) {
    die("Error getting max user_id: " . $conn->error);
}

$maxUserIdRow = $resultMaxUserId->fetch_assoc();
$maxUserId = $maxUserIdRow['max_user_id'];

// Increment the max user_id to get a new user_id
$newUserId = $maxUserId + 1;

// Perform resident insertion when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $role_id = 2; // Assuming role_id 2 corresponds to the "Resident" role
    $fullname = $_POST["fullname"];
    $email = $_POST["email"];
    $phone_number = $_POST["phone_number"];
    $ic_number = $_POST["ic_number"];
    $emergency_contact = $_POST["emergency_contact"];
    $unit_id = $_POST["unit_id"];

    // Insert resident into the database with the new user_id
    $insertResidentSql = "INSERT INTO user (user_id, username, password, role_id, fullname, email, phone_number, ic_number, emergency_contact, unit_id)
                          VALUES ($newUserId, '$username', '$password', $role_id, '$fullname', '$email', '$phone_number', '$ic_number', '$emergency_contact', $unit_id)";

    if ($conn->query($insertResidentSql) === TRUE) {
        echo '<script>alert("Resident added successfully!");</script>';
    } else {
        // Display the SQL error
        echo "Error: " . $insertResidentSql . "<br>" . $conn->error;
    }
}

// Fetch units for the dropdown
$unitOptions = '';
$sql = "SELECT unit_id, unit_number FROM unit";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $unitOptions .= '<option value="' . $row['unit_id'] . '">' . $row['unit_number'] . '</option>';
    }
}

$conn->close();
?>

<!-- HTML content for the add resident form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Resident</title>
</head>
<body>
    <h2>Add Resident</h2>
    <form action="add_resident.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>

        <label for="fullname">Full Name:</label>
        <input type="text" id="fullname" name="fullname" required><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>

        <label for="phone_number">Phone Number:</label>
        <input type="text" id="phone_number" name="phone_number" required><br>

        <label for="ic_number">IC Number:</label>
        <input type="text" id="ic_number" name="ic_number" required><br>

        <label for="emergency_contact">Emergency Contact:</label>
        <input type="text" id="emergency_contact" name="emergency_contact" required><br>

        <label for="unit_id">Unit ID:</label>
        <select id="unit_id" name="unit_id" required>
            <?php echo $unitOptions; ?>
        </select><br>

        <input type="submit" value="Add Resident">
    </form>
</body>
</html>
