<?php
include "../controllers/JobDashboardController.php";
echo "<h1>Employer Job Dashboard</h1><br>";
$message = $_GET["message"] ?? "";
$error = $_GET["error"] ?? "";
$username = $_SESSION["username"] ?? ($_SESSION["name"] ?? "");
?>
<!DOCTYPE html>
<html>
    <head>
        <script src='../controllers/js/ToggleStatus2.js'></script>
    </head>
    <body>
        <?php echo "Hello ".$username; ?>
        <br>
        <a href="../controllers/Logout.php">Logout</a>
        <br>
        <?php echo $message; ?>
        <?php echo $error; ?>
        <br><br>
        <a href='AddJob.php'>Add New Job</a>
        <br><br>
        <table border='1'>
            <tr>
                <td>Title</td>
                <td>Category</td>
                <td>Deadline</td>
                <td>Application Count</td>
                <td>Status</td>
                <td>Edit</td>
                <td>Delete</td>
            </tr>
            <?php foreach($jobs as $row){ ?>
            <tr>
                <td><?php echo $row['title']; ?></td>
                <td><?php echo $row['category_name']; ?></td>
                <td><?php echo $row['deadline']; ?></td>
                <td><?php echo $row['total_application']; ?></td>
                <td>
                    <?php if($row['status']=='active'){ ?>
                        <font id='s<?php echo $row['id']; ?>' color='green'><?php echo $row['status']; ?></font>
                    <?php } else { ?>
                        <font id='s<?php echo $row['id']; ?>' color='red'><?php echo $row['status']; ?></font>
                    <?php } ?>
                    <br>
                    <input type='button' value='Toggle' onclick='ToggleStatus2(<?php echo $row['id']; ?>)'>
                </td>
                <td><a href='EditJob.php?id=<?php echo $row['id']; ?>'>Edit</a></td>
                <td>
                    <form method='post' action='../controllers/DeleteJobController.php'>
                        <input type='hidden' name='id' value='<?php echo $row['id']; ?>'>
                        <input type='submit' value='Delete'>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </table>
    </body>
</html>
