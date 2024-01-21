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
        <a onclick="toAdminHomepage()"><img src="..\assets\images\logos\pup_logo.png" class="logo"/></a>
        <a onclick="toAdminHomepage()"><img src="..\assets\images\icons\group.svg" class="nav-button"/></a>
        <a onclick="toSchedule()"><img src="..\assets\images\icons\table.svg" class="nav-button"/></a>
        <a onclick="toSubjects()"><img src="..\assets\images\icons\book.svg" class="nav-button"/></a>
        <a onclick="toAnalytics()"><img src="..\assets\images\icons\graph.svg" class="nav-button"/></a>
      </div>
      <form method="POST" class="logout-form">
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
          <a onclick="toAdminHomepage()"><h1>PUP HDF Attendance System</h1></a>
        </div>
        <div class="right">
          <h5>ADMIN</h5>
        </div>
      </div>
      <h1 class="title">Computer Engineering Subjects</h1>
    </section>

    <div id="addSectionModal" class="modal-blur">
      <div class="modal-content">
        <div class="top-modal">
          <h6>ADD SECTION</h6>
        </div>
        <span class="close-modal" onclick="closeAddSectionModal()">&times;</span>
        <form method="POST" class="add-student-form">
          <div class="add-student-container">
            <p>Year Number</p>
            <input type="text" name="year" class="year-section-textbox" required></input>
            <p>Section Number</p>
            <input type="text" name="section" class="year-section-textbox" required></input>
          </div>
          <div class="add-button-container">
            <button type="submit" name="add-section" id="addButton" class="add-button">ADD</button>
          </div>
        </form>
      </div>
    </div>

    <div id="deleteSectionModal" class="modal-blur">
      <div class="modal-content">
        <div class="top-modal">
          <h6>DELETE SECTION</h6>
        </div>
          <span class="close-modal" onclick="closeDeleteSectionModal()">&times;</span>
          <img src="../assets/images/graphics/girl_trash.png" style="height: 40%; width: 40%;" />
          <h5 id="deleteSectionMessage" style="margin-bottom: 10px;"></h5>
          <p style="margin: 0px; text-align: center;">WARNING: All of the student data in this section will be deleted.</p>
          <p style="margin: 10px; text-align: center;">Are you sure you want to delete this section?</p>
          <div class="add-button-container">
            <button type="submit" name="confirm-delete-section" id="deleteButton">DELETE</button>
          </div>
        </form>
      </div>
    </div>

    <script src="../js/navbar_controller.js"></script>
    <script src="../js/delete_section.js"></script>
    <script>
      function toLogin() {
        window.location.href = "../index.php";
        return false;
      }
      function toAdminHomepage() {
        window.location.href = "admin_homepage.php";
        return false;
      }
      function toSubjects() {
        window.location.href = "admin_subjects_page.php";
        return false;
      }
      function toAnalytics() {
        window.location.href = "admin_analytics_page.php";
        return false;
      }
      function toSchedule() {
        window.location.href = "admin_schedule_page.php";
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
      function openDeleteSectionModal() {
        var addSectionModal = document.getElementById("deleteSectionModal");
        addSectionModal.style.display = "block";
      }
      function closeDeleteSectionModal() {
        var addSectionModal = document.getElementById("deleteSectionModal");
        addSectionModal.style.display = "none";
      }
    </script>
  </body>
</html>
