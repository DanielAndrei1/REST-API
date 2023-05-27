<?php

namespace app\RequestLimiter;
use PDO;

class RequestLimiter
{
 private PDO $conn;

 public function __constructor($conn){
    $this -> conn = $conn;
    // Cleanup expired request counts before checking the current request
    $this -> cleanupExpiredRequestCounts();
 }

public function checkRequestLimit($ip, $conn) {

    $requestsPerHour = 20; // Adjust this as needed
    $limitDuration = 3600; // In seconds

    // Get the request count for the IP address
    $requestCount = $this -> getRequestCount($ip, $conn);

    if ($requestCount >= $requestsPerHour) {
        // IP address has exceeded the limit
        $data = 'Rate Limit Exceeded';
        header('Content-Type: application/json');
        return json_encode($data);
    }
    
    // Increment the request count for the IP address
    $this -> incrementRequestCount($ip, $conn);

    // Set the expiration time for the request count
    $this ->setRequestCountExpiration($ip, time() + $limitDuration, $conn);
    return true;
}

public function getRequestCount($ip, $conn) {

    $requestCount = 0;

    // Retrieve the request count from the database based on the IP address
    $query =$conn->prepare('SELECT request_count FROM request_counts WHERE ip_address = ?');
    $query->execute([$ip]);
    $result = $query->fetch(PDO::FETCH_ASSOC);


    if ($result) {
        $requestCount = (int) $result['request_count'];
    }

    return $requestCount;
}

public function incrementRequestCount($ip, $conn) {

    // Increment the request count for the IP address in the database
    $query = $conn->prepare('INSERT INTO request_counts (ip_address, request_count) VALUES (:ip, 1)
                           ON DUPLICATE KEY UPDATE request_count = request_count + 1');
    $query->execute([':ip' => $ip]);
}

public function setRequestCountExpiration($ip, $expirationTime, $conn) {

    // Set the expiration time for the request count in the database
    $query = $conn->prepare('INSERT INTO request_expirations (ip_address, expiration_time) VALUES (:ip, :expiration)
                           ON DUPLICATE KEY UPDATE expiration_time = :expiration');
    $query->execute([':ip' => $ip, ':expiration' => $expirationTime]);
}

public function cleanupExpiredRequestCounts() {

    // Clean up expired request counts from the database
    $query = $this -> conn->prepare('DELETE FROM request_expirations WHERE expiration_time < ?');
    $query->execute([time()]);
}

}

?>