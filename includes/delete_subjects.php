<?php

if (isset($_POST['subjectCodes'])) {
  require 'database_connection.php';
  $subjectCodes = $_POST['subjectCodes'];

  //  Use prepared statement to prevent SQL injection
  $placeholders = implode("','", $subjectCodes);
  $deleteStudentSQL = "DELETE FROM subjects WHERE subject_code IN ('$placeholders')";

  // Prepare and execute the statement
  $stmtSubjects = mysqli_prepare($connection, $deleteStudentSQL);
  $success = mysqli_stmt_execute($stmtSubjects);

  // Close the statement
  mysqli_stmt_close($stmtSubjects);

  // Respond with a success or error message
  if ($success) {
    echo json_encode(['status' => 'success', 'message' => 'Deletion successful']);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . mysqli_error($connection)]);
  }

  // Close database connection
  mysqli_close($connection);
} else {
  echo json_encode(['error' => 'No subject codes numbers provided.']);
}
?>
