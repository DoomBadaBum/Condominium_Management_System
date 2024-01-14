<?php
include '../include/connection.php';
session_start();

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

<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Profile</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="../assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="../assets/fonts/typicons.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" />

</head>

<body id="page-top">
    <?php if ($user): ?>
    <div id="wrapper">
        <nav class="navbar align-items-start sidebar sidebar-dark accordion bg-gradient-primary p-0 navbar-dark">
            <div class="container-fluid d-flex flex-column p-0"><a class="navbar-brand d-flex justify-content-center align-items-center sidebar-brand m-0" href="#">
                    <div class="sidebar-brand-icon rotate-n-15" style="transform: rotate(0deg);"><i class="typcn typcn-home" style="transform: rotate(5deg);"></i></div>
                    <div class="sidebar-brand-text mx-3"><span>KEMUNCAK<br>SHAH ALAM</span></div>
                </a>
                <hr class="sidebar-divider my-0">
                <ul class="navbar-nav text-light" id="accordionSidebar">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Homepage</span></a></li>
                    <li class="nav-item"><a class="nav-link active" href="profile.php"><i class="fas fa-user"></i><span>Profile</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="facility_booking.php"><i class="fas fa-table"></i><span>Facility Booking</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="maintenance_requests.php"><svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="1em" viewBox="0 0 24 24" width="1em" fill="currentColor">
                                <g>
                                    <rect fill="none" height="24" width="24"></rect>
                                </g>
                                <g>
                                    <g>
                                        <polygon points="18,16 16,16 16,15 8,15 8,16 6,16 6,15 2,15 2,20 22,20 22,15 18,15"></polygon>
                                        <path d="M20,8h-3V6c0-1.1-0.9-2-2-2H9C7.9,4,7,4.9,7,6v2H4c-1.1,0-2,0.9-2,2v4h4v-2h2v2h8v-2h2v2h4v-4C22,8.9,21.1,8,20,8z M15,8 H9V6h6V8z"></path>
                                    </g>
                                </g>
                            </svg><span> Maintenance Request</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fa fa-power-off"></i><span>Logout</span></a></li>
                    <li class="nav-item"></li>
                    <li class="nav-item"></li>
                </ul>
                <div class="text-center d-none d-md-inline"><button class="btn rounded-circle border-0" id="sidebarToggle" type="button"></button></div>
            </div>
        </nav>
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <nav class="navbar navbar-expand bg-white shadow mb-4 topbar static-top navbar-light">
                    <div class="container-fluid"><button class="btn btn-link d-md-none rounded-circle me-3" id="sidebarToggleTop" type="button"><i class="fas fa-bars"></i></button>
                        <ul class="navbar-nav flex-nowrap ms-auto">    
                            <li class="nav-item dropdown no-arrow">
                                <div class="nav-item dropdown no-arrow"><a class="dropdown-toggle nav-link" aria-expanded="false" data-bs-toggle="dropdown" href="#"><span class="d-none d-lg-inline me-2 text-gray-600 small"><?php echo $user['fullname']; ?></span>
                                
                                <?php if (!empty($user['profile_pic'])): ?>
                                    <img class="border rounded-circle img-profile" src="../profile_pics/<?php echo $user['profile_pic']; ?>" alt="Current Profile Picture" width="50">
                                <?php else: ?>
                                    <img class="border rounded-circle img-profile" src="../profile_pics/no-user.png" alt="Current Profile Picture" width="50">
                                <?php endif; ?>
                                <!--<img class="border rounded-circle img-profile" src="../assets/img/avatars/avatar1.jpeg"> --></a>
                                    <div class="dropdown-menu shadow dropdown-menu-end animated--grow-in"><a class="dropdown-item" href="profile.php"><i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Profile</a>
                                        <div class="dropdown-divider"></div><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Logout</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
                <div class="container-fluid">
                    <h3 class="text-dark mb-4">View Resident Details</h3>
                    <div class="row" style="margin-right: 327px;margin-left: 352px;">
                        <div class="col">
                            <div class="card mb-3">
                                <div class="card-body text-center shadow">
                                    <?php if (!empty($user['profile_pic'])): ?>
                                        <a data-fancybox="profile-picture" href="../profile_pics/<?php echo $user['profile_pic']; ?>">
                                            <img class="rounded-circle mb-3 mt-4" src="../profile_pics/<?php echo $user['profile_pic']; ?>" alt="Current Profile Picture" width="140" height="140">
                                        </a>
                                    <?php else: ?>
                                        <a data-fancybox="profile-picture" href="../profile_pics/no-user.png">
                                            <img class="rounded-circle mb-3 mt-4" src="../profile_pics/no-user.png" alt="Current Profile Picture" width="50">
                                        </a>
                                    <?php endif; ?>
                                    <!--<img class="rounded-circle mb-3 mt-4" src="../assets/img/dogs/image2.jpeg" width="160" height="160">-->
                                </div>
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
                                <p class="text-primary m-0 fw-bold">Resident Details</p>
                            </div>
                            <div class="card-body">
                                <form action="update_profile.php" method="post" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col">
                                            <div class="mb-3"><label class="form-label" for="username"><strong>Username</strong></label><input class="form-control" type="text" id="username" placeholder="user.name" name="username" value="<?php echo $user['username']; ?>" readonly></div>
                                        </div>
                                        <div class="col">
                                            <div class="mb-3"><label class="form-label" for="email"><strong>Email Address</strong></label><input class="form-control" type="email" id="email" placeholder="user@example.com" name="email" value="<?php echo $user['email']; ?>"></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="mb-3"><label class="form-label" for="first_name"><strong>Full Name</strong></label><input class="form-control" type="text" id="fullname" placeholder="John" name="fullname" value="<?php echo $user['fullname']; ?>" readonly></div>
                                        </div>
                                        <div class="col">
                                            <div class="mb-3"><label class="form-label" for="age"><strong>Age</strong></label><input class="form-control" type="text" id="age" placeholder="John" name="age" value="<?php echo $user['age']; ?>" readonly></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="mb-3"><label class="form-label" for="gender"><strong>Gender</strong></label><input class="form-control" type="text" id="gender" placeholder="Doe" name="gender" value="<?php echo $user['gender']; ?>" readonly></div>
                                        </div>
                                        <div class="col">
                                            <div class="mb-3"><label class="form-label" for="last_name"><strong>Phone Number</strong></label><input class="form-control" type="text" id="phone" placeholder="Doe" name="phone" value="<?php echo $user['phone_number']; ?>"></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="mb-3"><label class="form-label" for="first_name"><strong>IC Number</strong></label><input class="form-control" type="text" id="ic_number" placeholder="John" name="ic_number" value="<?php echo $user['ic_number']; ?>" readonly></div>
                                        </div>
                                        <div class="col">
                                            <div class="mb-3"><label class="form-label" for="last_name"><strong>Emergency Contact</strong></label><input class="form-control" type="text" id="emergency_contact-2" placeholder="Doe" name="emergency_contact" value="<?php echo $user['emergency_contact']; ?>"></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="mb-3"><label class="form-label" for="first_name"><strong>Unit Number</strong></label><input class="form-control" type="text" id="unit_number" placeholder="John" name="unit_number" value="<?php echo $user['unit_number']; ?>" readonly></div>
                                        </div>
                                        <div class="col">
                                            <div class="mb-3"><label class="form-label" for="last_name"><strong>Block Number</strong></label><input class="form-control" type="text" id="block_number" placeholder="Doe" name="block_number" value="<?php echo $user['block_number']; ?>" readonly></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="mb-3"><label class="form-label" for="first_name"><strong>Floor</strong></label><input class="form-control" type="text" id="floor" placeholder="John" name="floor" value="<?php echo $user['floor']; ?>" readonly></div>
                                        </div>
                                        <div class="col">
                                            <div class="mb-3"><label class="form-label" for="last_name"><strong>Unit Size m<sup>2</sup></strong></label><input class="form-control" type="text" id="size" placeholder="Doe" name="size" value="<?php echo $user['size']; ?>" readonly></div>
                                        </div>
                                    </div>
                                    <div class="mb-3"><button class="btn btn-primary btn-sm" type="submit">Save Detail</button></div>
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
    <?php else: ?>
        <p>Error: Resident not found.</p>
    <?php endif; ?>

    <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="../assets/js/bs-init.js"></script>
    <script src="../assets/js/theme.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>

    <script>
        // Add this JavaScript code
        $(document).ready(function () {
            // Initialize Fancybox
            $('[data-fancybox="gallery"]').fancybox({
                buttons: [
                    "zoom",
                    "fullScreen",
                    "close"
                ],
                loop: true
            });
        });
    </script>
</body>
</html>