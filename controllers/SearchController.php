<?php
include "../models/db.php";
session_start();

if($_SERVER["REQUEST_METHOD"] == "GET")
{
	$user_id = $_SESSION["user_id"];
	$q = $_GET["q"] ?? "";
	$job_type = $_GET["job_type"] ?? "";
	$category_id = $_GET["category_id"] ?? "";
	$location = $_GET["location"] ?? "";
	$salary_range = $_GET["salary_range"] ?? "";
	

    $database = new db();
    $connection = $database->connection();
    $result = $database->filterSearchJobs($connection, $q, $category_id, $job_type, $location, $salary_range);
	
	$savedResult = $database->getSavedJobs($connection, $user_id);
    $savedJobs = array();

    while($s = $savedResult->fetch_assoc()){
        $savedJobs[] = $s["job_id"];
    }

    if($result && $result->num_rows > 0)
    {
        while($row = $result->fetch_assoc())
        {
            $isSaved = in_array($row["id"], $savedJobs);

            $bookmarkicon = $isSaved ? "❤️" : "♡";
?>

            <div style="border:1px solid black; padding:10px; margin:10px;">

                <h3><?php echo $row["title"]; ?></h3>
                <p>Location: <?php echo $row["location"]; ?></p>
                <p>Type: <?php echo $row["job_type"]; ?></p>
                <p>Salary: <?php echo $row["salary_range"]; ?></p>

				<button class="save-btn" id="save-btn" onclick="saveJob(this, <?php echo $row['id']; ?>)"><?php echo $bookmarkicon; ?></button>

                <a href="../views/JobDetails.php?id=<?php echo $row['id']; ?>">
                    View Details
                </a>

            </div>

<?php
        }
    }
    else
    {
        echo "No Jobs Found.";
    }
}
else
{
    echo "Invalid.";
}
?>