<?php
// Establish a database connection
include '../include/connection.php';
session_start();

// Check if the user is authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Fetch the current user's username
$userId = $_SESSION["user_id"];
$sql = "SELECT username, fullname, profile_pic FROM user WHERE user_id = $userId";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    $username = $user["username"];
} else {
    // Handle error (username not found)
    $username = "Unknown";
}

// Define the number of announcements per page
$announcementsPerPage = 2;

// Fetch total number of announcements
$totalAnnouncementsSql = "SELECT COUNT(*) AS total_announcements FROM announcement";
$totalAnnouncementsResult = $conn->query($totalAnnouncementsSql);
$totalAnnouncementsRow = $totalAnnouncementsResult->fetch_assoc();
$totalAnnouncements = $totalAnnouncementsRow['total_announcements'];

// Calculate the total number of pages
$totalPages = ceil($totalAnnouncements / $announcementsPerPage);

// Ensure the current page is within valid bounds
$current_page = isset($_GET['page']) ? max(1, min($_GET['page'], $totalPages)) : 1;

// Calculate the offset for the SQL query
$offset = ($current_page - 1) * $announcementsPerPage;

// Fetch announcements for the resident with pagination
$announcementsSql = "SELECT announcement_id, title, content, DATE_FORMAT(date, '%M %e, %Y') AS formatted_date, TIME_FORMAT(time, '%h:%i %p') AS formatted_time, media_url, username AS worker_name
                     FROM announcement
                     JOIN user ON announcement.worker_id = user.user_id
                     ORDER BY date DESC, time DESC
                     LIMIT $offset, $announcementsPerPage";
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
    <title>Resident - Homepage</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="../assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="../assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/fonts/typicons.min.css">
    <link rel="stylesheet" href="../assets/fonts/fontawesome5-overrides.min.css">
    <!-- Add this inside the <head> section of your HTML -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" />
    <style>
        /* Add your custom CSS styling here */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }
        
        .pagination a {
            padding: 8px 16px;
            text-decoration: none;
            color: black;
            background-color: #f1f1f1;
            border: 1px solid #ddd;
            margin: 0 4px;
            cursor: pointer;
        }

        .pagination a:hover {
            background-color: #ddd;
        }

        .pagination a.active {
            background-color: #4e73df;
            color: white;
        }
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
                    <li class="nav-item"><a class="nav-link active" href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Homepage</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="profile.php"><i class="fas fa-user"></i><span>Profile</span></a></li>
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
                                
                                <!--<img class="border rounded-circle img-profile" src="assets/img/avatars/avatar1.jpeg">--></a>
                                    <div class="dropdown-menu shadow dropdown-menu-end animated--grow-in"><a class="dropdown-item" href="profile.php"><i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Profile</a><a class="dropdown-item" href="#">
                                        <div class="dropdown-divider"></div><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Logout</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
                <div class="container-fluid">
                    <h3 class="text-dark mb-1">&nbsp&nbsp&nbsp&nbspHomepage</h3>
                    <div class="row">
                        <div class="col">
                            <h4 style="color: rgb(0,0,0);">&nbsp&nbsp&nbsp&nbsp&nbspLatest - Announcements</h4>
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
                        <nav>
                            <ul class="pagination">
                                <p style="margin-top:5px;">Showing <?php echo ($offset + 1) . " - " . min($offset + $announcementsPerPage, $totalAnnouncements); ?> out of <?php echo $totalAnnouncements; ?></p>
                                <a href="?page=1" <?php echo ($current_page == 1) ? 'style="background-color: #ddd;"' : ''; ?>>First</a>
                                <a href="?page=<?php echo max(1, $current_page - 1); ?>" <?php echo ($current_page == 1) ? 'style="background-color: #ddd;"' : ''; ?>><</a>
                                <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                                    <a href="?page=<?php echo $page; ?>" <?php echo ($current_page == $page) ? 'class="active"' : ''; ?>><?php echo $page; ?></a>
                                <?php endfor; ?>
                                <a href="?page=<?php echo min($totalPages, $current_page + 1); ?>" <?php echo ($current_page == $totalPages) ? 'style="background-color: #ddd;"' : ''; ?>>></a>
                                <a href="?page=<?php echo $totalPages; ?>" <?php echo ($current_page == $totalPages) ? 'style="background-color: #ddd;"' : ''; ?>>Last</a>
                                </ul>
                        </nav>
                    </div>
                    <?php else: ?>
                        <p>No announcements available.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p>Error: Resident not found.</p>
                <?php endif; ?>
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
