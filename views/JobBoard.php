<?php
session_start();

$_SESSION["user_id"]="2";

$username = $_SESSION["username"] ?? "";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Job Board</title>
	<style>
		a{
			text-decoration: none;
			padding: 6px 18px;
			border-radius: 5px;
			border: 1px solid black;
			font-size: 14px;
		}
	</style>
</head>
<body onload="SearchJobs()">
<script src="../controllers/js/SearchJobs.js"></script>
    <div style="margin-top:20px;">
        <a href="../views/MyApplication.php">My Applications</a>
        <a href="../views/SavedJobList.php">Bookmarked Job</a>
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