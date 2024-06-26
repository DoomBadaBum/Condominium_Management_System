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
        echo '<script>alert("Wrong Username or Password!");</script>';
        echo '<script>window.location.href = "login.php";</script>';
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Resident - Login</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="../assets/css/Lightbox-Gallery-baguetteBox.min.css">

    <script>
    function showForgotPasswordPopup() {
        alert("Please contact staff +60186673078 for password assistance.");
    }
</script>

</head>

<body class="bg-gradient-primary d-flex align-items-center justify-content-center min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-12 col-xl-10">
                <div class="card shadow-lg o-hidden border-0 my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-flex">
                                <div class="flex-grow-1 bg-login-image" style="background: url(&quot;../assets/img/dogs/login.jpg&quot;) right / cover;"></div>
                            </div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h4 class="text-dark mb-4">Welcome To <br>Kemuncak Shah Alam!</h4>
                                    </div>
                                    <form class="user" action="login.php" method="post">
                                        <div class="mb-3"><input class="form-control form-control-user" type="text" id="username" aria-describedby="emailHelp" placeholder="Username" name="username"></div>
                                        <div class="mb-3"><input class="form-control form-control-user" type="password" id="exampleInputPassword" placeholder="Password" name="password"></div>
                                        <div class="mb-3">
                                            <div class="custom-control custom-checkbox small">
                                                <div class="form-check"><input class="form-check-input custom-control-input" type="checkbox" id="formCheck-1"><label class="form-check-label custom-control-label" for="formCheck-1">Remember Me</label></div>
                                            </div>
                                        </div><button class="btn btn-primary d-block btn-user w-100" type="submit">Login</button>
                                        <hr>
                                    </form>
                                    <div class="text-center"><a class="small" href="#" onclick="showForgotPasswordPopup()">Forgot Password?</a></div>
                                    <div class="text-center"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="../assets/js/bs-init.js"></script>
    <script src="../assets/js/Lightbox-Gallery-baguetteBox.min.js"></script>
    <script src="../assets/js/Lightbox-Gallery.js"></script>
    <script src="../assets/js/theme.js"></script>
</body>

</html>