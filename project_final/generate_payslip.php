<?php
session_start();
// generate_payslip.php
require_once 'db.php';
require_once 'header.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['employee_id'])) {
    header('Location: payroll.php');
    exit;
}

$employee_id = intval($_GET['employee_id']);

// Get employee details
$employee = $conn->query("
    SELECT e.*, p.hours_worked, p.leave_deductions, p.final_salary
    FROM employees e
    LEFT JOIN payroll_data p ON e.employee_id = p.employee_id
    WHERE e.employee_id = $employee_id
")->fetch_assoc();

if (!$employee) {
    // header('Location: payroll.php');
    // exit;
}

// Calculate payroll details
$hourly_rate = $employee['final_salary'] / $employee['hours_worked'];
$leave_hours = $employee['leave_deductions'] * 8;
$deduction_amount = $hourly_rate * $leave_hours;
$net_salary = $employee['final_salary'] - $deduction_amount;

// Current month and year
$current_month = date('F Y');
?>

<div class="container">
    <div class="card mt-4">
        <div class="card-header text-center">
            <h3>ModernTech Solutions</h3>
            <h4>Monthly Payslip</h4>
            <p class="mb-0"><?php echo $current_month; ?></p>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Employee Information</h5>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($employee['name']); ?></p>
                    <p><strong>Employee ID:</strong> <?php echo $employee['employee_id']; ?></p>
                    <p><strong>Position:</strong> <?php echo htmlspecialchars($employee['position']); ?></p>
                    <p><strong>Department:</strong> <?php echo htmlspecialchars($employee['department']); ?></p>
                </div>
                <div class="col-md-6 text-end">
                    <h5>Payroll Details</h5>
                    <p><strong>Pay Date:</strong> <?php echo date('Y-m-d'); ?></p>
                    <p><strong>Pay Period:</strong> <?php echo $current_month; ?></p>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Description</th>
                            <th class="text-end">Amount (R)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Basic Salary</td>
                            <td class="text-end"><?php echo number_format($employee['salary'], 2); ?></td>
                        </tr>
                        <tr>
                            <td>Overtime/Allowances</td>
                            <td class="text-end"><?php echo number_format($employee['final_salary'] - $employee['salary'], 2); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Gross Salary</strong></td>
                            <td class="text-end"><strong><?php echo number_format($employee['final_salary'], 2); ?></strong></td>
                        </tr>
                        <tr>
                            <td>Leave Deductions (<?php echo $employee['leave_deductions']; ?> days)</td>
                            <td class="text-end">-<?php echo number_format($deduction_amount, 2); ?></td>
                        </tr>
                        <tr class="table-active">
                            <td><strong>Net Salary</strong></td>
                            <td class="text-end"><strong><?php echo number_format($net_salary, 2); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-6">
                    <h5>Summary</h5>
                    <p><strong>Hours Worked:</strong> <?php echo $employee['hours_worked']; ?></p>
                    <p><strong>Hourly Rate:</strong> R<?php echo number_format($hourly_rate, 2); ?></p>
                </div>
                <div class="col-md-6 text-end">
                    <div class="border p-3">
                        <h5>Bank Transfer</h5>
                        <p class="mb-0">Amount: <strong>R<?php echo number_format($net_salary, 2); ?></strong></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-muted text-center">
            <p class="mb-0">This is an automated payslip. No signature required.</p>
            <p class="mb-0">ModernTech Solutions HR System</p>
        </div>
    </div>
    
    <div class="text-center mt-3">
        <button class="btn btn-primary" onclick="window.print()">
            <i class="bi bi-printer"></i> Print Payslip
        </button>
        <a href="payroll.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Payroll
        </a>
    </div>
</div>

<script>
// Automatically print when the page loads if print parameter is set
window.onload = function() {
    if (window.location.search.includes('print=true')) {
        window.print();
    }
};
</script>

<?php require_once 'footer.php'; ?>