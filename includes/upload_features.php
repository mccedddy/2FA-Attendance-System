<?php 
require 'database_connection.php';
require 'utils.php';

$csvFile = str_replace("\\", "/", dirname(__DIR__)) . '/captures/features_all.csv';

// Ensure the CSV file exists and is readable
if (!file_exists($csvFile) || !is_readable($csvFile)) {
   echo json_encode(['status' => 'error', 'message' => 'CSV file does not exist or is not readable.']);
   exit();
}

// Create a temporary table to load the CSV data
$tempTableSql = "
    CREATE TEMPORARY TABLE temp_features LIKE features;";

if (!$connection->query($tempTableSql)) {
    echo json_encode(['status' => 'error', 'message' => 'Error creating temporary table: ' . $connection->error]);
    exit();
}

// SQL to load data from CSV file
$loadDataSql = "
    LOAD DATA INFILE '" . $csvFile . "'
    INTO TABLE temp_features
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

if (!$connection->query($loadDataSql)) {
  echo json_encode(['status' => 'error', 'message' => 'Error loading CSV data into temporary table: ' . $connection->error]);
  exit();
}

// Insert or update records in the main table
$mergeDataSql = "
    INSERT INTO features (id_number, 
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
    f121, f122, f123, f124, f125, f126, f127, f128)
    SELECT id_number, 
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
    f121, f122, f123, f124, f125, f126, f127, f128
    FROM temp_features
    ON DUPLICATE KEY UPDATE
    f1 = VALUES(f1), f2 = VALUES(f2), f3 = VALUES(f3), f4 = VALUES(f4), f5 = VALUES(f5),
    f6 = VALUES(f6), f7 = VALUES(f7), f8 = VALUES(f8), f9 = VALUES(f9), f10 = VALUES(f10),
    f11 = VALUES(f11), f12 = VALUES(f12), f13 = VALUES(f13), f14 = VALUES(f14), f15 = VALUES(f15),
    f16 = VALUES(f16), f17 = VALUES(f17), f18 = VALUES(f18), f19 = VALUES(f19), f20 = VALUES(f20),
    f21 = VALUES(f21), f22 = VALUES(f22), f23 = VALUES(f23), f24 = VALUES(f24), f25 = VALUES(f25),
    f26 = VALUES(f26), f27 = VALUES(f27), f28 = VALUES(f28), f29 = VALUES(f29), f30 = VALUES(f30),
    f31 = VALUES(f31), f32 = VALUES(f32), f33 = VALUES(f33), f34 = VALUES(f34), f35 = VALUES(f35),
    f36 = VALUES(f36), f37 = VALUES(f37), f38 = VALUES(f38), f39 = VALUES(f39), f40 = VALUES(f40),
    f41 = VALUES(f41), f42 = VALUES(f42), f43 = VALUES(f43), f44 = VALUES(f44), f45 = VALUES(f45),
    f46 = VALUES(f46), f47 = VALUES(f47), f48 = VALUES(f48), f49 = VALUES(f49), f50 = VALUES(f50),
    f51 = VALUES(f51), f52 = VALUES(f52), f53 = VALUES(f53), f54 = VALUES(f54), f55 = VALUES(f55),
    f56 = VALUES(f56), f57 = VALUES(f57), f58 = VALUES(f58), f59 = VALUES(f59), f60 = VALUES(f60),
    f61 = VALUES(f61), f62 = VALUES(f62), f63 = VALUES(f63), f64 = VALUES(f64), f65 = VALUES(f65),
    f66 = VALUES(f66), f67 = VALUES(f67), f68 = VALUES(f68), f69 = VALUES(f69), f70 = VALUES(f70),
    f71 = VALUES(f71), f72 = VALUES(f72), f73 = VALUES(f73), f74 = VALUES(f74), f75 = VALUES(f75),
    f76 = VALUES(f76), f77 = VALUES(f77), f78 = VALUES(f78), f79 = VALUES(f79), f80 = VALUES(f80),
    f81 = VALUES(f81), f82 = VALUES(f82), f83 = VALUES(f83), f84 = VALUES(f84), f85 = VALUES(f85),
    f86 = VALUES(f86), f87 = VALUES(f87), f88 = VALUES(f88), f89 = VALUES(f89), f90 = VALUES(f90),
    f91 = VALUES(f91), f92 = VALUES(f92), f93 = VALUES(f93), f94 = VALUES(f94), f95 = VALUES(f95),
    f96 = VALUES(f96), f97 = VALUES(f97), f98 = VALUES(f98), f99 = VALUES(f99), f100 = VALUES(f100),
    f101 = VALUES(f101), f102 = VALUES(f102), f103 = VALUES(f103), f104 = VALUES(f104), f105 = VALUES(f105),
    f106 = VALUES(f106), f107 = VALUES(f107), f108 = VALUES(f108), f109 = VALUES(f109), f110 = VALUES(f110),
    f111 = VALUES(f111), f112 = VALUES(f112), f113 = VALUES(f113), f114 = VALUES(f114), f115 = VALUES(f115),
    f116 = VALUES(f116), f117 = VALUES(f117), f118 = VALUES(f118), f119 = VALUES(f119), f120 = VALUES(f120),
    f121 = VALUES(f121), f122 = VALUES(f122), f123 = VALUES(f123), f124 = VALUES(f124), f125 = VALUES(f125),
    f126 = VALUES(f126), f127 = VALUES(f127), f128 = VALUES(f128)
";

if ($connection->query($mergeDataSql) === TRUE) {
  echo json_encode(['status' => 'success', 'message' => 'CSV uploaded to the database.']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'Error uploading CSV data: ' . $connection->error]);
}
?>