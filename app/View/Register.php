<?php

namespace app\View;

class Register
{

 public static function Register ($register, array $inputData){


    $storeUserRegistration = $register -> Register($inputData);       
    
     
        

    
    echo $storeUserRegistration;

}
}

?>