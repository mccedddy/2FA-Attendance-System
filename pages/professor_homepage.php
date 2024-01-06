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
        <h1 class="title">Computer Engineering Department Sections</h1>
        <div class="section-button-container">
          <a href="attendance_page.php"><button class="section-button">SECTION 4-1</button></a>
          <a href="attendance_page.php"><button class="section-button">SECTION 4-2</button></a>
          <a href="attendance_page.php"><button class="section-button">SECTION 4-3</button></a>
          <a href="attendance_page.php"><button class="section-button">SECTION 4-4</button></a>
          <a href="attendance_page.php"><button class="section-button">SECTION 4-5</button></a>
          <a href="attendance_page.php"><button class="section-button">SECTION 4-6</button></a>
        </div>
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
