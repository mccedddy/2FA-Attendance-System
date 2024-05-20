<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
use Dotenv\Dotenv;

// Load .env variables
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Use the encryption key from the environment variable
$encryptionKey = $_ENV['ENCRYPTION_KEY'];
$encryptionHelper = new EncryptionHelper($encryptionKey);

class EncryptionHelper {
    private $encryptionKey;

    public function __construct($key) {
        $this->encryptionKey = $key;
    }

    public function encryptData($data) {
        $encryptedData = openssl_encrypt($data, 'AES-256-CBC', $this->encryptionKey, 0, $this->encryptionKey);
        return base64_encode($encryptedData);
    }

    public function decryptData($encryptedData) {
        $encryptedData = base64_decode($encryptedData);
        return openssl_decrypt($encryptedData, 'AES-256-CBC', $this->encryptionKey, 0, $this->encryptionKey);
    }
}
?>
