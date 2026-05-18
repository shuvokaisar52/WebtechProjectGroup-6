<?php
include "../controllers/CategoryController.php";
echo "<h1>Add Category Page</h1><br>";
$error = $_GET["error"] ?? "";
?>
<!DOCTYPE html>
<html>
    <body>
        <?php echo $error; ?>
        <form method='post' action='../controllers/CategoryController.php'>
            <table>
                <tr>
                    <td>Category Name:</td>
                    <td><input type='text' name='name'></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type='submit' value='Add Category'></td>
                </tr>
            </table>
        </form>
        <br>
        <a href='CategoryDashboard.php'>Back</a>
    </body>
</html>
