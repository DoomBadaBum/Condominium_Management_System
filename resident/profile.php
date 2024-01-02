<?php
include '../include/connection.php';
session_start();
// Header sidebar
include '../include/header.php'; 
include '../include/sidebar.php';

// Check if the user is authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Fetch resident details
$userId = $_SESSION["user_id"];
$sql = "SELECT u.*, r.unit_number, r.block_number, r.floor, r.size
        FROM user u
        LEFT JOIN unit r ON u.unit_id = r.unit_id
        WHERE u.user_id = $userId";

$result = $conn->query($sql);

if ($result === false) {
    die("Error executing user query: " . $conn->error);
}

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
} else {
    $user = null;
}

$conn->close();
?>

<!-- HTML content for the resident profile page with update form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Profile</title>
</head>
<body>
    <h2>Resident Profile</h2>

    <?php if ($user): ?>
        <form action="update_profile.php" method="post" enctype="multipart/form-data">
            <!-- Display current profile picture -->
            <label for="current_profile_pic">Current Profile Picture:</label>
            <?php if (!empty($user['profile_pic'])): ?>
                <img src="../profile_pics/<?php echo $user['profile_pic']; ?>" alt="Current Profile Picture" width="50">
            <?php else: ?>
                No Picture
            <?php endif; ?>
            <!--
             File input for uploading a new profile picture
            <label for="profile_pic">Upload New Profile Picture:</label>
            <input type="file" id="profile_pic" name="profile_pic"><br>
            -->
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo $user['username']; ?>" readonly><br>

            <label for="fullname">Full Name:</label>
            <input type="text" id="fullname" name="fullname" value="<?php echo $user['fullname']; ?>"><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>"><br>

            <label for="phone">Phone Number:</label>
            <input type="tel" id="phone" name="phone" value="<?php echo $user['phone_number']; ?>"><br>

            <label for="address">IC Number:</label>
            <input type="text" id="address" name="address" value="<?php echo $user['ic_number']; ?>" readonly><br>

            <label for="emergency_contact">Emergency Contact:</label>
            <input type="text" id="emergency_contact" name="emergency_contact" value="<?php echo $user['emergency_contact']; ?>"><br>

            <!-- Display unit information -->
            <label for="unit_number">Unit Number:</label>
            <input type="text" id="unit_number" name="unit_number" value="<?php echo $user['unit_number']; ?>" readonly><br>

            <label for="block_number">Block Number:</label>
            <input type="text" id="block_number" name="block_number" value="<?php echo $user['block_number']; ?>" readonly><br>

            <label for="floor">Floor:</label>
            <input type="text" id="floor" name="floor" value="<?php echo $user['floor']; ?>" readonly><br>

            <label for="unit_size">Unit Size :</label>
            <input type="text" id="unit_size" name="unit_size" value="<?php echo $user['size']; ?>" readonly><br>

            <input type="submit" value="Update Profile">
        </form>
    <?php else: ?>
        <p>Error: Resident not found.</p>
    <?php endif; ?>
</body>
</html>
