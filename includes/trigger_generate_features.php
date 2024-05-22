<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
use Dotenv\Dotenv;

// Load .env variables
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$idNumber = $_POST['idNumber'];
$pythonPath = $_ENV['PYTHON_PATH'];
$scriptPath = 'generate_features.py'; 
$command = escapeshellcmd("$pythonPath $scriptPath 2>&1");
$output = shell_exec($command); 

echo json_encode(['Generate features' => 'done', 'output' => $output]);
?>