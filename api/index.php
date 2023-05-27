<?php
declare(strict_types=1);

use app\Model\BookingGateway;

require dirname(__DIR__) . "/vendor/autoload.php";

use app\ErrorHandler\ErrorHandler;
use app\Controller\BookingController;
use app\Model\Database;

set_exception_handler([ErrorHandler::class, 'handleException']);

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));

$dotenv->load();

$parts = explode("/", $path);

$resource = $parts[3];

//print_r($parts);
$storedData = [];
    for ($i = 4; $i < count($parts); $i++) {
        $storedData[$i] = $parts[$i] ?? null;
    }



if ($resource != "booking") {
    //header("HTTP/1.1 404 NOT FOUND");
    http_response_code(404);
    exit;
}



header("Content-type:application/json; charset=UTF-8");

$database = new Database ($_ENV["DB_HOST"],$_ENV["DB_NAME"],$_ENV["DB_USER"],$_ENV["DB_PASS"]);

$booking_gateway = new BookingGateway($database);

$controller = new BookingController($booking_gateway);

$controller->processRequest($_SERVER['REQUEST_METHOD'], $storedData);

?>