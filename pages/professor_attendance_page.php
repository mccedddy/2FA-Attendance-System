<?php 
session_start();
require '../includes/database_connection.php';
date_default_timezone_set('Asia/Manila');

// If logged in
if (isset($_SESSION['student_number'])) {
  // Redirect to student homepage
  header("Location: student_homepage.php");
}
if (isset($_SESSION['id_number'])) {

  // Redirect to homepage if no section is selected
  if (!isset($_SESSION['selected_section'])) {
    header("Location: professor_home.php");
  } else {
    $sectionPage = $_SESSION['selected_section'];
  }

  // Professor ID
  $idNumber = $_SESSION['id_number'];

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
    <link rel="stylesheet" href="../css/professor_homepage.css" />
    <script type="text/javascript" src="../js/tableToExcel.js"></script>
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
      <h1 id="title">SECTION <?php echo $sectionPage ?> ATTENDANCE</h1>
      <input type="date" id="date" class="date-filter" required value="<?php echo date('Y-m-d'); ?>">
      <div style="display:flex; flex-wrap:wrap; justify-content:space-between; margin: 5px 0px; gap: 5px;">
        <div style="display:flex; gap:5px;">
          <select id="subjectFilter">
            <option value="ALL">ALL</option>
              <?php
                require '../includes/database_connection.php';

                $sql = "SELECT subject_code, subject_name FROM subjects";
                $result = mysqli_query($connection, $sql); 

                while ($row = mysqli_fetch_assoc($result)) {
                  $optionText = $row['subject_code'] . " - " . $row['subject_name'];
                  $optionValue = $row['subject_code'];
                  echo "<option class='subjectOption' value='$optionValue'>$optionText</option>";
                }

                mysqli_free_result($result);
                mysqli_close($connection);
              ?>
          </select>
          <button id="addSchedule" onclick="openAddAttendanceModal()">ADD</Button>
        </div>
        <div style="display: flex; flex-wrap: wrap; gap:5px;">
          <label for="fileInput" class="custom-file-input" id="fileInputLabel">Choose File</label>
          <span class="file-name" id="fileName">No file chosen</span>
          <input type="file" id="fileInput" accept=".xlsx" />
          <div style="display:flex; gap:5px;">
            <button id="import"><p>IMPORT DATA</p><img src="..\assets\images\icons\upload.svg"/></button>
            <button id="export"><p>EXPORT DATA</p><img src="..\assets\images\icons\download.svg"/></button>
          </div>
        </div>
      </div>
      <table id="attendanceTable" data-cols-width="20,20,10,10,15">
        <thead>
          <tr>
            <th>STUDENT NAME</th>
            <th>STUDENT NUMBER</th>
            <th>STATUS</th>
            <th>TIME</th>
            <th>DATE</th>
            <th>ROOM</th>
            <th>SUBJECT</th>
            <th>PROFESSOR</th>
            <!-- <th>SCHEDULE ID</th> -->
          </tr>
        </thead>
        <tbody>
          
        </tbody>
      </table>
      <div style="height:50px;"></div>
    </section>

     <div id="addAttendanceModal" class="modal-blur">
      <div class="modal-content">
        <div class="top-modal">
          <h6>ADD ATTENDANCE</h6>
        </div>
        <span class="close-modal" onclick="closeAddAttendanceModal()">&times;</span>
        <form method="POST" class="add-attendance-form">
          <div class="add-attendance-container">
            <p>Student</p>
            <input type="hidden" id="attendanceSection" value="<?php echo $sectionPage; ?>" />
            <select name="student" id="attendanceStudent" class="add-attendance-dropdown" required>
              <option value="" disabled selected>Select Student</option>
              <?php
              // Fetch students
              require '../includes/database_connection.php';
              $studentsSQL = "SELECT * FROM students WHERE section = '$sectionPage' ORDER BY last_name ASC";
              $studentsResult = mysqli_query($connection, $studentsSQL);
              while ($studentRow = mysqli_fetch_assoc($studentsResult)) {
                $studentName = $studentRow['last_name'] . ', ' . $studentRow['first_name'];
                $studentNumber = $studentRow['student_number'];
                echo "<option value=\"{$studentNumber}\">{$studentNumber} - {$studentName}</option>";
              }
              mysqli_free_result($studentsResult);
              mysqli_close($connection);  
              ?>
            </select>
          </div>
          <div class="add-attendance-container">
            <p>Status</p>
            <select name="status" id="attendanceStatus" class="add-attendance-dropdown" required>
              <option value="" disabled selected>Select Status</option>
              <option value="Present">Present</option>
              <option value="Late">Late</option>
            </select>
          </div>
          <div class="add-attendance-container">
            <p>Time</p>
            <input type="time" name="time" id="attendanceTime" class="add-attendance-dropdown" value="<?php echo date("H:i"); ?>" required></input>
          </div>
          <div class="add-attendance-container">
            <p>Date</p>
            <input type="date" name="time" id="attendanceDate" class="add-attendance-dropdown" value="<?php echo date('Y-m-d'); ?>" required></input>
          </div>
          <div class="add-attendance-container">
            <p>Room</p>
            <input type="text" id="attendanceRoom" class="add-attendance-textbox" required />
          </div>
          <div class="add-attendance-container">
            <p>Subject</p>
            <select name="subject" id="attendanceSubject" class="add-attendance-dropdown" required>
              <option value="" disabled selected>Select Subject</option>
              <?php
              // Fetch subjects
              require '../includes/database_connection.php';
              $subjectsSQL = "SELECT * FROM subjects ORDER BY subject_code ASC";
              $subjectsResult = mysqli_query($connection, $subjectsSQL);
              while ($subjectRow = mysqli_fetch_assoc($subjectsResult)) {
                $subjectName = $subjectRow['subject_name'];
                $subjectCode = $subjectRow['subject_code'];
                echo "<option value=\"{$subjectCode}\">{$subjectCode} - {$subjectName}</option>";
              }
              mysqli_free_result($subjectsResult);
              mysqli_close($connection);
              ?>
            </select>
          </div>
          <div class="add-attendance-container">
            <p>Professor</p>
            <select name="professor" id="attendanceProfessor" class="add-attendance-dropdown" required>
              <option value="" disabled selected>Select Professor</option>
              <?php
              // Fetch professors
              require '../includes/database_connection.php';
              $professorsSQL = "SELECT * FROM professors WHERE id_number != 'admin' ORDER BY last_name ASC";
              $professorsResult = mysqli_query($connection, $professorsSQL);
              while ($professorRow = mysqli_fetch_assoc($professorsResult)) {
                $professorName = $professorRow['last_name'] . ', ' . $professorRow['first_name'];
                $professorID = $professorRow['id_number'];
                echo "<option value=\"{$professorID}\">{$professorName}</option>";
              }
              mysqli_free_result($professorsResult);
              mysqli_close($connection);
              ?>
            </select>
          </div>
          <div class="add-button-container">
            <button name="add-attendance" id="addButton" class="add-button">ADD</button>
          </div>
        </form>
      </div>
    </div>

    <script src="../js/navbar_controller.js"></script>
    <script src="../js/attendance.js"></script>
    <script>
      function toLogin() {
        window.location.href = "../index.php";
        return false;
      }
      function toProfessorHomepage() {
        window.location.href = "professor_home.php";
        return false;
      }
      function toSettings() {
        window.location.href = "professor_settings_page.php";
        return false;
      }
      function openAddAttendanceModal() {
        var addAttendanceModal = document.getElementById("addAttendanceModal");
        addAttendanceModal.style.display = "block";
      }
      function closeAddAttendanceModal() {
        var addAttendanceModal = document.getElementById("addAttendanceModal");
        addAttendanceModal.style.display = "none";
      }
    </script>
  </body>
</html>
