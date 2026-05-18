<?php
include "../Model/db.php";
session_start();

if(!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"]!=true)
    {
        Header("Location:../View/Login.php");
        exit();
    }

if(($_SESSION["role"] ?? "")!="employer")
    {
        echo "Only Employer Can Access This Page";
        exit();
    }

$employer_id = $_SESSION["id"];

$database = new db();
$connection = $database->connection();

$jobs = $database->ShowEmployerJob($connection, $employer_id);
?>
