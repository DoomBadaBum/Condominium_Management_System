<?php
include '../include/connection.php';

session_start();

// Check if the user is authenticated and has admin privileges
if (!isset($_SESSION["user_id"])) {
    header("Location: login_admin.php");
    exit();
}

$userId = $_SESSION["user_id"];
$sql = "SELECT * FROM user WHERE user_id = $userId";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
} else {
    $user = null;
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
    $role_id = 3; // Assuming role_id 3 corresponds to the "Worker" role
    $fullname = $_POST["fullname"];
    $email = $_POST["email"];
    $phone_number = $_POST["phone_number"];
    $ic_number = $_POST["ic_number"];
    $emergency_contact = $_POST["emergency_contact"];
    $gender = $_POST["gender"];
    $age = $_POST["age"];

    // Handle image upload
    $target_dir = "../profile_pics/";
    $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES["profile_pic"]["tmp_name"]);
        if($check !== false) {
            echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
    }

    if (file_exists($target_file)) {
        echo '';
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["profile_pic"]["size"] > 5000000) {
        echo 'Sorry, your image file is too large.';
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif") {
        echo 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.';
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo 'Sorry, your file was not uploaded.';
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            echo "The file ". basename( $_FILES["profile_pic"]["name"]). " has been uploaded.";
        } else {
            echo '<script>alert("Sorry, there was an error uploading your file.");</script>';
            echo '<script>window.location.href = "view_worker.php";</script>';
        }
    }

    // Insert resident into the database with the new user_id and profile picture file name
    $profilePicFileName = basename($_FILES["profile_pic"]["name"]);
    $insertResidentSql = "INSERT INTO user (user_id, username, password, role_id, fullname, email, phone_number, ic_number, emergency_contact, profile_pic, gender, age)
                          VALUES ($newUserId, '$username', '$password', $role_id, '$fullname', '$email', '$phone_number', '$ic_number', '$emergency_contact', '$profilePicFileName', '$gender', '$age')";

    if ($conn->query($insertResidentSql) === TRUE) {
        echo '<script>alert("Staff added successfully!");</script>';
        echo '<script>window.location.href = "view_worker.php";</script>';
    } else {
        // Display the SQL error
        echo "Error: " . $insertResidentSql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Admin - Add Worker</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="../assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="../assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/fonts/typicons.min.css">
    <link rel="stylesheet" href="../assets/fonts/fontawesome5-overrides.min.css">
    <link rel="stylesheet" href="../assets/css/Lightbox-Gallery-baguetteBox.min.css">
</head>

<body id="page-top">
    <div id="wrapper">
        <nav class="navbar align-items-start sidebar sidebar-dark accordion bg-gradient-primary p-0 navbar-dark">
            <div class="container-fluid d-flex flex-column p-0"><a class="navbar-brand d-flex justify-content-center align-items-center sidebar-brand m-0" href="#">
                    <div class="sidebar-brand-icon rotate-n-15" style="transform: rotate(0deg);"><i class="typcn typcn-home" style="transform: rotate(5deg);"></i></div>
                    <div class="sidebar-brand-text mx-3"><span>KEMUNCAK<br>SHAH ALAM</span></div>
                </a>
                <hr class="sidebar-divider my-0">
                <ul class="navbar-nav text-light" id="accordionSidebar">
                    <li class="nav-item"><a class="nav-link" href="dashboard_admin.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                    <li class="nav-item dropdown show"><a class="dropdown-toggle nav-link active" aria-expanded="true" data-bs-toggle="dropdown" href="#" style="color: rgb(255,255,255);"><i class="fas fa-user"></i>Staff</a>
                        <div class="dropdown-menu" data-bs-popper="none"><a class="dropdown-item" href="view_worker.php"><span>View Staff</span></a><a class="dropdown-item" href="add_worker.php"><span>Add Staff</span></a></div>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="logout_admin.php"><i class="fa fa-power-off"></i><span>Logout</span></a></li>
                    <li class="nav-item"></li>
                    <li class="nav-item"></li>
                </ul>
            </div>
        </nav>
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
            <nav class="navbar navbar-expand bg-white shadow mb-4 topbar static-top navbar-light">
                    <div class="container-fluid"><button class="btn btn-link d-md-none rounded-circle me-3" id="sidebarToggleTop" type="button"><i class="fas fa-bars"></i></button>
                        <ul class="navbar-nav flex-nowrap ms-auto">
                            <li class="nav-item dropdown d-sm-none no-arrow"><a class="dropdown-toggle nav-link" aria-expanded="false" data-bs-toggle="dropdown" href="#"><i class="fas fa-search"></i></a>
                                <div class="dropdown-menu dropdown-menu-end p-3 animated--grow-in" aria-labelledby="searchDropdown">
                                    <form class="me-auto navbar-search w-100">
                                        <div class="input-group"><input class="bg-light form-control border-0 small" type="text" placeholder="Search for ...">
                                            <div class="input-group-append"><button class="btn btn-primary py-0" type="button"><i class="fas fa-search"></i></button></div>
                                        </div>
                                    </form>
                                </div>
                            </li>
                            
                            <li class="nav-item dropdown no-arrow">
                                <div class="nav-item dropdown no-arrow"><a class="dropdown-toggle nav-link" aria-expanded="false" data-bs-toggle="dropdown" href="#"><span class="d-none d-lg-inline me-2 text-gray-600 small"><?php echo $user['fullname']; ?></span><!--<img class="border rounded-circle img-profile" src="assets/img/avatars/avatar1.jpeg">--></a>
                                    <div class="dropdown-menu shadow dropdown-menu-end animated--grow-in"><!--<a class="dropdown-item" href="#"><i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Profile</a><a class="dropdown-item" href="#"><i class="fas fa-cogs fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Settings</a><a class="dropdown-item" href="#"><i class="fas fa-list fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Activity log</a>-->
                                        <!--<div class="dropdown-divider"></div>--><a class="dropdown-item" href="logout_admin.php"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Logout</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
                <div class="container">
                    <h3 class="text-dark mb-4">Add Staff</h3>
                    <div class="row">
                        <div class="col">
                            <div class="card shadow mb-3">
                                <div class="card-header py-3">
                                    <p class="text-primary m-0 fw-bold">Add Staff</p>
                                </div>
                                <div class="card-body">
                                    <form action="add_worker.php" method="post" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3"><label class="form-label" for="username"><strong>Username</strong></label><input class="form-control"id="username" name="username" placeholder="username" required></div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-3"><label class="form-label" for="password"><strong>Password</strong></label><input class="form-control" id="password" name="password" placeholder="Password" required></div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3"><label class="form-label" for="fullname"><strong>Full Name</strong></label><input class="form-control" type="text" id="fullname" name="fullname" placeholder="Fullname" required></div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-3"><label class="form-label" for="email"><strong>Email</strong></label><input class="form-control" type="email" id="email" name="email" placeholder="Email" required></div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3"><label class="form-label" for="age"><strong>Age</strong></label><input class="form-control" type="text" id="age" name="age" placeholder="Age" required></div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-3"><label class="form-label" for="gender"><strong>Gender</strong></label>
                                                    <select  class="form-select" id="gender" name="gender" required>
                                                        <option value="Male">Male</option>
                                                        <option value="Female">Female</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3"><label class="form-label" for="ic_number"><strong>IC&nbsp;Number</strong></label><input minlength="14" maxlength="14" class="form-control" type="text" id="ic_number" name="ic_number" placeholder="IC Number" required></div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-3"><label class="form-label" for="phone_number"><strong>Phone Number</strong></label><input class="form-control" type="text" id="phone_number" name="phone_number" placeholder="Phone Number" required></div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <!--<div class="col">
                                                <div class="mb-3"><label class="form-label" for="unit_id"><strong>Unit ID</strong></label>
                                                    <select class="form-select" id="unit_id" name="unit_id" required>
                                                        <?php echo $unitOptions; ?>
                                                    </select>
                                                </div>
                                            </div>-->
                                            <div class="col">
                                                <div class="mb-3"><label class="form-label" for="emergency_contact"><strong>Emergency Contact</strong></label><input class="form-control" type="text" id="emergency_contact" name="emergency_contact" placeholder="Emergency Contact" required></div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3"><label class="form-label" for="profile_pic"><strong>Profile Picture</strong></label>
                                                <input class="form-control" type="file" name="profile_pic" id="profile_pic" accept="image/*"></div>
                                            </div>
                                        </div> 
                                        <div class="mb-3">
                                            <input class="btn btn-primary btn-sm" type="submit" value="Add Staff">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <footer class="bg-white sticky-footer">
                <div class="container my-auto">
                    <div class="text-center my-auto copyright"><span>Copyright © Kemuncak Shah Alam 2024</span></div>
                </div>
            </footer>
        </div><a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
    </div>
    <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="../assets/js/bs-init.js"></script>
    <script src="../assets/js/Lightbox-Gallery-baguetteBox.min.js"></script>
    <script src="../assets/js/Lightbox-Gallery.js"></script>
    <script src="../assets/js/theme.js"></script>
</body>

</html>