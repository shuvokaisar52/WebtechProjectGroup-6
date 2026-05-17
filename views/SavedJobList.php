<?php
session_start();
include "../models/db.php";

$user_id = $_SESSION["user_id"];


$database = new db();
$connection = $database->connection();
$result = $database->getSavedJobs($connection,$user_id);



?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
<script src="../controllers/js/SearchJobs.js"></script>
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