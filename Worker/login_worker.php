<!-- login_worker.php -->
<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard_worker.php");
    exit();
}

include '../include/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM user WHERE username = '$username' AND password = '$password' AND role_id = 3";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION["user_id"] = $user['user_id'];
        header("Location: dashboard_worker.php");
        exit();
    } else {
        $error_message = "Invalid username or password.";
    }
}

$conn->close();
?>

<!-- HTML form for worker login -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Login</title>
</head>
<body>
    <h2>Worker Login</h2>
    <?php if (isset($error_message)) echo "<p>$error_message</p>"; ?>
    <form action="login_worker.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>

        <input type="submit" value="Login">
    </form>
</body>
</html>
