<?php
session_start();
require '../includes/database_connection.php';
require '../includes/database_operations.php';
require '../includes/utils.php';
redirect('admin');

// Clear selection
unset($_SESSION['selected_section']);

// Fetch section
$sections = fetchSections();

// Section button
if (isset($_POST['section-button'])) {
  sectionHeader('admin_classlist.php');
}
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
    <link rel="stylesheet" href="../css/section.css" />
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
      <h2 class="page-title">SECTIONS</h2>
      <div class="add-section-container">
        <button class="add-section-button" onclick="openAddSectionModal()">
          <img src="..\assets\images\icons\plus.svg">
          <p>ADD SECTION</p>
        </button>
      </div>
      <div class="section-button-container">
        <?php foreach ($sections as $section): ?>
          <form method="POST">
            <span class="delete-section" onclick="openDeleteSectionModal()">&times;</span>
            <input type="hidden" name="section" value="<?php echo $section; ?>">
            <button type="submit" name="section-button" class="section-button"><?php echo $section; ?></button>
          </form>
        <?php endforeach; ?>
        <form method="POST">
          <input type="hidden" name="section" value="professors">
          <button type="submit" name="section-button" class="section-button">PROFESSORS</button>
        </form>
      </div>
    </section>

    <div id="addModal" class="modal-blur">
      <div class="modal-content">
        <div class="top-modal">
          <h6>ADD SECTION</h6>
        </div>
        <span class="close-modal" onclick="closeAddSectionModal()">&times;</span>
        <form method="POST" class="add-student-form">
          <div class="add-student-container">
            <p>Course and Year</p>
            <input type="text" name="year" class="year-section-textbox" required></input>
            <p>Section</p>
            <input type="text" name="section" class="year-section-textbox" required></input>
          </div>
          <div class="submit-button-container">
            <button type="submit" name="add-section" id="addButton" class="add-button">ADD</button>
          </div>
        </form>
      </div>
    </div>

    <div id="deleteModal" class="modal-blur">
      <div class="modal-content">
        <div class="top-modal">
          <h6>DELETE SECTION</h6>
        </div>
        <span class="close-modal" onclick="closeDeleteSectionModal()">&times;</span>
        <img src="../assets/images/graphics/girl_trash.png" style="height: 40%; width: 40%;" />
        <form method="POST">
          <h5 id="deleteSectionMessage" style="margin-bottom: 10px; text-align: center;"></h5>
          <p style="margin: 0px; text-align: center;">WARNING: All of the student data in this section will be deleted.</p>
          <p style="margin: 10px; text-align: center;">Are you sure you want to delete this section?</p>
          <div class="submit-button-container">
            <button type="submit" name="confirm-delete-section" id="deleteButton">DELETE</button>
          </div>
        </form>
      </div>
    </div>

    <script src="../js/section.js"></script>
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
      function openAddSectionModal() {
        var addSectionModal = document.getElementById("addModal");
        addSectionModal.style.display = "block";
      }
      function closeAddSectionModal() {
        var addSectionModal = document.getElementById("addModal");
        addSectionModal.style.display = "none";
      }
      function openDeleteSectionModal() {
        var addSectionModal = document.getElementById("deleteModal");
        addSectionModal.style.display = "block";
      }
      function closeDeleteSectionModal() {
        var addSectionModal = document.getElementById("deleteModal");
        addSectionModal.style.display = "none";
      }
    </script>
  </body>
</html>

