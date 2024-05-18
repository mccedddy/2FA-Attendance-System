<?php
$idNumber = $_POST['idNumber'];
$pythonPath = 'D:\DEV\Python\Python312\python.exe';
$scriptPath = 'generate_features.py'; 
$command = escapeshellcmd("$pythonPath $scriptPath 2>&1");
$output = shell_exec($command); 

if ($output === null) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to execute Python script']);
} else {
    echo json_encode(['status' => 'sucess', 'output' => $output]);
}
?>