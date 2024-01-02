<?php
include '../include/connection.php';
session_start();

// Check if the user is authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login_worker.php");
    exit();
}

// Check if user_id is provided in the query parameters
if (!isset($_GET["user_id"])) {
    header("Location: view_residents.php");
    exit();
}

$userId = $_GET["user_id"];

// Fetch resident information
$sqlResident = "SELECT * FROM user WHERE role_id = 2 AND user_id = $userId";
$resultResident = $conn->query($sqlResident);

if ($resultResident === false) {
    die("Error executing resident query: " . $conn->error);
}

if ($resultResident->num_rows != 1) {
    header("Location: view_residents.php");
    exit();
}

$resident = $resultResident->fetch_assoc();

// Fetch units for the dropdown
$unitOptions = '';
$sqlUnits = "SELECT unit_id, unit_number FROM unit";
$resultUnits = $conn->query($sqlUnits);

if ($resultUnits->num_rows > 0) {
    while ($rowUnits = $resultUnits->fetch_assoc()) {
        $selected = ($resident['unit_id'] == $rowUnits['unit_id']) ? 'selected' : '';
        $unitOptions .= '<option value="' . $rowUnits['unit_id'] . '" ' . $selected . '>' . $rowUnits['unit_number'] . '</option>';
    }
}

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST["fullname"];
    $email = $_POST["email"];
    $phone_number = $_POST["phone_number"];
    $emergency_contact = $_POST["emergency_contact"];
    $unit_id = $_POST["unit_id"];

    // Handle profile picture upload
    $profilePicFileName = $resident['profile_pic']; // Keep the existing profile picture if no new file is uploaded
    if (!empty($_FILES['profile_pic']['name'])) {
        $targetDir = "../profile_pics/";
        $profilePicFileName = basename($_FILES['profile_pic']['name']);
        $targetFilePath = $targetDir . $profilePicFileName;
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetFilePath);
    }

    // Update resident information in the database
    $updateResidentSql = "UPDATE user
                          SET fullname = '$fullname',
                              email = '$email',
                              phone_number = '$phone_number',
                              emergency_contact = '$emergency_contact',
                              unit_id = $unit_id,
                              profile_pic = '$profilePicFileName'
                          WHERE user_id = $userId";

    if ($conn->query($updateResidentSql) === TRUE) {
        echo '<script>alert("Resident updated successfully!");</script>';
        echo '<script>window.location.href = "view_resident.php?user_id=' . $userId . '";</script>';
        exit();
    } else {
        // Display the SQL error
        echo "Error: " . $updateResidentSql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!-- HTML content for updating resident information -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Resident - Worker</title>
</head>
<body>
    <h2>Update Resident - Worker</h2>
    <form action="update_resident.php?user_id=<?php echo $userId; ?>" method="post" enctype="multipart/form-data">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?php echo $resident['username']; ?>" readonly><br>

        <label for="fullname">Full Name:</label>
        <input type="text" id="fullname" name="fullname" value="<?php echo $resident['fullname']; ?>" required><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo $resident['email']; ?>" required><br>

        <label for="phone_number">Phone Number:</label>
        <input type="text" id="phone_number" name="phone_number" value="<?php echo $resident['phone_number']; ?>" required><br>

        <label for="ic_number">IC Number:</label>
        <input type="text" id="ic_number" name="ic_number" value="<?php echo $resident['ic_number']; ?>" readonly><br>

        <label for="emergency_contact">Emergency Contact:</label>
        <input type="text" id="emergency_contact" name="emergency_contact" value="<?php echo $resident['emergency_contact']; ?>" required><br>

        <!-- Dropdown for selecting unit -->
        <label for="unit_id">Select Unit:</label>
        <select id="unit_id" name="unit_id" required>
            <?php echo $unitOptions; ?>
        </select><br>

        <!-- Display current profile picture -->
        <label for="current_profile_pic">Current Profile Picture:</label>
        <?php if (!empty($resident['profile_pic'])): ?>
            <img src="../profile_pics/<?php echo $resident['profile_pic']; ?>" alt="Current Profile Picture" width="50">
        <?php else: ?>
            No Picture
        <?php endif; ?>
        <br>
        <!-- File input for uploading a new profile picture -->
        <label for="profile_pic">Upload New Profile Picture:</label>
        <input type="file" id="profile_pic" name="profile_pic"><br>
        <input type="submit" value="Update Resident">
    </form>
</body>
</html>
