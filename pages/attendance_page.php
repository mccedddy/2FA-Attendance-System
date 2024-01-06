<?php 
$sectionPage = '4-6';
$students = [
  ['name' => 'Jacob, Kyle Lester', 'student_number' => '2020-XXXXX-MN-0', 'room' => '312', 'time' => '3:00 PM'],
  ['name' => 'Libre, Maria Angela', 'student_number' => '2020-XXXXX-MN-0', 'room' => '312', 'time' => '3:00 PM'],
  ['name' => 'Macallan, Daniel John Cedric', 'student_number' => '2020-12082-MN-0', 'room' => '312', 'time' => '3:00 PM'],
  ['name' => 'Rebulanan, Brient Neilson', 'student_number' => '2020-XXXXX-MN-0', 'room' => '312', 'time' => '3:00 PM'],
]
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
  </head>
  <body>
    <nav class="navbar">
      <div class="navbar-top">
        <img src="..\assets\images\icons\arrow_left.svg" id="closeNavbar" class="nav-button" onclick="toggleMobileNavbar()"/>
        <a onclick="toProfessorHomepage()"><img src="..\assets\images\logos\pup_logo.png" /></a>
        <a onclick="toProfessorHomepage()"><img src="..\assets\images\icons\notepad.svg" class="nav-button"/></a>
      </div>
      <a href="..\index.php"><img src="..\assets\images\icons\logout.svg" class="nav-button"/></a>
    </nav>
    <section class="main">
        <div class="header">
          <div class="mobile-navbar-toggle" onclick="toggleMobileNavbar()">
            <img src="..\assets\images\icons\hamburger.svg" class="hamburger">
          </div>
            <a onclick="toProfessorHomepage()"><h1>PUP HDF Attendance System</h1></a>
        </div>
        <h1 class="title">SECTION <?php echo $sectionPage ?></h1>
        <div class="search-container">
          <div class="search-textbox">
            <input type="text" name="search" id="search">
            <img src="..\assets\images\icons\search.svg"/>
          </div>
        </div>
        <select id="roomFilter">
            <option value="option1">ALL</option>
            <option value="option2">ROOM 300</option>
            <option value="option2">ROOM 310</option>
            <option value="option2">ROOM 311</option>
            <option value="option2">ROOM 312</option>
            <option value="option2">ROOM 313</option>
            <option value="option2">ROOM 314</option>
            <option value="option2">ROOM 315</option>
            <option value="option2">ROOM 316</option>
        </select>
        <div class="filters-and-export">
          <div class="filters-container">
            <input type="date" id="date" class="date-time-filter" required>
            <div class="time-container">
              <input type="time" id="startTime" class="date-time-filter" required>
              <input type="time" id="endTime" class="date-time-filter" required>
            </div>
          </div>
          <button id="export"><p>EXPORT DATA</p><img src="..\assets\images\icons\download.svg"/></button>
        </div>
        <table id="attendanceTable">
            <thead>
              <tr>
                <th>STUDENT NAME</th>
                <th>STUDENT NUMBER</th>
                <th>ROOM</th>
                <th>TIME IN</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($students as $student): ?>
                <tr>
                  <td><?php echo $student['name']; ?></td>
                  <td><?php echo $student['student_number']; ?></td>
                  <td><?php echo $student['room']; ?></td>
                  <td><?php echo $student['time']; ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
    </section>
    <script src="../js/navbar_controller.js"></script>
    <script>
      function toLogin() {
        window.location.href = "../index.php";
        return false;
      }
      function toProfessorHomepage() {
        window.location.href = "professor_homepage.php";
        return false;
      }
    </script>
  </body>
</html>
