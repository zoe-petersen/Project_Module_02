<?php
require_once 'db.php';
require_once 'header.php';



if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = "Payroll Management";


// Calculate payroll for all employees
$payrollData = $conn->query("
    SELECT e.employee_id, e.name, e.position, e.salary, 
    p.hours_worked, p.leave_deductions, p.final_salary
    FROM employees e
    JOIN payroll_data p ON e.employee_id = p.employee_id
    ORDER BY e.name
");

// Process payroll calculations
$payrollResults = [];
$totalGross = 0;
$totalDeductions = 0;
$totalNet = 0;

while ($employee = $payrollData->fetch_assoc()) {
    // Set default hours worked if value is 0 or null
    $hoursWorked = ($employee['hours_worked'] && $employee['hours_worked'] > 0) ? $employee['hours_worked'] : 160; // Default: 160 hours
    $hourlyRate = $employee['final_salary'] / $hoursWorked;
    $leaveHours = $employee['leave_deductions'] * 8;
    $deductionAmount = $hourlyRate * $leaveHours;
    $netSalary = $employee['final_salary'] - $deductionAmount;

    $payrollResults[] = [
        'employeeId' => $employee['employee_id'],
        'name' => $employee['name'],
        'position' => $employee['position'],
        'hoursWorked' => $hoursWorked,
        'leaveDeductions' => $employee['leave_deductions'],
        'grossSalary' => $employee['final_salary'],
        'hourlyRate' => number_format($hourlyRate, 2),
        'leaveHours' => $leaveHours,
        'deductionAmount' => number_format($deductionAmount, 2),
        'netSalary' => number_format($netSalary, 2)
    ];

    $totalGross += $employee['final_salary'];
    $totalDeductions += $deductionAmount;
    $totalNet += $netSalary;
}
?>

<div class="d-flex justify-content-between mb-3">
    <h2>Payroll Management</h2>
</div>



<div class="card">
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Position</th>
                    <th>Hours Worked</th>
                    <th>Leave Days</th>
                    <th>Hourly Rate</th>
                    <th>Gross Salary</th>
                    <th>Deductions</th>
                    <th>Net Salary</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payrollResults as $employee): ?>
                <tr>
                    <td><?php echo $employee['name']; ?></td>
                    <td><?php echo $employee['position']; ?></td>
                    <td><?php echo $employee['hoursWorked']; ?></td>
                    <td><?php echo $employee['leaveDeductions']; ?></td>
                    <td>R<?php echo $employee['hourlyRate']; ?></td>
                    <td>R<?php echo $employee['grossSalary']; ?></td>
                    <td class="text-danger">-R<?php echo $employee['deductionAmount']; ?></td>
                    <td class="text-success">R<?php echo $employee['netSalary']; ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" 
                                data-bs-target="#payslipModal"
                                data-name="<?php echo $employee['name']; ?>"
                                data-id="<?php echo $employee['employeeId']; ?>"
                                data-gross="<?php echo $employee['grossSalary']; ?>"
                                data-deductions="<?php echo $employee['deductionAmount']; ?>"
                                data-net="<?php echo $employee['netSalary']; ?>">
                            <i class="bi bi-file-earmark-text"></i> Payslip
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Payslip Modal -->
<div class="modal fade" id="payslipModal" tabindex="-1" aria-labelledby="payslipModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="payslipModalLabel">Employee Payslip</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="payslip-container" id="payslipContent">
                    <div class="text-center mb-4">
                        <h3>ModernTech Solutions</h3>
                        <p class="mb-0">12 Life Choices, Cape Town, 2000</p>
                        <p>Phone: (021) 657-4567 | Email: hr@moderntech.com</p>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Employee Information</h5>
                            <p class="mb-1"><strong>Name:</strong> <span id="payslipName"></span></p>
                            <p class="mb-1"><strong>Employee ID:</strong> <span id="payslipId"></span></p>
                            <p class="mb-1"><strong>Pay Period:</strong> July 2025</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h5>Payslip Details</h5>
                            <p class="mb-1"><strong>Issue Date:</strong> <?php echo date('Y-m-d'); ?></p>
                            <p class="mb-1"><strong>Payslip #:</strong> PS-<?php echo rand(1000, 9999); ?></p>
                        </div>
                    </div>
                    
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Description</th>
                                <th class="text-end">Amount (R)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Basic Salary</td>
                                <td class="text-end" id="payslipGross"></td>
                            </tr>
                            <tr>
                                <td>Leave Deductions</td>
                                <td class="text-end text-danger" id="payslipDeductions"></td>
                            </tr>
                            <tr class="table-success">
                                <td><strong>Net Salary</strong></td>
                                <td class="text-end"><strong id="payslipNet"></strong></td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="mt-4">
                        <p class="mb-1"><strong>Payment Method:</strong> Bank Transfer</p>
                        <p class="mb-1"><strong>Bank Name:</strong> Life Choices Bank</p>
                        <p class="mb-1"><strong>Account Number:</strong> **** **** **** 1234</p>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top text-center">
                        <p class="mb-0">This is a computer generated payslip and does not require a signature.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-dark" onclick="printPayslip()">
                    <i class="bi bi-printer"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Set modal data when payslip button is clicked
document.addEventListener('DOMContentLoaded', function() {
    var payslipModal = document.getElementById('payslipModal');
    payslipModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        document.getElementById('payslipName').textContent = button.getAttribute('data-name');
        document.getElementById('payslipId').textContent = button.getAttribute('data-id');
        document.getElementById('payslipGross').textContent = button.getAttribute('data-gross');
        document.getElementById('payslipDeductions').textContent = button.getAttribute('data-deductions');
        document.getElementById('payslipNet').textContent = button.getAttribute('data-net');
    });
});

function printPayslip() {
    var payslipContent = document.getElementById('payslipContent').innerHTML;
    var originalContent = document.body.innerHTML;
    
    document.body.innerHTML = payslipContent;
    window.print();
    document.body.innerHTML = originalContent;
    window.location.reload();
}
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #payslipContent, #payslipContent * {
        visibility: visible;
    }
    #payslipContent {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
}
</style>

<?php
$conn->close();
require_once 'footer.php';
?>