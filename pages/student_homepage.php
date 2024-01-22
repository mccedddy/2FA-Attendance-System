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
      <div>
        <h2><?php echo $name ?></h2>
        <h2><?php echo $studentNumber ?></h2>
      </div>
      <div>
        <img
          src="..\assets\images\graphics\scientists.png"
          class="main-graphics"
        />
      </div>
      <div><h3>HEALTH DECLARATION FORM</h3></div>
      <div>
        <button class="answer-button" onclick="toHDF()">ANSWER</button>
      </div>
    </section>
    <!-- <script src="../scripts.js"></script> -->
    <script>
      function toHDF() {
        window.location.href = "student_hdf_page.php";
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
