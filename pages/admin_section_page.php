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
      <h1 class="title" id="title">SECTION <?php echo $sectionPage ?></h1>
      <div class="search-container">
        <div class="search-textbox">
          <input type="text" name="search" id="search" value="">
          <img src="..\assets\images\icons\search.svg"/>
        </div>
      </div>
      <div class="edit-and-export">
        <div class="edit-container">
          <button class="edit-class-button" onclick="openAddStudentModal()">
            <img src="..\assets\images\icons\plus_white.svg"/>
            <p>New</p>
          </button>
          <button class="edit-class-button">
            <img src="..\assets\images\icons\pencil_white.svg"/>
            <p>Edit</p>
          </button>
          <button class="edit-class-button">
            <img src="..\assets\images\icons\trash_white.svg"/>
            <p>Delete</p>
          </button>
        </div>
        <button id="export"><p>EXPORT DATA</p><img src="..\assets\images\icons\download.svg"/></button>
      </div>
      <table id="attendanceTable">
        <thead>
          <tr>
            <th></th>
            <th>LAST NAME</th>
            <th>FIRST NAME</th>
            <th>STUDENT NUMBER</th>
            <th>SECTION</th>
            <th>NFC UID</th>
            <th>EMAIL</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><input type="checkbox"></td>
            <td>Jacob</td>
            <td>Kyle Lester</td>
            <td>2020-12100-MN-0</td>
            <td>4-6</td>
            <td>63 A4 BC 23</td>
            <td>kylelesterjacob17@gmail.com</td>
          </tr>
          <tr>
            <td><input type="checkbox"></td>
            <td>Libre</td>
            <td>Maria Angela</td>
            <td>2020-12329-MN-0</td>
            <td>4-6</td>
            <td>63 A1 C2 B7</td>
            <td>libremaan@gmail.com</td>
          </tr>
          <tr>
            <td><input type="checkbox"></td>
            <td>Macallan</td>
            <td>Daniel John Cedric</td>
            <td>2020-12082-MN-0</td>
            <td>4-6</td>
            <td>31 CE 3E 6B</td>
            <td>cedricmacallan@gmail.com</td>
          </tr>
          <tr>
            <td><input type="checkbox"></td>
            <td>Rebulanan</td>
            <td>Brient Neilson</td>
            <td>2020-08935-MN-0</td>
            <td>4-6</td>
            <td>38 A1 3E 43</td>
            <td>brientneilson@gmail.com</td>
          </tr>
        <td>/tbody>
      <td>/table>
    </section>

    <div id="addSectionModal" class="modal-blur">
      <div class="modal-content">
        <div class="top-modal">
          <h6>ADD STUDENT</h6>
        </div>
        <span class="close-modal" onclick="closeAddStudentModal()">&times;</span>
        <form class="add-student-form">
          <div class="add-student-container">
            <p>Last Name</p>
            <input type="text" name="class-list" class="add-student-textbox" required></input>
          </div>
          <div class="add-student-container">
            <p>First Name</p>
            <input type="text" name="class-list" class="add-student-textbox" required></input>
          </div>
          <div class="add-student-container">
            <p>Student Number</p>
            <input type="text" name="class-list" class="add-student-textbox" required></input>
          </div>
          <div class="add-student-container">
            <p>NFC UID</p>
            <input type="text" name="class-list" class="add-student-textbox" required></input>
          </div>
          <div class="add-student-container">
            <p>Email</p>
            <input type="email" name="class-list" class="add-student-textbox" required></input>
          </div>
          <div class="add-student-container">
            <p>Year Number</p>
            <input type="text" name="class-list" class="year-section-textbox" value="<?php echo $sectionPage[0]; ?>" required readonly></input>
            <p>Section Number</p>
            <input type="text" name="class-list" class="year-section-textbox" value="<?php echo $sectionPage[2]; ?>" required readonly></input>
          </div>
          <div class="add-button-container">
            <button type="submit" id="addButton">ADD</button>
          </div>
        </form>
      </div>
    </div>

    <script src="../js/table2excel.js"></script>
    <script src="../js/navbar_controller.js"></script>
    <script src="../js/attendance.js"></script>
    <script>
      function toAdminHomepage() {
        window.location.href = "admin_homepage.php";
        return false;
      }
      function toSettings() {
        window.location.href = "admin_settings_page.php";
        return false;
      }
      function openAddStudentModal() {
        var addSectionModal = document.getElementById("addSectionModal");
        addSectionModal.style.display = "block";
      }
      function closeAddStudentModal() {
        var addSectionModal = document.getElementById("addSectionModal");
        addSectionModal.style.display = "none";
      }
    </script>
  </body>
</html>
