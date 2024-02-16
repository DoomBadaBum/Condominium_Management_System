<?php
include '../include/connection.php';

session_start();

// Check if the user is authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login_admin.php");
    exit();
}
// Fetch user details
$sqlUser = "SELECT * FROM user WHERE user_id = " . $_SESSION["user_id"];
$resultUser = $conn->query($sqlUser);

if ($resultUser === false) {
    die("Error executing user query: " . $conn->error);
}

$user = $resultUser->fetch_assoc();
// Fetch residents
$sqlResidents = "SELECT u.user_id, u.username, u.fullname, u.email, u.phone_number, u.ic_number, u.emergency_contact, u.unit_id, ur.unit_number, ur.block_number, ur.floor, ur.size, u.profile_pic, u.age, u.gender
                 FROM user u
                 LEFT JOIN unit ur ON u.unit_id = ur.unit_id
                 WHERE u.role_id = 3"; // Assuming role_id 3 corresponds to the "Resident" role
$resultResidents = $conn->query($sqlResidents);

if ($resultResidents === false) {
    die("Error executing residents query: " . $conn->error);
}

// Pagination
$limit = 5; // Number of records per page
$totalRecords = $resultResidents->num_rows;
$totalPages = ceil($totalRecords / $limit);

if (!isset($_GET['page'])) {
    $page = 1;
} else {
    $page = $_GET['page'];
}

$offset = ($page - 1) * $limit;

$sqlResidents .= " LIMIT $offset, $limit";
$resultResidents = $conn->query($sqlResidents);

$conn->close();
?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Admin - View Worker</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="../assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="../assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/fonts/typicons.min.css">
    <link rel="stylesheet" href="../assets/fonts/fontawesome5-overrides.min.css">
    <link rel="stylesheet" href="../assets/css/Lightbox-Gallery-baguetteBox.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" />
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
                            <li class="nav-item dropdown no-arrow">
                                <div class="nav-item dropdown no-arrow"><a class="dropdown-toggle nav-link" aria-expanded="false" data-bs-toggle="dropdown" href="#"><span class="d-none d-lg-inline me-2 text-gray-600 small"><?php echo $user['fullname']; ?></span>
                                <!--<img class="border rounded-circle img-profile" src="assets/img/avatars/avatar1.jpeg">--></a>
                                    <div class="dropdown-menu shadow dropdown-menu-end animated--grow-in">
                                        <a class="dropdown-item" href="logout_admin.php"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Logout</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
                <div class="container-fluid">
                    <h3 class="text-dark mb-4">View Staff</h3>
                    <div class="card shadow" style="margin-top: 20px;">
                        <div class="card-header py-3">
                            <p class="text-primary m-0 fw-bold">View Staff</p>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive table mt-2" id="dataTable" role="grid" aria-describedby="dataTable_info">
                                <?php if ($resultResidents->num_rows > 0): ?>
                                <table class="table my-0" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th style="padding-right: 184px;width: 151.922px;">Picture</th>
                                            <th style="width: 167.703px;">Username</th>
                                            <th style="width: 263.188px;">Fullname</th>
                                            <th style="width: 125px;">Age</th>
                                            <th style="width: 157.844px;">Gender</th>
                                            <th style="width: 129.156px;">Phone No.</th>
                                            <th style="width: 129.156px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php while ($row = $resultResidents->fetch_assoc()): ?>
                                        <tr>
                                            <td style="height: 42px;">
                                                <?php if (!empty($row['profile_pic'])): ?>
                                                    <a data-fancybox="gallery" href="../profile_pics/<?php echo $row['profile_pic']; ?>">
                                                        <img src="../profile_pics/<?php echo $row['profile_pic']; ?>" alt="Profile Picture" width="50">
                                                    </a>
                                                <?php else: ?>
                                                    No Picture
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $row['username']; ?></td>
                                            <td><?php echo $row['fullname']; ?></td>
                                            <td><?php echo $row['age']; ?></td>
                                            <td><?php echo $row['gender']; ?></td>
                                            <td><?php echo $row['phone_number']; ?></td>
                                            <td>
                                                <a class="btn btn-primary" style="min-width: 79px; margin-top: 7px;" href="view_worker_details.php?user_id=<?php echo $row['user_id']; ?>">View</a>
                                                <a class="btn btn-warning" style="margin-top: 7px;" href="update_worker.php?user_id=<?php echo $row['user_id']; ?>">Update</a>
                                                <a class="btn btn-danger" style="min-width: 79px;margin-top: 7px;" href="delete_worker.php?user_id=<?php echo $row['user_id']; ?>" onclick="return confirm('Are you sure you want to delete this staff?')">Delete</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr></tr>
                                    </tfoot>
                                    <?php endwhile; ?>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-md-6 align-self-center">
                                    <p id="dataTable_info" class="dataTables_info" role="status" aria-live="polite">
                                        <?php
                                        $startRecord = $offset + 1;
                                        $endRecord = min(($offset + $limit), $totalRecords);
                                        echo "Showing $startRecord to $endRecord of $totalRecords";
                                        ?>
                                    </p>
                                </div>
                                <?php if ($totalPages > 1): ?>
                                <div class="col-md-6">
                                    <nav class="d-lg-flex justify-content-lg-end dataTables_paginate paging_simple_numbers">
                                        <ul class="pagination">
                                            <?php if ($page > 1) : ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=<?php echo ($page - 1); ?>" aria-label="Previous">
                                                        <span aria-hidden="true">&laquo; Previous</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                                                <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                                </li>
                                            <?php endfor; ?>
                                            <?php if ($page < $totalPages) : ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=<?php echo ($page + 1); ?>" aria-label="Next">
                                                        <span aria-hidden="true">Next &raquo;</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                    <p>No staffs available.</p>
                <?php endif; ?>
            </div>
        </div><a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>

        <footer class="bg-white sticky-footer">
                <div class="container my-auto">
                    <div class="text-center my-auto copyright"><span>Copyright © Kemuncak Shah Alam 2024</span></div>
                </div>
        </footer>
    </div>
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