<?php
namespace app\Model;
use \Firebase\JWT\JWT;
use app\RequestLimiter\RequestLimiter;
use PDO;
use app\Encryption\EncryptionUtil;
use app\RequestLimiter\getIPAddress;
use app\Mail\Mail;

class BookingGateway
{
    private PDO $conn;
    private getIPAddress $ipaddress;
    private RequestLimiter $request_limiter;
    private Mail $mail;
    private $clientIP;
    private $checkRequestLimit;

    public function __construct(Database $database)
    {
        $this -> conn = $database->getConnection();
        $this->ipaddress = new getIPAddress(); // Initialize the $ipaddress property
        $this -> request_limiter = new RequestLimiter();
        $this -> mail = new Mail();
        // Get the IP address from the request
        $clientIP =$this ->ipaddress -> getIPAddress();
        $this->checkRequestLimit = $this->request_limiter->checkRequestLimit($clientIP, $this->conn);


    }

    private function error422($message){
        $data = [
            'status' => 422,
            'message' => $message
        ];
        header('HTTP/1.0 405 Unprocessable Entity');
        echo json_encode($data);
        exit();
    }
    
    public function Register(array $registrationInput) {
    
       
    
    
        if ($this -> checkRequestLimit) {
        // Continue processing the request
        // The IP address has not exceeded the rate limit
    
        $User_ID = $registrationInput[5];
        $Pass = $registrationInput[6];
        $First_Name = $registrationInput[7];
        $Surname = $registrationInput[8];
        $Mobile_No = $registrationInput[9];
        $Email = $registrationInput[10];
        $Cabin_no = $registrationInput[11];
        $Admin = $registrationInput[12];
    
        if(empty(trim($User_ID))){
    
            return $this -> error422('Adaugati UserName');
        }elseif(empty(trim($Pass))){
    
            return $this -> error422('Adaugati parola');
    
        }elseif(empty(trim($First_Name))){
    
            return $this -> error422('Adaugati prenume');
    
        }elseif(empty(trim($Surname))){
            return $this -> error422('Adaugati nume de familie');
    
        }elseif(empty(trim($Email))){
            return $this -> error422('Adaugati Email');
    
        }
        else{
            
            
        $query = "INSERT INTO User (User_ID, Pass, First_Name, Surname, Mobile_No, Email, Cabin_no, Administrator)
        VALUES(?,?,?,?,?,?,?,?)";
        $stmt = $this -> conn->prepare($query);
        $stmt->execute([$User_ID, $Pass, $First_Name, $Surname, $Mobile_No, $Email, $Cabin_no, $Admin]);
    
    
            if($stmt){
                //$data = 'Registration Completed.';
                $data = [
                    "status" => 201,
                    'message' => 'Registration Completed.'
                ];           
                header('HTTP/1.0 201 Created');
                 $this -> mail -> mailSent($Email);
                return json_encode($data);
            } else{
                $data = [
                    'status' => 500,
                    'message' => 'Internal Error'
                ];
                header('HTTP/1.0 500 Internal Server Error');
                return json_encode($data);
            }
    
            }
        } else {
        // Display an error message or perform appropriate actions
        echo "Too many requests. Please try again later.";
        }
    
    
    }
    
    public function Login($LoginInput)
    {
      
    
        if ($this -> checkRequestLimit == true) {
        // Continue processing the request
        // The IP address has not exceeded the rate limit
        
        $User_Name = $LoginInput[5];
        $Password = $LoginInput[6];
    
        //$Passworddecrypted = EncryptionUtil::decrypt($Password, "password");
        //echo "\n UserName: {$User_Name} and password: {$Passworddecrypted} \n;
    
        if(empty(trim($User_Name))){
    
            return $this -> error422('Adaugati username!');
        }elseif(empty(trim($Password))){
    
            return $this -> error422('Adaugati parola!');
    
        }
    
        $query = "SELECT Pass,First_Name, Surname, Email FROM User WHERE User_ID=?";
        $stmt = $this -> conn->prepare($query);
        $stmt->execute([$User_Name]);
        $results = $stmt->fetchALL(PDO::FETCH_ASSOC);
        print_r($results);
        $matchingRows = [];
    
        foreach ($results as $result) {
            //$email = EncryptionUtil::decrypt($result['Email'], "ID");
           // $decryptedPass = EncryptionUtil::decrypt($result['Pass'], "row password");
           // $firstname = EncryptionUtil::decrypt($result['First_Name'], "FirstName");
           // $lastname = EncryptionUtil::decrypt($result['Surname'], "Last Name");
           $email = $result["Email"];
           $decryptedPass = $result["Pass"];
           $firstname = $result["First_Name"];
           $lastname = $result["Surname"];

            
            if ($decryptedPass === $Password) {
                // The decrypted values match, add the row to the matchingRows array
                $matchingRows[] = $result;
                $secret_key = "1971121ADLlqwd@";
                $issuer_claim = "CPANEL"; // this can be the servername
                $audience_claim = "THE_AUDIENCE";
                $issuedat_claim = time(); // issued at
                $notbefore_claim = $issuedat_claim + 10; //not before in seconds
                $expire_claim = $issuedat_claim + 60; // expire time in seconds
                $token = array(
                    "iss" => $issuer_claim,
                    "aud" => $audience_claim,
                    "iat" => $issuedat_claim,
                    "nbf" => $notbefore_claim,
                    "exp" => $expire_claim,
                    "data" => array(
                        "User_Name" => $User_Name,
                        "firstname" => $firstname,
                        "lastname" => $lastname,
                        "email" => $email
                ));
    
                $jwt = JWT::encode($token, $secret_key, "HS256");
            echo json_encode(
                array(
                    "message" => "Successful login.",
                    "jwt" => $jwt,
                    "User_Name" => $User_Name,
                    "expireAt" => $expire_claim
                ));
                 }
            }
    
        if (!empty($matchingRows)) {
            // At least one matching row was found
            $data = [
                "status" => 200,
                "rows" => $matchingRows
                ];
            header('HTTP/1.0 200 Ok');
            return json_encode($data);
        } else {
            // No matching rows were found
            $data = [
                'status' => 404,
                'message' => 'No record found',
            ];
            header('HTTP/1.0 404 No record found');
            return json_encode($data);
        }
        
    
        } else {
        // Display an error message or perform appropriate actions
            header('HTTP/1.0 429 Too Many Requests');
            echo "Too many requests. Please try again later.";
        }
        
    }
        
    
    
    
    function getExcursions()
    {
   
    
        if ($this -> checkRequestLimit) {
        // Continue processing the request
        // The IP address has not exceeded the rate limit
        global $conn;
       
    
        $query = "SELECT * FROM Excursion";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        if (!empty($rows)) {
            // At least one matching row was found
            $data = [
                "rows" => $rows
                ];
            header('HTTP/1.0 200 Ok');
            return json_encode($data);
        } else {
            // No matching rows were found
            $data = [
                'status' => 404,
                'message' => 'No record found',
            ];
            header('HTTP/1.0 404 No record found');
            return json_encode($data);
        }
        } else {
        // Display an error message or perform appropriate actions
        echo "Too many requests. Please try again later.";
    }   
        
    }
    
    
   public function getDataList()
    {

    if ($this->checkRequestLimit == true) {
        $query = "SELECT * FROM SecureEncStrings";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($rows)) {
            $data = [
                "status" => 200,
                "rows" => $rows
            ];
            header('HTTP/1.0 200 OK');
            return json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'No records found',
            ];
            header('HTTP/1.0 404 Not Found');
            return json_encode($data);
        }
    } else {
        return json_encode("Too many requests");
    }
    }

    

}


?>