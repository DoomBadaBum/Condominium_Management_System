<!-- dashboard_worker.php -->
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login_worker.php");
    exit();
}

include '../include/connection.php';

$userId = $_SESSION["user_id"];
$sql = "SELECT * FROM user WHERE user_id = $userId";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
} else {
    $user = null;
}

$conn->close();
?>

<!-- Include header and sidebar -->
<?php include '../include/header_worker.php'; ?>
<?php include '../include/sidebar_worker.php'; ?>

<!-- HTML content for worker dashboard -->
<main>
    <h2>Welcome to the Worker Dashboard</h2>
    <?php if ($user): ?>
        <p>Worker: <?php echo $user['username']; ?></p>
        <!-- Add worker-specific content here -->
        <p><a href="logout_worker.php">Logout</a></p>
    <?php else: ?>
        <p>Error: Worker not found.</p>
    <?php endif; ?>
</main>

<!-- Include footer -->
<?php include '../include/footer_worker.php'; ?>
