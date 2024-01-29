<?php
date_default_timezone_set('Asia/Manila');

if (isset($_POST['UIDresult'])) {
  require '../includes/database_connection.php';

  $uid = $_POST['UIDresult'];
  $time = date("H:i:s");
  $date = date('Y-m-d');

  // SQL query to fetch student data
  $studentSQL = "SELECT last_name, student_number, section, nfc_uid FROM students WHERE nfc_uid = '$uid'";
  $studentStmt = mysqli_prepare($connection, $studentSQL);
  mysqli_stmt_execute($studentStmt);
  $result = mysqli_stmt_get_result($studentStmt);

  // If no student found
  if ($result == NULL || mysqli_num_rows($result) == 0) {
    echo json_encode(['studentData' => ['last_name' => 'none', 'student_number' => 'none', 'nfc_uid' => $uid]]);
    exit;
  }

  // Get student data
  $studentData = mysqli_fetch_assoc($result);
  $studentNumber = $studentData['student_number'];
  mysqli_free_result($result);

  // Response: Student Data
  echo json_encode(['studentData' => $studentData]);

  // SQL query to retrieve HDF data
  $hdfSQL = "SELECT * 
             FROM hdf 
             WHERE student_number = '$studentNumber'
             AND DATE(timestamp) = '$date'";
  $hdfStmt = mysqli_prepare($connection, $hdfSQL);
  mysqli_stmt_execute($hdfStmt);
  $result = mysqli_stmt_get_result($hdfStmt);

  if ($result && mysqli_num_rows($result) > 0) {
    $hdfData = mysqli_fetch_assoc($result);
    $hdfId = $hdfData["id"];

    $hdfSQL = "UPDATE hdf SET verified = 'true' WHERE id = $hdfId";
    $hdfStmt = mysqli_prepare($connection, $hdfSQL);
    mysqli_stmt_execute($hdfStmt);

    // Response: HDF Data
    echo json_encode(['hdf' => $hdfData]);
    exit;
  } else {
    echo json_encode(['hdf' => ['score' => -1]]);
  }
}
?>
