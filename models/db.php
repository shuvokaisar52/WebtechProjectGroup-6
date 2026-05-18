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

	function AddCategory($connection, $tablename, $name)
{
    $sql= "INSERT INTO ".$tablename."(name) VALUES (?)";
    $statement=$connection->prepare($sql);
    $statement->bind_param("s",$name);
    $result = $statement->execute();
    return $result;
}

function ShowCategory($connection, $tablename)
{
    $sql = "SELECT * FROM ".$tablename." ORDER BY id DESC";
    $statement=$connection->prepare($sql);
    $statement->execute();
    $result = $statement->get_result();
    return $result;
}

function CheckCategory($connection, $tablename, $name)
{
    $sql = "SELECT * FROM ".$tablename." WHERE name=?";
    $statement=$connection->prepare($sql);
    $statement->bind_param("s",$name);
    $statement->execute();
    $result = $statement->get_result();
    return $result;
}

function CategoryById($connection, $tablename, $id)
{
    $sql = "SELECT * FROM ".$tablename." WHERE id=?";
    $statement=$connection->prepare($sql);
    $statement->bind_param("i",$id);
    $statement->execute();
    $result = $statement->get_result();
    return $result;
}

function UpdateCategory($connection, $tablename, $id, $name)
{
    $sql = "UPDATE ".$tablename." SET name=? WHERE id=?";
    $statement=$connection->prepare($sql);
    $statement->bind_param("si",$name,$id);
    $result = $statement->execute();
    return $result;
}

function CheckCategoryJob($connection, $tablename, $category_id)
{
    $sql = "SELECT * FROM ".$tablename." WHERE category_id=?";
    $statement=$connection->prepare($sql);
    $statement->bind_param("i",$category_id);
    $statement->execute();
    $result = $statement->get_result();
    return $result;
}

function DeleteCategory($connection, $tablename, $id)
{
    $sql = "DELETE FROM ".$tablename." WHERE id=?";
    $statement=$connection->prepare($sql);
    $statement->bind_param("i",$id);
    $result = $statement->execute();
    return $result;
}

function AddJob($connection, $tablename, $employer_id, $category_id, $title, $description, $requirements, $salary_range, $location, $job_type, $deadline)
{
    $status="active";

    $sql= "INSERT INTO ".$tablename."(employer_id, category_id, title, description, requirements, salary_range, location, job_type, deadline, status, created_at) VALUES (?,?,?,?,?,?,?,?,?,?,NOW())";
    $statement=$connection->prepare($sql);
    $statement->bind_param("iissssssss",$employer_id,$category_id,$title,$description,$requirements,$salary_range,$location,$job_type,$deadline,$status);
    $result = $statement->execute();
    return $result;
}

function ShowEmployerJob($connection, $employer_id)
{
    $sql = "SELECT jobs.id, jobs.title, categories.name AS category_name, jobs.deadline, jobs.status, COUNT(applications.id) AS total_application
    FROM jobs
    LEFT JOIN categories ON jobs.category_id=categories.id
    LEFT JOIN applications ON jobs.id=applications.job_id
    WHERE jobs.employer_id=?
    GROUP BY jobs.id, jobs.title, categories.name, jobs.deadline, jobs.status, jobs.created_at
    ORDER BY jobs.created_at DESC";

    $statement=$connection->prepare($sql);
    $statement->bind_param("i",$employer_id);
    $statement->execute();
    $result = $statement->get_result();
    return $result;
}

function JobById($connection, $tablename, $id, $employer_id)
{
    $sql = "SELECT * FROM ".$tablename." WHERE id=? AND employer_id=?";
    $statement=$connection->prepare($sql);
    $statement->bind_param("ii",$id,$employer_id);
    $statement->execute();
    $result = $statement->get_result();
    return $result;
}

function UpdateJob($connection, $tablename, $id, $employer_id, $category_id, $title, $description, $requirements, $salary_range, $location, $job_type, $deadline)
{
    $sql = "UPDATE ".$tablename." SET category_id=?, title=?, description=?, requirements=?, salary_range=?, location=?, job_type=?, deadline=? WHERE id=? AND employer_id=?";
    $statement=$connection->prepare($sql);
    $statement->bind_param("isssssssii",$category_id,$title,$description,$requirements,$salary_range,$location,$job_type,$deadline,$id,$employer_id);
    $result = $statement->execute();
    return $result;
}

function DeleteJob($connection, $tablename, $id, $employer_id)
{
    $sql = "DELETE FROM ".$tablename." WHERE id=? AND employer_id=?";
    $statement=$connection->prepare($sql);
    $statement->bind_param("ii",$id,$employer_id);
    $result = $statement->execute();
    return $result;
}

function GetJobStatus($connection, $tablename, $id, $employer_id)
{
    $sql = "SELECT * FROM ".$tablename." WHERE id=? AND employer_id=?";
    $statement=$connection->prepare($sql);
    $statement->bind_param("ii",$id,$employer_id);
    $statement->execute();
    $result = $statement->get_result();
    return $result;
}

function ChangeJobStatus($connection, $tablename, $id, $employer_id, $status)
{
    $sql = "UPDATE ".$tablename." SET status=? WHERE id=? AND employer_id=?";
    $statement=$connection->prepare($sql);
    $statement->bind_param("sii",$status,$id,$employer_id);
    $result = $statement->execute();
    return $result;
}
}
?>