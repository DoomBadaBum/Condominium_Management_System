<?php
include '../include/connection.php';
session_start();

// Check if the user is authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login_worker.php");
    exit();
}

// Get user id worker
$userWorkId = $_SESSION["user_id"];
$sql = "SELECT * FROM user WHERE user_id = $userWorkId";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $userWork = $result->fetch_assoc();
} else {
    $userWork = null;
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
    $username = $_POST["username"];
    $fullname = $_POST["fullname"];
    $ic_number = $_POST["ic_number"];
    $email = $_POST["email"];
    $phone_number = $_POST["phone_number"];
    $emergency_contact = $_POST["emergency_contact"];
    $unit_id = $_POST["unit_id"];
    $age = $_POST["age"];
    $gender = $_POST["gender"];

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
                          SET username ='$username',
                              fullname = '$fullname',
                              email = '$email',
                              phone_number = '$phone_number',
                              ic_number = '$ic_number',
                              emergency_contact = '$emergency_contact',
                              unit_id = $unit_id,
                              profile_pic = '$profilePicFileName',
                              age = '$age',
                              gender = '$gender'
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

<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Staff - Update Resident Details</title>
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
                            <li class="nav-item dropdown no-arrow">
                                <div class="nav-item dropdown no-arrow"><a class="dropdown-toggle nav-link" aria-expanded="false" data-bs-toggle="dropdown" href="#"><span class="d-none d-lg-inline me-2 text-gray-600 small"><?php echo $userWork['fullname']; ?></span>
                                <!--<img class="border rounded-circle img-profile" src="assets/img/avatars/avatar1.jpeg">--></a>
                                    <div class="dropdown-menu shadow dropdown-menu-end animated--grow-in">
                                        <a class="dropdown-item" href="logout_worker.php"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Logout</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
                <div class="container-fluid">
                    <h3 class="text-dark mb-4">Update Resident Details</h3>
                    <div class="row" style="margin-right: 327px;margin-left: 352px;">
                        <div class="col">
                            <div class="card mb-3">
                                <div class="card-body text-center shadow">
                                    <?php if (!empty($resident['profile_pic'])): ?>
                                        <a data-fancybox="profile-picture" href="../profile_pics/<?php echo $resident['profile_pic']; ?>">
                                            <img class="rounded-circle mb-3 mt-4" src="../profile_pics/<?php echo $resident['profile_pic']; ?>" alt="Current Profile Picture" width="140" height="140">
                                        </a>
                                    <?php else: ?>
                                        <a data-fancybox="profile-picture" href="../profile_pics/no-user.png">
                                            <img class="rounded-circle mb-3 mt-4" src="../profile_pics/no-user.png" alt="Current Profile Picture" width="50">
                                        </a>
                                    <?php endif; ?>                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="card shadow mb-3">
                            <div class="card-header py-3">
                                <p class="text-primary m-0 fw-bold">Update Resident Details</p>
                            </div>
                            <div class="card-body">
                                <form action="update_resident.php?user_id=<?php echo $userId; ?>" method="post" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col">
                                            <div class="mb-3"><label class="form-label" for="username"><strong>Username</strong></label><input class="form-control" id="username" name="username" value="<?php echo $resident['username']; ?>" required></div>
                                        </div>
                                        <div class="col">
                                            <div class="mb-3"><label class="form-label" for="email"><strong>Email Address</strong></label><input class="form-control" type="email" id="email" name="email" value="<?php echo $resident['email']; ?>" required></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="mb-3"><label class="form-label" for="fullname"><strong>Full Name</strong></label><input class="form-control" type="text" id="fullname" name="fullname" value="<?php echo $resident['fullname']; ?>" required></div>
                                        </div>
                                        <div class="col">
                                            <div class="mb-3"><label class="form-label" for="age"><strong>Age</strong></label><input class="form-control" type="text" id="age" name="age" value="<?php echo $resident['age']; ?>" required></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                        <label class="form-label" for="gender"><strong>Gender</strong></label>
                                            <select class="form-select" id="gender" name="gender" required>
                                                <option value="Male" <?php echo ($resident['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                                                <option value="Female" <?php echo ($resident['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <div class="mb-3"><label class="form-label" for="phone_number"><strong>Phone Number</strong></label><input class="form-control" type="text" id="phone_number" name="phone_number" value="<?php echo $resident['phone_number']; ?>" required></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="mb-3"><label class="form-label" for="ic_number"><strong>IC&nbsp;Number</strong></label><input class="form-control" type="text" minlength="14" maxlength="14" id="ic_number" name="ic_number" value="<?php echo $resident['ic_number']; ?>" required></div>
                                        </div>
                                        <div class="col">
                                            <div class="mb-3"><label class="form-label" for="emergency_contact"><strong>Emergency Contact</strong></label><input class="form-control" type="text" id="emergency_contact" name="emergency_contact" value="<?php echo $resident['emergency_contact']; ?>" required></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="mb-3"><label class="form-label" for="unit_id"><strong>Unit Number</strong></label>
                                                <select class="form-select" id="unit_id" name="unit_id" required>
                                                    <?php echo $unitOptions; ?>
                                                </select></div>
                                        </div>
                                        <div class="col">
                                            <div class="mb-3"><label class="form-label" for="last_name"><strong></strong>&nbsp;</label><input class="form-control" type="hidden" id="last_name-1" placeholder="Doe" name="last_name"></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="mb-3"><label class="form-label" for="profile_pic"><strong>Upload new profile picture</strong></label>
                                                <input class="form-control" type="file" id="profile_pic" name="profile_pic">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <a class="btn btn-primary btn-sm" href="view_resident.php">Back</a>
                                        <input class="btn btn-warning btn-sm" type="submit" value="Update Resident">
                                    </div>
                                </form>
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