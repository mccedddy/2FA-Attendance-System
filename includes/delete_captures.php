<?php
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

$basePath = '../captures/';
$csvFilePath = '../captures/features_all.csv';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete the CSV file
    if (file_exists($csvFilePath) && unlink($csvFilePath)) {
        $response[] = ['status' => 'success', 'message' => 'CSV file deleted successfully'];
    } else {
        $response[] = ['status' => 'error', 'message' => 'Failed to delete CSV file'];
    }

    // Delete the captures directory
    if (deleteDirectory($basePath)) {
        $response[] = ['status' => 'success', 'message' => 'Captures directory deleted successfully'];
    } else {
        $response[] = ['status' => 'error', 'message' => 'Failed to delete captures directory'];
    }
} else {
    $response = ['status' => 'error', 'message' => 'Invalid request method'];
}

// Clear any unexpected output
ob_end_clean();

// Ensure correct content type
header('Content-Type: application/json');
echo json_encode($response);
?>
