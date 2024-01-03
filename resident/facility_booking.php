<?php
session_start();

// Initialize $page for pagination
$page = isset($_GET['page']) ? $_GET['page'] : 1;

// Check if the user is authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Establish a database connection
include '../include/connection.php';

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

// Fetch the list of available facilities
$facilitySql = "SELECT * FROM facility";
$facilityResult = $conn->query($facilitySql);

// Initialize $limit to avoid "Undefined variable" notice
$limit = 5;

// Fetch the booked time slots for the selected facility with pagination
if (isset($_GET['facility'])) {
    $selectedFacility = $_GET['facility'];
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Define $limit within the scope
    $bookedSlotsSql = "SELECT booking_id, booking_date, start_time, end_time 
                      FROM booking 
                      WHERE facility_id = $selectedFacility
                      LIMIT $limit OFFSET $offset";

    $bookedSlotsResult = $conn->query($bookedSlotsSql);

    // Get total number of booked slots for pagination
    $totalBookedSlotsSql = "SELECT COUNT(*) AS total FROM booking WHERE facility_id = $selectedFacility";
    $totalBookedSlotsResult = $conn->query($totalBookedSlotsSql);
    $totalBookedSlots = $totalBookedSlotsResult->fetch_assoc()['total'];
    $totalPagesBookedSlots = ($limit > 0) ? ceil($totalBookedSlots / $limit) : 1;
}

$userId = $_SESSION["user_id"];
$userBookedSlotsSql = "SELECT booking_id, f.facility_name, b.booking_date, b.start_time, b.end_time 
                      FROM booking b
                      JOIN facility f ON b.facility_id = f.facility_id
                      WHERE b.user_id = $userId";

$userBookedSlotsResult = $conn->query($userBookedSlotsSql);

// Get total number of user booked slots for pagination
$totalUserBookedSlotsSql = "SELECT COUNT(*) AS total FROM booking WHERE user_id = $userId";
$totalUserBookedSlotsResult = $conn->query($totalUserBookedSlotsSql);
$totalUserBookedSlots = $totalUserBookedSlotsResult->fetch_assoc()['total'];
$totalPagesUserBookedSlots = ($limit > 0) ? ceil($totalUserBookedSlots / $limit) : 1;

