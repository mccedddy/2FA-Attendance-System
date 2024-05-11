<?php

if (isset($_POST['idNumbers'])) {
   require 'database_connection.php';
   $idNumbers = $_POST['idNumbers'];

  //  Use prepared statement to prevent SQL injection
  $placeholders = implode("','", $idNumbers);
  $deleteAttendanceSQL = "DELETE FROM attendance WHERE id_number IN ('$placeholders')";
  $deleteStudentSQL = "DELETE FROM students WHERE id_number IN ('$placeholders')";

  // Prepare and execute the statement
  $stmtAttendance = mysqli_prepare($connection, $deleteAttendanceSQL);
  $stmtStudent = mysqli_prepare($connection, $deleteStudentSQL);
  mysqli_stmt_execute($stmtAttendance);
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
