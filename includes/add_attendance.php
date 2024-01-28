<?php
if (isset($_POST['student'])) {
  require 'database_connection.php';
  $studentNumber = $_POST['student'];
  $status = $_POST['status'];
  $time = $_POST['time'];
  $date = $_POST['date'];
  $room = $_POST['room'];
  $subjectCode = $_POST['subject'];
  $idNumber = $_POST['professor'];
  $section = $_POST['section'];

  $dayOfWeek = date('l', strtotime($date));

  // SQL query to retrieve the schedule id
  $sql = "SELECT * 
          FROM schedule 
          WHERE subject_code = '$subjectCode' 
          AND section = '$section' 
          AND professor = '$idNumber'
          AND day = '$dayOfWeek'
          AND '$time' BETWEEN DATE_SUB(start_time, INTERVAL 1 HOUR) AND end_time";

  $stmt = mysqli_prepare($connection, $sql);
  mysqli_stmt_execute($stmt);

  // Execute query and get the result
  $result = mysqli_stmt_get_result($stmt);
  $scheduleData = mysqli_fetch_assoc($result);
  echo json_encode(['scheduleData' => $scheduleData]);

  if ($result && mysqli_num_rows($result) > 0) {
    $scheduleId = $scheduleData['id'];

    // SQL query to insert data into the attendance table
    $sql_insert = "INSERT INTO attendance (student_number, room, time, date, status, schedule_id)
                   VALUES ('$studentNumber', '$room', '$time', '$date', '$status', '$scheduleId')";

    // Execute insert query
    mysqli_query($connection, $sql_insert);

  } else {
    exit;
  }
}
?>