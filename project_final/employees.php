<?php
require_once 'db.php';
require_once 'header.php';

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

// Handle employee deletion
if (isset($_GET['delete_id'])) {
    try {
        $delete_id = (int)$_GET['delete_id'];
        executeQuery("DELETE FROM employees WHERE employee_id = ?", [$delete_id]);
        $_SESSION['message'] = "Employee deleted successfully!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error deleting employee: " . $e->getMessage();
    }
}

// Get all employees
$result = $conn->query("SELECT * FROM employees ORDER BY name");
?>

<div class="container-fluid">
    <!-- Display messages -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <h3>Employee Management</h3>
    
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Employee Directory</span>
            <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                <i class="bi bi-plus"></i> Add Employee
            </button>
        </div>
        <div class="card-body">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
                <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-header bg-dark text-white d-flex justify-content-between">
                            <h5 class="card-title mb-0"><?php echo htmlspecialchars($row['name']); ?></h5>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown">
                                    <i class="bi bi-gear"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editEmployeeModal" 
                                            data-id="<?php echo $row['employee_id']; ?>"
                                            data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                            data-position="<?php echo htmlspecialchars($row['position']); ?>"
                                            data-department="<?php echo htmlspecialchars($row['department']); ?>"
                                            data-salary="<?php echo $row['salary']; ?>"
                                            data-history="<?php echo htmlspecialchars($row['employment_history']); ?>"
                                            data-contact="<?php echo htmlspecialchars($row['contact_email']); ?>">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="employees.php?delete_id=<?php echo $row['employee_id']; ?>" 
                                           onclick="return confirm('Are you sure you want to delete this employee?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <div class="avatar bg-secondary text-white d-flex align-items-center justify-content-center" 
                                         style="width: 50px; height: 50px; border-radius: 50%; font-size: 1.25rem;">
                                        <?php echo strtoupper(substr($row['name'], 0, 1)); ?>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($row['position']); ?></h6>
                                    <span class="badge bg-dark"><?php echo htmlspecialchars($row['department']); ?></span>
                                </div>
                            </div>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Salary</span>
                                    <span class="fw-bold">R<?php echo number_format($row['salary'], 2); ?></span>
                                </li>
                                <li class="list-group-item">
                                    <span class="d-block text-muted small">Contact</span>
                                    <a href="mailto:<?php echo htmlspecialchars($row['contact_email']); ?>">
                                        <?php echo htmlspecialchars($row['contact_email']); ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-footer bg-transparent">
                            <button class="btn btn-sm btn-outline-dark w-100" data-bs-toggle="modal" data-bs-target="#viewEmployeeModal" 
                                data-id="<?php echo $row['employee_id']; ?>"
                                data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                data-position="<?php echo htmlspecialchars($row['position']); ?>"
                                data-department="<?php echo htmlspecialchars($row['department']); ?>"
                                data-salary="<?php echo $row['salary']; ?>"
                                data-history="<?php echo htmlspecialchars($row['employment_history']); ?>"
                                data-contact="<?php echo htmlspecialchars($row['contact_email']); ?>">
                                <i class="bi bi-eye"></i> View Details
                            </button>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add Employee Modal -->
<div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEmployeeModalLabel">Add New Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="add_employee.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="position" class="form-label">Position</label>
                        <input type="text" class="form-control" id="position" name="position" required>
                    </div>
                    <div class="mb-3">
                        <label for="department" class="form-label">Department</label>
                        <select class="form-select" id="department" name="department" required>
                            <option value="">Select Department</option>
                            <option value="Development">Development</option>
                            <option value="HR">HR</option>
                            <option value="QA">QA</option>
                            <option value="Sales">Sales</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Design">Design</option>
                            <option value="IT">IT</option>
                            <option value="Finance">Finance</option>
                            <option value="Support">Support</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="salary" class="form-label">Salary</label>
                        <input type="number" step="0.01" class="form-control" id="salary" name="salary" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact_email" class="form-label">Contact Email</label>
                        <input type="email" class="form-control" id="contact_email" name="contact_email" required>
                    </div>
                    <div class="mb-3">
                        <label for="employment_history" class="form-label">Employment History</label>
                        <textarea class="form-control" id="employment_history" name="employment_history" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-dark">Add Employee</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Employee Modal -->
<div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-labelledby="editEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editEmployeeModalLabel">Edit Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="edit_employee.php" method="POST">
                <input type="hidden" id="edit_id" name="employee_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_position" class="form-label">Position</label>
                        <input type="text" class="form-control" id="edit_position" name="position" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_department" class="form-label">Department</label>
                        <select class="form-select" id="edit_department" name="department" required>
                            <option value="">Select Department</option>
                            <option value="Development">Development</option>
                            <option value="HR">HR</option>
                            <option value="QA">QA</option>
                            <option value="Sales">Sales</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Design">Design</option>
                            <option value="IT">IT</option>
                            <option value="Finance">Finance</option>
                            <option value="Support">Support</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_salary" class="form-label">Salary</label>
                        <input type="number" step="0.01" class="form-control" id="edit_salary" name="salary" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_contact_email" class="form-label">Contact Email</label>
                        <input type="email" class="form-control" id="edit_contact_email" name="contact_email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_employment_history" class="form-label">Employment History</label>
                        <textarea class="form-control" id="edit_employment_history" name="employment_history" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Employee Modal -->
<div class="modal fade" id="viewEmployeeModal" tabindex="-1" aria-labelledby="viewEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewEmployeeModalLabel">Employee Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <p class="form-control-static" id="viewName"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Position</label>
                    <p class="form-control-static" id="viewPosition"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Department</label>
                    <p class="form-control-static" id="viewDepartment"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Salary</label>
                    <p class="form-control-static" id="viewSalary"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contact Email</label>
                    <p class="form-control-static" id="viewContact"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Employment History</label>
                    <p class="form-control-static" id="viewHistory"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Handle view modal data
document.getElementById('viewEmployeeModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    document.getElementById('viewName').textContent = button.getAttribute('data-name');
    document.getElementById('viewPosition').textContent = button.getAttribute('data-position');
    document.getElementById('viewDepartment').textContent = button.getAttribute('data-department');
    document.getElementById('viewSalary').textContent = 'R' + parseFloat(button.getAttribute('data-salary')).toFixed(2);
    document.getElementById('viewContact').textContent = button.getAttribute('data-contact');
    document.getElementById('viewHistory').textContent = button.getAttribute('data-history') || 'No history available';
});

// Handle edit modal data
document.getElementById('editEmployeeModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    document.getElementById('edit_id').value = button.getAttribute('data-id');
    document.getElementById('edit_name').value = button.getAttribute('data-name');
    document.getElementById('edit_position').value = button.getAttribute('data-position');
    document.getElementById('edit_department').value = button.getAttribute('data-department');
    document.getElementById('edit_salary').value = button.getAttribute('data-salary');
    document.getElementById('edit_contact_email').value = button.getAttribute('data-contact');
    document.getElementById('edit_employment_history').value = button.getAttribute('data-history') || '';
});
</script>

<?php require_once 'footer.php'; ?>