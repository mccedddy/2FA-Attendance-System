<?php
require 'database_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Check if dataArray is set in the POST data
  if (isset($_POST['dataArray'])) {
    $dataArray = json_decode($_POST['dataArray'], true);

    // Loop through each row of dataArray
    foreach ($dataArray as $row) {
      $subjectCode = $row[0];
      $subjectName = $row[1];

      // Check if subject with the same subject code already exists
      $checkSubjectSQL = "SELECT COUNT(*) as subjectCount FROM subjects WHERE subject_code = '$subjectCode'";
      $stmtCheckSubject = mysqli_prepare($connection, $checkSubjectSQL);
      mysqli_stmt_execute($stmtCheckSubject);
      mysqli_stmt_bind_result($stmtCheckSubject, $subjectCount);
      mysqli_stmt_fetch($stmtCheckSubject);
      mysqli_stmt_close($stmtCheckSubject);

      if ($subjectCount == 0) {
        // Insert the student into the students table
        $insertSubjectSQL = "INSERT INTO subjects (subject_code, subject_name) VALUES ('$subjectCode', '$subjectName')";
        $stmtInsertSubject = mysqli_prepare($connection, $insertSubjectSQL);
        mysqli_stmt_execute($stmtInsertSubject);
        mysqli_stmt_close($stmtInsertSubject);
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
