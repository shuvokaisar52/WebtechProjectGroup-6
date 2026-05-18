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

$name = "";
$database = new db();
$connection = $database->connection();

if($_SERVER["REQUEST_METHOD"]=="POST")
    {
        $name = $_POST["name"] ?? "";

        if(empty($name))
            {
                Header("Location:../View/AddCategory.php?error=Category+Name+Required");
                exit();
            }
        else
            {
                $check = $database->CheckCategory($connection,"categories", $name);

                if($check->num_rows>0)
                    {
                        Header("Location:../View/AddCategory.php?error=Category+Already+Exists");
                        exit();
                    }
                else
                    {
                        $result = $database->AddCategory($connection,"categories", $name);

                        if($result)
                            {
                                Header("Location:../View/CategoryDashboard.php?message=Category+Added+Successfully");
                                exit();
                            }
                        else
                            {
                                Header("Location:../View/AddCategory.php?error=Category+Not+Added");
                                exit();
                            }
                    }
            }
    }

$categories = $database->ShowCategory($connection,"categories");
?>
