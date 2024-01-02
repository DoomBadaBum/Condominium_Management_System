<?php
// Establish a database connection
include '../include/connection.php';
session_start();

// Check if the user is authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Header sidebar
include '../include/header.php'; 
include '../include/sidebar.php';

// Fetch the current user's username
$userId = $_SESSION["user_id"];
$sql = "SELECT username FROM user WHERE user_id = $userId";
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
                     ORDER BY date DESC
                     LIMIT $offset, $announcementsPerPage";
$announcementsResult = $conn->query($announcementsSql);

if ($announcementsResult === false) {
    die("Error executing announcements query: " . $conn->error);
}

$conn->close();
?>

<!-- HTML content for announcements page -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements - Resident</title>
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
            background-color: #4CAF50;
            color: white;
        }
    </style>
</head>
<body>
    <h2>Announcements - Resident</h2>
    <?php if ($user): ?>
        <p>Resident: <?php echo $user['username']; ?></p>
        <!-- Display announcements -->
        <?php if ($announcementsResult->num_rows > 0): ?>
            <ul>
                <?php while ($row = $announcementsResult->fetch_assoc()): ?>
                    <li>
                        <h3><?php echo $row['title']; ?></h3>
                        <p>Date: <?php echo $row['formatted_date']; ?> at <?php echo $row['formatted_time']; ?></p>
                        <p>Posted by: <?php echo $row['worker_name']; ?></p>
                        <?php if (!empty($row['media_url'])): ?>
                            <?php $mediaExtension = pathinfo($row['media_url'], PATHINFO_EXTENSION); ?>
                            <?php if (in_array($mediaExtension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                <img src="../<?php echo $row['media_url']; ?>" alt="Image">
                            <?php elseif (in_array($mediaExtension, ['mp4', 'webm', 'ogg'])): ?>
                                <video width="320" height="240" controls>
                                    <source src="../<?php echo $row['media_url']; ?>" type="video/<?php echo $mediaExtension; ?>">
                                    Your browser does not support the video tag.
                                </video>
                            <?php endif; ?>
                        <?php endif; ?>
                        <p><?php echo $row['content']; ?></p>
                    </li>
                <?php endwhile; ?>
            </ul>
            <!-- Pagination section -->
            <div class="pagination">
                <p>Showing <?php echo ($offset + 1) . " - " . min($offset + $announcementsPerPage, $totalAnnouncements); ?> out of <?php echo $totalAnnouncements; ?></p>
                <a href="?page=1" <?php echo ($current_page == 1) ? 'style="background-color: #ddd;"' : ''; ?>>First</a>
                <a href="?page=<?php echo max(1, $current_page - 1); ?>" <?php echo ($current_page == 1) ? 'style="background-color: #ddd;"' : ''; ?>>Previous</a>
                <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                    <a href="?page=<?php echo $page; ?>" <?php echo ($current_page == $page) ? 'class="active"' : ''; ?>><?php echo $page; ?></a>
                <?php endfor; ?>
                <a href="?page=<?php echo min($totalPages, $current_page + 1); ?>" <?php echo ($current_page == $totalPages) ? 'style="background-color: #ddd;"' : ''; ?>>Next</a>
                <a href="?page=<?php echo $totalPages; ?>" <?php echo ($current_page == $totalPages) ? 'style="background-color: #ddd;"' : ''; ?>>Last</a>
            </div>
        <?php else: ?>
            <p>No announcements available.</p>
        <?php endif; ?>
    <?php else: ?>
        <p>Error: Worker not found.</p>
    <?php endif; ?>
</body>
</html>