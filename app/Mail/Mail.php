<?php

namespace app\Mail;
use app\Encryption\EncryptionUtil;
class Mail 
{


function mailSent($toAddress) {
    //$decryptedToAddress = EncryptionUtil::decrypt($toAddress, "email");
    $subject = 'Confirmation Registration Booking System';
    $message = 'This is a test email, Thank you for registering.';
    $headers = 'From: danyandrei@gmail.com' . "\r\n" .
               'Reply-To: yourname@example.com' . "\r\n" .
               'X-Mailer: PHP/' . phpversion();


    if (mail($toAddress, $subject, $message, $headers)) {
        echo "Email sent successfully";
    } else {
        echo "Email failed to send";
    }

    // Print the decrypted email address

    return null;
}
}
?>
