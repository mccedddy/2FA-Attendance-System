<?php 
require '../includes/database_connection.php';
date_default_timezone_set('Asia/Manila');
require_once '../includes/encryption.php';
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

// Fetch sections
function fetchSections() {
  global $connection;
  $sectionsSQL = "SELECT * FROM sections";
  $sectionsResult = mysqli_query($connection, $sectionsSQL);
  $sections = [];
  while ($row = mysqli_fetch_assoc($sectionsResult)) {
    $sectionsInfo = [
              'section'      => $row['section'],
            ];
    $sections[] = $sectionsInfo['section'];
  }
  return $sections;
  mysqli_free_result($sectionsResult);
}

function fetchClasslist($tableName, $condition = '') {
  global $connection;
  global $encryptionHelper;
    
  $classlistSQL = "SELECT * FROM $tableName $condition";
  $classlistResult = mysqli_query($connection, $classlistSQL);
  $classlist = [];
  
  while ($row = mysqli_fetch_assoc($classlistResult)) {
    $profileInfo = [
          'lastName'      => $row['last_name'],
          'firstName'     => $row['first_name'],
          'idNumber'      => isset($row['student_number']) ? $row['student_number'] : $row['id_number'],
          'section'       => isset($row['section']) ? $row['section'] : '',
          'nfcUid'        => isset($row['nfc_uid']) ? $row['nfc_uid'] : '',
          'email'         => $encryptionHelper->decryptData($row['email']),
      ];
    $classlist[] = $profileInfo;
  }
  mysqli_free_result($classlistResult);   
  return $classlist;
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

// Fetch profile data
if (!isset($_SESSION['name'])) {
  $idNumber = $_SESSION['id_number'];
  $sql = "SELECT * FROM professors WHERE id_number = '$idNumber'";
  $result = mysqli_query($connection, $sql);

  // Check if the query was successful
  if ($result) {
    $professor = mysqli_fetch_assoc($result);

    // Get professor info
    if ($professor) {
      $name = strtoupper($professor['last_name']) . ', ' . strtoupper($professor['first_name']);
      $_SESSION['name'] = $name;
    }
          
    // Free result from memory
    mysqli_free_result($result);
  } else {
    echo 'Error: ' . mysqli_error($connection);
  }
}

// Add section
if (isset($_POST['add-section'])) {
  require '../includes/database_connection.php';
  $section = $_POST['year'] . '-' . $_POST['section'];

  // Check if section exists
  $checkSectionSQL = "SELECT COUNT(*) as sectionCount FROM sections WHERE section = '$section'";

  // Prepare and execute query
  $stmtCheckSection = mysqli_prepare($connection, $checkSectionSQL);
  mysqli_stmt_execute($stmtCheckSection);
  mysqli_stmt_bind_result($stmtCheckSection, $sectionCount);
  mysqli_stmt_fetch($stmtCheckSection);
  mysqli_stmt_close($stmtCheckSection);

  // Create section
  if ($sectionCount == 0) {
    // SQL query
    $sql = "INSERT INTO sections (section) VALUES ('$section')";

    // Execute query
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
  }

  header("Location: ../pages/admin_sections.php");
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