<?php 
session_start();
require 'database_connection.php';

// Check if the 'date' key is set in the $_POST array
if (isset($_POST['selectedSection'])) {
    $selectedSection = $_POST['selectedSection'];

    // Use prepared statement to prevent SQL injection
    $deleteFeaturesSQL = "DELETE f FROM features f INNER JOIN students s ON f.id_number = s.id_number WHERE s.section = '$selectedSection'";
    $deleteStudentsSQL = "DELETE FROM students WHERE section = '$selectedSection'";
    $deleteSectionSQL = "DELETE FROM sections WHERE section = '$selectedSection'";
    
    // Prepare the statements
    $stmtFeatures = mysqli_prepare($connection, $deleteFeaturesSQL);
    $stmtStudents = mysqli_prepare($connection, $deleteStudentsSQL);
    $stmtSection = mysqli_prepare($connection, $deleteSectionSQL);

    // Execute the statements
    $successFeatures = mysqli_stmt_execute($stmtFeatures);
    $successStudents = mysqli_stmt_execute($stmtStudents);
    $successSection = mysqli_stmt_execute($stmtSection);

    // Close the statements
    mysqli_stmt_close($stmtFeatures);
    mysqli_stmt_close($stmtStudents);
    mysqli_stmt_close($stmtSection);

    // Check if both deletions were successful
    if ($successFeatures && $successStudents && $successSection) {
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
