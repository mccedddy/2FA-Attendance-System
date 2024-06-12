<?php
require 'database_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Check if dataArray is set in the POST data
  if (isset($_POST['dataArray'])) {
    $dataArray = json_decode($_POST['dataArray'], true);

    // Loop through each row of dataArray
    foreach ($dataArray as $row) {
      $name = $row[0];
      $idNumber = $row[1];
      $status = $row[2];
      $time = $row[3];
      $date = $row[4];
      $room = $row[5];
      $subjectName = $row[6];
      $professor = $row[7];

      // Split professor name into first name and last name
      list($lastName, $firstName) = explode(', ', $professor);

      // Get subject code using subject name
      $subjectQuery = $connection->prepare("SELECT subject_code FROM subjects WHERE subject_name = '$subjectName'");
      $subjectQuery->execute();
      $subjectResult = $subjectQuery->get_result();

      if ($subjectResult->num_rows > 0) {
        $subjectRow = $subjectResult->fetch_assoc();
        $subjectCode = $subjectRow['subject_code'];

        // Get professor ID using last name and first name
        $professorQuery = $connection->prepare("SELECT id_number FROM professors WHERE last_name = '$lastName' AND first_name = '$firstName'");
        $professorQuery->execute();
        $professorResult = $professorQuery->get_result();

        if ($professorResult->num_rows > 0) {
          $professorRow = $professorResult->fetch_assoc();
          $professorId = $professorRow['id_number'];

          // Get the day of the week from the date
          $dayOfWeek = date('l', strtotime($date));

          // Get schedule ID using subject code, professor ID, and day of the week
          $scheduleQuery = $connection->prepare("SELECT id FROM schedule WHERE subject_code = '$subjectCode' AND professor = '$professorId' AND day = '$dayOfWeek'");
          $scheduleQuery->execute();
          $scheduleResult = $scheduleQuery->get_result();

          if ($scheduleResult->num_rows > 0) {
            $scheduleRow = $scheduleResult->fetch_assoc();
            $scheduleId = $scheduleRow['id'];

            // Insert attendance data into the attendance table
            $attendanceQuery = $connection->prepare("INSERT INTO attendance (id_number, room, time, date, status, schedule_id, verified) VALUES ('$idNumber', '$room', '$time', '$date', '$status', '$scheduleId', '1')");
            $attendanceQuery->execute();
          } else {
            // Respond with an error if no matching schedule is found
            echo json_encode(['status' => 'error', 'message' => 'No matching schedule found']);
            exit;
          }
        } else {
          // Respond with an error if no matching professor is found
          echo json_encode(['status' => 'error', 'message' => 'No matching professor found']);
          exit;
        }
      } else {
        // Respond with an error if no matching subject is found
        echo json_encode(['status' => 'error', 'message' => 'No matching subject found']);
        exit;
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
$connection->close();
?>