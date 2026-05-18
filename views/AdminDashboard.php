<?php
include "../controllers/AdminController.php";
echo "<h1>Admin Dashboard</h1><br>";
?>

<!DOCTYPE html>
<html>
    <body>
        <?php echo "Hello Admin ".$username; ?>

        <br><br>

        <table>
            <tr>
                <td>
                    <form method="get" action="CategoryDashboard.php">
                        <input type="submit" value="Category Dashboard">
                    </form>
                </td>
            </tr>

            <tr>
                <td>
                    <form method="get" action="CategoryDashboard.php">
                        <input type="submit" value="Update Category">
                    </form>
                </td>
            </tr>

            <tr>
                <td>
                    <form method="get" action="../controllers/Logout.php">
                        <input type="submit" value="Logout">
                    </form>
                </td>
            </tr>
        </table>
    </body>
</html>