<?php

session_start();
require_once 'db.php';


if (!isset($_SESSION['loggedin'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit;
}
if (isset($_GET['employee_id'])) {
    $employee_id = intval($_GET['employee_id']);
    $result = $conn->query("
        SELECT date, status
        FROM attendance
        WHERE employee_id = $employee_id
        ORDER BY date DESC
    ");
    $attendance = [];
    while ($row = $result->fetch_assoc()) {
        $attendance[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($attendance);
    exit;
}
header('HTTP/1.1 400 Bad Request');
?>








