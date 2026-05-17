<?php
session_start();
include "../models/db.php";

$option = "";


if($_SERVER["REQUEST_METHOD"]=="POST")
{
   	$user_id = $_SESSION["user_id"];
    $job_id = $_POST["job_id"];
    $cover_letter = $_POST["cover_letter"];
    $option = $_POST["resume_option"] ?? "";
	
	$database = new db();
	$connection = $database->connection();

	$check = $database->checkAlreadyApplied($connection, $job_id, $user_id);

	if($check->num_rows > 0)
	{
		echo "Already Applied.";
	}
	
	if(!empty($cover_letter) && $option!="")
		{
			$_SESSION["option"] = $option;

			if($option == "profile")
			{
				$sql = "SELECT file_path FROM users WHERE id='$user_id'";
				$result = $connection->query($sql);
				$user = $result->fetch_assoc();

				if(empty($user["file_path"]))
				{
					echo "No resume found!";
				}

				$resume_path = $user["file_path"];
			}
			else
			{
				$file = $_FILES["resume"];

				if($file["name"] != "")
				{
					$allowedTypes = array("pdf", "docx");
					$maxFileSize = 3 * 1024 * 1024;

					$fileName = $file["name"];
					$fileSize = $file["size"];

					$fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

					if(!in_array($fileType, $allowedTypes)){
						echo "Type is error. try again!";
						exit();
					}

					if($fileSize > $maxFileSize){
						echo "File too large!";
						exit();
					}

					$target = "../public/uploads/";
					$resume_path = $target.basename($fileName);

					move_uploaded_file($file["tmp_name"], $resume_path);
				}
				else
				{
					$resume_path = $_SESSION["filepath"];
				}
			}

			$result = $database->applyJob($connection, $job_id, $user_id, $cover_letter, $resume_path);
			if($result)
			{
				echo "Application Submitted";
				Header("Location: ../views/JobDetails.php?id=$job_id");
			}
			else
			{
				echo "Error";
			}
		}else{
			echo "Fill all field";
		}
		
		if($option == "upload"){
			if(!isset($_FILES["resume"])){
				echo "Please upload a resume!";
			}
		}
}
?>