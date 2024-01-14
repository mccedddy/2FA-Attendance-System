<?php
require 'database_connection.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Convert array to string
    $q2string = implode(", ", $_POST['q2']);

    $studentNumber = $_SESSION['student_number'];
    $q1 = mysqli_real_escape_string($connection, $_POST['q1']);
    $q2 = mysqli_real_escape_string($connection, $q2string);
    $q3 = mysqli_real_escape_string($connection, $_POST['q3']);
    $q4 = mysqli_real_escape_string($connection, $_POST['q4']);
    $q5 = mysqli_real_escape_string($connection, $_POST['q5']);
    $q6a = mysqli_real_escape_string($connection, $_POST['q6a']);
    $q6b = mysqli_real_escape_string($connection, $_POST['q6b']);
    $q7a = mysqli_real_escape_string($connection, $_POST['q7a']);
    $q7b = mysqli_real_escape_string($connection, $_POST['q7b']);
    $q8a = mysqli_real_escape_string($connection, $_POST['q8a']);
    $q8b = mysqli_real_escape_string($connection, $_POST['q8b']);
    $q8c = mysqli_real_escape_string($connection, $_POST['q8c']);
    $q8d = mysqli_real_escape_string($connection, $_POST['q8d']);

    // Insert to database
    $sql = "INSERT INTO hdf (student_number, q1, q2, q3, q4, q5, q6a, q6b, q7a, q7b, q8a, q8b, q8c, q8d) VALUES ('$studentNumber', '$q1', '$q2', '$q3', '$q4', '$q5', '$q6a', '$q6b', '$q7a', '$q7b', '$q8a', '$q8b', '$q8c', '$q8d')";
    mysqli_query($connection, $sql);
}
?>
