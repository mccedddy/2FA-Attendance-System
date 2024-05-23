<?php

// delete student features on the db (features table).
if (isset($_POST['idNumber'])) {
  require 'database_connection.php';
  $idNumber = $_POST['idNumber'];

  $deleteFeaturesSQL = "DELETE FROM features WHERE id_number IN ('$idNumber')";

  $stmtFeatures = mysqli_prepare($connection, $deleteFeaturesSQL);
  $success = mysqli_stmt_execute($stmtFeatures);

  mysqli_stmt_close($stmtFeatures);

  mysqli_close($connection);
} else {
  echo json_encode(['error' => 'No student number provided.']);
}

?>

