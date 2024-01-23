<?php
if (isset($_POST['schedule'])) {
  require 'database_connection.php'; 

  $schedule = $_POST['schedule'];

  // Use a prepared statement to prevent SQL injection
  $sql = "SELECT * FROM schedule WHERE id = '$schedule'";
  $stmt = mysqli_prepare($connection, $sql);
  mysqli_stmt_execute($stmt);

  // Get the result
  $result = mysqli_stmt_get_result($stmt);

  // Fetch the subject data
  $scheduleData = mysqli_fetch_assoc($result);

  // Close the statement and connection
  mysqli_stmt_close($stmt);
  mysqli_close($connection);

  // Return the subject data as JSON
  echo json_encode($scheduleData);
} else {
  echo json_encode(['error' => 'No schedule provided.']);
}
?>