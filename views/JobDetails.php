<?php
session_start();
include "../models/db.php";

$user_id = $_SESSION["user_id"] ?? null;
if (!$user_id) {
    header("Location: ../index.php");
    exit;
}
$job_id = $_GET["id"] ?? 0;

$database = new db();
$connection = $database->connection();

$result = $database->getJobById($connection, $job_id);
$job = $result->fetch_assoc();

$check = $database->checkAlreadyApplied($connection, $job_id, $user_id);
?>
	<link rel="stylesheet" href="css/seeker.css">
    <div style="margin-top:20px;">
        <a href="../controllers/ProfileController.php">Profile</a>
        <a href="../views/MyApplication.php">My Applications</a>
        <a href="../views/SavedJobList.php">Saved Jobs</a>
		<a href="../controllers/LogoutController.php">Logout</a>
    </div>
<h2 style="text-align:center;">Job Details Page</h2>

<div style="border:1px solid black; margin:10px; padding:10px;">
<h2><?php echo $job["title"]; ?></h2>
<p><?php echo $job["description"]; ?></p>
<p><?php echo $job["location"]; ?></p>

<?php if($check->num_rows > 0){ ?>

    <button disabled>Applied ✓</button>

<?php } else { ?>

    <a href="../views/ApplyJob.php?job_id=<?php echo $job_id; ?>">
        <button>Apply Now</button>
    </a>
</div>

<?php } ?>