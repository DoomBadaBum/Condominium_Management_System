<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Establish a database connection
include '../include/connection.php';

// Fetch the current user's information along with unit details
$userId = $_SESSION["user_id"];
$sql = "SELECT user.*, resident.*, unit.unit_number, unit.block_number, unit.floor, unit.size
        FROM user
        JOIN resident ON user.user_id = resident.user_id
        JOIN unit ON resident.unit_id = unit.unit_id
        WHERE user.user_id = $userId";

$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
} else {
    // Handle error (user not found)
    $user = null;
}

// Close the database connection
$conn->close();
?>

<?php include '../include/header.php'; ?>

<div class="../include/container">
    <?php include '../include/sidebar.php'; ?>

    <main>
        <h2>Resident Profile</h2>
        <?php if ($user): ?>
            <form action="update_profile.php" method="post">
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
            <p>Error: User not found.</p>
        <?php endif; ?>
    </main>
</div>

<?php include '../include/footer.php'; ?>
