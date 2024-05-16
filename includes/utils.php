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

// Logout
if (isset($_POST['logout'])) {
  unset($_SESSION['userId']);
  unset($_SESSION['user_name']);
  unset($_SESSION['section']);
  unset($_SESSION['selected_section']);
  session_destroy();
  header("Location: ../index.php");
  exit();
}
?>