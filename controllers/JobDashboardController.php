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

$employer_id = $_SESSION["user_id"] ?? ($_SESSION["id"] ?? "");

$database = new db();
$connection = $database->connection();

$jobs = $database->ShowEmployerJob($connection, $employer_id);
?>
