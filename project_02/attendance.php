<?php
require_once 'db.php';
require_once 'header.php';


if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}
// Getting all employees with their attendance
$result = $conn->query("
    SELECT e.employee_id, e.name, e.position, e.department,a.date, a.status
    FROM employees e
    LEFT JOIN attendance a ON e.employee_id = a.employee_id
    ORDER BY e.name, a.date DESC
");
// Organize data by employee
$employees = [];
while ($row = $result->fetch_assoc()) {
    $employee_id = $row['employee_id'];
    if (!isset($employees[$employee_id])) {
        $employees[$employee_id] = [
            'name' => $row['name'],
            'position' => $row['position'],
            'department' => $row['department'],
            'attendance' => []
        ];
    }
    if ($row['date']) {
        $employees[$employee_id]['attendance'][] = [
            'date' => $row['date'],
            'status' => $row['status']
        ];
    }
}
?>
<div class="container-fluid">
    <h3>Attendance Tracking</h3>
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Employee Attendance Records</span>
            <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#addAttendanceModal">
                <i class="bi bi-plus"></i> Add Attendance Record
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Position</th>
                            <th>Department</th>
                            <th>Recent Attendance</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employees as $id => $employee): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($employee['name']); ?></td>
                            <td><?php echo htmlspecialchars($employee['position']); ?></td>
                            <td><?php echo htmlspecialchars($employee['department']); ?></td>
                            <td>
                                <?php if (!empty($employee['attendance'])): ?>
                                    <?php
                                    $recent = array_slice($employee['attendance'], 0, 3);
                                    foreach ($recent as $record):
                                    ?>
                                        <span class="badge bg-<?php echo $record['status'] == 'Present' ? 'success' : 'danger'; ?> me-1">
                                            <?php echo htmlspecialchars($record['date']) . ': ' . htmlspecialchars($record['status']); ?>
                                        </span>
                                    <?php endforeach; ?>
                                    <?php if (count($employee['attendance']) > 3): ?>
                                        <span class="text-muted">+<?php echo count($employee['attendance']) - 3; ?> more</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">No records</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#viewAttendanceModal"
                                    data-employee-id="<?php echo $id; ?>"
                                    data-employee-name="<?php echo htmlspecialchars($employee['name']); ?>">
                                    <i class="bi bi-calendar3"></i> View All
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Adding Attendance Modal -->
<div class="modal fade" id="addAttendanceModal" tabindex="-1" aria-labelledby="addAttendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAttendanceModalLabel">Add Attendance Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="add_attendance.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="employee_id" class="form-label">Employee</label>
                        <select class="form-select" id="employee_id" name="employee_id" required>
                            <option value="">Select Employee</option>
                            <?php
                            $employees_list = $conn->query("SELECT employee_id, name FROM employees ORDER BY name");
                            while ($emp = $employees_list->fetch_assoc()):
                            ?>
                            <option value="<?php echo $emp['employee_id']; ?>"><?php echo htmlspecialchars($emp['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="Present">Present</option>
                            <option value="Absent">Absent</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-dark">Add Record</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- View Attendance Modal -->
<div class="modal fade" id="viewAttendanceModal" tabindex="-1" aria-labelledby="viewAttendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewAttendanceModalLabel">Attendance Records</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 id="employeeAttendanceTitle"></h6>
                <div class="table-responsive mt-3">
                    <table class="table table-sm" id="attendanceTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Attendance records will be loaded here via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
// Handle view attendance modal
document.getElementById('viewAttendanceModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const employeeId = button.getAttribute('data-employee-id');
    const employeeName = button.getAttribute('data-employee-name');
    document.getElementById('employeeAttendanceTitle').textContent = 'Attendance for ' + employeeName;
    // Load attendance data via AJAX
    fetch('get_attendance.php?employee_id=' + employeeId)
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector('#attendanceTable tbody');
            tbody.innerHTML = '';
            if (data.length > 0) {
                data.forEach(record => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${record.date}</td>
                        <td><span class="badge bg-${record.status === 'Present' ? 'success' : 'danger'}">${record.status}</span></td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                const row = document.createElement('tr');
                row.innerHTML = '<td colspan="2" class="text-center text-muted">No attendance records found</td>';
                tbody.appendChild(row);
            }
        });
});
</script>

<style>
    .btn-info{
         #71808fff;
    }
</style>
<?php require_once 'footer.php'; ?>