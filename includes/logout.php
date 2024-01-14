<?php
session_start();
unset($_SESSION['idNumber']);
unset($_SESSION['studentNumber']);
session_destroy();
header("Location: ../index.php");
exit();
?>
