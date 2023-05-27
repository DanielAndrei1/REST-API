<?php

namespace app\Controller;
use app\Model\BookingGateway;
use app\View\Register;
use app\View\getData;
use app\View\Login;

class BookingController
{
    private $gateway;
    public function __construct(BookingGateway $gateway)
    {
        $this->gateway = $gateway;

    }
    public function processRequest(string $method, array $storedData): void
    {
        print_r($storedData);
        if($storedData[4] == null)
        {
            if($method == "GET")
            {
            getData::getData($this->gateway);
            } elseif ($method == "POST")
            {   
                echo    "Post";
            } else {
                    $this->respondMethodAllowed("Get, Post");
            }
        }else {
                switch ($method) {
                    
                    case "GET":
                        echo "show $storedData";
                        break;
                    
                    case "PATCH":
                        echo "update $storedData";
                        break;
                    
                    case "DELETE":
                        echo "delete $storedData";
                        break;
                        
                    case "POST":
                        if($storedData[4] == "register"){
                            Register::Register($this -> gateway, $storedData);
                        } else if($storedData[4] == "login") {
                            Login::Login($this -> gateway, $storedData);

                        }
                        break;
                        
                    default:
                        $this ->respondMethodAllowed(" GET, PATCH, DELETE, POST");
                }
        }
        
    }
    
    private function respondMethodAllowed(string $allowed_methods): void
    {
                http_response_code(405);
                header("Allow: $allowed_methods");
    }
}

?>