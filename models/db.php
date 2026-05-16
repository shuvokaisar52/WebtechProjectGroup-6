<?php
include "../config/db_config.php";

class db {

    function connection() {
        $connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if ($connection->connect_error) {
            die("Could not Connect Database: " . $connection->connect_error);
        }
        return $connection;
    }
}
?>
