<?php 
require 'database_connection.php';

// Check if the 'date' key is set in the $_POST array
if (isset($_POST['date'])) {
    $date = $_POST['date'];

    // Use prepared statement to prevent SQL injection
    $sql = "SELECT a.student_number, a.room, TIME_FORMAT(a.time, '%H:%i'), a.date, s.last_name, s.first_name
            FROM attendance a
            INNER JOIN students s ON a.student_number = s.student_number
            WHERE a.date = '$date'";
    
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

        // Format the name as "Lastname, Firstname"
        foreach ($data as &$row) {
            $row['name'] = $row['last_name'] . ', ' . $row['first_name'];
            // Remove unnecessary columns
            unset($row['last_name']);
            unset($row['first_name']);

            // Reorder the fields
            $row = ['name' => $row['name']] + $row;
        }
        unset($row); // Unset the reference

        // Return the modified data as JSON
        echo json_encode($data);
    }

    // Close the statement
    mysqli_stmt_close($stmt);

} else {
    // If 'timestamp' is not set, return an error response
    echo json_encode(['error' => 'Timestamp not set in the request.']);
}
?>
