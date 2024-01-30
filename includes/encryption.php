<?php
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
