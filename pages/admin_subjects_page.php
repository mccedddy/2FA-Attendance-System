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
    header("Location: professor_home.php");
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

// Add subject
if (isset($_POST['add-subject'])) {
  require '../includes/database_connection.php';
  $subjectCode = $_POST['subject_code'];
  $subjectName = $_POST['subject_name'];

  // SQL query
  $sql = "INSERT INTO subjects (subject_code, subject_name)
            VALUES ('$subjectCode', '$subjectName')";

  // Use prepared statement
  $stmt = mysqli_prepare($connection, $sql);

  try {
    // Execute query
    mysqli_stmt_execute($stmt);

    // Close the statement
    mysqli_stmt_close($stmt);
    
    header("Location: admin_subjects_page.php");
  } catch (mysqli_sql_exception $exception) {
    // Check if duplicate entry
    if ($exception->getCode() == 1062) {
      header("Location: admin_subjects_page.php");
      exit; 
    } else {
      throw $exception;
    }
  }
}

// Edit subject
if (isset($_POST['edit-subject'])) {
  require '../includes/database_connection.php';
  $editSubjectCode = $_POST['subject_code'];
  $editSubjectName = $_POST['subject_name'];
  $originalStudentNumber = $_POST['original_subject_code'];

  // SQL query to update data in the students table
  $editSQL = "UPDATE subjects 
            SET subject_code = '$editSubjectCode', 
                subject_name = '$editSubjectName' 
            WHERE subject_code = '$originalStudentNumber'";

  // Execute query
  $stmt = mysqli_prepare($connection, $editSQL);

  try {
    // Execute query
    mysqli_stmt_execute($stmt);

    // Close the statement
    mysqli_stmt_close($stmt);
    
    header("Location: admin_subjects_page.php");
  } catch (mysqli_sql_exception $exception) {
    // Check if duplicate entry
    if ($exception->getCode() == 1062) {
      header("Location: admin_subjects_page.php");
      exit; 
    } else {
      throw $exception;
    }
  }
}

// Fetch subjects
require '../includes/database_connection.php';
$subjectsSQL = "SELECT * FROM subjects";
$subjectsResult = mysqli_query($connection, $subjectsSQL);
$subjects = [];
while ($row = mysqli_fetch_assoc($subjectsResult)) {
  $subjectInfo = [
            'subjectCode'       => $row['subject_code'],
            'subjectName'       => $row['subject_name'],
          ];
  $subjects[] = $subjectInfo;
}
mysqli_free_result($subjectsResult);
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
      <h1 class="title">Computer Engineering Subjects</h1>
      <div class="edit-and-export">
        <div class="edit-container">
          <button class="edit-class-button" onclick="openAddSubjectModal()">
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
          </tr>
        </thead>
        <tbody>
          <?php foreach ($subjects as $subject): ?>
            <tr>
              <td data-exclude="true"><input type="checkbox" name="selectedSubjects[]"></td>
              <td><?php echo $subject['subjectCode']; ?></td>
              <td><?php echo $subject['subjectName']; ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div style="height:50px;"></div>
    </section>

    <div id="addStudentModal" class="modal-blur">
      <div class="modal-content">
        <div class="top-modal">
          <h6>ADD SUBJECT</h6>
        </div>
        <span class="close-modal" onclick="closeAddSubjectModal()">&times;</span>
        <form method="POST" class="add-student-form">
          <div class="add-student-container">
            <p>Subject Code</p>
            <input type="text" name="subject_code" class="add-student-textbox" required></input>
          </div>
          <div class="add-student-container">
            <p>Subject Name</p>
            <input type="text" name="subject_name" class="add-student-textbox" required></input>
          </div>
          <div class="add-button-container">
            <button type="submit" name="add-subject" id="addButton" class="add-button">ADD</button>
          </div>
        </form>
      </div>
    </div>

    <div id="editStudentModal" class="modal-blur">
      <div class="modal-content">
        <div class="top-modal">
          <h6 id="editStudentTitle">EDIT SUBJECT</h6>
        </div>
        <span class="close-modal" onclick="closeEditSubjectModal()">&times;</span>
        <form method="POST" class="add-student-form">
          <input id="originalStudentNumber" name="original_subject_code" type="hidden"></input>
          <div class="add-student-container">
            <p>Subject Code</p>
            <input type="text" name="subject_code" id="editSubjectCode" class="add-student-textbox" required></input>
          </div>
          <div class="add-student-container">
            <p>Subject Name</p>
            <input type="text" name="subject_name" id="editSubjectName" class="add-student-textbox" required></input>
          </div>
          <div class="add-button-container">
            <button type="submit" name="edit-subject" id="saveStudentButton" class="add-button">SAVE</button>
          </div>
        </form>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
    <script src="../js/subjects.js"></script>
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
      function openAddSubjectModal() {
        var addStudentModal = document.getElementById("addStudentModal");
        addStudentModal.style.display = "block";
      }
      function closeAddSubjectModal() {
        var addStudentModal = document.getElementById("addStudentModal");
        addStudentModal.style.display = "none";
      }
      function closeEditSubjectModal() {
        var editStudentModal = document.getElementById("editStudentModal");
        editStudentModal.style.display = "none";
      }
    </script>
  </body>
</html>
