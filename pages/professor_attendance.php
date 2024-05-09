<?php 
session_start();
require '../includes/database_connection.php';
require '../includes/database_operations.php';
require '../includes/utils.php';
redirect('professor');
date_default_timezone_set('Asia/Manila');
require_once '../includes/encryption.php';
$encryptionHelper = new EncryptionHelper($encryptionKey);

// Check selected section
$sectionPage = checkSection();

// Fetch students
$classlist = fetchClasslist('students', "WHERE section = '$sectionPage'");
// echo $classlist;

// Fetch professors
$professors = fetchClasslist('professors');

// Fetch subjects
$subjects = fetchSubjects();
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
    <link rel="stylesheet" href="../css/global.css" />
    <link rel="stylesheet" href="../css/dashboard.css" />
    <link rel="stylesheet" href="../css/table.css" />
    <link rel="stylesheet" href="../css/modal.css" />
    <script type="text/javascript" src="../js/tableToExcel.js"></script>
  </head>
  <body>
    <nav class="navbar">
      <div class="top">
        <img
          src="..\assets\images\icons\arrow_left.svg"
          id="closeNavbar"
          class="close-nav"
          onclick="toggleMobileNavbar()"
          alt="arrow left"
        />
        <a onclick="toProfessorHomepage()"
          ><img
            src="..\assets\images\logos\pup_logo.png"
            alt="pup logo"
            class="logo"
        /></a>
        <a onclick="toSection()"
          ><img
            src="..\assets\images\icons\group.svg"
            alt="group"
            class="button"
        /></a>
        <!-- <a onclick="toSettings()"
          ><img
            src="..\assets\images\icons\settings.svg"
            alt="settings"
            class="button"
        /></a> -->
      </div>
      <form method="POST" class="bottom">
        <button type="submit" name="logout" class="logout">
          <img
            src="..\assets\images\icons\logout.svg"
            alt="logout"
            class="button"
          />
        </button>
      </form>
    </nav>
    <section class="main">
      <div class="header">
        <div class="left">
          <img
            src="..\assets\images\icons\hamburger.svg"
            alt="hamburger"
            class="hamburger"
            onclick="toggleMobileNavbar()"
          />
          <h3 onclick="toProfessorHomepage()" class="title">PUPHAS</h3>
        </div>
        <div class="right">
          <h6><?php echo $name; ?></h6>
          <h6><?php echo $idNumber; ?></h6>
        </div>
      </div>
      <h2 class="page-title" id="title">SECTION <?php echo $sectionPage ?> ATTENDANCE</h2>
      <input type="date" id="date" class="date-filter" required value="<?php echo date('Y-m-d'); ?>">
      <div class="table-controls">
        <div class="left">
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
        <div class="right">
          <label for="fileInput" class="file-input" id="fileInputLabel">Choose File</label>
          <span class="file-name" id="fileName">No file chosen</span>
          <input type="file" id="fileInput" accept=".xlsx" />
          <button class="import-export" id="import"><p>IMPORT DATA</p><img src="..\assets\images\icons\upload.svg"/></button>
          <button class="import-export" id="export"><p>EXPORT DATA</p><img src="..\assets\images\icons\download.svg"/></button>
        </div>
      </div>
      <table id="attendanceTable" data-cols-width="15,20,20,10,15,35">
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

     <div id="addModal" class="modal-blur">
      <div class="modal-content">
        <div class="top-modal">
          <h6>ADD ATTENDANCE</h6>
        </div>
        <span class="close-modal" onclick="closeAddAttendanceModal()">&times;</span>
        <form method="POST">
          <div>
            <p>Student</p>
            <input type="hidden" id="attendanceSection" value="<?php echo $sectionPage; ?>" />
            <select name="student" id="attendanceStudent" required>
              <option value="" disabled selected>Select Student</option>
              <?php foreach ($classlist as $student): ?>
                <?php 
                  $studentName = $student['lastName'] . ', ' . $student['firstName']; 
                  $studentNumber = $student['idNumber']; 
                  echo "<option value=\"{$studentNumber}\">{$studentNumber} - {$studentName}</option>"; 
                ?>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <p>Status</p>
            <select name="status" id="attendanceStatus" required>
              <option value="" disabled selected>Select Status</option>
              <option value="Present">Present</option>
              <option value="Late">Late</option>
            </select>
          </div>
          <div>
            <p>Time</p>
            <input type="time" name="time" id="attendanceTime" value="<?php echo date("H:i"); ?>" required></input>
          </div>
          <div>
            <p>Date</p>
            <input type="date" name="time" id="attendanceDate" class="add-attendance-date" value="<?php echo date('Y-m-d'); ?>" required></input>
          </div>
          <div>
            <p>Room</p>
            <input type="text" id="attendanceRoom"required />
          </div>
          <div>
            <p>Subject</p>
            <select name="subject" id="attendanceSubject" required>
              <option value="" disabled selected>Select Subject</option>
              <?php foreach ($subjects as $subject): ?>
                  <option value="<?php echo $subject['subjectCode']; ?>">
                      <?php echo $subject['subjectCode'] . ' - ' . $subject['subjectName']; ?>
                  </option>
              <?php endforeach; ?>
            </select>   
          </div>
          <div>
            <p>Professor</p>
            <select name="professor" id="attendanceProfessor" required>
              <option value="" disabled selected>Select Professor</option>
              <?php foreach ($professors as $professor): ?>
                <?php $professorName = $professor['lastName'] . ', ' . $professor['firstName']; ?>
                <option value="<?php echo $professor['idNumber']; ?>"><?php echo $professorName; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="submit-button-container">
            <button type="submit" name="add-attendance" id="addButton" class="add-button">ADD</button>
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
      function toSection() {
        window.location.href = "professor_section.php";
        return false;
      }

      function openAddAttendanceModal() {
        var addModal = document.getElementById("addModal");
        addModal.style.display = "block";
      }
      function closeAddAttendanceModal() {
        var addModal = document.getElementById("addModal");
        addModal.style.display = "none";
      }
    </script>
  </body>
</html>
