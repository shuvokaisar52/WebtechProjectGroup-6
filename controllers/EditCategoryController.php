<?php
include "../models/db.php";
session_start();

if((!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"]!=true) && !isset($_SESSION["user_id"]))
    {
        Header("Location:../views/Login.php");
        exit();
    }

if(($_SESSION["role"] ?? "")!="admin")
    {
        echo "Only Admin Can Access This Page";
        exit();
    }

$database = new db();
$connection = $database->connection();

$id = $_GET["id"] ?? ($_POST["id"] ?? "");
$name = "";

if($_SERVER["REQUEST_METHOD"]=="POST")
    {
        $id = $_POST["id"] ?? "";
        $name = $_POST["name"] ?? "";

        if(empty($id) || empty($name))
            {
                Header("Location:../views/CategoryDashboard.php?error=All+Fields+Required");
                exit();
            }
        else
            {
                $result = $database->UpdateCategory($connection,"categories", $id, $name);

                if($result)
                    {
                        Header("Location:../views/CategoryDashboard.php?message=Category+Updated");
                        exit();
                    }
                else
                    {
                        Header("Location:../views/EditCategory.php?id=$id&error=Category+Not+Updated");
                        exit();
                    }
            }
    }

$categoryResult = $database->CategoryById($connection,"categories", $id);

if($categoryResult->num_rows==1)
    {
        $category = $categoryResult->fetch_assoc();
    }
else
    {
        echo "Category Not Found";
        exit();
    }
?>
