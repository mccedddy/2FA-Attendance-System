<?php
require 'database_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if dataArray is set in the POST data
    if (isset($_POST['dataArray'])) {
        $dataArray = json_decode($_POST['dataArray'], true);

        // Loop through each row of dataArray
        foreach ($dataArray as $row) {
            $section = $row[6];
            $subjectCode = $row[0];
            $day = $row[2];
            $startTime = $row[3];
            $endTime = $row[4];
            $professorName = $row[5];

            // Split the professor name into last name and first name
            list($lastName, $firstName) = explode(', ', $professorName);

            // Check if the professor with the given name exists
            $getProfessorIdSQL = "SELECT id_number FROM professors WHERE last_name = '$lastName' AND first_name = '$firstName'";
            $stmtGetProfessorId = mysqli_prepare($connection, $getProfessorIdSQL);
            mysqli_stmt_execute($stmtGetProfessorId);
            mysqli_stmt_bind_result($stmtGetProfessorId, $professorId);
            mysqli_stmt_fetch($stmtGetProfessorId);
            mysqli_stmt_close($stmtGetProfessorId);

            if ($professorId) {
                // Check if subject with the same subject code, day, and professor already exists
                $checkScheduleSQL = "SELECT COUNT(*) as scheduleCount FROM schedule WHERE section = '$section' AND subject_code = '$subjectCode' AND day = '$day' AND start_time = '$startTime' AND end_time = '$endTime' AND professor = '$professorId'";
                $stmtCheckSchedule = mysqli_prepare($connection, $checkScheduleSQL);
                mysqli_stmt_execute($stmtCheckSchedule);
                mysqli_stmt_bind_result($stmtCheckSchedule, $scheduleCount);
                mysqli_stmt_fetch($stmtCheckSchedule);
                mysqli_stmt_close($stmtCheckSchedule);

                if ($scheduleCount == 0) {
                    // Insert the schedule into the schedule table
                    $insertScheduleSQL = "INSERT INTO schedule (section, subject_code, day, start_time, end_time, professor) VALUES ('$section', '$subjectCode', '$day', '$startTime', '$endTime', '$professorId')";
                    $stmtInsertSchedule = mysqli_prepare($connection, $insertScheduleSQL);
                    mysqli_stmt_execute($stmtInsertSchedule);
                    mysqli_stmt_close($stmtInsertSchedule);
                }
            } else {
                // Handle the case where the professor with the given name was not found
                echo json_encode(['status' => 'error', 'message' => "Professor '$professorName' not found."]);
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
