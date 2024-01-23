<?php
if (isset($_POST['subjectCode'])) {
  require 'database_connection.php'; 

  $subjectCode = $_POST['subjectCode'];

  // Use a prepared statement to prevent SQL injection
  $sql = "SELECT * FROM subjects WHERE subject_code = '$subjectCode'";
  $stmt = mysqli_prepare($connection, $sql);
  mysqli_stmt_execute($stmt);

  // Get the result
  $result = mysqli_stmt_get_result($stmt);

  // Fetch the subject data
  $subjectData = mysqli_fetch_assoc($result);

  // Close the statement and connection
  mysqli_stmt_close($stmt);
  mysqli_close($connection);

  // Return the subject data as JSON
  echo json_encode($subjectData);
} else {
  echo json_encode(['error' => 'No subject code provided.']);
}
?>