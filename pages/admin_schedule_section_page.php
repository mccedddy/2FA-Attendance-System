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
    header("Location: professor_homepage.php");
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

// Add student
// if (isset($_POST['add-student'])) {
//   require '../includes/database_connection.php';
//   $lastName = $_POST['last_name'];
//   $firstName = $_POST['first_name'];
//   $studentNumber = $_POST['student_number'];
//   $nfcUid = $_POST['nfc_uid'];
//   $email = $_POST['email'];
//   $section = $_POST['year'] . '-' . $_POST['section'];

//   // Hash the password (Default: Last Name)
//   $hashedPassword = password_hash($lastName, PASSWORD_DEFAULT);

//   // SQL query to insert data into the students table
//   $sql = "INSERT INTO students (last_name, first_name, student_number, section, nfc_uid, email, password)
//             VALUES ('$lastName', '$firstName', '$studentNumber', '$section', '$nfcUid', '$email', '$hashedPassword')";

//   // Use prepared statement
//   $stmt = mysqli_prepare($connection, $sql);

//   try {
//     // Execute query
//     mysqli_stmt_execute($stmt);

//     // Close the statement
//     mysqli_stmt_close($stmt);

//     header("Location: admin_classlist_page.php");
//   } catch (mysqli_sql_exception $exception) {
//     // Check if duplicate entry
//     if ($exception->getCode() == 1062) {
//       header("Location: admin_classlist_page.php");
//       exit;
//     } else {
//       throw $exception;
//     }
//   }
// }

// Edit student
// if (isset($_POST['edit-student'])) {
//   require '../includes/database_connection.php';
//   $editLastName = $_POST['last_name'];
//   $editFirstName = $_POST['first_name'];
//   $editStudentNumber = $_POST['student_number'];
//   $editNfcUid = $_POST['nfc_uid'];
//   $editEmail = $_POST['email'];
//   $originalStudentNumber = $_POST['original_student_number'];

//   // SQL query to update data in the students table
//   $editSQL = "UPDATE students
//             SET last_name = '$editLastName',
//                 first_name = '$editFirstName',
//                 student_number = '$editStudentNumber',
//                 nfc_uid = '$editNfcUid',
//                 email = '$editEmail'
//             WHERE student_number = '$originalStudentNumber'";

//   // Execute query
//   $stmt = mysqli_prepare($connection, $editSQL);

//   try {
//     // Execute query
//     mysqli_stmt_execute($stmt);

//     // Close the statement
//     mysqli_stmt_close($stmt);

//     header("Location: admin_classlist_page.php");
//   } catch (mysqli_sql_exception $exception) {
//     // Check if duplicate entry
//     if ($exception->getCode() == 1062) {
//       header("Location: admin_classlist_page.php");
//       exit;
//     } else {
//       throw $exception;
//     }
//   }
// }

// Fetch class list
require '../includes/database_connection.php';
$scheduleSQL = "SELECT schedule.subject_code, subjects.subject_name, schedule.day, schedule.start_time, schedule.end_time,
                       CONCAT(professors.last_name, ', ', professors.first_name) AS professor_name
               FROM schedule
               INNER JOIN subjects ON schedule.subject_code = subjects.subject_code
               INNER JOIN professors ON schedule.professor = professors.id_number
               WHERE schedule.section = '$sectionPage'";
$scheduleResult = mysqli_query($connection, $scheduleSQL);
$schedule = [];

