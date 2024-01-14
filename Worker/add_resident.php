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
    $role_id = 2; // Assuming role_id 2 corresponds to the "Resident" role
    $fullname = $_POST["fullname"];
    $email = $_POST["email"];
    $phone_number = $_POST["phone_number"];
    $ic_number = $_POST["ic_number"];
    $emergency_contact = $_POST["emergency_contact"];
    $unit_id = $_POST["unit_id"];
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
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["profile_pic"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            echo "The file ". basename( $_FILES["profile_pic"]["name"]). " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }

    // Insert resident into the database with the new user_id and profile picture file name
    $profilePicFileName = basename($_FILES["profile_pic"]["name"]);
    $insertResidentSql = "INSERT INTO user (user_id, username, password, role_id, fullname, email, phone_number, ic_number, emergency_contact, unit_id, profile_pic, gender, age)
                          VALUES ($newUserId, '$username', '$password', $role_id, '$fullname', '$email', '$phone_number', '$ic_number', '$emergency_contact', $unit_id, '$profilePicFileName', '$gender', '$age')";

    if ($conn->query($insertResidentSql) === TRUE) {
        echo '<script>alert("Resident added successfully!");</script>';
        echo '<script>window.location.href = "view_resident.php";</script>';
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

<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Profile - Brand</title>
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
                    <li class="nav-item"><a class="nav-link" href="dashboard_worker.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                    <li class="nav-item dropdown show"><a class="dropdown-toggle nav-link" aria-expanded="true" data-bs-toggle="dropdown" href="#" style="color: rgb(255,255,255);"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-tools">
                                <path d="M1 0 0 1l2.2 3.081a1 1 0 0 0 .815.419h.07a1 1 0 0 1 .708.293l2.675 2.675-2.617 2.654A3.003 3.003 0 0 0 0 13a3 3 0 1 0 5.878-.851l2.654-2.617.968.968-.305.914a1 1 0 0 0 .242 1.023l3.27 3.27a.997.997 0 0 0 1.414 0l1.586-1.586a.997.997 0 0 0 0-1.414l-3.27-3.27a1 1 0 0 0-1.023-.242L10.5 9.5l-.96-.96 2.68-2.643A3.005 3.005 0 0 0 16 3c0-.269-.035-.53-.102-.777l-2.14 2.141L12 4l-.364-1.757L13.777.102a3 3 0 0 0-3.675 3.68L7.462 6.46 4.793 3.793a1 1 0 0 1-.293-.707v-.071a1 1 0 0 0-.419-.814zm9.646 10.646a.5.5 0 0 1 .708 0l2.914 2.915a.5.5 0 0 1-.707.707l-2.915-2.914a.5.5 0 0 1 0-.708M3 11l.471.242.529.026.287.445.445.287.026.529L5 13l-.242.471-.026.529-.445.287-.287.445-.529.026L3 15l-.471-.242L2 14.732l-.287-.445L1.268 14l-.026-.529L1 13l.242-.471.026-.529.445-.287.287-.445.529-.026z"></path>
                            </svg>&nbsp;Maintenance Requests</a>
                        <div class="dropdown-menu" data-bs-popper="none"><a class="dropdown-item" href="assign_maintenance.php"><span>Assign Maintenance Requests</span></a><a class="dropdown-item" href="view_maintenance_requests.php"><span>View Maintenance Requests</span></a><a class="dropdown-item" href="maintenance_requests_worker.php"><span>Update Maintenance Requests</span></a></div>
                    </li>
                    <li class="nav-item dropdown show"><a class="dropdown-toggle nav-link" aria-expanded="true" data-bs-toggle="dropdown" href="#" style="color: rgb(255,255,255);"><i class="fas fa-window-maximize"></i>Announcements</a>
                        <div class="dropdown-menu" data-bs-popper="none"><a class="dropdown-item" href="announcements.php"><span>View Announcements</span></a><a class="dropdown-item" href="add_announcement.php"><span>Add Announcements</span></a></div>
                    </li>
                    <li class="nav-item dropdown show"><a class="dropdown-toggle nav-link active" aria-expanded="true" data-bs-toggle="dropdown" href="#" style="color: rgb(255,255,255);"><i class="fas fa-user"></i>Resident</a>
                        <div class="dropdown-menu" data-bs-popper="none"><a class="dropdown-item" href="view_resident.php"><span>View Resident</span></a><a class="dropdown-item" href="add_resident.php"><span>Add Resident</span></a></div>
                    </li>
                    <li class="nav-item dropdown show"><a class="dropdown-toggle nav-link" aria-expanded="true" data-bs-toggle="dropdown" href="#" style="color: rgb(255,255,255);"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-building-fill">
                                <path d="M3 0a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h3v-3.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5V16h3a1 1 0 0 0 1-1V1a1 1 0 0 0-1-1zm1 2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5M4 5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM7.5 5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5m2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM4.5 8h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5m2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5"></path>
                            </svg>&nbsp;Facility</a>
                        <div class="dropdown-menu" data-bs-popper="none"><a class="dropdown-item" href="view_facility.php"><span>View Facility</span></a><a class="dropdown-item" href="add_facility.php"><span>Add Facility</span></a></div>
                    </li>
                    <li class="nav-item dropdown show"><a class="dropdown-toggle nav-link" aria-expanded="true" data-bs-toggle="dropdown" href="#" style="color: rgb(255,255,255);"><i class="fa fa-home"></i>Unit</a>
                        <div class="dropdown-menu" data-bs-popper="none"><a class="dropdown-item" href="view_unit.php"><span>View Unit</span></a><a class="dropdown-item" href="add_unit.php"><span>Add Unit</span></a></div>
                    </li>
                    <li class="nav-item dropdown show"><a class="dropdown-toggle nav-link" aria-expanded="true" data-bs-toggle="dropdown" href="#" style="color: rgb(255,255,255);"><i class="fas fa-table"></i>Booking Facility</a>
                        <div class="dropdown-menu" data-bs-popper="none"><a class="dropdown-item" href="view_booking.php"><span>View Booking Facility</span></a><a class="dropdown-item" href="add_booking_facility.php"><span>Add Booking Facility</span></a></div>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="logout_worker.php"><i class="fa fa-power-off"></i><span>Logout</span></a></li>
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
                                        <!--<div class="dropdown-divider"></div>--><a class="dropdown-item" href="logout_worker.php"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Logout</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
                <div class="container">
                    <h3 class="text-dark mb-4">Add Resident</h3>
                    <div class="row">
                        <div class="col">
                            <div class="card shadow mb-3">
                                <div class="card-header py-3">
                                    <p class="text-primary m-0 fw-bold">Add Resident</p>
                                </div>
                                <div class="card-body">
                                    <form action="add_resident.php" method="post" enctype="multipart/form-data">
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
                                            <div class="col">
                                                <div class="mb-3"><label class="form-label" for="unit_id"><strong>Unit ID</strong></label>
                                                    <select class="form-select" id="unit_id" name="unit_id" required>
                                                        <?php echo $unitOptions; ?>
                                                    </select>
                                                </div>
                                            </div>
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
                                            <input class="btn btn-primary btn-sm" type="submit" value="Add Resident">
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