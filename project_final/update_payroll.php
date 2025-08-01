<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employeeId = $_POST['employee_id'];
    $hoursWorked = $_POST['hours_worked'];
    $leaveDeductions = $_POST['leave_deductions'];
    $grossSalary = $_POST['gross_salary'];

    // Update the payroll data in the database
    $stmt = $conn->prepare("UPDATE payroll_data SET hours_worked = ?, leave_deductions = ?, final_salary = ? WHERE employee_id = ?");
    $stmt->bind_param("dddi", $hoursWorked, $leaveDeductions, $grossSalary, $employeeId);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Payroll data updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating payroll data: " . $conn->error;
    }
    
    $stmt->close();
    $conn->close();
    
    header('Location: payroll.php');
    exit;
}
?>