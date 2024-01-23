<?php

if (isset($_POST['schedules'])) {
  require 'database_connection.php';
  $schedules = $_POST['schedules'];

  //  Use prepared statement to prevent SQL injection
  $placeholders = implode("','", $schedules);
  $deleteScheduleSQL = "DELETE FROM schedule WHERE id IN ('$placeholders')";

  // Prepare and execute the statement
  $stmtSchedule = mysqli_prepare($connection, $deleteScheduleSQL);
  $success = mysqli_stmt_execute($stmtSchedule);

  // Close the statement
  mysqli_stmt_close($stmtSchedule);

  // Respond with a success or error message
  if ($success) {
    echo json_encode(['status' => 'success', 'message' => 'Deletion successful']);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . mysqli_error($connection)]);
  }

  // Close database connection
  mysqli_close($connection);
} else {
  echo json_encode(['error' => 'No schedule provided.']);
}
?>
