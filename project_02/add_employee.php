<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = $_POST['name'];
        $position = $_POST['position'];
        $department = $_POST['department'];
        $salary = (float)$_POST['salary'];
        $contact_email = $_POST['contact_email'];
        $employment_history = $_POST['employment_history'] ?? '';

        // Getting the next employee ID
        $result = $conn->query("SELECT MAX(employee_id) FROM employees");
        $max_id = $result->fetch_row()[0];
        $new_id = $max_id + 1;

        // Inserting new employee
        executeQuery(
            "INSERT INTO employees (employee_id, name, position, department, salary, employment_history, contact_email) 
            VALUES (?, ?, ?, ?, ?, ?, ?)",
            [$new_id, $name, $position, $department, $salary, $employment_history, $contact_email]
        );

        $_SESSION['message'] = "Employee added successfully!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error adding employee: " . $e->getMessage();
    }
}

header('Location: employees.php');
exit;
?>