<?php
// Database details
$db_host = 'localhost';
$db_user = 'admin';
$db_password = 'admin';
$db_name = 'pup_hdf_attendance_system';

// Connect to database
$connection = mysqli_connect($db_host, $db_user, $db_password, $db_name);

// Check connection
if (!$connection) {
    die('Connection error: ' . mysqli_connect_error());
}
?>
