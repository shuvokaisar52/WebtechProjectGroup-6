<?php
include "../models/db.php";
session_start();

if((!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"]!=true) && !isset($_SESSION["user_id"]))
    {
        Header("Location:../views/Login.php");
        exit();
    }

if(($_SESSION["role"] ?? "")!="employer")
    {
        echo "Only Employer Can Access This Page";
        exit();
    }

$database = new db();
$connection = $database->connection();

if($_SERVER["REQUEST_METHOD"]=="POST")
    {
        $id = $_POST["id"] ?? "";
        $employer_id = $_SESSION["user_id"] ?? ($_SESSION["id"] ?? "");

        if(empty($id))
            {
                Header("Location:../views/EmployerDashboard.php?error=Job+Id+Missing");
                exit();
            }

        $result = $database->DeleteJob($connection,"jobs", $id, $employer_id);

        if($result)
            {
                Header("Location:../views/EmployerDashboard.php?message=Job+Deleted");
                exit();
            }
        else
            {
                Header("Location:../views/EmployerDashboard.php?error=Job+Not+Deleted");
                exit();
            }
    }
?>
