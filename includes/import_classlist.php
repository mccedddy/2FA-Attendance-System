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
      $section = $row[3];
      $nfcUid = $row[4];
      $email = $row[5];

      // Hash the password (Default: Last Name)
      $hashedPassword = password_hash($lastName, PASSWORD_DEFAULT);

      // Encrypt email
      $encryptedEmail = $encryptionHelper->encryptData($email);

      // Check if student with the same id_number already exists
      $checkStudentSQL = "SELECT COUNT(*) as studentCount FROM students WHERE id_number = '$idNumber'";
      $stmtCheckStudent = mysqli_prepare($connection, $checkStudentSQL);
      mysqli_stmt_execute($stmtCheckStudent);
      mysqli_stmt_bind_result($stmtCheckStudent, $studentCount);
      mysqli_stmt_fetch($stmtCheckStudent);
      mysqli_stmt_close($stmtCheckStudent);

      if ($studentCount == 0) {
        // Insert the student into the students table
        $insertStudentSQL = "INSERT INTO students (last_name, first_name, id_number, section, nfc_uid, email, password) VALUES ('$lastName', '$firstName', '$idNumber', '$section', '$nfcUid', '$encryptedEmail', '$hashedPassword')";
        $stmtInsertStudent = mysqli_prepare($connection, $insertStudentSQL);
        mysqli_stmt_execute($stmtInsertStudent);
        mysqli_stmt_close($stmtInsertStudent);
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
