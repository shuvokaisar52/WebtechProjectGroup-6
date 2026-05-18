<?php
include "../controllers/JobController.php";
echo "<h1>Add Job Page</h1><br>";
$error = $_GET["error"] ?? "";
$types = array("Full-time", "Part-time", "Remote");
?>
<!DOCTYPE html>
<html>
    <body>
        <?php echo $error; ?>
        <form method='post' action='../controllers/JobController.php'>
            <table>
                <tr>
                    <td>Title:</td>
                    <td><input type='text' name='title'></td>
                </tr>
                <tr>
                    <td>Category:</td>
                    <td>
                        <select name='category_id'>
                            <option value=''>Select Category</option>
                            <?php foreach($categories as $category){ ?>
                                <option value='<?php echo $category["id"]; ?>'><?php echo $category["name"]; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Description:</td>
                    <td><textarea name='description'></textarea></td>
                </tr>
                <tr>
                    <td>Requirements:</td>
                    <td><textarea name='requirements'></textarea></td>
                </tr>
                <tr>
                    <td>Salary Range:</td>
                    <td><input type='text' name='salary_range'></td>
                </tr>
                <tr>
                    <td>Location:</td>
                    <td><input type='text' name='location'></td>
                </tr>
                <tr>
                    <td>Job Type:</td>
                    <td>
                        <?php foreach($types as $type){ ?>
                            <input type='radio' name='job_type' value='<?php echo $type; ?>'> <?php echo $type; ?>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td>Deadline:</td>
                    <td><input type='date' name='deadline'></td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type='hidden' name='action' value='add'>
                        <input type='submit' value='Add Job'>
                    </td>
                </tr>
            </table>
        </form>
        <br>
        <a href='EmployerDashboard.php'>Back</a>
    </body>
</html>
