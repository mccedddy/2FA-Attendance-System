<?php
session_start();
require '../includes/database_connection.php';
require '../includes/database_operations.php';
require '../includes/utils.php';
redirect('professor');
require_once '../includes/encryption.php';
$error_message = '';


// Change email and password details
if (isset($_POST['change-email'])) {
  require '../includes/database_connection.php';
  $newEmail = $_POST['new_email'];
  $confirmEmail = $_POST['confirm_email'];
  $encryptEmail = $encryptionHelper->encryptData($confirmEmail);
  
  if ($confirmEmail != $userEmail && 
    $newEmail != $userEmail && 
    $newEmail == $confirmEmail) {
      
      $sql = "UPDATE professors SET email = '$encryptEmail' WHERE id_number = '$userId'";

     // Execute query
    $stmt = mysqli_prepare($connection, $sql);
    
    try {
      // Execute query
      mysqli_stmt_execute($stmt);

      // Close the statement
      mysqli_stmt_close($stmt);
      $_SESSION['user_email'] = $newEmail;
      
      header("Location: professor_settings.php");
    } catch (mysqli_sql_exception $exception) {
      // Check if duplicate entry
      if ($exception->getCode() == 1062) {
        header("Location: professor_settings.php");
        exit; 
      } else {
        throw $exception;
      }
    }
  } else if ($newEmail != $confirmEmail) {
    header("Location: professor_settings_page.php");
  } else {
    header("Location: professor_settings_page.php");
  }
}

