<?php
session_start();
require '../includes/database_connection.php';
$userId = '';
$error_message = '';

// If logged in
if (isset($_SESSION['id_number'])) {
  // Redirect to professor homepage
  header("Location: professor_home.php");
}
if (isset($_SESSION['student_number'])) {
  // Redirect to professor homepage
  header("Location: student_homepage.php");
}

// Check if the form is submitted
if (isset($_POST['login'])) {
  // Retrieve the values from the form
  $userId = $_POST['id-number'];
  $password = $_POST['password'];

  // Connect to database
  require '../includes/database_connection.php';

  // SQL query
  $sql = "SELECT * FROM professors WHERE id_number = '$userId'";
  $result = mysqli_query($connection, $sql);

  // Check if the query was successful
  if ($result) {
    $professors = mysqli_fetch_assoc($result);

    // Check the password
    if ($professors && password_verify($password, $professors['password'])) {
      $_SESSION['id_number'] = $userId;
      if ($userId == 'admin') {
        header("Location: admin_home.php");
      } else {
        header("Location: professor_home.php");
      }
      exit();
    } else {
      $error_message = 'ID number or password is incorrect!';
    }
  }
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>2FA Attendance System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,700;1,400;1,700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../css/global.css" />
    <link rel="stylesheet" href="../css/login.css" />
  </head>
  <body>
    <section class="main">
      <h1>PROFESSOR LOGIN</h1>
      <form method="POST" class="login-form">
        <div class="login-textbox-container">
          <img
            src="../assets/images/icons/person.svg"
            class="textbox-icon"
            alt="person"
          />
          <input
            type="text"
            class="large-textbox"
            name="id-number"
            value="<?php echo htmlspecialchars($userId); ?>"
            placeholder="ID Number"
          />
        </div>
        <div class="login-textbox-container">
          <img
            src="../assets/images/icons/lock.svg"
            class="textbox-icon"
            alt="lock"
          />
          <input
            type="password"
            class="large-textbox"
            name="password"
            placeholder="Password"
          />
        </div>
        <p class="error-message"><?php echo $error_message ?></p>
        <button type="submit" name="login" class="large-button">LOGIN</button>
      </form>
      <a href="forgot_password.php"> Forgot your password? </a>
    </section>
    <section class="graphics">
      <img src="../assets/images/graphics/professor.png" alt="professor" />
    </section>
  </body>
</html>
