<?php
require_once 'db.php';
require_once 'header.php';



if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

// Handle form submission for new performance review
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = intval($_POST['employee_id']);
    $employee_name = $conn->real_escape_string($_POST['employee_name']);
    $department = $conn->real_escape_string($_POST['department']);
    $reviewer = $conn->real_escape_string($_POST['reviewer']);
    $punctuality = $conn->real_escape_string($_POST['punctuality']);
    $dependability = $conn->real_escape_string($_POST['dependability']);

    $sql = "INSERT INTO performance_reviews (employee_id, employee_name, department, reviewer, punctuality, dependability)
            VALUES ($employee_id, '$employee_name', '$department', '$reviewer', '$punctuality', '$dependability')";

    if ($conn->query($sql)) {
        $_SESSION['message'] = "Performance review added successfully!";
    } else {
        $_SESSION['error'] = "Error adding performance review: " . $conn->error;
    }
    
    header('Location: performance.php');
    exit;
}

// Get all performance reviews
$reviews = $conn->query("
    SELECT pr.*, e.position 
    FROM performance_reviews pr
    JOIN employees e ON pr.employee_id = e.employee_id
    ORDER BY pr.employee_name
");
?>

<div class="container-fluid">
    <h3>Performance Reviews</h3>
    
    <!-- Display messages -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Employee Performance Reviews</span>
            <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#addReviewModal">
                <i class="bi bi-plus"></i> Add Review
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
                            <th>Reviewer</th>
                            <th>Punctuality</th>
                            <th>Dependability</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($review = $reviews->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($review['employee_name']); ?></td>
                            <td><?php echo htmlspecialchars($review['position']); ?></td>
                            <td><?php echo htmlspecialchars($review['department']); ?></td>
                            <td><?php echo htmlspecialchars($review['reviewer']); ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $review['punctuality'] == 'Excellent' ? 'success' : 
                                         ($review['punctuality'] == 'Good' ? 'primary' : 
                                         ($review['punctuality'] == 'Average' ? 'warning' : 'danger')); 
                                ?>">
                                    <?php echo htmlspecialchars($review['punctuality']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $review['dependability'] == 'Excellent' ? 'success' : 
                                         ($review['dependability'] == 'Good' ? 'primary' : 
                                         ($review['dependability'] == 'Average' ? 'warning' : 'danger')); 
                                ?>">
                                    <?php echo htmlspecialchars($review['dependability']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Review Modal -->
<div class="modal fade" id="addReviewModal" tabindex="-1" aria-labelledby="addReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addReviewModalLabel">Add Performance Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="employee_id" class="form-label">Employee</label>
                        <select class="form-select" id="employee_id" name="employee_id" required>
                            <option value="">Select Employee</option>
                            <?php
                            $employees = $conn->query("SELECT employee_id, name FROM employees ORDER BY name");
                            while ($emp = $employees->fetch_assoc()):
                            ?>
                            <option value="<?php echo $emp['employee_id']; ?>"><?php echo htmlspecialchars($emp['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="employee_name" class="form-label">Employee Name</label>
                        <input type="text" class="form-control" id="employee_name" name="employee_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="department" class="form-label">Department</label>
                        <input type="text" class="form-control" id="department" name="department" required>
                    </div>
                    <div class="mb-3">
                        <label for="reviewer" class="form-label">Reviewer</label>
                        <input type="text" class="form-control" id="reviewer" name="reviewer" required>
                    </div>
                    <div class="mb-3">
                        <label for="punctuality" class="form-label">Punctuality</label>
                        <select class="form-select" id="punctuality" name="punctuality" required>
                            <option value="">Select Rating</option>
                            <option value="Excellent">Excellent</option>
                            <option value="Good">Good</option>
                            <option value="Average">Average</option>
                            <option value="Poor">Poor</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="dependability" class="form-label">Dependability</label>
                        <select class="form-select" id="dependability" name="dependability" required>
                            <option value="">Select Rating</option>
                            <option value="Excellent">Excellent</option>
                            <option value="Good">Good</option>
                            <option value="Average">Average</option>
                            <option value="Poor">Poor</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-dark">Add Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Auto-fill employee details when selected
document.getElementById('employee_id').addEventListener('change', function() {
    const employeeId = this.value;
    if (employeeId) {
        fetch('get_employee.php?id=' + employeeId)
            .then(response => response.json())
            .then(data => {
                if (data) {
                    document.getElementById('employee_name').value = data.name;
                    document.getElementById('department').value = data.department;
                }
            });
    }
});
</script>

<?php require_once 'footer.php'; ?>