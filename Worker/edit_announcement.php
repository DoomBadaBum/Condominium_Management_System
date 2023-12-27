<!-- edit_announcement.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Announcement</title>
</head>
<body>
    <?php
    include '../include/connection.php';
    session_start();

    // Check if the user is authenticated
    if (!isset($_SESSION["user_id"])) {
        header("Location: login_worker.php");
        exit();
    }

    // Check if the announcement_id is provided in the request
    if (isset($_GET['announcement_id'])) {
        $announcementId = $_GET['announcement_id'];

        // Check if the logged-in worker created the announcement
        $workerId = $_SESSION["user_id"];
        $checkOwnershipSql = "SELECT * FROM announcement WHERE announcement_id = $announcementId AND worker_id = $workerId";
        $result = $conn->query($checkOwnershipSql);

        if ($result->num_rows == 1) {
            // Fetch the announcement details for pre-filling the form
            $row = $result->fetch_assoc();
            $title = $row['title'];
            $content = $row['content'];
            $mediaUrl = $row['media_url'];
            ?>

            <h2>Edit Announcement</h2>
            <form action="update_announcement.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="announcement_id" value="<?php echo $announcementId; ?>">

                <label for="title">Title:</label>
                <input type="text" id="title" name="title" value="<?php echo $title; ?>" required><br>

                <label for="content">Content:</label>
                <textarea id="content" name="content" required><?php echo $content; ?></textarea><br>

                <label for="media">Attach Media:</label>
                <input type="file" id="media" name="media">
                <?php if (!empty($mediaUrl)): ?>
                    <p>Current Media: <a href="../<?php echo $mediaUrl; ?>" target="_blank"><?php echo $mediaUrl; ?></a></p>
                <?php endif; ?><br>

                <input type="submit" value="Update Announcement">
            </form>

            <?php
        } else {
            // Unauthorized access, redirect to announcements page
            header("Location: announcements.php");
            exit();
        }
    } else {
        // announcement_id not provided, redirect to announcements page
        header("Location: announcements.php");
        exit();
    }

    $conn->close();
    ?>
</body>
</html>
