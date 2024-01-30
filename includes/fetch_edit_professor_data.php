<?php
if (isset($_POST['studentNumber'])) {
  require 'database_connection.php'; 
  require_once 'encryption.php';
  $encryptionHelper = new EncryptionHelper($encryptionKey);

  $idNumber = $_POST['studentNumber'];

  // Use a prepared statement to prevent SQL injection
  $sql = "SELECT * FROM professors WHERE id_number = '$idNumber'";
  $stmt = mysqli_prepare($connection, $sql);
  mysqli_stmt_execute($stmt);

  // Get the result
  $result = mysqli_stmt_get_result($stmt);

  // Fetch the student data
  $studentData = mysqli_fetch_assoc($result);
  $decryptedEmail = $encryptionHelper->decryptData($studentData['email']);
  $studentData['email'] = $decryptedEmail;

  // Close the statement and connection
  mysqli_stmt_close($stmt);
  mysqli_close($connection);

  // Return the student data as JSON
  echo json_encode($studentData);
} else {
  echo json_encode(['error' => 'No student number provided.']);
}
?>