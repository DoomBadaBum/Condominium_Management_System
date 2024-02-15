<?php
session_start();

// Set the timezone to Kuala Lumpur
date_default_timezone_set('Asia/Kuala_Lumpur');

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
    $bookedSlotsSql = "SELECT b.booking_id, b.booking_date, b.start_time, b.end_time, f.facility_name
                        FROM booking b
                        JOIN facility f ON b.facility_id = f.facility_id
                        WHERE b.facility_id = $selectedFacility 
                        ORDER BY b.booking_date DESC, b.start_time DESC
                        LIMIT $limit OFFSET $offset";

    $bookedSlotsResult = $conn->query($bookedSlotsSql);

    // Get total number of booked slots for pagination
    $totalBookedSlotsSql = "SELECT COUNT(*) AS total FROM booking WHERE facility_id = $selectedFacility";
    $totalBookedSlotsResult = $conn->query($totalBookedSlotsSql);
    $totalBookedSlots = $totalBookedSlotsResult->fetch_assoc()['total'];
    $totalPagesBookedSlots = ($limit > 0) ? ceil($totalBookedSlots / $limit) : 1;
}

$offset = ($page - 1) * $limit;

$userId = $_SESSION["user_id"];
// Fetch the user's booked time slots with pagination
$userPage = isset($_GET['userPage']) ? $_GET['userPage'] : 1;
$userOffset = ($userPage - 1) * $limit;

$userBookedSlotsSql = "SELECT booking_id, f.facility_name, b.booking_date, b.start_time, b.end_time 
                        FROM booking b
                        JOIN facility f ON b.facility_id = f.facility_id
                        WHERE b.user_id = $userId
                        ORDER BY b.booking_date DESC, b.start_time DESC
                        LIMIT $limit OFFSET $userOffset";

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
    <title>Resident - Facility Booking</title>
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
                    <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Homepage</span></a></li>
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
                    <h3 class="text-dark mb-4">Facility Booking</h3>
                    <div class="card shadow" style="margin-top: 20px;">
                        <div class="card-header py-3" style="color: var(--bs-secondary-color);border-style: solid;border-top: 3px solid var(--bs-primary) ;border-right: 3px solid var(--bs-primary) ;border-bottom-style: solid;border-bottom-color: var(--bs-primary);border-left: 3px solid var(--bs-primary) ;">
                            <p class="fs-4 text-center text-primary m-0 fw-bold">RULES FOR BOOKING</p>
                        </div>
                        <div class="card-body" style="border-top-style: none;border-top-color: var(--bs-primary);border-right-style: solid;border-right-color: var(--bs-primary);border-bottom-style: solid;border-bottom-color: var(--bs-primary);border-left-style: solid;border-left-color: var(--bs-primary);">
                            <div class="row">
                                <div class="col text-center">
                                    <p class="text-start text-dark">1. Bookings are on a first-come, first-served basis.<br>2. Time slots for facility use may be limited to a specific duration.<br>3. Late cancellations may incur consequences or affect booking system operations.<br>4. Facility bookings are limited to residents and their registered guests.<br>5. Residents are responsible for the behavior of their guests.<br>6. Guests must be accompanied by a resident during facility use.<br>7. Users must keep noise levels at a reasonable volume to avoid disturbing other residents.<br>8. Users must comply with all condominium rules and regulations during facility use.<br>9. Parents or guardians must supervise children at all times.<br>10. Children may not be left unattended in the facility.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
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
                                                    <th>Facility Name</th>
                                                    <th style="padding-right: 184px;">Date</th>
                                                    <th>Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($slot = $bookedSlotsResult->fetch_assoc()): ?>
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
                            </ul>
                        </div>

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
                        <?php if ($userPage > 1): ?>
                            <a href="facility_booking.php?userPage=<?php echo $userPage - 1; ?>" class="btn btn-primary">Previous</a>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $totalPagesUserBookedSlots; $i++) : ?>
                            <a href="facility_booking.php?userPage=<?php echo $i; ?>"<?php echo ($i == $userPage) ? ' class="active"' : ''; ?>><?php echo $i; ?></a>
                        <?php endfor; ?>
                        <?php if ($userPage < $totalPagesUserBookedSlots): ?>
                            <a href="facility_booking.php?userPage=<?php echo $userPage + 1; ?>" class="btn btn-primary">Next</a>
                        <?php endif; ?>
                    </div>
                    <!-- Add Booking Form -->
                    <div class="card shadow" style="margin-top: 20px;">
                        <div class="card-header py-3">
                            <p class="text-primary m-0 fw-bold">Add Facility Booking</p>
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
                <br>
                <br>
                <br>
                <br>
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