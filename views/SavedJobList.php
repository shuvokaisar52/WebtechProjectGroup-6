<?php
session_start();
include "../models/db.php";

$user_id = $_SESSION["user_id"] ?? null;
if (!$user_id) {
    header("Location: ../index.php");
    exit;
}


$database = new db();
$connection = $database->connection();
$result = $database->getSavedJobs($connection,$user_id);



?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
	<link rel="stylesheet" href="css/seeker.css">
</head>
<body>
<script src="../controllers/js/SearchJobs.js"></script>
<script src="../controllers/js/ToggleJob.js"></script>
	<div style="margin-top:20px;">
        <a href="../views/MyApplication.php">My Applications</a>
        <a href="../views/SavedJobList.php">Saved Jobs</a>
		<a href="../controllers/LogoutController.php">Logout</a>
    </div>
<h2 style="text-align:center;">Saved Jobs</h2>

<?php
if($result && $result->num_rows > 0){
	while($row = $result->fetch_assoc()){
		$job_id = $row["job_id"];
		$result2 = $database->getJobById($connection,$job_id);
		$values=$result2->fetch_assoc();
		
	?>

	<div style="border:1px solid black; margin:10px; padding:10px;">

		<h3><?php echo $values["title"]; ?></h3>
		<p><?php echo $values["location"]; ?></p>

		<button onclick="removeSaved(<?php echo $values['id']; ?>)">Remove</button>

	</div>

	<?php 
	}

}else{
?>
    <h2 style="text-align:center; color:red;">No job found.</h2>
<?php
}
	?>
</body>
</html>