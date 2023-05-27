<?php

header('Access-Control-Allow-Origin:*');
header('Content-Type: application/json');
header('Access-Control-Allow-Method: GET');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Request-With');

require './database/function.php';
$requestMethod = $_SERVER["REQUEST_METHOD"];
if (isset($_SESSION['LAST_CALL'])) {
  $last = strtotime($_SESSION['LAST_CALL']);
  $curr = strtotime(date("Y-m-d h:i:s"));
  $sec =  abs($last - $curr);
  if ($sec <= 1) {
    $data = 'Rate Limit Exceeded';  // rate limit
    header('Content-Type: application/json');
    die (json_encode($data));        
  }
}
$requestMethod = $_SERVER["REQUEST_METHOD"];

if($requestMethod == 'GET'){
    
    $excursionList = getExcursions();
    echo $excursionList;

}else{
    $data = [
        'status' => 405,
        'message' => $requestMethod. 'Method Not Allowed',
    ];
    header('HTTP/1.0 405 Method Not Allowed');
    echo "Method Not Allowed";
}

?>