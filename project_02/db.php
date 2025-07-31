<?php
$host = 'localhost';
$username = 'root';
$password = 'ZoeTyler#23'; // Replace with your actual password
$database = 'moderntech_hr';

$conn = new mysqli($host, $username, $password, $database);// Create the connection
// Checking the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
//to throw error if database not found
function executeQuery($query, $params = []) {
    global $conn;
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    if ($params) {
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) $types .= 'i';
            elseif (is_float($param)) $types .= 'd';
            else $types .= 's';
        }
        $stmt->bind_param($types, ...$params);
    }
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    $stmt->close();
}
?>