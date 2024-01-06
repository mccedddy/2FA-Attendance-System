<?php
$name = 'LAST NAME, FIRST NAME, M.I';
$studentNumber = 'STUDENT NUMBER';
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
      <a class="logout-button" onclick="toLogin()">LOGOUT</a>
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
      function toLogin() {
        window.location.href = "../index.php";
        return false;
      }
      function toHDF() {
        window.location.href = "hdf_page.php";
        return false;
      }
      function toStudentHomepage() {
        window.location.href = "student_homepage.php";
        return false;
      }
    </script>
  </body>
</html>
