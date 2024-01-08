<!-- maintenance_announcements_worker.php -->
<?php
include '../include/connection.php';
session_start();

// Check if the user is authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login_worker.php");
    exit();
}

$userId = $_SESSION["user_id"];
$sql = "SELECT * FROM user WHERE user_id = $userId";
$result = $conn->query($sql);

if ($result === false) {
    die("Error executing user query: " . $conn->error);
}

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
} else {
    $user = null;
}

// Fetch announcements for the worker
$announcementsSql = "SELECT announcement_id, title, content, DATE_FORMAT(date, '%M %e, %Y') AS formatted_date, TIME_FORMAT(time, '%h:%i %p') AS formatted_time, media_url, username AS worker_name
                     FROM announcement
                     JOIN user ON announcement.worker_id = user.user_id
                     ORDER BY date DESC, time DESC";
$announcementsResult = $conn->query($announcementsSql);

if ($announcementsResult === false) {
    die("Error executing announcements query: " . $conn->error);
}

$conn->close();
?>


<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Blank Page - Brand</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="../assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="../assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/fonts/typicons.min.css">
    <link rel="stylesheet" href="../assets/fonts/fontawesome5-overrides.min.css">
    <link rel="stylesheet" href="../assets/css/Lightbox-Gallery-baguetteBox.min.css">
    <!-- Add this inside the <head> section of your HTML -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" />
    <style>
        .image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
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
                    <li class="nav-item dropdown show"><a class="dropdown-toggle nav-link active" aria-expanded="true" data-bs-toggle="dropdown" href="#" style="color: rgb(255,255,255);"><i class="fas fa-window-maximize"></i>Announcements</a>
                        <div class="dropdown-menu" data-bs-popper="none"><a class="dropdown-item" href="announcements.php"><span>View Announcements</span></a><a class="dropdown-item" href="add_announcement.php"><span>Add Announcements</span></a></div>
                    </li>
                    <li class="nav-item dropdown show"><a class="dropdown-toggle nav-link" aria-expanded="true" data-bs-toggle="dropdown" href="#" style="color: rgb(255,255,255);"><i class="fas fa-user"></i>Resident</a>
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
                    <li class="nav-item"><a class="nav-link" href="view_booking.php"><i class="fas fa-table"></i><span>Facility Booking</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="register.html"><i class="fa fa-power-off"></i><span>Logout</span></a></li>
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
                                <div class="nav-item dropdown no-arrow"><a class="dropdown-toggle nav-link" aria-expanded="false" data-bs-toggle="dropdown" href="#"><span class="d-none d-lg-inline me-2 text-gray-600 small"><?php echo $user['fullname']; ?></span>
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
                    <h3 class="text-dark mb-1">&nbsp&nbsp&nbsp&nbspView Announcements</h3>
                    <div class="row">
                        <div class="col">
                            <!--<h3 style="color: rgb(0,0,0);">Latest - Announcements</h4>-->
                        </div>
                    </div>

                    <?php if ($user): ?>
                    <!-- Display announcements -->
                    <?php if ($announcementsResult->num_rows > 0): ?>
                        <ul>
                            <?php while ($row = $announcementsResult->fetch_assoc()): ?>
                                <div class="card">
                                    <div class="card-body">          
                                        <div class="col image-container" style="text-align: center;">
                                            <?php if (!empty($row['media_url'])): ?>
                                                <?php $mediaExtension = pathinfo($row['media_url'], PATHINFO_EXTENSION); ?>
                                                <?php if (in_array($mediaExtension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                                    <a data-fancybox="gallery" href="../<?php echo $row['media_url']; ?>">
                                                        <img style="width: 851px; height: 315px;" src="../<?php echo $row['media_url']; ?>" alt="Image">
                                                    </a>
                                                <?php elseif (in_array($mediaExtension, ['mp4', 'webm', 'ogg'])): ?>
                                                    <a data-fancybox="gallery" href="../<?php echo $row['media_url']; ?>">
                                                        <video width="851" height="315" controls>
                                                            <source src="../<?php echo $row['media_url']; ?>" type="video/<?php echo $mediaExtension; ?>">
                                                            Your browser does not support the video tag.
                                                        </video>
                                                    </a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                        <h4 style="color : black; margin-top: 30px; margin-left: 30px;" class="card-title" style="padding-top: 29px;"><strong><?php echo $row['title']; ?></strong></h4>
                                        <h6 style="color : black; margin-left: 30px;" class="card-subtitle mb-2" >Date: <?php echo $row['formatted_date']; ?> at <?php echo $row['formatted_time']; ?></h6>
                                        <p class="card-text"><p style="color : black; margin-left: 30px;" ><?php echo $row['content']; ?></p></p>
                                        <!-- Add a "Delete" link with confirmation -->
                                        <a class="btn btn-warning" style="margin-left: 30px;" href="edit_announcement.php?announcement_id=<?php echo $row['announcement_id']; ?>">Update</a>
                                        <a class="btn btn-danger" href="delete_announcement.php?announcement_id=<?php echo $row['announcement_id']; ?>" onclick="return confirm('Are you sure you want to delete this announcement? (Yes/No)')">Delete</a>
                                    </div>
                                </div>
                                <br>
                            <?php endwhile; ?>
                        </ul>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col" style="margin-top: 33px;">
                        <h6>Heading</h6>
                        <nav>
                            <ul class="pagination">
                                <li class="page-item"><a class="page-link" aria-label="Previous" href="#"><span aria-hidden="true">«</span></a></li>
                                <li class="page-item"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item"><a class="page-link" href="#">4</a></li>
                                <li class="page-item"><a class="page-link" href="#">5</a></li>
                                <li class="page-item"><a class="page-link" aria-label="Next" href="#"><span aria-hidden="true">»</span></a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <?php else: ?>
                        <p>No announcements available.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p>Error: Resident not found.</p>
                <?php endif; ?>
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