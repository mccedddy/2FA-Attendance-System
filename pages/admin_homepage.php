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

  // Redirect to professor homepage
  if ($idNumber != 'admin') {
    header("Location: professor_homepage.php");
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
  header("Location: admin_section_page.php");
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
    <link rel="stylesheet" href="../css/admin_homepage.css" />
  </head>
  <body>
    <nav class="navbar">
      <div class="navbar-top">
        <img src="..\assets\images\icons\arrow_left.svg" id="closeNavbar" class="nav-button" onclick="toggleMobileNavbar()"/>
        <a onclick="toAdminHomepage()"><img src="..\assets\images\logos\pup_logo.png" /></a>
        <a onclick="toAdminHomepage()"><img src="..\assets\images\icons\notepad.svg" class="nav-button"/></a>
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
        <div class="mobile-navbar-toggle" onclick="toggleMobileNavbar()">
          <img src="..\assets\images\icons\hamburger.svg" class="hamburger">
        </div>
        <a onclick="toAdminHomepage()"><h1>PUP HDF Attendance System (Admin)</h1></a>
      </div>
      <h1 class="title">Computer Engineering Department Sections</h1>
      <div class="add-section-container">
        <button class="add-section-button" onclick="openAddSectionModal()">
          <img src="..\assets\images\icons\plus.svg" class="hamburger">
          <p>ADD SECTION</p>
        </button>
      </div>
      <div class="section-button-container">
        <form method="POST">
          <input type="hidden" name="section" value="4-1">
          <button type="submit" name="section-button" class="section-button">SECTION 4-1</button>
        </form>
        <form method="POST">
          <input type="hidden" name="section" value="4-2">
          <button type="submit" name="section-button" class="section-button">SECTION 4-2</button>
        </form>
        <form method="POST">
          <input type="hidden" name="section" value="4-3">
          <button type="submit" name="section-button" class="section-button">SECTION 4-3</button>
        </form>
        <form method="POST">
          <input type="hidden" name="section" value="4-4">
          <button type="submit" name="section-button" class="section-button">SECTION 4-4</button>
        </form>
        <form method="POST">
          <input type="hidden" name="section" value="4-5">
          <button type="submit" name="section-button" class="section-button">SECTION 4-5</button>
        </form>
        <form method="POST">
          <input type="hidden" name="section" value="4-6">
          <button type="submit" name="section-button" class="section-button">SECTION 4-6</button>
        </form>
        <form method="POST">
          <input type="hidden" name="section" value="professors">
          <button type="submit" name="section-button" class="section-button">PROFESSORS</button>
        </form>
      </div>
    </section>

    <div id="addSectionModal" class="modal-blur">
      <div class="modal-content">
        <div class="top-modal">
          <h6>ADD CLASS SECTION</h6>
        </div>
        <span class="close-modal" onclick="closeAddSectionModal()">&times;</span>
        <form class="add-student-form">
          <div class="add-student-container">
            <p>Year Number</p>
            <input type="text" name="class-list" class="year-section-textbox" required></input>
            <p>Section Number</p>
            <input type="text" name="class-list" class="year-section-textbox" required></input>
          </div>
          <div class="add-student-container">
            <p>Import Classlist</p>
            <input type="email" name="class-list" class="add-student-textbox" required></input>
          </div>
          <div class="add-button-container">
            <button type="submit" id="addButton">ADD</button>
          </div>
        </form>
      </div>
    </div>

    <script src="../js/navbar_controller.js"></script>
    <script>
      function toLogin() {
        window.location.href = "../index.php";
        return false;
      }
      function toAdminHomepage() {
        window.location.href = "admin_homepage.php";
        return false;
      }
      function toSettings() {
        window.location.href = "admin_settings_page.php";
        return false;
      }
      function openAddSectionModal() {
        var addSectionModal = document.getElementById("addSectionModal");
        addSectionModal.style.display = "block";
      }
      function closeAddSectionModal() {
        var addSectionModal = document.getElementById("addSectionModal");
        addSectionModal.style.display = "none";
      }
    </script>
  </body>
</html>
