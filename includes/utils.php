<?php 

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

// Logout
if (isset($_POST['logout'])) {
  require '../includes/logout.php';
}

?>