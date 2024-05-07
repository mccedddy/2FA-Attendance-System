<?php 
require 'database_connection.php';
date_default_timezone_set('Asia/Manila');
require_once 'encryption.php';
$encryptionHelper = new EncryptionHelper($encryptionKey);

// Set logged in name and id number
if (!isset($_SESSION['id_number'])) {
  $idNumber = '';
} else {
  $idNumber = $_SESSION['id_number'];
}
if (!isset($_SESSION['name'])) {
  $name = '';
} else {
  $name = $_SESSION['name'];
}

// Redirect professor and admin pages
function redirect($currentPage) {
  if(isset($_SESSION['id_number'])) {
    $idNumber = $_SESSION['id_number'];

    if ($idNumber == 'admin' && $currentPage == 'professor') {
      header("Location: admin_home.php");
    }

    if ($idNumber != 'admin' && $currentPage == 'admin') {
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
    header("Location: ../pages/admin_home.php");
  } else {
    return $sectionPage = $_SESSION['selected_section'];
  }
}

// Logout
if (isset($_POST['logout'])) {
  unset($_SESSION['idNumber']);
  unset($_SESSION['name']);
  unset($_SESSION['section']);
  unset($_SESSION['selected_section']);
  session_destroy();
  header("Location: ../index.php");
  exit();
}
?>