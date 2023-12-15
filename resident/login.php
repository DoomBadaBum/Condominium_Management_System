<?php
include '../include/connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get username and password from the form
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Perform SQL query to check user credentials
    $sql = "SELECT * FROM user WHERE username='$username' AND password='$password' AND role_id = 2";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        // User is authenticated, redirect to the dashboard
        $user = $result->fetch_assoc();
        $_SESSION["user_id"] = $user["user_id"];
        header("Location: dashboard.php");
    } else {
        // Invalid credentials, redirect to login page
        header("Location: login.php?error=1");
    }
}

$conn->close();
?>

<form action="login.php" method="post">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required><br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br>

    <input type="submit" value="Login">
</form>