while ($row = mysqli_fetch_assoc($scheduleResult)) {
    $classInfo = [
        'subjectCode'   => $row['subject_code'],
        'subjectName'   => $row['subject_name'],
        'day'           => $row['day'],
        'startTime'     => $row['start_time'],
        'endTime'       => $row['end_time'],
        'professor'     => $row['professor_name'],
    ];
    $schedule[] = $classInfo;
}
mysqli_free_result($scheduleResult);
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
    <script type="text/javascript" src="../js/tableToExcel.js"></script>
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
      <h1 class="title" id="title">SECTION <?php echo $sectionPage ?> SCHEDULE</h1>
      <div class="search-container">
      </div>
      <div class="edit-and-export">
        <div class="edit-container">
          <button class="edit-class-button" onclick="openAddClassModal()">
            <img src="..\assets\images\icons\plus_white.svg"/>
            <p>New</p>
          </button>
          <button class="edit-class-button" id="editStudentBtn">
            <img src="..\assets\images\icons\pencil_white.svg"/>
            <p>Edit</p>
          </button>
          <button class="edit-class-button" id="deleteStudentsBtn">
            <img src="..\assets\images\icons\trash_white.svg"/>
            <p>Delete</p>
          </button>
        </div>
        <div style="display: flex; gap: 5px; flex-wrap: wrap;">
          <label for="fileInput" class="custom-file-input" id="fileInputLabel">Choose File</label>
          <span class="file-name" id="fileName">No file chosen</span>
          <input type="file" id="fileInput" accept=".xlsx" />
          <button class="import-export" id="import"><p>IMPORT DATA</p><img src="..\assets\images\icons\upload.svg"/></button>
          <button class="import-export" id="export"><p>EXPORT DATA</p><img src="..\assets\images\icons\download.svg"/></button>
        </div>
      </div>
      <table id="attendanceTable" data-cols-width="15,20,20,10,15,35">
        <thead>
          <tr>
            <th data-exclude="true"></th>
            <th>SUBJECT CODE</th>
            <th>SUBJECT NAME</th>
            <th>DAY</th>
            <th>START TIME</th>
            <th>END TIME</th>
            <th>PROFESSOR</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($schedule as $class): ?>
            <tr>
              <td data-exclude="true"><input type="checkbox" name="selectedStudents[]"></td>
              <td><?php echo $class['subjectCode']; ?></td>
              <td><?php echo $class['subjectName']; ?></td>
              <td><?php echo $class['day']; ?></td>
              <td><?php echo $class['startTime']; ?></td>
              <td><?php echo $class['endTime']; ?></td>
              <td><?php echo $class['professor']; ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div style="height:50px;"></div>
    </section>

    <div id="addStudentModal" class="modal-blur">
      <div class="modal-content">
        <div class="top-modal">
          <h6>ADD CLASS</h6>
        </div>
        <span class="close-modal" onclick="closeAddClassModal()">&times;</span>
        <form method="POST" class="add-student-form">
          <div class="add-student-container">
            <p>Subject</p>
            <input type="text" name="subject" class="add-student-textbox" required></input>
          </div>
          <div class="add-student-container">
            <p>Day</p>
            <input type="text" name="day" class="add-student-textbox" required></input>
          </div>
          <div class="add-student-container">
            <p>Start Time</p>
            <input type="text" name="start_time" class="add-student-textbox" required></input>
          </div>
          <div class="add-student-container">
            <p>End Time</p>
            <input type="text" name="end_time" class="add-student-textbox" required></input>
          </div>
          <div class="add-student-container">
            <p>Professor</p>
            <input type="email" name="professor" class="add-student-textbox" required></input>
          </div>
          <div class="add-button-container">
            <button type="submit" name="add-student" id="addButton" class="add-button">ADD</button>
          </div>
        </form>
      </div>
    </div>

    <div id="editStudentModal" class="modal-blur">
      <div class="modal-content">
        <div class="top-modal">
          <h6 id="editStudentTitle">EDIT CLASS</h6>
        </div>
        <span class="close-modal" onclick="closeEditClassModal()">&times;</span>
        <form method="POST" class="add-student-form">
          <div class="add-student-container">
            <p>Subject</p>
            <input type="text" name="subject" id="editSubject" class="add-student-textbox" required></input>
          </div>
          <div class="add-student-container">
            <p>Day</p>
            <input type="text" name="day" id="editDay" class="add-student-textbox" required></input>
          </div>
          <div class="add-student-container">
            <p>Start Time</p>
            <input type="text" name="start_time" id="editStartTime" class="add-student-textbox" required></input>
          </div>
          <div class="add-student-container">
            <p>End Time</p>
            <input type="text" name="end_time" id="editEndTime" class="add-student-textbox" required></input>
          </div>
          <div class="add-student-container">
            <p>Professor</p>
            <input type="email" name="professor" id="editProfessor" class="add-student-textbox" required></input>
          </div>
          <div class="add-button-container">
            <button type="submit" name="edit-student" id="saveStudentButton" class="add-button">SAVE</button>
          </div>
        </form>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
    <script src="../js/navbar_controller.js"></script>
    <script src="../js/schedule.js"></script>
    <script>
      function toAdminHomepage() {
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
      function openAddClassModal() {
        var addStudentModal = document.getElementById("addStudentModal");
        addStudentModal.style.display = "block";
      }
      function closeAddClassModal() {
        var addStudentModal = document.getElementById("addStudentModal");
        addStudentModal.style.display = "none";
      }
      function closeEditClassModal() {
        var editStudentModal = document.getElementById("editStudentModal");
        editStudentModal.style.display = "none";
      }
    </script>
  </body>
</html>
