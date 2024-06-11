<?php 
session_start();
require '../includes/database_connection.php';
require '../includes/database_operations.php';
require '../includes/utils.php';
redirect('professor');
date_default_timezone_set('Asia/Manila');
require_once '../includes/encryption.php';
$encryptionHelper = new EncryptionHelper($encryptionKey);
$weekBefore = date('Y-m-d', strtotime('-1 week'));
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
    <link rel="stylesheet" href="../css/analytics.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
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
        <a onclick="toAnalytics()"
          ><img
            src="..\assets\images\icons\graph.svg"
            alt="group"
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
          <h3 onclick="toProfessorHomepage()" class="title">2FA Attendance System</h3>
        </div>
        <div class="right">
          <h6><?php echo $userName; ?></h6>
          <h6><?php echo $userId; ?></h6>
        </div>
      </div>
      <h2 class="page-title" id="title">ATTENDANCE ANALYTICS</h2>
      <div class="table-controls">
        <div class="left">
          <div>
            <select id="subject">
              <option value="ALL">ALL</option>
                <?php
                  require '../includes/database_connection.php';

                  $sql = "SELECT * FROM sections";
                  $result = mysqli_query($connection, $sql); 

                  while ($row = mysqli_fetch_assoc($result)) {
                    $option = $row['section'];
                    echo "<option class='subjectOption' value='$option'>$option</option>";
                  }

                  mysqli_free_result($result);
                  mysqli_close($connection);
                ?>
            </select>
            <select id="subject">
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
          </div>
          <div>
            <input type="date" id="startDate" class="date-filter" required value="<?php echo $weekBefore; ?>">
            <input type="date" id="endDate" class="date-filter" required value="<?php echo date('Y-m-d'); ?>">
          </div>
          <div style="display:flex; gap:20px;">
            <button id="attendanceOverviewButton" class="page-button" onclick="toAttendanceOverview()">ATTENDANCE OVERVIEW</button>
            <button id="hoursAbsentButton" class="page-button" onclick="toHoursAbsent()">GENERATION FOR NO. OF HOURS ABSENT</button>
          </div>
        </div>
      </div>

      <div id="attendanceOverview">
        <div class="summary">
          <div class="container">
            <img src="../assets/images/icons/group_overview.svg" />
            <div class="text-container">
              <p>TOTAL NUMBER OF STUDENTS</p>
              <h2>100</h2>
            </div>
          </div>
          <div class="container">
            <img src="../assets/images/icons/check_overview.svg" />
            <div class="text-container">
              <p>OVERALL ATTENDANCE PERCENTAGE</p>
              <h2>80%</h2>
            </div>
          </div>
          <div class="container">
            <img src="../assets/images/icons/time_overview.svg" />
            <div class="text-container">
              <p>LATE PERCENTAGE</p>
              <h2>50%</h2>
            </div>
          </div>
        </div>
        <div class="graphs">
          <h4>ATTENDANCE OVERVIEW</h4>
          <canvas id="attendanceOverviewTable" width="800" height="300"></canvas>
        </div>
      </div>

      <div id="hoursAbsent">
        <div class="summary">
          <div class="container">
            <img src="../assets/images/icons/group_overview.svg" />
            <div class="text-container">
              <p>TOTAL NUMBER OF STUDENTS</p>
              <h2>100</h2>
            </div>
          </div>
          <div class="container">
            <img src="../assets/images/icons/time_overview.svg" />
            <div class="text-container">
              <p>LATE PERCENTAGE</p>
              <h2>50%</h2>
            </div>
          </div>
        </div>
        <table id="attendanceTable" data-cols-width="15,20,20,10,15,35">
          <thead>
            <tr>
              <th>STUDENT NAME</th>
              <th>STUDENT NUMBER</th>
              <th>SECTION</th>
              <th>NO. OF HOURS ABSENT</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Jacob, Kyle Lester</td>
              <td>2020-12100-MN-0</td>
              <td>BSCPE 4-6</td>
              <td>10</td>
            </tr>
            <tr>
              <td>Libre, Maria Angela</td>
              <td>2020-23923-MN-0</td>
              <td>BSCPE 4-6</td>
              <td>8</td>
            </tr>
            <tr>
              <td>Macallan, Daniel John Cedric</td>
              <td>2020-12082-MN-0</td>
              <td>BSCPE 4-6</td>
              <td>7</td>
            </tr>
            <tr>
              <td>Rebulanan, Brient Neilson</td>
              <td>2020-12345-MN-0</td>
              <td>BSCPE 4-6</td>
              <td>3</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div style="padding:15px;"></div>
    </section>
            
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="../js/analytics.js"></script>
    <script src="../js/toastr.js"></script>
    <script src="../js/navbar_controller.js"></script>
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
        window.location.href = "professor_settings.php";
        return false;
      }
      function toSection() {
        window.location.href = "professor_sections.php";
        return false;
      }
      function toAnalytics() {
        window.location.href = "professor_analytics.php";
        return false; 
      }

      var attendanceOverview = document.getElementById("attendanceOverview");
      var attendanceOverviewButton = document.getElementById("attendanceOverviewButton");
      var hoursAbsent = document.getElementById("hoursAbsent");
      var hoursAbsentButton = document.getElementById("hoursAbsentButton");
      attendanceOverviewButton.style.border = "0px";
      hoursAbsentButton.style.border = "0px";
      attendanceOverviewButton.style.borderBottom = "2px solid #810000";

      function toAttendanceOverview() {
        attendanceOverview.style.display = "block";
        hoursAbsent.style.display = "none";
        attendanceOverviewButton.style.borderBottom = "2px solid #810000";
        hoursAbsentButton.style.borderBottom = "0px solid #810000";
      }

      function toHoursAbsent() {
        attendanceOverview.style.display = "none";
        hoursAbsent.style.display = "block";
        attendanceOverviewButton.style.borderBottom = "0px solid #810000";
        hoursAbsentButton.style.borderBottom = "2px solid #810000";
      }
    </script>
  </body>
</html>
