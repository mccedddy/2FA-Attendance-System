<?php
session_start();
require '../includes/database_connection.php';
require '../includes/utils.php';
redirect('admin');
date_default_timezone_set('Asia/Manila');

// Check selected section
$sectionPage = checkSection();

// Add student
if (isset($_POST['add-class'])) {
  require '../includes/database_connection.php';
  $subjectCode = $_POST['subject'];
  $day = $_POST['day'];
  $startTime = $_POST['start_time'];
  $endTime = $_POST['end_time'];
  $professor = $_POST['professor'];

  // SQL query 
  $sql = "INSERT INTO schedule (section, subject_code, day, start_time, end_time, professor)
            VALUES ('$sectionPage', '$subjectCode', '$day', '$startTime', '$endTime', '$professor')";

  // Use prepared statement
  $stmt = mysqli_prepare($connection, $sql);

  try {
    // Execute query
    mysqli_stmt_execute($stmt);

    // Close the statement
    mysqli_stmt_close($stmt);

    header("Location: admin_schedule.php");
  } catch (mysqli_sql_exception $exception) {
    // Check if duplicate entry
    if ($exception->getCode() == 1062) {
      header("Location: admin_schedule.php");
      exit;
    } else {
      throw $exception;
    }
  }
}

// Edit class
if (isset($_POST['edit-schedule'])) {
  require '../includes/database_connection.php';
  $editSubject = $_POST['subject'];
  $editDay = $_POST['day'];
  $editStartTime = $_POST['start_time'];
  $editEndTime = $_POST['end_time'];
  $editProfessor = $_POST['professor'];
  $scheduleId = $_POST['schedule_id'];

  // SQL query 
  $editSQL = "UPDATE schedule 
            SET subject_code = '$editSubject', 
                day = '$editDay', 
                start_time = '$editStartTime', 
                end_time = '$editEndTime',  
                professor = '$editProfessor' 
            WHERE id = '$scheduleId'";

  // Execute query
  $stmt = mysqli_prepare($connection, $editSQL);

  try {
    // Execute query
    mysqli_stmt_execute($stmt);

    // Close the statement
    mysqli_stmt_close($stmt);

    // header("Location: admin_schedule_menu.php");
  } catch (mysqli_sql_exception $exception) {
    // Check if duplicate entry
    if ($exception->getCode() == 1062) {
      // header("Location: admin_schedule_menu.php");
      exit; 
    } else {
      throw $exception;
    }
  }
}

