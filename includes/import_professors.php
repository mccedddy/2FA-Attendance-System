<?php
require 'database_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  require_once 'encryption.php';
  $encryptionHelper = new EncryptionHelper($encryptionKey);
  
  // Check if dataArray is set in the POST data
  if (isset($_POST['dataArray'])) {
    $dataArray = json_decode($_POST['dataArray'], true);

    // Loop through each row of dataArray
    foreach ($dataArray as $row) {
      $lastName = $row[0];
      $firstName = $row[1];
      $idNumber = $row[2];
      $email = $row[3];

      // Hash the password (Default: Last Name)
      $hashedPassword = password_hash($lastName, PASSWORD_DEFAULT);

      // Encrypt email
      $encryptedEmail = $encryptionHelper->encryptData($email);

      // Check if student with the same student_number already exists
      $checkProfessorSQL = "SELECT COUNT(*) as professorCount FROM professors WHERE id_number = '$idNumber'";
      $stmtCheckProfessor = mysqli_prepare($connection, $checkProfessorSQL);
      mysqli_stmt_execute($stmtCheckProfessor);
      mysqli_stmt_bind_result($stmtCheckProfessor, $professorCount);
      mysqli_stmt_fetch($stmtCheckProfessor);
      mysqli_stmt_close($stmtCheckProfessor);

      if ($professorCount == 0) {
        // Insert the student into the students table
        if ($lastName && $firstName && $idNumber && $email) {
          $insertProfessorSQL = "INSERT INTO professors (last_name, first_name, id_number, email, password) VALUES ('$lastName', '$firstName', '$idNumber', '$encryptedEmail', '$hashedPassword')";
          $stmtInsertProfessor = mysqli_prepare($connection, $insertProfessorSQL);
          mysqli_stmt_execute($stmtInsertProfessor);
          mysqli_stmt_close($stmtInsertProfessor);
        }
      }
    }

    // Respond with a success message
    echo json_encode(['status' => 'success', 'message' => 'Import successful']);
  } else {
    // Respond with an error message if dataArray is not set
    echo json_encode(['status' => 'error', 'message' => 'dataArray not set in the request']);
  }
} else {
  // Respond with an error message if the request method is not POST
  echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

// Close the database connection
mysqli_close($connection);
?>
