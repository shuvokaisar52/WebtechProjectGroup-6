<?php
include "../config/db_config.php";

class db
{

    function connection()
    {
        $connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if ($connection->connect_error) {
            die("Could not Connect Database: " . $connection->connect_error);
        }
        return $connection;
    }
	
	//Tofayel Hossain
	function getUser($connection, $user_id){
		$sql = "SELECT * FROM users WHERE id='".$user_id."'";
		$result = $connection->query($sql);
        return $result;
	}

    function getAllActiveJobs($connection) {
		$today = date('Y-m-d');
		
		$sql = "SELECT * FROM jobs WHERE status='active' AND deadline>='".$today."'";
        $result = $connection->query($sql);
        return $result;
    }

    function getJobById($connection, $id) {
		$sql = "SELECT * FROM jobs WHERE id=".$id;
		$result = $connection->query($sql);
        return $result;
    }

    function getSavedJobs($connection, $user_id) {
		$sql = "SELECT job_id FROM saved_jobs WHERE user_id='".$user_id."'";
		$result = $connection->query($sql);
		return $result;
    }

    function toggleJobStatus($connection, $user_id, $job_id) {
		$now = date('Y-m-d H:i:s');
		$check = "SELECT * FROM saved_jobs WHERE user_id='".$user_id."' AND job_id='".$job_id."'";
		$result = $connection->query($check);
		
		if($result->num_rows > 0){
			$sql = "DELETE FROM saved_jobs WHERE user_id='".$user_id."' AND job_id='".$job_id."'";
			return $connection->query($sql);

		} else {
			$sql = "INSERT INTO saved_jobs(user_id, job_id, created_at) VALUES('".$user_id."', '".$job_id."', '".$now."')";
			return $connection->query($sql);
		}
    }

    function filterSearchJobs($connection, $q, $category_id, $job_type, $location,$salary_range) {
		$today = date('Y-m-d H:i:s');
		$sql = "SELECT * FROM jobs WHERE status='active' AND deadline >= '".$today."'";
		
		if(!empty($q)){
			$sql .= " AND title LIKE '%".$q."%'";
		}
		if(!empty($job_type)){
			$sql .= " AND job_type='".$job_type."'";
		}
		if(!empty($category_id)){
			$sql .= " AND category_id='".$category_id."'";
		}
		if(!empty($location)){
			$sql .= " AND location='".$location."'";
		}
		if(!empty($salary_range)){
			$sql .= " AND salary_range >= '".$salary_range."'";
		}
		
		$result=$connection->query($sql);
		return $result;
    }

    function applyJob($connection, $job_id, $seeker_id, $cover_letter, $resume_path) {
		$now = date('Y-m-d H:i:s');
		$sql = "INSERT INTO applications(job_id, seeker_id, cover_letter, resume_path, status, created_at) VALUES('".$job_id."', '".$seeker_id."', '".$cover_letter."', '".$resume_path."', 'Submitted', '".$now."')";
		$result = $connection->query($sql);
		return $result;
    }

    function checkAlreadyApplied($connection, $job_id, $seeker_id) {
		$sql = "SELECT * FROM applications WHERE job_id='".$job_id."' AND seeker_id='".$seeker_id."'";
		$result = $connection->query($sql);
		return $result;
    }

    function getMyApplications($connection, $seeker_id) {
		$sql = "SELECT * FROM applications WHERE seeker_id='".$seeker_id."'";
		$result = $connection->query($sql);
		return $result;
    }
}
?>