<?php
session_start();
require_once 'db.php';


if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Getting form data
    $employee_id = intval($_POST['employee_id']);
    $date = ($_POST['date']);
    $reason = ($_POST['reason']);
    // allows you to insert a new leave request
    $sql = "INSERT INTO leave_requests (employee_id, date, reason, status)
            VALUES ($employee_id, '$date', '$reason', 'Pending')";
    if ($conn->query($sql)) {
        $_SESSION['message'] = "Leave request added successfully!";
    } else {
        $_SESSION['error'] = "Error adding leave request: " . $conn->error;
    }
    $conn->close();
    header('Location: leave_requests.php');
    exit;
}
?>






