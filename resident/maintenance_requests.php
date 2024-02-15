<?php
session_start();

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

// Fetch the list of maintenance requests for the current resident
$userId = $_SESSION["user_id"];

$resultsPerPage = 5; // Adjust the number of results per page as needed
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $resultsPerPage;

if ($offset < 0) {
    $offset = 0;
}

// Fetch maintenance requests
$maintenanceRequestsSql = "SELECT maintenance_request.request_id, category.category_name, maintenance_request.description, maintenance_request.location, maintenance_request.request_date, maintenance_request.urgency, maintenance_request.status
                          FROM maintenance_request
                          INNER JOIN category ON maintenance_request.category_id = category.category_id
                          WHERE user_id = $userId
                          LIMIT $resultsPerPage OFFSET $offset";

// Execute the maintenance requests query
$maintenanceRequestsResult = $conn->query($maintenanceRequestsSql);

if ($maintenanceRequestsResult === false) {
    die("Error executing maintenance requests query: " . $conn->error);
}


// Fetch categories from the database
$categoriesSql = "SELECT category_id, category_name FROM category";
$categoriesResult = $conn->query($categoriesSql);

// Check if the maintenance request form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoryID = $_POST["category"];
    $location = $_POST["location"];
    $description = $_POST["description"];
    $urgency = $_POST["urgency"];

    // Insert the maintenance request into the database
    $insertRequestSql = "INSERT INTO maintenance_request (user_id, category_id, location, description, urgency, status, request_date)
                        VALUES ((SELECT user_id FROM user WHERE user_id = $userId), $categoryID, '$location', '$description', '$urgency', 'Pending', NOW())";

    if ($conn->query($insertRequestSql) === true) {
        echo '<script>alert("Maintenance requests added successfully!");</script>';
        echo '<script>window.location.href = "maintenance_requests.php";</script>';
    } else {
        $requestSubmissionMessage = "Error submitting maintenance request: " . $conn->error;
    }
}
// Extract page number from URL parameters
$page = isset($_GET['page']) ? $_GET['page'] : 1;

?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Resident - Maintenance Request</title>
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
                    <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Homepage</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="profile.php"><i class="fas fa-user"></i><span>Profile</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="facility_booking.php"><i class="fas fa-table"></i><span>Facility Booking</span></a></li>
                    <li class="nav-item"><a class="nav-link active" href="maintenance_requests.php"><svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="1em" viewBox="0 0 24 24" width="1em" fill="currentColor">
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
        <h3 class="text-dark mb-4">Maintenance Requests</h3>

        <div class="card shadow" style="margin-top: 20px;">
            <div class="card-header py-3">
                <p class="text-primary m-0 fw-bold">Submitted Requests</p>
            </div>
            <div class="card-body">
                <?php if ($maintenanceRequestsResult->num_rows > 0): ?>
                    <div class="table-responsive table mt-2" id="dataTable" role="grid" aria-describedby="dataTable_info">
                        <!-- Display submitted maintenance requests table here -->
                        <table class="table my-0" id="dataTable">
                        <thead>
                                        <tr>
                                            <th style="padding-right: 184px;width: 151.922px;">Category</th>
                                            <th style="width: 100.703px;">Location</th>
                                            <th style="width: 263.188px;">Description</th>
                                            <th style="width: 80px;">Urgency</th>
                                            <th style="width: 157.844px;">Request Date</th>
                                            <th style="width: 129.156px;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $maintenanceRequestsResult->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $row['category_name']; ?></td>
                                                <td><?php echo $row['location']; ?></td>
                                                <td><?php echo $row['description']; ?></td>
                                                <td><?php echo $row['urgency']; ?></td>
                                                <td><?php echo $row['request_date']; ?></td>
                                                <td><?php echo $row['status']; ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr></tr>
                                    </tfoot>
                        </table>

                        <!-- Display pagination for Maintenance Requests -->
                        <div class="row">
                        <div class="col-md-12">
                                        <div class="text-end">
                                            <nav aria-label="Page navigation example">
                                                <ul class="pagination">

                                                    <?php
                                                    // Calculate total number of results
                                                    $totalResultsQuery = $conn->query("SELECT COUNT(*) as total FROM maintenance_request WHERE user_id = $userId");
                                                    if ($totalResultsQuery === false) {
                                                        die("Error calculating total results: " . $conn->error);
                                                    }

                                                    $totalResultsRow = $totalResultsQuery->fetch_assoc();
                                                    $totalResults = $totalResultsRow['total'];
                                                    $totalPages = ceil($totalResults / $resultsPerPage);

                                                    // Previous Button
                                                    if ($page > 1) {
                                                        echo "<li class='page-item'><a class='page-link' href='maintenance_requests.php?page=" . ($page - 1) . "'>&laquo; Previous</a></li>";
                                                    }

                                                    for ($i = 1; $i <= $totalPages; $i++) {
                                                        echo "<li class='page-item" . ($page == $i ? " active" : "") . "'><a class='page-link' href='maintenance_requests.php?page=$i'>$i</a></li>";
                                                    }

                                                    // Next Button
                                                    if ($page < $totalPages) {
                                                        echo "<li class='page-item'><a class='page-link' href='maintenance_requests.php?page=" . ($page + 1) . "'>Next &raquo;</a></li>";
                                                    }
                                                    ?>
                                                </ul>
                                            </nav>
                                        </div>
                                    </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card shadow" style="margin-top: 20px;">
            <div class="card-header py-3">
                <p class="text-primary m-0 fw-bold">Add Maintenance Requests</p>
            </div>
            <div class="card-body">
                <form action="maintenance_requests.php" method="post">
                    <div class="col">
                        <label class="form-label" style="color: rgb(0,0,0);">Category</label>
                        <select id="category" name="category" required>
                            <?php while ($categoryRow = $categoriesResult->fetch_assoc()): ?>
                                <option value="<?php echo $categoryRow['category_id']; ?>"><?php echo $categoryRow['category_name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col" style="margin-top: 15px;">
                        <label class="form-label" style="color: rgb(0,0,0);">Location</label>
                        <input id="location" name="location" class="form-control" type="text">
                    </div>
                    <div class="col" style="margin-top: 15px;">
                        <label class="form-label" style="color: rgb(0,0,0);">Description</label>
                        <textarea class="form-control" id="description" name="description"></textarea>
                    </div>
                    <div class="col" style="margin-top: 15px;">
                        <label for="urgency" class="form-label" style="color: rgb(0,0,0);">Urgency</label>
                        <select id="urgency" name="urgency" required class="form-select">
                            <option value="Low" selected="">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                        </select>
                    </div>
                    <div class="col" style="margin-top: 15px;">
                        <input class="btn btn-primary" type="submit" value="Submit">
                    </div>
                </form>
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
    <?php
    $conn->close(); // Move this line here
    ?>
</body>
</html>