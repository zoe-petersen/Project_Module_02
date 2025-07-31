<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $employee_id = (int)$_POST['employee_id'];
        $name = $_POST['name'];
        $position = $_POST['position'];
        $department = $_POST['department'];
        $salary = (float)$_POST['salary'];
        $contact_email = $_POST['contact_email'];
        $employment_history = $_POST['employment_history'] ?? '';

        executeQuery(
            "UPDATE employees SET 
                name = ?, 
                position = ?, 
                department = ?, 
                salary = ?, 
                employment_history = ?, 
                contact_email = ?
            WHERE employee_id = ?",
            [$name, $position, $department, $salary, $employment_history, $contact_email, $employee_id]
        );

        $_SESSION['message'] = "Employee updated successfully!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error updating employee: " . $e->getMessage();
    }
}

header('Location: employees.php');
exit;
?>