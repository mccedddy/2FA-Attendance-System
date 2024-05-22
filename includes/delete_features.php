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

// delete student folder on captures (../captures/2020-XXXXX-MN-0).
// delete student features on features_all.csv (../captures/features_all.csv)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure no unexpected output
ob_start();

function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return false;
    }
    if (!is_dir($dir)) {
        return unlink($dir);
    }
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }
    return rmdir($dir);
}

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idNumber'])) {
  $idNumber = basename($_POST['idNumber']); // Prevent directory traversal
  $basePath = '../captures/';
  $csvFilePath = '../captures/features_all.csv';
  $folderPath = $basePath . $idNumber;
  if (deleteDirectory($folderPath)) {
    $response = ['status' => 'success', 'message' => 'Folder deleted successfully'];
  } else {
    $response = ['status' => 'error', 'message' => 'Failed to delete folder'];
  }
  // Read the CSV file into an array
  if (!file_exists($csvFilePath)) {
    $response = ['status' => 'error', 'message' => 'CSV file does not exist'];
  } else {
    $lines = file($csvFilePath, FILE_IGNORE_NEW_LINES); // Read all lines into an array

    // Filter out the line with the specific idNumber
    $filteredLines = array_filter($lines, function($line) use ($idNumber) {
      return strpos($line, $idNumber) === false;
    });

    if (count($filteredLines) === 0) {
      // If there are no remaining lines, delete the CSV file
      if (unlink($csvFilePath)) {
        $response = ['status' => 'success', 'message' => 'CSV file deleted successfully'];
      } else {
        $response = ['status' => 'error', 'message' => 'Failed to delete CSV file'];
      }
    } else {
      // Write the remaining lines back to the CSV file
      $content = implode("\n", $filteredLines);
      if (file_put_contents($csvFilePath, $content) !== false) {
        $response = ['status' => 'success', 'message' => 'Line deleted successfully'];
      } else {
        $response = ['status' => 'error', 'message' => 'Failed to write to CSV file'];
      }
    }
  }
} else {
    $response = ['status' => 'error', 'message' => 'No idNumber provided'];
}

// Clear any unexpected output
ob_end_clean();

// Ensure correct content type
header('Content-Type: application/json');
echo json_encode($response);

?>

