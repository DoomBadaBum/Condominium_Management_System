<!-- logout_worker.php -->
<?php
session_start();
session_unset();
session_destroy();
header("Location: login_worker.php");
exit();
?>
