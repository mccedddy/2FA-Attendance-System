<?php 
require 'database_connection.php';
date_default_timezone_set('Asia/Manila');
require_once 'encryption.php';
$encryptionHelper = new EncryptionHelper($encryptionKey);

// Set logged in name and id number
if (!isset($_SESSION['id_number'])) {
  $userId = '';
} else {
  $userId = $_SESSION['id_number'];
}
if (!isset($_SESSION['user_name'])) {
  $userName = '';
} else {
  $userName = $_SESSION['user_name'];
}
if (!isset($_SESSION['user_email'])) {
  $userEmail = '';
} else {
  $userEmail = $_SESSION['user_email'];
}
if (!isset($_SESSION['user_password'])) {
  $userPassword = '';
} else {
  $userPassword = $_SESSION['user_password'];
}

// Redirect professor and admin pages
function redirect($currentPage) {
  if(isset($_SESSION['id_number'])) {
    $userId = $_SESSION['id_number'];

    if ($userId == 'admin' && $currentPage == 'professor') {
      header("Location: admin_home.php");
    }

    if ($userId != 'admin' && $currentPage == 'admin') {
      header("Location: professor_home.php");
    }

  } else {
    header("Location: ../index.php");
  }
}

// Check selected section
function checkSection() {
  // Redirect to home if no section is selected
  if (!isset($_SESSION['selected_section'])) {
    if ($userId != 'admin') {
      header("Location: ../pages/professor_home.php");
    } else {
      header("Location: ../pages/admin_home.php");
    }
  } else {
    return $sectionPage = $_SESSION['selected_section'];
  }
}

// Section header
function sectionHeader($location) {
  $_SESSION['selected_section'] = $_POST['section'];
  if ($_POST['section'] == 'professors') {
    header("Location: admin_professors.php");
  } else {
    header("Location: {$location}");
  }
}

function readCSV($csvFile) {
    $file_handle = fopen($csvFile, 'r');
    $data = [];
    while (!feof($file_handle)) {
        $row = fgetcsv($file_handle);
        if ($row) {
            $data[] = $row;
        }
    }
    fclose($file_handle);
    return $data;
}

// Logout
if (isset($_POST['logout'])) {
  unset($_SESSION['user_id']);
  unset($_SESSION['user_name']);
  unset($_SESSION['user_email']);
  unset($_SESSION['section']);
  unset($_SESSION['user_password']);
  unset($_SESSION['selected_section']);
  session_destroy();
  header("Location: ../index.php");
  exit();
}
?>