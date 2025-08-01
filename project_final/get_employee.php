<?php
session_start();
require_once 'db.php';


if (!isset($_SESSION['loggedin'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit;
}

if (isset($_GET['id'])) {
    $employee_id = intval($_GET['id']);
    $result = $conn->query("SELECT name, department FROM employees WHERE employee_id = $employee_id");
    
    if ($result->num_rows > 0) {
        $employee = $result->fetch_assoc();
        header('Content-Type: application/json');
        echo json_encode($employee);
        exit;
    }
}

header('HTTP/1.1 404 Not Found');
?>