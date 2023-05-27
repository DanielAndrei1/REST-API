<?php

namespace app\Encryption;

class Encryption {
public static function encrypt($plainData) {
    try {
        // Generate a random IV (Initialization Vector)
        $ivBytes = openssl_random_pseudo_bytes(16);
        
        // Check if IV length is 15 bytes, pad it to 16 bytes if necessary
        if (strlen($ivBytes) < 16) {
            $ivBytes = str_pad($ivBytes, 16, "\0");
        }
        
        // Encrypt the plain data
        $encryptedValue = openssl_encrypt(
            $plainData,
            "AES-128-CBC",
            "Jar12345Jar12345",
            OPENSSL_RAW_DATA,
            $ivBytes
        );
        
        // Encode the encrypted value and IV as base64 strings
        $encryptedValueBase64 = base64_encode($encryptedValue);
        $ivBytesBase64 = base64_encode($ivBytes);
        
        // Create an array containing the encrypted value and IV
        $encryptedData = array(
            'encryptedValue' => $encryptedValueBase64,
            'ivBytes' => $ivBytesBase64
        );
        
        return $encryptedData;
    } catch (Exception $ex) {
        echo "An error occurred: " . $ex->getMessage();
    }
    
    return null;
}

}
?>






