<?php
session_start();
require '../includes/database_connection.php';

// Check if the form is submitted
if (isset($_POST['register'])) {
    // Retrieve the values from the form
    $lastName = $_POST['last_name'];
    $firstName = $_POST['first_name'];
    $idNumber = $_POST['id_number'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // SQL query to insert data into the professorss table
    $sql = "INSERT INTO professors (last_name, first_name, id_number, email, password)
            VALUES ('$lastName', '$firstName', '$idNumber', '$email', '$hashedPassword')";
    
    // Use prepared statement to prevent SQL injection
    $stmt = mysqli_prepare($connection, $sql);
    // mysqli_stmt_bind_param($stmt, 'sssssss', $lastName, $firstName, $idNumber, $section, $nfcUid, $email, $hashedPassword);
    
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

    <h2>Professor Registration</h2>

    <?php if (isset($error_message)) : ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" required><br>

        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" required><br>

        <label for="id_number">ID Number:</label>
        <input type="text" name="id_number" required><br>

        <label for="email">Email:</label>
        <input type="email" name="email" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br>

        <button type="submit" name="register">Register</button>
    </form>

</body>
</html>
