<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
use Dotenv\Dotenv;

// Load .env variables
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Database details from environment variables
$db_host = $_ENV['DB_HOST'];
$db_user = $_ENV['DB_USER'];
$db_password = $_ENV['DB_PASSWORD'];
$db_name = $_ENV['DB_NAME'];

// Connect to database
$connection = mysqli_connect($db_host, $db_user, $db_password, $db_name);

// Check connection
if (!$connection) {
    die('Connection error: ' . mysqli_connect_error());
}
?>
