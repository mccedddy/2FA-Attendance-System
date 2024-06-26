<?php

if (isset($_POST['attendanceIds'])) {
   require 'database_connection.php';
   $attendanceIds = $_POST['attendanceIds'];

  //  Use prepared statement to prevent SQL injection
  $placeholders = implode("','", $attendanceIds);
  $deleteAttendanceSQL = "DELETE FROM attendance WHERE id IN ('$placeholders')";

  // Prepare and execute the statement
  $stmtAttendance = mysqli_prepare($connection, $deleteAttendanceSQL);
  $success = mysqli_stmt_execute($stmtAttendance);

  // Close the statement
  mysqli_stmt_close($stmtAttendance);

  // Respond with a success or error message
  if ($success) {
    echo json_encode(['status' => 'success', 'message' => 'Deletion successful']);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . mysqli_error($connection)]);
  }

  // Close database connection
  mysqli_close($connection);
} else {
  echo json_encode(['error' => 'No attendance provided.']);
}
?>
