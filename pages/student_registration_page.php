<?php
session_start();
require '../includes/database_connection.php';

// Check if the form is submitted
if (isset($_POST['register'])) {
    // Retrieve the values from the form
    $lastName = $_POST['last_name'];
    $firstName = $_POST['first_name'];
    $studentNumber = $_POST['student_number'];
    $section = $_POST['section'];
    $nfcUid = $_POST['nfc_uid'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // SQL query to insert data into the students table
    $sql = "INSERT INTO students (last_name, first_name, student_number, section, nfc_uid, email, password)
            VALUES ('$lastName', '$firstName', '$studentNumber', '$section', '$nfcUid', '$email', '$hashedPassword')";
    
    // Use prepared statement to prevent SQL injection
    $stmt = mysqli_prepare($connection, $sql);
    // mysqli_stmt_bind_param($stmt, 'sssssss', $lastName, $firstName, $studentNumber, $section, $nfcUid, $email, $hashedPassword);
    
    // Execute the statement
    if (mysqli_stmt_execute($stmt)) {
        // Registration successful
        $error_message = 'Registration successful';
        // exit();
    } else {
        // Registration failed
        $error_message = 'Registration failed. Please try again.';
    }

    // Close the statement
    mysqli_stmt_close($stmt);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
</head>
<body>

    <h2>Student Registration</h2>

    <?php if (isset($error_message)) : ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" required><br>

        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" required><br>

        <label for="student_number">Student Number:</label>
        <input type="text" name="student_number" required><br>

        <label for="section">Section:</label>
        <input type="text" name="section" required><br>

        <label for="nfc_uid">NFC UID:</label>
        <input type="text" name="nfc_uid" required><br>

        <label for="email">Email:</label>
        <input type="email" name="email" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br>

        <button type="submit" name="register">Register</button>
    </form>

</body>
</html>
