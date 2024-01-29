<?php
// Database details
$db_host = 'localhost';
$db_user = 'puphas';
$db_password = 'Puphas-2024';
$db_name = 'puphas';

// Connect to database
$connection = mysqli_connect($db_host, $db_user, $db_password, $db_name);

// Check connection
if (!$connection) {
    die('Connection error: ' . mysqli_connect_error());
}
?>
