<?php
require 'database_connection.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Loop through the posted data
    foreach ($_POST as $questionName => $answer) {
        // Convert array to string
        if ($questionName == 'q2') {
            $answer = implode(", ", $_POST['q2']);
        }

        // Remove +
        if ($answer && $answer[0] == '+') {
            $answer = substr($answer, 1);
        }

        // Output the question name and answer
        echo $questionName . ": " . $answer . "<br>";
    }

    // You can also use this data to insert into the database if needed
    // For example, you can construct an SQL query to insert the data
    // Make sure to validate and sanitize the data before inserting it into the database
}
?>
