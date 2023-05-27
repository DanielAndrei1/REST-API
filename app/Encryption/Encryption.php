<?php

namespace app\Encryption;

class EncryptionUtil {
    private static $key = "test199555544";

    public static function decrypt($encryptedDataString, $Name) {
        try {
            $parts = preg_split('/(?<=[=]{2})/', $encryptedDataString, 2);
            
            // The separated strings from the parts
            $ivcleanresponse = $parts[0];
            $encryptedvaluecleanresponse = $parts[1];

            // Decode Encrypted value
            $ivBytes = base64_decode($ivcleanresponse);
            $encryptedValue = base64_decode($encryptedvaluecleanresponse);
            
             // Check if IV length is 15 bytes, pad it to 16 bytes if necessary
            if (strlen($ivBytes) < 16) {
                $ivBytes = str_pad($ivBytes, 16, "\0");
            }

            // Convert to hexadecimal strings
            $decodedIV = bin2hex($ivBytes);
            $decodedEncryptedValue = bin2hex($encryptedValue);
            
              $decryptedValue = openssl_decrypt(
                $encryptedValue,
                "AES-128-CBC",
                self::$key,
                OPENSSL_RAW_DATA,
                $ivBytes
            );
            //if ($Name != "row User_ID" && $Name != "row password") {
                echo " \n IV  split {$Name}: {$ivcleanresponse} \n";
                echo "EV Split {$Name}: {$encryptedvaluecleanresponse} \n";
                echo "IV base64_decode {$Name}: {$ivBytes} \n";
                echo "Enc Val base64_decode {$Name}: {$encryptedValue} \n";
                echo "IV decoded {$Name}: {$decodedIV} \n";
                echo "Enc Val decoded {$Name}: {$decodedEncryptedValue} \n";
                echo " \n Original: {$decryptedValue} \n ";

            //}
            
         
            

          

            return $decryptedValue;
        } catch (\Exception $ex) {
            echo "An error occurred: " . $ex->getMessage();
        }
    
        return null;
    }
    
   
}

class EncryptedData {
    private $encryptedValue;
    private $initializationVector;

    public function getEncryptedValue() {
        return $this->encryptedValue;
    }

    public function setEncryptedValue($encryptedValue) {
        $this->encryptedValue = $encryptedValue;
    }

    public function getInitializationVector() {
        return $this->initializationVector;
    }

    public function setInitializationVector($initializationVector) {
        $this->initializationVector = $initializationVector;
    }
}

?>
