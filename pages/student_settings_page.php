<?php
session_start();
require '../includes/database_connection.php';

// If logged in
if (isset($_SESSION['id_number'])) {
  // Redirect to professor homepage
  header("Location: professor_homepage.php");
}
if (isset($_SESSION['student_number'])) {
  $studentNumber = $_SESSION['student_number'];

  // SQL query
  $sql = "SELECT * FROM students WHERE student_number = '$studentNumber'";
  $result = mysqli_query($connection, $sql);

  // Check if the query was successful
  if ($result) {
    $student = mysqli_fetch_assoc($result);

    // Get student info
    if ($student) {
      $name = strtoupper($student['last_name']) . ', ' . strtoupper($student['first_name']);
      $studentNumber = $student['student_number'];
    }
        
    // Free result from memory
    mysqli_free_result($result);
  } else {
    echo 'Error: ' . mysqli_error($connection);
  }
    
  // Close database connection
  mysqli_close($connection);
} else {
  // Redirect to login
  header("Location: ../index.php");
}

// Logout
if (isset($_POST['logout'])) {
  require '../includes/logout.php';
}
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PUP HDF Attendance System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,700;1,400;1,700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../css/student_homepage.css" />
  </head>
  <body>
    <nav class="navbar">
      <a onclick="toStudentHomepage()"><h1>PUP HDF Attendance System</h1></a>
      <form method="POST" class="logout-form">
        <button type="submit" name="logout" class="logout-button"><p class="logout-text">LOGOUT</p></button>
        <img src="../assets/images/icons/settings_black.svg" onclick="toSettings()" class="nav-button" />  
    </form>
    </nav>
    <section class="main">
        <h1 class="title">Account Settings</h1>
        <form>
            <div class="settings-container">
                <h4>RECOVERY E-MAIL</h4>
                <div class="div-left-right">
                    <div class="settings-input">
                        <p>E-mail:</p>
                        <input type="email" name="email" class="settings-textbox"></input>
                    </div>
                    <div class="settings-input">
                        <p>Confirm E-mail:</p>
                        <input type="email" name="confirm-email" class="settings-textbox"></input>
                    </div>
                </div>
            </div>
            <div class="settings-container">
                <h4>CHANGE PASSWORD</h4>
                <div class="settings-input">
                    <p>Current Password:</p>
                    <input type="text" name="email" class="settings-textbox"></input>
                </div>
                <div class="div-left-right">
                    <div class="settings-input">
                        <p>New Password:</p>
                        <input type="text" name="confirm-email" class="settings-textbox"></input>
                    </div>
                    <div class="settings-input">
                        <p>Confirm New Password:</p>
                        <input type="text" name="confirm-email" class="settings-textbox"></input>
                    </div>
                </div>
            </div>
            <div class="save-button-container">
                <p class="save-response">Saved!</p>
                <button type="submit" name="submit" class="save-button">SAVE</button>
            <div>
        </form>
    </section>
    <!-- <script src="../scripts.js"></script> -->
    <script>
      function toHDF() {
        window.location.href = "hdf_page.php";
        return false;
      }
      function toStudentHomepage() {
        window.location.href = "student_homepage.php";
        return false;
      }
      function toSettings() {
        window.location.href = "student_settings_page.php";
        return false;
      }
    </script>
  </body>
</html>