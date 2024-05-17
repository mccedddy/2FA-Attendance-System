<?php
if (isset($_POST['image']) && isset($_POST['filename']) && isset($_POST['idNumber'])) {
  $image = $_POST['image'];
  $filename = $_POST['filename'];
  $idNumber = $_POST['idNumber'];

  // Remove the base64 part of the data URL
  $image = str_replace('data:image/png;base64,', '', $image);
  $image = str_replace(' ', '+', $image);
  $imageData = base64_decode($image);

  // Define the directory path using the id-number
  $directoryPath = '../captures/' . $idNumber;

  // Check if the directory exists, if not, create it
  if (!is_dir($directoryPath)) {
    mkdir($directoryPath, 0777, true);
  }

  // Save the image to the specified directory
  $filePath = $directoryPath . '/' . $filename;
  file_put_contents($filePath, $imageData);
  echo json_encode(['status' => 'success', 'message' => 'Image saved successfully']);
} else if (isset($_POST['idNumber'])) {
  $idNumber = $_POST['idNumber'];
  $directoryPath = '../captures/' . $idNumber;

  // Count the number of existing images in the directory
  if (is_dir($directoryPath)) {
    $files = glob($directoryPath . '/*.png');
    $imgCount = count($files) + 1;
  } else {
    $imgCount = 1;
  }
  
  echo json_encode(['status' => 'success', 'imgCount' => $imgCount]);
} else {
  echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
