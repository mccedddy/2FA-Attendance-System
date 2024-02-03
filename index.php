<?php 
session_start();
require 'includes/database_connection.php';

// If logged in
if (isset($_SESSION['student_number'])) {
  // Redirect to student homepage
  header("Location: pages/student_homepage.php");
}
if (isset($_SESSION['id_number'])) {
  // Redirect to student homepage
  header("Location: pages/professor_homepage.php");
}
?>

<!doctype html>
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
    <link rel="stylesheet" href="css/global.css" />
    <link rel="stylesheet" href="css/index.css" />
  </head>
  <body>
    <section class="graphics">
      <img
        src="assets\images\graphics\girl_with_phone.png"
        alt="girl with phone"
      />
    </section>
    <section class="main">
      <img src="assets\images\logos\pup_logo.png" alt="puphas logo" />
      <h1>LOGIN</h1>
      <div class="button-container">
        <button class="large-button" onclick="toStudentLogin()">
          AS A STUDENT
        </button>
        <button class="large-button" onclick="toProfessorLogin()">
          AS A PROFESSOR
        </button>
      </div>
    </section>
    <script>
      function toStudentLogin() {
        window.location.href = "student_login.php";
        return false;
      }
      function toProfessorLogin() {
        window.location.href = "professor_login.php";
        return false;
      }
    </script>
  </body>
</html>
