<?php
session_start();

$isLoggedIn = $_SESSION["loggedIn"] ?? false;
$user_id = $_SESSION["id"] ?? ($_SESSION["user_id"] ?? "");
$username = $_SESSION["username"] ?? ($_SESSION["name"] ?? "");
$role = $_SESSION["role"] ?? "";

if(!$isLoggedIn && empty($user_id))
    {
        Header("Location:../views/Login.php");
        exit();
    }

if($role!="admin")
    {
        echo "Only Admin Can Access This Page";
        exit();
    }
?>