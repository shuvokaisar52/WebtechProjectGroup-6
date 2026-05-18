<?php
include "../Controller/JobController.php";
echo "<h1>Edit Job Page</h1><br>";
$error = $_GET["error"] ?? "";
$types = array("Full-time", "Part-time", "Remote");
?>
<!DOCTYPE html>
<html>
    <body>
        <?php echo $error; ?>
        <form method='post' action='../Controller/JobController.php'>
            <table>
                <tr>
                    <td>Title:</td>
                    <td>
                        <input type='hidden' name='id' value='<?php echo $job["id"]; ?>'>
                        <input type='text' name='title' value='<?php echo $job["title"]; ?>'>
                    </td>
                </tr>
                <tr>
                    <td>Category:</td>
                    <td>
                        <select name='category_id'>
                            <?php foreach($categories as $category){ ?>
                                <option value='<?php echo $category["id"]; ?>' <?php if($category["id"]==$job["category_id"]){ echo "selected"; } ?>><?php echo $category["name"]; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Description:</td>
                    <td><textarea name='description'><?php echo $job["description"]; ?></textarea></td>
                </tr>
                <tr>
                    <td>Requirements:</td>
                    <td><textarea name='requirements'><?php echo $job["requirements"]; ?></textarea></td>
                </tr>
                <tr>
                    <td>Salary Range:</td>
                    <td><input type='text' name='salary_range' value='<?php echo $job["salary_range"]; ?>'></td>
                </tr>
                <tr>
                    <td>Location:</td>
                    <td><input type='text' name='location' value='<?php echo $job["location"]; ?>'></td>
                </tr>
                <tr>
                    <td>Job Type:</td>
                    <td>
                        <?php foreach($types as $type){ ?>
                            <input type='radio' name='job_type' value='<?php echo $type; ?>' <?php if($type==$job["job_type"]){ echo "checked"; } ?>> <?php echo $type; ?>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td>Deadline:</td>
                    <td><input type='date' name='deadline' value='<?php echo $job["deadline"]; ?>'></td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type='hidden' name='action' value='edit'>
                        <input type='submit' value='Update Job'>
                    </td>
                </tr>
            </table>
        </form>
        <br>
        <a href='EmployerDashboard.php'>Back</a>
    </body>
</html>
