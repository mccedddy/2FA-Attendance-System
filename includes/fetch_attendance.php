<?php 
session_start();
require 'database_connection.php';

// Check if the 'date' key is set in the $_POST array
if (isset($_POST['date'])) {
  $date = $_POST['date'];
  $subject = $_POST['subject'];
  $selectedSection = $_SESSION['selected_section'];

  // Use prepared statement to prevent SQL injection
  if ($subject == 'ALL') {
    $sql = "SELECT a.id_number, a.room, a.schedule_id, TIME_FORMAT(a.time, '%H:%i') AS time, a.date, s.last_name AS student_last_name, s.first_name AS student_first_name, a.status, p.last_name AS professor_last_name, p.first_name AS professor_first_name, sub.subject_name AS subject_name
          FROM attendance a
          INNER JOIN students s ON a.id_number = s.id_number
          INNER JOIN schedule sch ON a.schedule_id = sch.id
          INNER JOIN subjects sub ON sch.subject_code = sub.subject_code
          LEFT JOIN professors p ON sch.professor = p.id_number
          WHERE a.date = '$date' AND s.section = '$selectedSection' 
          ORDER BY s.last_name";
  } else {
    $sql = "SELECT a.id_number, a.room, a.schedule_id, TIME_FORMAT(a.time, '%H:%i') AS time, a.date, s.last_name AS student_last_name, s.first_name AS student_first_name, a.status, p.last_name AS professor_last_name, p.first_name AS professor_first_name, sub.subject_name AS subject_name
          FROM attendance a
          INNER JOIN students s ON a.id_number = s.id_number
          INNER JOIN schedule sch ON a.schedule_id = sch.id
          INNER JOIN subjects sub ON sch.subject_code = sub.subject_code
          LEFT JOIN professors p ON sch.professor = p.id_number
          WHERE a.date = '$date' AND s.section = '$selectedSection' AND sub.subject_code = '$subject'
          ORDER BY s.last_name";
  }

  // Prepare and execute the statement
  $stmt = mysqli_prepare($connection, $sql);
  mysqli_stmt_execute($stmt);

  // Get the result
  $result = mysqli_stmt_get_result($stmt);

  if (!$result) {
    echo json_encode(['error' => 'Query error.']);
  } else {
    // Fetch the data as an associative array
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Format the name of the student and professor
    foreach ($data as &$row) {
      $row['student_name'] = $row['student_last_name'] . ', ' . $row['student_first_name'];
      $row['professor_name'] = $row['professor_last_name'] ? $row['professor_last_name'] . ', ' . $row['professor_first_name'] : 'N/A';
      // Remove unnecessary columns
      unset($row['student_last_name']);
      unset($row['student_first_name']);
      unset($row['professor_last_name']);
      unset($row['professor_first_name']);

      // Reorder the fields
      $row = ['student_name' => $row['student_name'], 'professor_name' => $row['professor_name']] + $row;
    }
    unset($row); // Unset the reference

    // Return the modified data as JSON
    echo json_encode($data);
  }

  // Close the statement
  mysqli_stmt_close($stmt);

} else {
  // If 'date' is not set, return an error response
  echo json_encode(['error' => 'Date not set in the request.']);
}
?>
