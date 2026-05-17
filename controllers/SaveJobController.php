<?php
session_start();
include "../models/db.php";


if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $job_id = $_POST["job_id"];
    $user_id = $_SESSION["user_id"];


    $database = new db();
    $connection = $database->connection();
    $result = $database->toggleJobStatus($connection, $user_id, $job_id);

    if($result){
		echo "success";
	} else {
		echo "error";
	}
}
?>