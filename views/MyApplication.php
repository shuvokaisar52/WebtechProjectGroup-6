<?php
session_start();
include "../models/db.php";

$user_id = $_SESSION["user_id"];
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Applications Page</title>
</head>
<script src="../controllers/js/FetchApplications.js"></script>
<body onload="FetchApplications()">
<h2 style="text-align:center;">My Applications</h2>

<div id="application_list" style="margin-top: 20px;"></div>

</body>
</html>