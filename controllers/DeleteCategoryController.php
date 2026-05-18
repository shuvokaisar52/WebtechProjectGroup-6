<?php
include "../Model/db.php";
session_start();

if(!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"]!=true)
    {
        Header("Location:../View/Login.php");
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
                Header("Location:../View/CategoryDashboard.php?error=Category+Id+Missing");
                exit();
            }

        $check = $database->CheckCategoryJob($connection,"jobs", $id);

        if($check->num_rows>0)
            {
                Header("Location:../View/CategoryDashboard.php?error=Cannot+Delete+Category+Because+Jobs+Use+It");
                exit();
            }
        else
            {
                $result = $database->DeleteCategory($connection,"categories", $id);

                if($result)
                    {
                        Header("Location:../View/CategoryDashboard.php?message=Category+Deleted");
                        exit();
                    }
                else
                    {
                        Header("Location:../View/CategoryDashboard.php?error=Category+Not+Deleted");
                        exit();
                    }
            }
    }
?>
