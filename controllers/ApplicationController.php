<?php
session_start();
include "../models/db.php";

$user_id = $_SESSION["user_id"];
$database = new db();
$connection = $database->connection();

$result = $database->getMyApplications($connection, $user_id);

if($result && $result->num_rows > 0){
	while($row = $result->fetch_assoc()){

		$job_id = $row["job_id"];

		$jobResult = $database->getJobById($connection, $user_id);
		$job = $jobResult->fetch_assoc();

		$emp_id = $job["employer_id"];
		$empQuery = "SELECT * FROM employer_profiles WHERE user_id='".$emp_id."'";
		$empResult = $connection->query($empQuery);
		$company = $empResult->fetch_assoc();
		if(!$company){
			$company_name = "No Company";
		} else {
			$company_name = $company["company_name"];
		}
	?>

	<div style="border:1px solid black; margin:10px; padding:10px;">

		<h3><?php echo $job["title"]; ?></h3>
		<p>Company Name: <?php echo $company_name; ?></p>
		<p>Date Applied: <?php echo $row["created_at"]; ?></p>
		<p>
			Status: 
			<span style="padding:5px; border-radius:5px; background-color:lightgray;">
				<?php echo $row["status"]; ?>
			</span>
		</p>

	</div>

<?php 
	} 
}else{
?>
    <h2 style="text-align:center; color:red;">No application found.</h2>
<?php
}
?>