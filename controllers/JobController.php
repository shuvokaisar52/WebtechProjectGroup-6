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
$employer_id = $_SESSION["user_id"] ?? ($_SESSION["id"] ?? "");

$id = $_GET["id"] ?? ($_POST["id"] ?? "");
$title = "";
$category_id = "";
$description = "";
$requirements = "";
$salary_range = "";
$location = "";
$job_type = "";
$deadline = "";

if($_SERVER["REQUEST_METHOD"]=="POST")
    {
        $action = $_POST["action"] ?? "";
        $id = $_POST["id"] ?? "";
        $title = $_POST["title"] ?? "";
        $category_id = $_POST["category_id"] ?? "";
        $description = $_POST["description"] ?? "";
        $requirements = $_POST["requirements"] ?? "";
        $salary_range = $_POST["salary_range"] ?? "";
        $location = $_POST["location"] ?? "";
        $job_type = $_POST["job_type"] ?? "";
        $deadline = $_POST["deadline"] ?? "";

        if(empty($title) || empty($category_id) || empty($description) || empty($requirements) || empty($salary_range) || empty($location) || empty($job_type) || empty($deadline))
            {
                if($action=="edit")
                    {
                        Header("Location:../views/EditJob.php?id=$id&error=All+Fields+Required");
                        exit();
                    }
                else
                    {
                        Header("Location:../views/AddJob.php?error=All+Fields+Required");
                        exit();
                    }
            }
        else if($job_type!="Full-time" && $job_type!="Part-time" && $job_type!="Remote")
            {
                if($action=="edit")
                    {
                        Header("Location:../views/EditJob.php?id=$id&error=Invalid+Job+Type");
                        exit();
                    }
                else
                    {
                        Header("Location:../views/AddJob.php?error=Invalid+Job+Type");
                        exit();
                    }
            }
        else if(strtotime($deadline)==false)
            {
                if($action=="edit")
                    {
                        Header("Location:../views/EditJob.php?id=$id&error=Invalid+Deadline");
                        exit();
                    }
                else
                    {
                        Header("Location:../views/AddJob.php?error=Invalid+Deadline");
                        exit();
                    }
            }
        else
            {
                $categoryCheck = $database->CategoryById($connection,"categories", $category_id);

                if($categoryCheck->num_rows!=1)
                    {
                        if($action=="edit")
                            {
                                Header("Location:../views/EditJob.php?id=$id&error=Invalid+Category");
                                exit();
                            }
                        else
                            {
                                Header("Location:../views/AddJob.php?error=Invalid+Category");
                                exit();
                            }
                    }

                if($action=="add")
                    {
                        $result = $database->AddJob($connection,"jobs", $employer_id, $category_id, $title, $description, $requirements, $salary_range, $location, $job_type, $deadline);

                        if($result)
                            {
                                Header("Location:../views/EmployerDashboard.php?message=Job+Added+Successfully");
                                exit();
                            }
                        else
                            {
                                Header("Location:../views/AddJob.php?error=Job+Not+Added");
                                exit();
                            }
                    }
                else if($action=="edit")
                    {
                        $jobCheck = $database->JobById($connection,"jobs", $id, $employer_id);

                        if($jobCheck->num_rows!=1)
                            {
                                Header("Location:../views/EmployerDashboard.php?error=You+Can+Only+Edit+Your+Own+Job");
                                exit();
                            }

                        $result = $database->UpdateJob($connection,"jobs", $id, $employer_id, $category_id, $title, $description, $requirements, $salary_range, $location, $job_type, $deadline);

                        if($result)
                            {
                                Header("Location:../views/EmployerDashboard.php?message=Job+Updated");
                                exit();
                            }
                        else
                            {
                                Header("Location:../views/EditJob.php?id=$id&error=Job+Not+Updated");
                                exit();
                            }
                    }
                else
                    {
                        Header("Location:../views/EmployerDashboard.php?error=Invalid+Action");
                        exit();
                    }
            }
    }

$categories = $database->ShowCategory($connection,"categories");

if(!empty($id))
    {
        $jobResult = $database->JobById($connection,"jobs", $id, $employer_id);

        if($jobResult->num_rows==1)
            {
                $job = $jobResult->fetch_assoc();
            }
        else
            {
                echo "Job Not Found Or This Is Not Your Job";
                exit();
            }
    }
?>
