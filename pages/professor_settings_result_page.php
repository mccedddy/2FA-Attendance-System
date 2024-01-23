<?php
session_start();
require '../includes/database_connection.php';

// Clear selection
unset($_SESSION['selected_section']);

// If logged in
if (isset($_SESSION['student_number'])) {
  // Redirect to student homepage
  header("Location: student_homepage.php");
}
if (isset($_SESSION['id_number'])) {
  $idNumber = $_SESSION['id_number'];

  // Redirect to admin homepage
  if ($idNumber == 'admin') {
    header("Location: admin_section_page.php");
  }

  // SQL query
  $sql = "SELECT * FROM professors WHERE id_number = '$idNumber'";
  $result = mysqli_query($connection, $sql);

  // Check if the query was successful
  if ($result) {
    $professor = mysqli_fetch_assoc($result);

    // Get professor info
    if ($professor) {
      $name = strtoupper($professor['last_name']) . ', ' . strtoupper($professor['first_name']);
      $idNumber = $professor['id_number'];
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

// Section button
if (isset($_POST['section-button'])) {
  $_SESSION['selected_section'] = $_POST['section'];
  header("Location: professor_attendance_page.php");
}

// Logout
if (isset($_POST['logout'])) {
  require '../includes/logout.php';
}

// Result
$result = '';
if (isset($_SESSION['email']) && isset($_SESSION['password'])) {
    if ($_SESSION['email'] === 'true' && $_SESSION['password'] === 'true') {
        $result = '<h4 style="color: green;">Email and password changed!</h4>';
    } else if ($_SESSION['email'] === 'true' && $_SESSION['password'] === 'false') {
        $result = '<h4 style="color: green;">Email changed!</h4>';
    } else if ($_SESSION['email'] === 'false' && $_SESSION['password'] === 'true') {
        $result = '<h4 style="color: green;">Password changed!</h4>';
    } else if ($_SESSION['password'] === 'invalid') {
        $result = '<h4 style="color: red;">Invalid password!</h4>';
    } else if ($_SESSION['email'] === 'invalid') {
        $result = '<h4 style="color: red;">Invalid email!</h4>';
    } else {
        $result = '<h4 style="color: red;">An error occured</h4>';
    }
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
    <link rel="stylesheet" href="../css/professor_homepage.css" />
  </head>
  <body>
    <nav class="navbar">
      <div class="navbar-top">
        <img src="..\assets\images\icons\arrow_left.svg" id="closeNavbar" class="nav-button" onclick="toggleMobileNavbar()"/>
        <a onclick="toProfessorHomepage()"><img src="..\assets\images\logos\pup_logo.png" /></a>
        <a onclick="toProfessorHomepage()"><img src="..\assets\images\icons\group.svg" class="nav-button"/></a>
        <a onclick="toProfessorHomepage()"><img src="..\assets\images\icons\graph.svg" class="nav-button"/></a>
      </div>
      <form method="POST" class="logout-form">
        <a onclick="toSettings()"><img src="..\assets\images\icons\settings.svg"/></a>
        <button type="submit" name="logout" class="logout-button">
          <img src="..\assets\images\icons\logout.svg" class="nav-button"/>
        </button>
      </form>
    </nav>
    <section class="main">
      <div class="header">
        <div class="left">
          <div class="mobile-navbar-toggle" onclick="toggleMobileNavbar()">
            <img src="..\assets\images\icons\hamburger.svg" class="hamburger">
          </div>
          <a onclick="toProfessorHomepage()"><h1>PUP HDF Attendance System</h1></a>
        </div>
        <div class="right">
          <h5><?php echo $name; ?></h5>
          <h5><?php echo $idNumber; ?></h5>
        </div>
      </div>
      <h1 class="title">Account Settings</h1>
      <center>
        <div style="border: 2px solid #810000; border-radius: 15px; padding: 0px 40px 0px 40px; display: inline-flex">
          <?php echo $result; ?>
        </div>
      </center>
    </section>
    <script src="../js/navbar_controller.js"></script>
    <script>
      function toLogin() {
        window.location.href = "../index.php";
        return false;
      }
      function toProfessorHomepage() {
        window.location.href = "professor_homepage.php";
        return false;
      }
      function toSettings() {
        window.location.href = "professor_settings_page.php";
        return false;
      }
    </script>
  </body>
</html>