// Fetch schedule
$schedule = fetchSchedule();
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
        <a onclick="toAdminHomepage()"
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
        <a onclick="toSchedule()"
          ><img
            src="..\assets\images\icons\table.svg"
            alt="table"
            class="button"
        /></a>
        <a onclick="toSubjects()"
          ><img src="..\assets\images\icons\book.svg" alt="book" class="button"
        /></a>
        <a onclick="toAnalytics()"
          ><img
            src="..\assets\images\icons\graph.svg"
            alt="graph"
            class="button"
        /></a>
        <a onclick="toSettings()"
          ><img
            src="..\assets\images\icons\settings.svg"
            alt="settings"
            class="button"
        /></a>
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
          <h3 onclick="toAdminHomepage()" class="title">PUPHAS</h3>
        </div>
        <div class="right">
          <h6>ADMIN</h6>
        </div>
      </div>
      <h2 class="page-title">SECTION <?php echo $sectionPage ?> SCHEDULE</h2>
      <div class="table-controls">
        <div class="left">
          <button onclick="openAddScheduleModal()">
            <img src="..\assets\images\icons\plus_white.svg"/>
            New
          </button>
          <button id="editScheduleBtn" onclick="openEditScheduleModal()">
            <img src="..\assets\images\icons\pencil_white.svg"/>
            Edit
          </button>
          <button id="deleteSchedulesBtn">
            <img src="..\assets\images\icons\trash_white.svg"/>
            Delete
          </button>
        </div>
        <div class="right">
          <label for="fileInput" class="file-input" id="fileInputLabel">Choose File</label>
          <span class="file-name" id="fileName">No file chosen</span>
          <input type="file" id="fileInput" accept=".xlsx" />
          <button class="import-export" id="import"><p>IMPORT DATA</p><img src="..\assets\images\icons\upload.svg"/></button>
          <button class="import-export" id="export"><p>EXPORT DATA</p><img src="..\assets\images\icons\download.svg"/></button>
        </div>
      </div>
      <table id="schedulesTable" data-cols-width="15,20,20,10,15,35">
        <thead>
          <tr>
            <th data-exclude="true"></th>
            <th>SUBJECT CODE</th>
            <th>SUBJECT NAME</th>
            <th>DAY</th>
            <th>START TIME</th>
            <th>END TIME</th>
            <th>PROFESSOR</th>
            <th style="display: none;" data-exclude="true">ID</th>
            <th>SECTION</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($schedule as $class): ?>
            <tr>
              <td data-exclude="true"><input type="checkbox" name="selectedSchedules[]"></td>
              <td><?php echo $class['subjectCode']; ?></td>
              <td><?php echo $class['subjectName']; ?></td>
              <td><?php echo $class['day']; ?></td>
              <td><?php echo $class['startTime']; ?></td>
              <td><?php echo $class['endTime']; ?></td>
              <td><?php echo $class['professor']; ?></td>
              <td style="display: none;" data-exclude="true"><?php echo $class['id']; ?></td>
              <td><?php echo $class['section']; ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div style="height:50px;"></div>
    </section>

    <div id="addModal" class="modal-blur">
      <div class="modal-content">
        <div class="top-modal">
          <h6>ADD SCHEDULE</h6>
        </div>
        <span class="close-modal" onclick="closeAddScheduleModal()">&times;</span>
        <form method="POST">
          <div>
            <p>Subject</p>
            <select name="subject" class="modal-input" required>
              <option value="" disabled selected>Select Subject</option>
              <?php
              // Fetch subjects
              $subjectsSQL = "SELECT * FROM subjects ORDER BY subject_code ASC";
              $subjectsResult = mysqli_query($connection, $subjectsSQL);
              while ($subjectRow = mysqli_fetch_assoc($subjectsResult)) {
                $subjectName = $subjectRow['subject_name'];
                $subjectCode = $subjectRow['subject_code'];
                echo "<option value=\"{$subjectCode}\">{$subjectCode} - {$subjectName}</option>";
              }
              mysqli_free_result($subjectsResult);
              ?>
            </select>
          </div>
          <div>
            <p>Day</p>
            <select name="day" class="modal-input" required>
              <option value="" disabled selected>Select Day</option>
              <option value="Monday">Monday</option>
              <option value="Tuesday">Tuesday</option>
              <option value="Wednesday">Wednesday</option>
              <option value="Thursday">Thursday</option>
              <option value="Friday">Friday</option>
              <option value="Saturday">Saturday</option>
              <option value="Sunday">Sunday</option>
            </select>
          </div>
          <div>
            <p>Start Time</p>
            <input type="time" name="start_time" class="modal-input" required></input>
          </div>
          <div>
            <p>End Time</p>
            <input type="time" name="end_time" class="modal-input" required></input>
          </div>
          <div>
            <p>Professor</p>
            <select name="professor" class="modal-input" required>
              <option value="" disabled selected>Select Professor</option>
              <?php
              // Fetch professors
              $professorsSQL = "SELECT * FROM professors WHERE id_number != 'admin' ORDER BY last_name ASC";
              $professorsResult = mysqli_query($connection, $professorsSQL);
              while ($professorRow = mysqli_fetch_assoc($professorsResult)) {
                $professorName = $professorRow['last_name'] . ', ' . $professorRow['first_name'];
                $professorID = $professorRow['id_number'];
                echo "<option value=\"{$professorID}\">{$professorName}</option>";
              }
              mysqli_free_result($professorsResult);
              ?>
            </select>
          </div>
          <div class="submit-button-container">
            <button type="submit" name="add-class" id="addButton" class="add-button">ADD</button>
          </div>
        </form>
      </div>
    </div>

    <div id="editModal" class="modal-blur">
      <div class="modal-content">
        <div class="top-modal">
          <h6>EDIT SCHEDULE</h6>
        </div>
        <span class="close-modal" onclick="closeEditScheduleModal()">&times;</span>
        <form method="POST" name="edit-subject">
          <input id="scheduleId" name="schedule_id" type="hidden"></input>
          <div>
            <p>Subject</p>
            <select name="subject" id="editSubject" class="modal-input" required>
              <?php
              // Fetch subjects
              $subjectsSQL = "SELECT * FROM subjects ORDER BY subject_code ASC";
              $subjectsResult = mysqli_query($connection, $subjectsSQL);
              while ($subjectRow = mysqli_fetch_assoc($subjectsResult)) {
                $subjectName = $subjectRow['subject_name'];
                $subjectCode = $subjectRow['subject_code'];
                echo "<option value=\"{$subjectCode}\">{$subjectCode} - {$subjectName}</option>";
              }
              mysqli_free_result($subjectsResult);
              ?>
            </select>
          </div>
          <div>
            <p>Day</p>
            <select name="day" id="editDay" class="modal-input" required>
              <option value="Monday">Monday</option>
              <option value="Tuesday">Tuesday</option>
              <option value="Wednesday">Wednesday</option>
              <option value="Thursday">Thursday</option>
              <option value="Friday">Friday</option>
              <option value="Saturday">Saturday</option>
              <option value="Sunday">Sunday</option>
            </select>
          </div>
          <div>
            <p>Start Time</p>
            <input type="time" name="start_time" id="editStartTime" class="modal-input" required></input>
          </div>
          <div>
            <p>End Time</p>
            <input type="time" name="end_time" id="editEndTime" class="modal-input" required></input>
          </div>
          <div>
            <p>Professor</p>
            <select name="professor" id="editProfessor" class="modal-input" required>
              <?php
              // Fetch professors
              $professorsSQL = "SELECT * FROM professors WHERE id_number != 'admin' ORDER BY last_name ASC";
              $professorsResult = mysqli_query($connection, $professorsSQL);
              while ($professorRow = mysqli_fetch_assoc($professorsResult)) {
                $professorName = $professorRow['last_name'] . ', ' . $professorRow['first_name'];
                $professorID = $professorRow['id_number'];
                echo "<option value=\"{$professorID}\">{$professorName}</option>";
              }
              mysqli_free_result($professorsResult);
              ?>
            </select>
          </div>
          <div class="submit-button-container">
            <button type="submit" name="edit-schedule" class="add-button">SAVE</button>
          </div>
        </form>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
    <script src="../js/schedule.js"></script>
    <script src="../js/navbar_controller.js"></script>
    <script>
      function toLogin() {
        window.location.href = "../index.php";
        return false;
      }
      function toAdminHomepage() {
        window.location.href = "admin_home.php";
        return false;
      }
      function toSection() {
        window.location.href = "admin_sections.php";
        return false;
      }
      function toSubjects() {
        window.location.href = "admin_subjects.php";
        return false;
      }
      function toAnalytics() {
        window.location.href = "admin_analytics.php";
        return false;
      }
      function toSchedule() {
        window.location.href = "admin_schedule_menu.php";
        return false;
      }
      function toSettings() {
        window.location.href = "admin_settings_page.php";
        return false;
      }
      function openAddScheduleModal() {
        var addModal = document.getElementById("addModal");
        addModal.style.display = "block";
      }
      function openEditScheduleModal() {
        var editModal = document.getElementById("editModal");
        editModal.style.display = "block";
      }
      function closeAddScheduleModal() {
        var addModal = document.getElementById("addModal");
        addModal.style.display = "none";
      }
      function closeEditScheduleModal() {
        var editModal = document.getElementById("editModal");
        editModal.style.display = "none";
      }
    </script>
  </body>
</html>
