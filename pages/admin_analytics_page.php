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

// Get student count
require '../includes/database_connection.php';
$checkStudentSQL = "SELECT COUNT(*) as studentCount FROM students";
$stmtCheckStudent = mysqli_prepare($connection, $checkStudentSQL);
mysqli_stmt_execute($stmtCheckStudent);
mysqli_stmt_bind_result($stmtCheckStudent, $studentCount);
mysqli_stmt_fetch($stmtCheckStudent);
mysqli_stmt_close($stmtCheckStudent);
mysqli_close($connection);
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
    <link rel="stylesheet" href="../css/admin_section_page.css" />
  </head>
  <body>
    <nav class="navbar">
      <div class="navbar-top">
        <img src="..\assets\images\icons\arrow_left.svg" id="closeNavbar" class="nav-button" onclick="toggleMobileNavbar()"/>
        <a onclick="toAdminHomepage()"><img src="..\assets\images\logos\pup_logo.png" class="logo"/></a>
        <a onclick="toSection()"><img src="..\assets\images\icons\group.svg" class="nav-button"/></a>
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
      <h1 class="title">Computer Engineering Attendance Analytics</h1>
      <div class="filters-and-export">
        <div class="filters-container">
          <div class="time-container">
              <p>From:</p><input type="date" id="startDate" class="date-filter" required>
              <p>To:</p><input type="date" id="endDate" class="date-filter" required>
          </div>
        </div>
      </div>
      <section class="overview-container">
        <div class="overview-box">
          <div class="box">
            <img src="../assets/images/icons/group_fill.svg" />
          </div>
          <div class="box-text-container">
            <p>TOTAL NUMBER OF STUDENTS</p>
            <h4><?php echo $studentCount; ?></h4>
          </div>
        </div>
        <div class="overview-box">
          <div class="box">
            <img src="../assets/images/icons/check.svg" />
          </div>
          <div class="box-text-container">
            <p>OVERALL ATTENDANCE PERCENTAGE</p>
            <h4>80%</h4>
          </div>
        </div>
        <div class="overview-box">
          <div class="box">
            <img src="../assets/images/icons/clock.svg" />
          </div>
          <div class="box-text-container">
            <p>OVERALL LATE PERCENTAGE</p>
            <h4>50%</h4>
          </div>
        </div>
      </section>
      <section class="charts-section">
        <div class="chart-container">
          <h4>Attendance Overview</h4>
          <canvas id="attendanceOverview" class="chart"></canvas>
        </div>
        <div class="chart-container">
          <h4>Attendance By Class</h4>
          <canvas id="attendanceByClass" class="chart"></canvas>
        </div>
      </section>
    </section>

    <script src="../js/navbar_controller.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script src="../js/analytics.js"></script>
    <script>
      function toLogin() {
        window.location.href = "../index.php";
        return false;
      }
      function toAdminHomepage() {
        window.location.href = "admin_homepage.php";
        return false;
      }
      function toSection() {
        window.location.href = "admin_section_page.php";
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
