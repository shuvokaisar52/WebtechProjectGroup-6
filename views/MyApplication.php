<?php
session_start();
include "../models/db.php";

$user_id = $_SESSION["user_id"] ?? null;
if (!$user_id) {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Applications Page</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<script src="../controllers/js/FetchApplications.js"></script>
<body >
    <div style="margin-top:20px;">
        <a href="../views/MyApplication.php">My Applications</a>
        <a href="../views/SavedJobList.php">Saved Jobs</a>
		<a href="../controllers/LogoutController.php">Logout</a>
    </div>
<h2 style="text-align:center;">My Applications</h2>

<div id="application_list" style="margin-top: 20px;"></div>
<script>
    FetchApplications()
</script>
</body>
</html>