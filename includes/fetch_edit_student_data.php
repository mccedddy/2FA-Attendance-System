<?php
if (isset($_POST['studentNumber'])) {
  require 'database_connection.php'; 

  $studentNumber = $_POST['studentNumber'];

  // Use a prepared statement to prevent SQL injection
  $sql = "SELECT * FROM students WHERE student_number = '$studentNumber'";
  $stmt = mysqli_prepare($connection, $sql);
  mysqli_stmt_execute($stmt);

  // Get the result
  $result = mysqli_stmt_get_result($stmt);

  // Fetch the student data
  $studentData = mysqli_fetch_assoc($result);

  // Close the statement and connection
  mysqli_stmt_close($stmt);
  mysqli_close($connection);

  // Return the student data as JSON
  echo json_encode($studentData);
} else {
  echo json_encode(['error' => 'No student number provided.']);
}
?>