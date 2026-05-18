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

if($_SERVER["REQUEST_METHOD"]=="POST")
    {
        $id = $_POST["id"] ?? "";

        if(empty($id))
            {
                Header("Location:../views/CategoryDashboard.php?error=Category+Id+Missing");
                exit();
            }

        $check = $database->CheckCategoryJob($connection,"jobs", $id);

        if($check->num_rows>0)
            {
                Header("Location:../views/CategoryDashboard.php?error=Cannot+Delete+Category+Because+Jobs+Use+It");
                exit();
            }
        else
            {
                $result = $database->DeleteCategory($connection,"categories", $id);

                if($result)
                    {
                        Header("Location:../views/CategoryDashboard.php?message=Category+Deleted");
                        exit();
                    }
                else
                    {
                        Header("Location:../views/CategoryDashboard.php?error=Category+Not+Deleted");
                        exit();
                    }
            }
    }
?>