// Change email and password details
if (isset($_POST['change-password'])) {
  require '../includes/database_connection.php';
  $currentPassword = $_POST['current_password'];
  $newPassword = $_POST['new_password'];
  $confirmPassword = $_POST['confirm_password'];
  $passwordLength = strlen($confirmPassword);
  $hashedPassword = password_hash($confirmPassword, PASSWORD_DEFAULT);

  if(password_verify($currentPassword, $userPassword)){
    if($newPassword == $confirmPassword){
      if($passwordLength>=8){
        $sql = "UPDATE professors SET password = '$hashedPassword' WHERE id_number = '$userId'";

            // Execute query
        $stmt = mysqli_prepare($connection, $sql);
        
        try {
          // Execute query
          mysqli_stmt_execute($stmt);

          // Close the statement
          mysqli_stmt_close($stmt);
          $_SESSION['user_password'] = $hashedPassword;
          
          header("Location: professor_settings.php");
        } catch (mysqli_sql_exception $exception) {
          // Check if duplicate entry
          if ($exception->getCode() == 1062) {
            header("Location: professor_settings.php");
            exit; 
          } else {
            throw $exception;
          }
        }
      }else{
        header("Location: professor_settings.php");
        $error_message = 'Kindly check the length of the password.';
      }
    }else{
      header("Location: professor_settings_page.php");
    }
  }else{
    header("Location: professor_settings_page.php");
  }
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
    <link rel="stylesheet" href="../css/settings.css" />
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
          <h3 onclick="toProfessorHomepage()" class="title">
            2FA Attendance System
          </h3>
        </div>
        <div class="right">
          <h6><?php echo $userName; ?></h6>
          <h6><?php echo $userId; ?></h6>
        </div>
      </div>
      <div class="settings">
        <h2 class="page-title" id="title">Account Settings</h2>
        <div class = "tableContainer">
          <table class = "tableInfo" id = "basicInfoTable">
            <thead>
              <tr>
                  <th><h5>Basic Info<h5></th>
                  <th></th>
                  <th></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Name</td>
                <td><?php echo $userName; ?></td>
                <td></td>
              </tr>
              <tr>
                <td>Email</td>
                <td><?php echo $userEmail; ?></td>
                <td>
                <div class="change-button-container">
                    <button onclick="openChangeEmailModal()" class="change-button" id = "changeEmailBtn">CHANGE</button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
          <table class = "tableInfo" id = "accountInfoTable">
            <thead>
              <tr>
                  <th><h5>Account Info<h5></th>
                  <th></th>
                  <th></th>
              </th>
              </thead>
            <tbody>
              <tr>
                <td>ID Number</td>
                <td><?php echo $userId; ?></td>
                <td></td>
              </tr>
              <tr>
                <td>Password</td>
                <td>********</td>
                <td>
                  <div class="change-button-container">
                    <button onclick="openChangePassModal()" class="change-button" id="changePassBtn" name = change-password-btn>CHANGE</button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div style="padding:15px;"></div>
      </div>

      <div id="changeEmailModal" class="modal-blur">
        <div class="modal-content">
          <div class="top-modal">
            <h6>CHANGE EMAIL</h6>
          </div>
          <span class="close-modal" onclick="closeChangeEmailModal()">&times;</span>
          <form method="POST" id = "emailForm">
            <div>
              <p>Current Email</p>
              <input type="email" name="current_email" placeholder="<?php echo $userEmail; ?>" id="currentEmail" disabled></input>
            </div>
            <div>
              <p>New Email</p>
              <input type="email" name="new_email" id="newEmail"></input>
            </div>
            <div>
              <p>Confirm Email</p>
              <input type="email" name="confirm_email" id="confirmEmail"></input>
            </div>
            <div id = "emailError"></div>
            <div class="submit-button-container">
              <button type="submit" name="change-email" id="changeEmailButton" class="change-email-button">SAVE</button>
            </div>
          </form>
        </div>
      </div>

      <div id="changePassModal" class="modal-blur">
        <div class="modal-content">
          <div class="top-modal">
            <h6>CHANGE PASSWORD</h6>
          </div>
          <span class="close-modal" onclick="closeChangePassModal()">&times;</span>
          <form method="POST">
            <input id="originalIdNumber" name="original_id_number" type="hidden"></input>
            <div>
              <p>Current Password</p>
              <input type="password" name="current_password" id="currentPassword" required></input>
            </div>
            <div>
              
              <p>New Password</p>
              <input type="password" name="new_password" id="newPassword" required></input>
            </div>
            <div>
              <p>Confirm Password</p>
              <input type="password" name="confirm_password" id="confirmPassword" required></input>
            </div>
            <p>Password must contain at least 8 characters<p>
            <div class="submit-button-container">
              <button type="submit" name="change-password" id="changePasswordButton" class="change-password-button" onclick="openResultModal()">SAVE</button>
            </div>
          </form>
        </div>
      </div>

      <div id="resultModal" class="modal-blur">
        <div class="modal-content">
          <div class="top-modal">
            <h6>RESULT</h6>
          </div>
          <span class="close-modal" onclick="closeResultModal()">&times;</span>
          <div>
              <p>CHANGE SUCCESS</p>
          </div>
          <div class="submit-button-container">
            <button type="submit" name="okay-result" id="okayButton" class="okay-button" onclick="closeResultModal()">OKAY</button>
          </div>
        </div>
      </div>
    </section>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
    <script src="../js/navbar_controller.js"></script>
    <script src="../js/settings.js"></script>
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
      function openChangeEmailModal() {
        var changeEmailModal = document.getElementById("changeEmailModal");
        changeEmailModal.style.display = "block";
      }

      function closeChangeEmailModal() {
        var changeEmailModal = document.getElementById("changeEmailModal");
        changeEmailModal.style.display = "none";
      }

      function openChangePassModal() {
        var changePassModal = document.getElementById("changePassModal");
        changePassModal.style.display = "block";
      }

      function closeChangePassModal() {
        var changePassModal = document.getElementById("changePassModal");
        changePassModal.style.display = "none";
      }

      function openResultModal() {
        var resultModal = document.getElementById("resultModal");
        resultModal.style.display = "block";
      }

      function closeResultModal() {
        var resultModal = document.getElementById("resultModal");
        resultModal.style.display = "none";
      }

    </script>
  </body>
</html>
