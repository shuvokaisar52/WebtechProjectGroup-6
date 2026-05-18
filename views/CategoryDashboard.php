<?php
include "../controllers/CategoryController.php";
echo "<h1>Category Dashboard</h1><br>";
$message = $_GET["message"] ?? "";
$error = $_GET["error"] ?? "";
$username = $_SESSION["username"] ?? ($_SESSION["name"] ?? "");
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="css/employer.css">
    </head>
    <body>
        <?php echo "Hello ".$username; ?>
        <br>
        <a href="../controllers/Logout.php">Logout</a>
        <br>
        <?php echo $message; ?>
        <?php echo $error; ?>
        <br><br>
        <a href='AddCategory.php'>Add Category</a>
        <br><br>
        <table border='1'>
            <tr>
                <td>ID</td>
                <td>Name</td>
                <td>Edit</td>
                <td>Delete</td>
            </tr>
            <?php foreach($categories as $row){ ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><a href='EditCategory.php?id=<?php echo $row['id']; ?>'>Edit</a></td>
                <td>
                    <form method='post' action='../controllers/DeleteCategoryController.php'>
                        <input type='hidden' name='id' value='<?php echo $row['id']; ?>'>
                        <input type='submit' value='Delete'>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </table>
    </body>
</html>
