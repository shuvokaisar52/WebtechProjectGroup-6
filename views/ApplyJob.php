<?php
session_start();
include "../models/db.php";

$user_id = $_SESSION["user_id"];
$job_id = $_GET["job_id"];

?>
<!DOCTYPE html>
<html>
<head>
    <title>Apply Job Page</title>
	<style>
		textarea {
			width: 300px;
			height: 100px;
			padding: 8px;
			border: 1px solid #ccc;
			border-radius: 5px;
			resize: none;
		}

		button {
			width: 300px;
			padding: 10px;
			background-color: #00653d;
			color: white;
			border: none;
			border-radius: 5px;
			font-size: 15px;
		}
	</style>
</head>
<body>
<h2 >Apply Job</h2>

<form action="../controllers/ApplyJobController.php" method="POST" enctype="multipart/form-data">

    <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">

    <textarea name="cover_letter" placeholder="Write cover letter"></textarea><br><br>

    <label>
        <input type="radio" name="resume_option" value="profile">
        Use the profile resume
    </label><br>

    <label>
        <input type="radio" name="resume_option" value="upload">
        Upload a new resume
    </label><br><br>

    <input type="file" name="resume"><br><br>

    <button type="submit">Submit Application</button>

</form>
</body>
</html>