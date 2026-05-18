<?php
include "../Controller/EditCategoryController.php";
echo "<h1>Edit Category Page</h1><br>";
$error = $_GET["error"] ?? "";
?>
<!DOCTYPE html>
<html>
    <body>
        <?php echo $error; ?>
        <form method='post' action='../Controller/EditCategoryController.php'>
            <table>
                <tr>
                    <td>Category Name:</td>
                    <td>
                        <input type='hidden' name='id' value='<?php echo $category["id"]; ?>'>
                        <input type='text' name='name' value='<?php echo $category["name"]; ?>'>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type='submit' value='Update'></td>
                </tr>
            </table>
        </form>
        <br>
        <a href='CategoryDashboard.php'>Back</a>
    </body>
</html>
