<?php

if (isset($_POST['studentNumbers'])) {
   require 'database_connection.php';
   $idNumbers = $_POST['studentNumbers'];

  //  Use prepared statement to prevent SQL injection
  $placeholders = implode("','", $idNumbers);
  $deleteStudentSQL = "DELETE FROM professors WHERE id_number IN ('$placeholders')";

  // Prepare and execute the statement
  $stmtProfessors = mysqli_prepare($connection, $deleteStudentSQL);
  $success = mysqli_stmt_execute($stmtProfessors);

  // Close the statement
  mysqli_stmt_close($stmtProfessors);

  // Respond with a success or error message
  if ($success) {
    echo json_encode(['status' => 'success', 'message' => 'Deletion successful']);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . mysqli_error($connection)]);
  }

  // Close database connection
  mysqli_close($connection);
} else {
  echo json_encode(['error' => 'No ID numbers provided.']);
}
?>
