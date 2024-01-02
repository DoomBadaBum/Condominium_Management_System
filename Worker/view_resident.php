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

// Fetch residents
$sqlResidents = "SELECT u.user_id, u.username, u.fullname, u.email, u.phone_number, u.ic_number, u.emergency_contact, u.unit_id, ur.unit_number, ur.block_number, ur.floor, ur.size, u.profile_pic
                 FROM user u
                 LEFT JOIN unit ur ON u.unit_id = ur.unit_id
                 WHERE u.role_id = 2"; // Assuming role_id 2 corresponds to the "Resident" role
$resultResidents = $conn->query($sqlResidents);

if ($resultResidents === false) {
    die("Error executing residents query: " . $conn->error);
}

$conn->close();
?>

<!-- HTML content for viewing residents -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Residents - Worker</title>
</head>
<body>
    <h2>View Residents - Worker</h2>
    <?php if ($resultResidents->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>Profile Picture</th>
                <th>Username</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>IC Number</th>
                <th>Emergency Contact</th>
                <th>Unit Number</th>
                <th>Block Number</th>
                <th>Floor</th>
                <th>Unit Size</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $resultResidents->fetch_assoc()): ?>
                <tr>
                    <td>
                        <?php if (!empty($row['profile_pic'])): ?>
                            <img src="../profile_pics/<?php echo $row['profile_pic']; ?>" alt="Profile Picture" width="50">
                        <?php else: ?>
                            No Picture
                        <?php endif; ?>
                    </td>
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['fullname']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['phone_number']; ?></td>
                    <td><?php echo $row['ic_number']; ?></td>
                    <td><?php echo $row['emergency_contact']; ?></td>
                    <td><?php echo $row['unit_number']; ?></td>
                    <td><?php echo $row['block_number']; ?></td>
                    <td><?php echo $row['floor']; ?></td>
                    <td><?php echo $row['size']; ?></td>
                    <td>
                        <a href="update_resident.php?user_id=<?php echo $row['user_id']; ?>">Update</a>
                        <a href="delete_resident.php?user_id=<?php echo $row['user_id']; ?>" onclick="return confirm('Are you sure you want to delete this resident?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No residents available.</p>
    <?php endif; ?>
</body>
</html>
