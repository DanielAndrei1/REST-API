<?php
namespace app\View;

class Login
{

 public static function Login ($register, $inputData){

       
    $login = $register -> Login($inputData);
    echo $login;
    
 }
}
?>