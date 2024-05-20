<?php 
require 'database_connection.php';
require 'utils.php';

$csvFile = str_replace("\\", "/", dirname(__DIR__)) . '/captures/features_all.csv';

  // Ensure the CSV file exists and is readable
  if (!file_exists($csvFile) || !is_readable($csvFile)) {
     echo json_encode(['status' => 'error', 'message' => 'CSV file does not exist or is not readable.']);
  }

// SQL to load data from CSV file
$loadDataSql = "
    LOAD DATA INFILE '" . $csvFile . "'
    INTO TABLE features
    FIELDS TERMINATED BY ',' 
    LINES TERMINATED BY '\n'
    (id_number, 
    f1, f2, f3, f4, f5, f6, f7, f8, f9, f10,
    f11, f12, f13, f14, f15, f16, f17, f18, f19, f20,
    f21, f22, f23, f24, f25, f26, f27, f28, f29, f30,
    f31, f32, f33, f34, f35, f36, f37, f38, f39, f40,
    f41, f42, f43, f44, f45, f46, f47, f48, f49, f50,
    f51, f52, f53, f54, f55, f56, f57, f58, f59, f60,
    f61, f62, f63, f64, f65, f66, f67, f68, f69, f70,
    f71, f72, f73, f74, f75, f76, f77, f78, f79, f80,
    f81, f82, f83, f84, f85, f86, f87, f88, f89, f90,
    f91, f92, f93, f94, f95, f96, f97, f98, f99, f100,
    f101, f102, f103, f104, f105, f106, f107, f108, f109, f110,
    f111, f112, f113, f114, f115, f116, f117, f118, f119, f120,
    f121, f122, f123, f124, f125, f126, f127, f128)";

if ($connection->query($loadDataSql) === TRUE) {
    echo json_encode(['status' => 'success', 'message' => 'CSV uploaded to the database.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error uploading CSV data: ' . $connection->error]);
}
?>