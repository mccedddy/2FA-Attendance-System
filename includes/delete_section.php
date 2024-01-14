<?php 
session_start();
require 'database_connection.php';

// Check if the 'date' key is set in the $_POST array
if (isset($_POST['selectedSection'])) {
    $selectedSection = $_POST['selectedSection'];

    // Use prepared statement to prevent SQL injection
    $deleteStudentsSQL = "DELETE FROM students WHERE section = '$selectedSection'";
    $deleteSectionSQL = "DELETE FROM cpe WHERE section = '$selectedSection'";
    
    // Prepare and execute the statement
    $stmtStudent = mysqli_prepare($connection, $deleteStudentsSQL);
    $stmtSection = mysqli_prepare($connection, $deleteSectionSQL);
    $successStudent = mysqli_stmt_execute($stmtStudent);
    $successSection = mysqli_stmt_execute($stmtSection);

    // Close the statements
    mysqli_stmt_close($stmtStudent);
    mysqli_stmt_close($stmtSection);

    // Check if both deletions were successful
    if ($successStudent && $successSection) {
        // Respond with a success message
        echo json_encode(['status' => 'success', 'message' => 'Deletion successful']);
    } else {
        // Respond with an error message
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . mysqli_error($connection)]);
    }

} else {
    // If 'timestamp' is not set, return an error response
    echo json_encode(['error' => 'Timestamp not set in the request.']);
}
?>
