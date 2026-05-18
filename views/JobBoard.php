<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Job Board Page</title>
	<link rel="stylesheet" href="css/seeker.css">
</head>
<body onload="SearchJobs()">
<script src="../controllers/js/SearchJobs.js"></script>
<script src="../controllers/js/ToggleJob.js"></script>
    <div style="margin-top:20px;">
        <a href="../controllers/ProfileController.php">Profile</a>
        <a href="../views/MyApplication.php">My Applications</a>
        <a href="../views/SavedJobList.php">Saved Jobs</a>
		<a href="../controllers/LogoutController.php">Logout</a>
    </div>

    <h2>Search Jobs</h2>
    <input type="text" id="search_keyword" placeholder="Search..." onkeyup="SearchJobs()">
    <select id="filter_category" onchange="FilterSearchJobs()">
		<option value="">All Category</option>
		<option value="1">Developer</option>
		<option value="2">Marketing</option>
	</select>
	<select id="filter_location" onchange="FilterSearchJobs()">
		<option value="">All Location</option>
		<option value="Feni">Feni</option>
		<option value="Dhaka">Dhaka</option>
		<option value="Chittagong">Chittagong</option>
	</select>
    <select id="filter_type" onchange="FilterSearchJobs()">
        <option value="">All Types</option>
        <option value="Full-time">Full time</option>
        <option value="Part-time">Part time</option>
        <option value="Remote">Remote</option>
    </select>
	<select id="filter_salary" onchange="FilterSearchJobs()">
		<option value="">All Salary</option>
		<option value="10000">10k+</option>
		<option value="20000">20k+</option>
		<option value="30000">30k+</option>
		<option value="50000">50k+</option>
	</select>

    <div id="job_list" style="margin-top: 20px;"></div>
</body>
</html>