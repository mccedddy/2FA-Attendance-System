<?php 
session_start();
require '../includes/database_connection.php';
require '../includes/database_operations.php';
require '../includes/utils.php';
redirect('admin');
date_default_timezone_set('Asia/Manila');
require_once '../includes/encryption.php';
$encryptionHelper = new EncryptionHelper($encryptionKey);

// Check selected section
$sectionPage = checkSection();

// Add student
if (isset($_POST['add-student'])) {
  addProfile('student');
}

// Edit student
if (isset($_POST['edit-student'])) {
  require '../includes/database_connection.php';
  $editLastName = $_POST['last_name'];
  $editFirstName = $_POST['first_name'];
  $editIdNumber = $_POST['id_number'];
  $editNfcUid = $_POST['nfc_uid'];
  $editEmail = $encryptionHelper->encryptData($_POST['email']);
  $originalIdNumber = $_POST['original_id_number'];

  // SQL query to update data in the students table
  $editSQL = "UPDATE students 
            SET last_name = '$editLastName', 
                first_name = '$editFirstName', 
                id_number = '$editIdNumber',
                nfc_uid = '$editNfcUid', 
                email = '$editEmail' 
            WHERE id_number = '$originalIdNumber'";

  // Execute query
  $stmt = mysqli_prepare($connection, $editSQL);

  try {
    // Execute query
    mysqli_stmt_execute($stmt);

    // Close the statement
    mysqli_stmt_close($stmt);
    
    header("Location: admin_classlist.php");
  } catch (mysqli_sql_exception $exception) {
    // Check if duplicate entry
    if ($exception->getCode() == 1062) {
      header("Location: admin_classlist.php");
      exit; 
    } else {
      throw $exception;
    }
  }
}

// Fetch Features
$features = fetchFeatures();

