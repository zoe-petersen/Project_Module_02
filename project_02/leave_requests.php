<?php
require_once 'db.php';
require_once 'header.php';


if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}
// Get all leave requests with employee details
$result = $conn->query("
    SELECT l.request_id, l.employee_id, l.date, l.reason, l.status, e.name, e.position
    FROM leave_requests l
    JOIN employees e ON l.employee_id = e.employee_id
    ORDER BY l.date DESC
");
?>
<div class="container-fluid">
    <h3>Leave Request Management</h3>
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Leave Requests</span>
            <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#addLeaveRequestModal">
                <i class="bi bi-plus"></i> Add Leave Request
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Position</th>
                            <th>Date</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['position']); ?></td>
                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                            <td><?php echo htmlspecialchars($row['reason']); ?></td>
                            <td>
                                <span class="badge bg-<?php
                                    echo $row['status'] == 'Approved' ? 'success' :
                                         ($row['status'] == 'Denied' ? 'danger' : 'warning');
                                ?>">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($row['status'] == 'Pending'): ?>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-success" onclick="updateLeaveStatus(<?php echo $row['request_id']; ?>, 'Approved')" style="border-radius: 10px">
                                            <i class="bi bi-check"></i> Approve
                                        </button>
                                        <button class="btn btn-danger" onclick="updateLeaveStatus(<?php echo $row['request_id']; ?>, 'Denied')" style="margin-left:10px; border-radius: 10px">
                                            <i class="bi bi-x"></i> Deny
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">Processed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Add Leave Request Modal -->
<div class="modal fade" id="addLeaveRequestModal" tabindex="-1" aria-labelledby="addLeaveRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addLeaveRequestModalLabel">Add Leave Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="add_leave_request.php" method="POST">
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
                        <label for="reason" class="form-label">Reason</label>
                        <select class="form-select" id="reason" name="reason" required>
                            <option value="">Select Reason</option>
                            <option value="Vacation">Vacation</option>
                            <option value="Sick Leave">Sick Leave</option>
                            <option value="Medical Appointment">Medical Appointment</option>
                            <option value="Family Responsibility">Family Responsibility</option>
                            <option value="Childcare">Childcare</option>
                            <option value="Bereavement">Bereavement</option>
                            <option value="Personal">Personal</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-dark">Add Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function updateLeaveStatus(requestId, status) {
    if (confirm(`Are you sure you want to ${status.toLowerCase()} this leave request?`)) {
        fetch('update_leave_request.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `request_id=${requestId}&status=${status}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating leave request: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the leave request');
        });
    }
}
</script>
<?php require_once 'footer.php'; ?>