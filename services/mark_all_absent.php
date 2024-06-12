<?php
date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['schedule_id']) && isset($_POST['date'])) {
    require '../includes/database_connection.php';

    $scheduleId = $_POST['schedule_id'];
    $date = $_POST['date'];

    // Open a log file
    $logFile = fopen("mark_all_absent.log", "a");
    fwrite($logFile, "Script started at " . date('Y-m-d H:i:s') . "\n");

    // Fetch the section for the schedule
    $query = "SELECT end_time, section FROM schedule WHERE id = '$scheduleId'";
    $result = mysqli_query($connection, $query);
    if ($result === false) {
        fwrite($logFile, "Error fetching schedule: " . mysqli_error($connection) . "\n");
        fclose($logFile);
        exit;
    }
    $row = mysqli_fetch_assoc($result);
    $endTime = $row['end_time'];
    $section = $row['section'];

    // Fetch all students in the section
    $studentQuery = "SELECT id_number FROM students WHERE section = '$section'";
    $studentResult = mysqli_query($connection, $studentQuery);
    $students = mysqli_fetch_all($studentResult, MYSQLI_ASSOC);

    foreach ($students as $student) {
        $studentNumber = $student['id_number'];

        // Check if the student has already been marked present or late
        $checkAttendanceSQL = "SELECT * FROM attendance WHERE id_number = '$studentNumber' AND date = '$date' AND schedule_id = '$scheduleId'";
        $checkAttendanceStmt = mysqli_prepare($connection, $checkAttendanceSQL);
        mysqli_stmt_execute($checkAttendanceStmt);
        $existingAttendance = mysqli_stmt_get_result($checkAttendanceStmt);

        if (mysqli_num_rows($existingAttendance) == 0) {
            // Insert absent entry
            $insertAttendanceSQL = "INSERT INTO attendance (id_number, room, time, date, status, schedule_id)
                VALUES ('$studentNumber', 'N/A', '$endTime', '$date', 'Absent', '$scheduleId')";
            mysqli_query($connection, $insertAttendanceSQL);
            if (mysqli_error($connection)) {
                fwrite($logFile, "Error inserting attendance for student $studentNumber: " . mysqli_error($connection) . "\n");
            }
        }

        mysqli_free_result($existingAttendance);
    }

    // Write end message
    fwrite($logFile, "Script ended at " . date('Y-m-d H:i:s') . "\n");

    // Close the log file
    fclose($logFile);

    echo json_encode(['status' => 'Success', 'message' => 'All absent students have been marked.']);
} else {
    echo json_encode(['status' => 'Error', 'message' => 'Invalid request.']);
}
?>
