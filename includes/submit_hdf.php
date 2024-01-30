<?php
date_default_timezone_set('Asia/Manila');

require_once 'encryption.php';
$encryptionKey = "Puphas-20240201.";
$encryptionHelper = new EncryptionHelper($encryptionKey);

require 'database_connection.php';
session_start();
$date = date('Y-m-d');
$studentNumber = $_SESSION['student_number'];

// Fetch latest HDF data for the student
$sql = "SELECT * FROM hdf WHERE student_number = '$studentNumber' ORDER BY timestamp DESC LIMIT 1";

$result = mysqli_query($connection, $sql);
if ($result) {
  $hdfData = mysqli_fetch_assoc($result);
  mysqli_free_result($result);

  $q1DefaultValue = isset($hdfData['q1']) ? $hdfData['q1'] : '';
  $q2DefaultValue = isset($hdfData['q2']) ? $hdfData['q2'] : '';
  $q3DefaultValue = isset($hdfData['q3']) ? $hdfData['q3'] : '';
  $q4DefaultValue = isset($hdfData['q4']) ? $hdfData['q4'] : '';
  $q5DefaultValue = isset($hdfData['q5']) ? $hdfData['q5'] : '';
  $q6aDefaultValue = isset($hdfData['q6a']) ? $hdfData['q6a'] : '';
  $q6bDefaultValue = isset($hdfData['q6b']) ? $hdfData['q6b'] : '';
  $q7aDefaultValue = isset($hdfData['q7a']) ? $hdfData['q7a'] : '';
  $q7bDefaultValue = isset($hdfData['q7b']) ? $hdfData['q7b'] : '';
  $q8aDefaultValue = isset($hdfData['q8a']) ? $hdfData['q8a'] : '';
  $q8bDefaultValue = isset($hdfData['q8b']) ? $hdfData['q8b'] : '';
  $q8cDefaultValue = isset($hdfData['q8c']) ? $hdfData['q8c'] : '';
  $q8dDefaultValue = isset($hdfData['q8d']) ? $hdfData['q8d'] : '';

  $symptoms = explode(',', $q2DefaultValue);
  $symptomStatus = array_fill_keys($symptoms, true);
}

// SQL query to check if HDF for today exists
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
    $q7a = mysqli_real_escape_string($connection, $encryptionHelper->encryptData($_POST['q7a']));
    $q7b = mysqli_real_escape_string($connection, $encryptionHelper->encryptData($_POST['q7b']));
    $q8a = mysqli_real_escape_string($connection, $encryptionHelper->encryptData($_POST['q8a']));
    $q8b = mysqli_real_escape_string($connection, $encryptionHelper->encryptData($_POST['q8b']));
    $q8c = mysqli_real_escape_string($connection, $encryptionHelper->encryptData($_POST['q8c']));
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
