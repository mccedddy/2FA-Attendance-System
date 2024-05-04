<?php 
session_start();
require '../includes/database_connection.php';
date_default_timezone_set('Asia/Manila');
require_once '../includes/encryption.php';
$encryptionHelper = new EncryptionHelper($encryptionKey);

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

// Add student
if (isset($_POST['add-student'])) {
  require '../includes/database_connection.php';
  $lastName = $_POST['last_name'];
  $firstName = $_POST['first_name'];
  $idNumber = $_POST['student_number'];
  $email = $_POST['email'];

  // Hash the password (Default: Last Name)
  $hashedPassword = password_hash($lastName, PASSWORD_DEFAULT);

  // Encrypt email
  $encryptedEmail = $encryptionHelper->encryptData($email);

  // SQL query to insert data into the students table
  $sql = "INSERT INTO professors (last_name, first_name, id_number, email, password)
            VALUES ('$lastName', '$firstName', '$idNumber', '$encryptedEmail', '$hashedPassword')";

  // Execute query
  $stmt = mysqli_prepare($connection, $sql);

  try {
    // Execute query
    mysqli_stmt_execute($stmt);

    // Close the statement
    mysqli_stmt_close($stmt);
    
    header("Location: admin_professor_page.php");
  } catch (mysqli_sql_exception $exception) {
    // Check if duplicate entry
    if ($exception->getCode() == 1062) {
      header("Location: admin_professor_page.php");
      exit; 
    } else {
      throw $exception;
    }
  }
}

// Edit student
if (isset($_POST['edit-student'])) {
  require '../includes/database_connection.php';
  $editLastName = $_POST['last_name'];
  $editFirstName = $_POST['first_name'];
  $editIdNumber = $_POST['student_number'];
  $editEmail = $encryptionHelper->encryptData($_POST['email']);
  $originaIdNumber = $_POST['original_student_number'];

  // SQL query to update data in the students table
  $editSQL = "UPDATE professors 
            SET last_name = '$editLastName', 
                first_name = '$editFirstName', 
                id_number = '$editIdNumber',
                email = '$editEmail' 
            WHERE id_number = '$originaIdNumber'";

  // Execute query
  $stmt = mysqli_prepare($connection, $editSQL);
  
  try {
    // Execute query
    mysqli_stmt_execute($stmt);

    // Close the statement
    mysqli_stmt_close($stmt);
    
    header("Location: admin_professor_page.php");
  } catch (mysqli_sql_exception $exception) {
    // Check if duplicate entry
    if ($exception->getCode() == 1062) {
      header("Location: admin_professor_page.php");
      exit; 
    } else {
      throw $exception;
    }
  }
}

// Fetch class list
require '../includes/database_connection.php';
$classListSQL = "SELECT * FROM professors";
$classListResult = mysqli_query($connection, $classListSQL);
$classList = [];
while ($row = mysqli_fetch_assoc($classListResult)) {
  $studentInfo = [
            'lastName'      => $row['last_name'],
            'firstName'     => $row['first_name'],
            'idNumber' => $row['id_number'],
            'email'         => $encryptionHelper->decryptData($row['email']),
          ];
  $classList[] = $studentInfo;
}
mysqli_free_result($classListResult);
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
      <h2 class="title" id="title">PROFESSORS</h2>
      <div class="table-controls">
        <div class="left">
          <button onclick="openAddProfessorModal()">
            <img src="..\assets\images\icons\plus_white.svg"/>
            New
          </button>
          <button id="editStudentBtn" onclick="openEditProfessorModal()">
            <img src="..\assets\images\icons\pencil_white.svg"/>
            Edit
          </button>
          <button id="deleteStudentsBtn">
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
      <table id="classlistTable" data-cols-width="15,20,20,10,15,35">
        <thead>
          <tr>
            <th data-exclude="true"></th>
            <th>LAST NAME</th>
            <th>FIRST NAME</th>
            <th>ID NUMBER</th>
            <th>EMAIL</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($classList as $student): ?>
            <?php if ($student['lastName'] != 'admin'): ?>
              <tr>
                <td data-exclude="true"><input type="checkbox" name="selectedStudents[]"></td>
                <td><?php echo $student['lastName']; ?></td>
                <td><?php echo $student['firstName']; ?></td>
                <td><?php echo $student['idNumber']; ?></td>
                <td><?php echo $student['email']; ?></td>
              </tr>
            <?php endif; ?>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div style="height:50px;"></div>
    </section>

    <div id="addModal" class="modal-blur">
      <div class="modal-content">
        <div class="top-modal">
          <h6>ADD PROFESSOR</h6>
        </div>
        <span class="close-modal" onclick="closeAddProfessorModal()">&times;</span>
        <form method="POST">
          <div>
            <p>Last Name</p>
            <input type="text" name="last_name" required></input>
          </div>
          <div>
            <p>First Name</p>
            <input type="text" name="first_name" required></input>
          </div>
          <div>
            <p>ID Number</p>
            <input type="text" name="student_number" required></input>
          </div>
          <div>
            <p>Email</p>
            <input type="email" name="email" required></input>
          </div>
          <div class="submit-button-container">
            <button type="submit" name="add-student" id="addButton" class="add-button">ADD</button>
          </div>
        </form>
      </div>
    </div>

    <div id="editModal" class="modal-blur">
      <div class="modal-content">
        <div class="top-modal">
          <h6 id="editStudentTitle">EDIT PROFESSOR</h6>
        </div>
        <span class="close-modal" onclick="closeEditProfessorModal()">&times;</span>
        <form method="POST">
          <input id="originalStudentNumber" name="original_student_number" type="hidden"></input>
          <div>
            <p>Last Name</p>
            <input type="text" name="last_name" id="editLastName" class="modal-input" required></input>
          </div>
          <div>
            <p>First Name</p>
            <input type="text" name="first_name" id="editFirstName" class="modal-input" required></input>
          </div>
          <div>
            <p>ID Number</p>
            <input type="text" name="student_number" id="editStudentNumber" class="modal-input" required></input>
          </div>
          <div>
            <p>Email</p>
            <input type="email" name="email" id="editEmail" class="modal-input" required></input>
          </div>
          <div class="submit-button-container">
            <button type="submit" name="edit-student" id="addButton" class="add-button">ADD</button>
          </div>
        </form>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
    <script src="../js/classlist.js"></script>
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
      function openAddProfessorModal() {
        var addModal = document.getElementById("addModal");
        addModal.style.display = "block";
      }
      function openEditProfessorModal() {
        var editModal = document.getElementById("editModal");
        editModal.style.display = "block";
      }
      function closeAddProfessorModal() {
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
