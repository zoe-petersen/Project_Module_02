<?php
session_start();

require_once 'db.php';


if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data
    $employee_id = intval($_POST['employee_id']);
    $date = ($_POST['date']);
    $status = ($_POST['status']);
    // Check if record exists for this employee and date
    $check = $conn->query("SELECT * FROM attendance WHERE employee_id = $employee_id AND date = '$date'");
    if ($check->num_rows > 0) {
        $_SESSION['error'] = "Attendance record already exists for this employee on the selected date.";
    } else {
        // Inserting new attendance record
        $sql = "INSERT INTO attendance (employee_id, date, status) VALUES ($employee_id, '$date', '$status')";
        if ($conn->query($sql)) {
            $_SESSION['message'] = "Attendance record added successfully!";
        } else {
            $_SESSION['error'] = "Error adding attendance record: " . $conn->error;
        }
    }
    $conn->close();
    header('Location: attendance.php');
    exit;
}
?>









