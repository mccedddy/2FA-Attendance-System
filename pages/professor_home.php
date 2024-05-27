<?php
session_start();
require '../includes/database_connection.php';
require '../includes/database_operations.php';
require '../includes/utils.php';
redirect('professor');

// Clear selection
unset($_SESSION['selected_section']);
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
    <link rel="stylesheet" href="../css/home.css" />
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
          <h6><?php echo $userId?></h6>
        </div>
      </div>
      <h2 class="page-title">HOME</h2>
      <div class="section-button-container">
        <button class="section-button" onclick="toSection()" onmouseover="changeSectionImage(true)" onmouseout="changeSectionImage(false)">
          <img src="../assets/images/icons/group_large_dark.svg" id="sectionButtonImg" />
          CHECK ATTENDANCE
        </button>
        <button class="section-button" onclick="toSettings()" onmouseover="changeSettingsImage(true)" onmouseout="changeSettingsImage(false)">
          <img src="../assets/images/icons/settings_large_dark.svg" id="settingsButtonImg" />
          SETTINGS
        </button>
      </div>
    </section>
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


      function changeSectionImage(isHovered) {
        var imgElement = document.getElementById("sectionButtonImg");

        if (isHovered) {
          imgElement.src = "../assets/images/icons/group_large.svg";
        } else {
          imgElement.src = "../assets/images/icons/group_large_dark.svg"; 
        }
      }

      function changeScheduleImage(isHovered) {
        var imgElement = document.getElementById("scheduleButtonImg");

        if (isHovered) {
          imgElement.src = "../assets/images/icons/table_large.svg"; 
        } else {
          imgElement.src = "../assets/images/icons/table_large_dark.svg";
        }
      }

      function changeSubjectsImage(isHovered) {
        var imgElement = document.getElementById("subjectsButtonImg");

        if (isHovered) {
          imgElement.src = "../assets/images/icons/book_large.svg"; 
        } else {
          imgElement.src = "../assets/images/icons/book_large_dark.svg"; 
        }
      }

      function changeSettingsImage(isHovered) {
        var imgElement = document.getElementById("settingsButtonImg");

        if (isHovered) {
          imgElement.src = "../assets/images/icons/settings_large.svg";
        } else {
          imgElement.src = "../assets/images/icons/settings_large_dark.svg"; 
        }
      }
    </script>
  </body>
</html>
