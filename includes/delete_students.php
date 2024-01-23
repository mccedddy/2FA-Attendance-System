<?php

if (isset($_POST['studentNumbers'])) {
   require 'database_connection.php';
   $studentNumbers = $_POST['studentNumbers'];

  //  Use prepared statement to prevent SQL injection
  $placeholders = implode("','", $studentNumbers);
  $deleteAttendanceSQL = "DELETE FROM attendance WHERE student_number IN ('$placeholders')";
  $deleteHdfSQL = "DELETE FROM hdf WHERE student_number IN ('$placeholders')";
  $deleteStudentSQL = "DELETE FROM students WHERE student_number IN ('$placeholders')";

  // Prepare and execute the statement
  $stmtAttendance = mysqli_prepare($connection, $deleteAttendanceSQL);
  $stmtHdf = mysqli_prepare($connection, $deleteHdfSQL);
  $stmtStudent = mysqli_prepare($connection, $deleteStudentSQL);
  mysqli_stmt_execute($stmtAttendance);
  mysqli_stmt_execute($stmtHdf);
  $success = mysqli_stmt_execute($stmtStudent);

  // Close the statement
  mysqli_stmt_close($stmt);

  // Respond with a success or error message
  if ($success) {
    echo json_encode(['status' => 'success', 'message' => 'Deletion successful']);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . mysqli_error($connection)]);
  }

  // Close database connection
  mysqli_close($connection);
} else {
  echo json_encode(['error' => 'No student numbers provided.']);
}
?>