// Cancel booking
if (isset($_POST['cancel_booking'])) {
    $bookingId = $_POST['cancel_booking'];

    // Fetch booking details
    $bookingDetailsSql = "SELECT booking_date, start_time FROM booking WHERE booking_id = $bookingId";
    $bookingDetailsResult = $conn->query($bookingDetailsSql);

    if ($bookingDetailsResult->num_rows > 0) {
        $bookingDetails = $bookingDetailsResult->fetch_assoc();
        $bookingDate = $bookingDetails['booking_date'];
        $bookingStartTime = $bookingDetails['start_time'];

        // Check if the booking date and time haven't arrived yet
        $currentDateTime = date("Y-m-d H:i:s");
        $bookingDateTime = $bookingDate . ' ' . $bookingStartTime;

        if ($currentDateTime < $bookingDateTime) {
            // Perform the cancellation
            $cancelBookingSql = "DELETE FROM booking WHERE booking_id = $bookingId";
            if ($conn->query($cancelBookingSql) === TRUE) {
                echo '<script>alert("Booking canceled successfully!"); window.location.href = "facility_booking.php";</script>';
                exit();
            } else {
                echo '<script>alert("Error canceling booking."); window.location.href = "facility_booking.php";</script>';
                exit();
            }
        } else {
            echo '<script>alert("Cannot cancel booking, as the date and time have already passed."); window.location.href = "facility_booking.php";</script>';
            exit();
        }
    } else {
        echo '<script>alert("Booking details not found."); window.location.href = "facility_booking.php";</script>';
        exit();
    }
}
?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Facility Booking</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="../assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="../assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/fonts/typicons.min.css">
    <link rel="stylesheet" href="../assets/fonts/fontawesome5-overrides.min.css">
    <link rel="stylesheet" href="../assets/css/Lightbox-Gallery-baguetteBox.min.css">
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
                    <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="profile.php"><i class="fas fa-user"></i><span>Profile</span></a></li>
                    <li class="nav-item"><a class="nav-link active" href="facility_booking.php"><i class="fas fa-table"></i><span>Facility Booking</span></a></li>
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
                        <form class="d-none d-sm-inline-block me-auto ms-md-3 my-2 my-md-0 mw-100 navbar-search">
                            <div class="input-group"><input class="bg-light form-control border-0 small" type="text" placeholder="Search for ..."><button class="btn btn-primary py-0" type="button"><i class="fas fa-search"></i></button></div>
                        </form>
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
                            <li class="nav-item dropdown no-arrow mx-1">
                                <div class="nav-item dropdown no-arrow"><a class="dropdown-toggle nav-link" aria-expanded="false" data-bs-toggle="dropdown" href="#"><span class="badge bg-danger badge-counter">3+</span><i class="fas fa-bell fa-fw"></i></a>
                                    <div class="dropdown-menu dropdown-menu-end dropdown-list animated--grow-in">
                                        <h6 class="dropdown-header">alerts center</h6><a class="dropdown-item d-flex align-items-center" href="#">
                                            <div class="me-3">
                                                <div class="bg-primary icon-circle"><i class="fas fa-file-alt text-white"></i></div>
                                            </div>
                                            <div><span class="small text-gray-500">December 12, 2019</span>
                                                <p>A new monthly report is ready to download!</p>
                                            </div>
                                        </a><a class="dropdown-item d-flex align-items-center" href="#">
                                            <div class="me-3">
                                                <div class="bg-success icon-circle"><i class="fas fa-donate text-white"></i></div>
                                            </div>
                                            <div><span class="small text-gray-500">December 7, 2019</span>
                                                <p>$290.29 has been deposited into your account!</p>
                                            </div>
                                        </a><a class="dropdown-item d-flex align-items-center" href="#">
                                            <div class="me-3">
                                                <div class="bg-warning icon-circle"><i class="fas fa-exclamation-triangle text-white"></i></div>
                                            </div>
                                            <div><span class="small text-gray-500">December 2, 2019</span>
                                                <p>Spending Alert: We've noticed unusually high spending for your account.</p>
                                            </div>
                                        </a><a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
                                    </div>
                                </div>
                            </li>
                            <li class="nav-item dropdown no-arrow mx-1">
                                <div class="nav-item dropdown no-arrow"><a class="dropdown-toggle nav-link" aria-expanded="false" data-bs-toggle="dropdown" href="#"><span class="badge bg-danger badge-counter">7</span><i class="fas fa-envelope fa-fw"></i></a>
                                    <div class="dropdown-menu dropdown-menu-end dropdown-list animated--grow-in">
                                        <h6 class="dropdown-header">alerts center</h6><a class="dropdown-item d-flex align-items-center" href="#">
                                            <div class="dropdown-list-image me-3"><img class="rounded-circle" src="../assets/img/avatars/avatar4.jpeg">
                                                <div class="bg-success status-indicator"></div>
                                            </div>
                                            <div class="fw-bold">
                                                <div class="text-truncate"><span>Hi there! I am wondering if you can help me with a problem I've been having.</span></div>
                                                <p class="small text-gray-500 mb-0">Emily Fowler - 58m</p>
                                            </div>
                                        </a><a class="dropdown-item d-flex align-items-center" href="#">
                                            <div class="dropdown-list-image me-3"><img class="rounded-circle" src="../assets/img/avatars/avatar2.jpeg">
                                                <div class="status-indicator"></div>
                                            </div>
                                            <div class="fw-bold">
                                                <div class="text-truncate"><span>I have the photos that you ordered last month!</span></div>
                                                <p class="small text-gray-500 mb-0">Jae Chun - 1d</p>
                                            </div>
                                        </a><a class="dropdown-item d-flex align-items-center" href="#">
                                            <div class="dropdown-list-image me-3"><img class="rounded-circle" src="../assets/img/avatars/avatar3.jpeg">
                                                <div class="bg-warning status-indicator"></div>
                                            </div>
                                            <div class="fw-bold">
                                                <div class="text-truncate"><span>Last month's report looks great, I am very happy with the progress so far, keep up the good work!</span></div>
                                                <p class="small text-gray-500 mb-0">Morgan Alvarez - 2d</p>
                                            </div>
                                        </a><a class="dropdown-item d-flex align-items-center" href="#">
                                            <div class="dropdown-list-image me-3"><img class="rounded-circle" src="../assets/img/avatars/avatar5.jpeg">
                                                <div class="bg-success status-indicator"></div>
                                            </div>
                                            <div class="fw-bold">
                                                <div class="text-truncate"><span>Am I a good boy? The reason I ask is because someone told me that people say this to all dogs, even if they aren't good...</span></div>
                                                <p class="small text-gray-500 mb-0">Chicken the Dog · 2w</p>
                                            </div>
                                        </a><a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
                                    </div>
                                </div>
                                <div class="shadow dropdown-list dropdown-menu dropdown-menu-end" aria-labelledby="alertsDropdown"></div>
                            </li>
                            <div class="d-none d-sm-block topbar-divider"></div>
                            <li class="nav-item dropdown no-arrow">
                                <div class="nav-item dropdown no-arrow"><a class="dropdown-toggle nav-link" aria-expanded="false" data-bs-toggle="dropdown" href="#"><span class="d-none d-lg-inline me-2 text-gray-600 small"><?php echo $user['fullname']; ?></span>
                                    <?php if (!empty($user['profile_pic'])): ?>
                                        <img class="border rounded-circle img-profile" src="../profile_pics/<?php echo $user['profile_pic']; ?>" alt="Current Profile Picture" width="50">
                                    <?php else: ?>
                                        <img class="border rounded-circle img-profile" src="../profile_pics/no-user.png" alt="Current Profile Picture" width="50">
                                    <?php endif; ?>
                                
                                <!--<img class="border rounded-circle img-profile" src="assets/img/avatars/avatar1.jpeg">--></a>
                                    <div class="dropdown-menu shadow dropdown-menu-end animated--grow-in"><a class="dropdown-item" href="profile.php"><i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Profile</a><a class="dropdown-item" href="#">
                                        <div class="dropdown-divider"></div><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Logout</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
                <div class="container-fluid">
                    <h3 class="text-dark mb-4">Facility Booking</h3>

                                <!-- Facility selection form -->
                    <form action="facility_booking.php" method="get">
                        <div class="row mb-3">
                            <div class="col">
                                <label for="facility" class="form-label" style="color: rgb(0, 0, 0);">Select Facility:</label>
                                <select id="facility" name="facility" class="form-select" required>
                                    <?php
                                    // Store facility results in an array
                                        $facilityOptions = [];
                                        while ($row = $facilityResult->fetch_assoc()) {
                                            $facilityOptions[] = $row;
                                        }

                                    // Reset the internal pointer
                                    reset($facilityOptions);

                                    // Display facility options
                                    foreach ($facilityOptions as $option) {
                                        echo '<option value="' . $option['facility_id'] . '">' . $option['facility_name'] . '</option>';
                                    }
                                    ?>
                                    </select>
                                    </div>
                                    <div class="col">
                                <input type="submit" value="View Booked Slots" class="btn btn-primary" style="margin-top: 31px;">
                            </div>
                        </div>
                    </form>

                    <?php if (isset($bookedSlotsResult)): ?>
                        <!-- Display booked time slots for the selected facility -->
                        <div class="card shadow" style="margin-top: 20px;">
                            <div class="card-header py-3">
                                <p class="text-primary m-0 fw-bold">Booked Time Slots</p>
                            </div>
                            <div class="card-body">
                                <?php if ($bookedSlotsResult->num_rows > 0): ?>
                                    <div class="table-responsive table mt-2">
                                        <table class="table my-0" id="dataTable">
                                            <thead>
                                                <tr>
                                                    <th style="padding-right: 184px;">Date</th>
                                                    <th>Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($slot = $bookedSlotsResult->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?php echo $slot['booking_date']; ?></td>
                                                        <td><?php echo $slot['start_time'] . ' - ' . $slot['end_time']; ?></td>
                                                        <td>
                                                            <?php
                                                            $currentDateTime = date("Y-m-d H:i:s");
                                                            $bookingDateTime = $slot['booking_date'] . ' ' . $slot['start_time'];

                                                            if ($currentDateTime < $bookingDateTime): ?>
                                                                <form action="facility_booking.php" method="post">
                                                                    <input type="hidden" name="cancel_booking" value="<?php echo $slot['booking_id']; ?>">
                                                                </form>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr></tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p>No booked time slots for the selected facility.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <!-- Display pagination for Booked Time Slots -->
                        <div class="pagination">
                            <ul class="pagination">
                                    <?php if ($page > 1): ?>
                                        <a href="facility_booking.php?facility=<?php echo $selectedFacility; ?>&page=<?php echo $page - 1; ?>" class="btn btn-primary">Previous</a>
                                    <?php endif; ?>
                                    <?php for ($i = 1; $i <= $totalPagesBookedSlots; $i++) : ?>
                                        <a href="facility_booking.php?facility=<?php echo $selectedFacility; ?>&page=<?php echo $i; ?>"<?php echo ($i == $page) ? ' class="active"' : ''; ?>><?php echo $i; ?></a>
                                    <?php endfor; ?>
                                    <?php if ($page < $totalPagesBookedSlots): ?>
                                        <a href="facility_booking.php?facility=<?php echo $selectedFacility; ?>&page=<?php echo $page + 1; ?>" class="btn btn-primary">Next</a>
                                    <?php endif; ?>
                                </div>
                            </ul>
                        <?php endif; ?>

                    <!-- Display user's booked time slots -->
                    <?php if (isset($userBookedSlotsResult)): ?>
                        <div class="card shadow" style="margin-top: 20px;">
                            <div class="card-header py-3">
                                <p class="text-primary m-0 fw-bold">Your Booked Time Slots</p>
                            </div>
                            <div class="card-body">
                                <?php if ($userBookedSlotsResult->num_rows > 0): ?>
                                    <div class="table-responsive table mt-2">
                                        <table class="table my-0" id="dataTable-1">
                                            <thead>
                                                <tr>
                                                    <th style="padding-right: 184px;">Facility</th>
                                                    <th>Date</th>
                                                    <th>Time</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($slot = $userBookedSlotsResult->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?php echo $slot['facility_name']; ?></td>
                                                        <td><?php echo $slot['booking_date']; ?></td>
                                                        <td><?php echo $slot['start_time'] . ' - ' . $slot['end_time']; ?></td>
                                                        <td>
                                                            <?php
                                                            $currentDateTime = date("Y-m-d H:i:s");
                                                            $bookingDateTime = $slot['booking_date'] . ' ' . $slot['start_time'];

                                                            if ($currentDateTime < $bookingDateTime): ?>
                                                                <form action="facility_booking.php" method="post">
                                                                    <input type="hidden" name="cancel_booking" value="<?php echo $slot['booking_id']; ?>">
                                                                    <input type="submit" value="Cancel Booking" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this booking? (Yes/No)')">
                                                                </form>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr></tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p>You have not booked any time slots.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <!-- Display pagination for User's Booked Time Slots -->
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="facility_booking.php?page=<?php echo $page - 1; ?>" class="btn btn-primary">Previous</a>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $totalPagesUserBookedSlots; $i++) : ?>
                            <a href="facility_booking.php?page=<?php echo $i; ?>"<?php echo ($i == $page) ? ' class="active"' : ''; ?>><?php echo $i; ?></a>
                        <?php endfor; ?>
                        <?php if ($page < $totalPagesUserBookedSlots): ?>
                            <a href="facility_booking.php?page=<?php echo $page + 1; ?>" class="btn btn-primary">Next</a>
                        <?php endif; ?>
                    </div>
                    <!-- Add Booking Form -->
                    <div class="card shadow" style="margin-top: 20px;">
                        <div class="card-header py-3">
                            <p class="text-primary m-0 fw-bold">Add Booking</p>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <form action="book_facility.php" method="post">
                                        <div class="row">
                                            <div class="col">
                                                <label for="selectFacility" class="form-label" style="color: rgb(0, 0, 0);">Select Facility:</label>
                                                <select id="facility" name="facility" class="form-select" required>
                                                    <?php
                                                    foreach ($facilityOptions as $option) {
                                                        echo '<option value="' . $option['facility_id'] . '">' . $option['facility_name'] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col">
                                                <label for="bookingDate" class="form-label" style="color: rgb(0, 0, 0);">Booking Date:</label>
                                                <input type="date" id="booking_date" name="booking_date" class="form-control" required>
                                            </div>
                                            <div class="col">
                                                <label for="startTime" class="form-label" style="color: rgb(0, 0, 0);">Start Time:</label>
                                                <input type="time" id="start_time" name="start_time" class="form-control" required>
                                            </div>
                                            <div class="col">
                                                <label for="endTime" class="form-label" style="color: rgb(0, 0, 0);">End Time:</label>
                                                <input type="time" id="end_time" name="end_time" class="form-control" required>
                                            </div>
                                            <div class="col">
                                                <input style="margin-top:32px;" type="submit" value="Book Facility" class="btn btn-primary">
                                            </div>
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
        </div>

        <!-- Scroll to Top Button -->
        <a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="../assets/js/bs-init.js"></script>
    <script src="../assets/js/Lightbox-Gallery-baguetteBox.min.js"></script>
    <script src="../assets/js/Lightbox-Gallery.js"></script>
    <script src="../assets/js/theme.js"></script>
</body>

</html>