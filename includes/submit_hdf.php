<?php
date_default_timezone_set('Asia/Manila');

require 'database_connection.php';
session_start();
$date = date('Y-m-d');
$studentNumber = $_SESSION['student_number'];

// SQL query to check if hdf exists
$hdfSQL = "SELECT * FROM hdf WHERE student_number = '$studentNumber' AND DATE(timestamp) = '$date'";
$hdfStmt = mysqli_prepare($connection, $hdfSQL);
mysqli_stmt_execute($hdfStmt);
$result = mysqli_stmt_get_result($hdfStmt);

if ($result && mysqli_num_rows($result) > 0) {
    $hdf = mysqli_fetch_assoc($result);
    $score = $hdf["score"];
    echo json_encode(['score' => $score]);
    exit;
}

// Check if the form was submitted
if (isset($_POST['score'])) {
    $q1 = mysqli_real_escape_string($connection, $_POST['q1']);
    $q2 = mysqli_real_escape_string($connection, $_POST['q2']);
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
    $score = mysqli_real_escape_string($connection, $_POST['score']);
    $verified = "false";

    // Insert to database
    $sql = "INSERT INTO hdf (student_number, q1, q2, q3, q4, q5, q6a, q6b, q7a, q7b, q8a, q8b, q8c, q8d, score, verified) VALUES ('$studentNumber', '$q1', '$q2', '$q3', '$q4', '$q5', '$q6a', '$q6b', '$q7a', '$q7b', '$q8a', '$q8b', '$q8c', '$q8d', '$score', '$verified')";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    echo json_encode(['status' => 'success']);
}
?>