// Fetch classlist
$classlist = fetchClasslist('students', "WHERE section = '$sectionPage' ORDER BY last_name");
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>2FA Attendance System</title>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
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
          <h3 onclick="toAdminHomepage()" class="title">2FA Attendance System</h3>
        </div>
        <div class="right">
          <h6>ADMIN</h6>
        </div>
      </div>
      <h2 class="page-title" id="title"><?php echo $sectionPage ?> CLASSLIST</h2>
      <div class="table-controls">
        <div class="left">
          <button onclick="openAddStudentModal()">
            <img src="..\assets\images\icons\plus_white.svg"/>
            New
          </button>
          <button id="editStudentBtn">
            <img src="..\assets\images\icons\pencil_white.svg"/>
            Edit
          </button>
          <button id="deleteStudentsBtn">
            <img src="..\assets\images\icons\trash_white.svg"/>
            Delete
          </button>
          <button id="processImages" style="width: 140px;">
            Process Images
          </button>
        </div>
        <div class="right">
          <button class="download-template" onclick="downloadTemplate()">
            <img src="..\assets\images\icons\arrow_down.svg"/>
            <img src="..\assets\images\icons\template.svg"/>
          </button>
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
            <th data-exclude="true">#</th>
            <th>LAST NAME</th>
            <th>FIRST NAME</th>
            <th>STUDENT NUMBER</th>
            <th>SECTION</th>
            <th>NFC UID</th>
            <th>EMAIL</th>
            <th style="display: none;" data-exclude="true">ID</th>
            <th data-exclude="true">REGISTERED</th>
            <th data-exclude="true">REGISTER FACE</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $index = 1; // Initialize the counter variable
          foreach ($classlist as $student): ?>
            <tr>
              <td data-exclude="true"><input type="checkbox" name="selectedStudents[]"></td>
              <td data-exclude="true"><?php echo $index; ?></td> <!-- Display the index -->
              <td><?php echo $student['lastName']; ?></td>
              <td><?php echo $student['firstName']; ?></td>
              <td><?php echo $student['idNumber']; ?></td>
              <td><?php echo $student['section']; ?></td>
              <td><?php echo $student['nfcUid']; ?></td>
              <td><?php echo $student['email']; ?></td>
              <td style="display: none;" data-exclude="true"><?php echo $student['id']; ?></td>
              <td data-exclude="true"></td>
              <td data-exclude="true"><button id="registerStudentBtn">Register</button></td>
            </tr>
          <?php
          $index++; // Increment the counter variable
          endforeach; ?>
        </tbody>
      </table>
      <div style="padding:15px;"></div>
    </section>

    <div id="addModal" class="modal-blur">
      <div class="modal-content">
        <div class="top-modal">
          <h6>ADD STUDENT</h6>
        </div>
        <span class="close-modal" onclick="closeAddStudentModal()">&times;</span>
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
            <p>Student Number</p>
            <input type="text" name="id_number" required></input>
          </div>
          <div>
            <p>NFC UID</p>
            <input type="text" name="nfc_uid" required></input>
          </div>
          <div>
            <p>Email</p>
            <input type="email" name="email" required></input>
          </div>
          <div>
            <p>Course and Year</p>
            <input type="text" name="year" value="<?php echo explode('-', $sectionPage)[0]; ?>" required readonly></input>
            <p>Section</p>
            <input type="text" name="section"  value="<?php echo explode('-', $sectionPage)[1]; ?>" required readonly></input>
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
          <h6 id="editStudentTitle">EDIT STUDENT</h6>
        </div>
        <span class="close-modal" onclick="closeEditStudentModal()">&times;</span>
        <form method="POST">
          <input id="originalIdNumber" name="original_id_number" type="hidden"></input>
          <div>
            <p>Last Name</p>
            <input type="text" name="last_name" id="editLastName" class="modal-input" required></input>
          </div>
          <div>
            <p>First Name</p>
            <input type="text" name="first_name" id="editFirstName" class="modal-input" required></input>
          </div>
          <div>
            <p>Student Number</p>
            <input type="text" name="id_number" id="editIdNumber" class="modal-input" required></input>
          </div>
          <div>
            <p>NFC UID</p>
            <input type="text" name="nfc_uid" id="editNfcUid" class="modal-input" required></input>
          </div>
          <div>
            <p>Email</p>
            <input type="email" name="email" id="editEmail" class="modal-input" required></input>
          </div>
          <div>
            <p>Course and Year</p>
            <input type="text" name="year" class="modal-input" value="<?php echo explode('-', $sectionPage)[0]; ?>" required readonly></input>
            <p>Section</p>
            <input type="text" name="section" class="modal-input" value="<?php echo explode('-', $sectionPage)[1]; ?>" required readonly></input>
          </div>
          <div class="submit-button-container">
            <button type="submit" name="edit-student" id="addButton" class="add-button">SAVE</button>
          </div>
        </form>
      </div>
    </div>

    <div id="registerModal" class="modal-blur">
      <div class="modal-content">
        <div class="top-modal">
          <h6 id="registerStudentTitle">REGISTER STUDENT FACE</h6>
        </div>
        <span class="close-modal" onclick="closeRegisterStudentModal()">&times;</span>
        <form method="POST">
          <div style="display: flex; justify-content: center; align-items: center; margin: 20px; position: relative;">
            <video id="camera-stream" width="248" height="248" style="object-fit: cover;"></video>
            <canvas id="canvas" style="position: absolute; top: 0; left: 200; width: 248px; height: 248px;"></canvas>
          </div>
          <p id="lastName"></p>
          <p id="firstName"></p>
          <p id="idNumber"></p>
          <div class="submit-button-container">
            <button id="capture" class="add-button">CAPTURE</button>
          </div>
          <div class="submit-button-container">
            <button id="clearData" class="add-button">CLEAR DATA</button>
          </div>
        </form>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="../js/toastr.js"></script>
    <script src="../js/navbar_controller.js"></script>
    <script src="../js/classlist.js"></script>
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
      function openAddStudentModal() {
        var addModal = document.getElementById("addModal");
        addModal.style.display = "block";
      }
      function openEditStudentModal() {
        var editModal = document.getElementById("editModal");
        editModal.style.display = "block";
      }
      function closeAddStudentModal() {
        var addModal = document.getElementById("addModal");
        addModal.style.display = "none";
      }
      function closeEditStudentModal() {
        var editModal = document.getElementById("editModal");
        editModal.style.display = "none";
      }
      
      function closeRegisterStudentModal() {
        var editModal = document.getElementById("registerModal");
        editModal.style.display = "none";
      }

      function downloadTemplate() {
          var fileUrl = '../templates/classlist_template.xlsx';

          fetch(fileUrl)
          .then(response => response.blob())
          .then(blob => {
              var url = URL.createObjectURL(blob);
              var link = document.createElement('a');
              link.href = url;
              link.download = 'Classlist Template.xlsx'; 

              document.body.appendChild(link);
              link.click();
              document.body.removeChild(link);
          })
          .catch(error => console.error('Error downloading file:', error));
      }
      var phpArray = <?php echo json_encode($features); ?>;
      var table = document.getElementById("classlistTable");
      for (var i = 1; i < table.rows.length; i++) { // Start from 1 to skip the header row
        var contentCell = table.rows[i].cells[4]; // Index of content cell
        var registeredCell = table.rows[i].cells[9]; // Index of registered cell
        var idNumber = contentCell.innerHTML.trim(); // Assuming id_numbers are stored in the content cell

        if (phpArray.includes(idNumber)) {
          // Do something if idNumber is in phpArray
          registeredCell.innerHTML = "Yes"; // Example action
        } else {
          // Do something if idNumber is not in phpArray
          registeredCell.innerHTML = "No"; // Example action
        }
      }
    </script>
  </body>
</html>