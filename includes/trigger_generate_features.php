<?php
$idNumber = $_POST['idNumber'];
$pythonPath = 'C:\Users\libre\AppData\Local\Programs\Python\Python312\python.exe';
$scriptPath = 'generate_features.py'; 
$command = escapeshellcmd("$pythonPath $scriptPath 2>&1");
$output = shell_exec($command); 

echo json_encode(['Generate features' => 'done', 'output' => $output]);
?>