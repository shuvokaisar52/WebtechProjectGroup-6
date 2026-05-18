<?php
include "../Model/db.php";
session_start();

header('Content-Type: application/json');

if(!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"]!=true)
    {
        echo json_encode(array("success"=>false,"message"=>"Please Login"));
        exit();
    }

if(($_SESSION["role"] ?? "")!="employer")
    {
        echo json_encode(array("success"=>false,"message"=>"Only Employer Can Change Status"));
        exit();
    }

$id = $_POST["id"] ?? "";
$employer_id = $_SESSION["id"];

if(empty($id))
    {
        echo json_encode(array("success"=>false,"message"=>"Job Id Missing"));
        exit();
    }

$database = new db();
$connection = $database->connection();

$result = $database->GetJobStatus($connection,"jobs", $id, $employer_id);

if($result->num_rows==1)
    {
        $row=$result->fetch_assoc();

        if($row["status"]=="active")
            {
                $status="closed";
            }
        else
            {
                $status="active";
            }

        $update = $database->ChangeJobStatus($connection,"jobs", $id, $employer_id, $status);

        if($update)
            {
                echo json_encode(array("success"=>true,"status"=>$status));
                exit();
            }
        else
            {
                echo json_encode(array("success"=>false,"message"=>"Status Not Changed"));
                exit();
            }
    }
else
    {
        echo json_encode(array("success"=>false,"message"=>"Job Not Found"));
        exit();
    }
?>
