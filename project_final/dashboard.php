<?php
require_once 'db.php';
require_once 'header.php';


if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

// Getting counts for cards on dashboard
$employee_count = $conn->query("SELECT COUNT(*) FROM employees")->fetch_row()[0];
$pending_leave = $conn->query("SELECT COUNT(*) FROM leave_requests WHERE status = 'Pending'")->fetch_row()[0];
$absent_today = $conn->query("SELECT COUNT(*) FROM attendance WHERE date = CURDATE() AND status = 'Absent'")->fetch_row()[0];

// Getting the recent attendance
$recent_attendance = $conn->query("
    SELECT a.date, a.status, e.name, e.position, e.department 
    FROM attendance a
    JOIN employees e ON a.employee_id = e.employee_id
    ORDER BY a.date DESC
    LIMIT 5
");

// Getting department distribution for bar graph
$departments = $conn->query("
    SELECT department, COUNT(*) as count 
    FROM employees 
    GROUP BY department
    ORDER BY count DESC
");

// Prepare data for bar chart
$dept_labels = [];
$dept_counts = [];
while ($dept = $departments->fetch_assoc()) {
    $dept_labels[] = $dept['department'];
    $dept_counts[] = $dept['count'];
}
$dept_labels_json = json_encode($dept_labels);
$dept_counts_json = json_encode($dept_counts);
?>

<div class="container-fluid">
    <!-- Header Section -->
<div class="home-header mb-4">
    <div class="d-flex align-items-center">
        <img src="assets/Logo.jpg" alt="ModernTech Logo" class="me-3 rounded-circle" style="width: 100px; height: 100px;">
        <h1 class="main-heading">WELCOME TO MODERNTECH SOLUTIONS</h1>
    </div>
</div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Employees</h5>
                    <p class="card-text display-4"><?php echo $employee_count; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Pending Leave Requests</h5>
                    <p class="card-text display-4"><?php echo $pending_leave; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">Absent Today</h5>
                    <p class="card-text display-4"><?php echo $absent_today; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Overview -->
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h2 class="mb-1">DEPARTMENT OVERVIEW</h2>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Department Bar Chart -->
                <div class="col-lg-5">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Employees by Department</h5>
                            <div style="height: 300px;">
                                <canvas id="departmentChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Attendance Table -->
                <div class="col-lg-7">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Recent Attendance</h5>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Employee</th>
                                            <th>Position</th>
                                            <th>Department</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($att = $recent_attendance->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($att['name']); ?></td>
                                            <td><?php echo htmlspecialchars($att['position']); ?></td>
                                            <td><?php echo htmlspecialchars($att['department']); ?></td>
                                            <td><?php echo htmlspecialchars($att['date']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $att['status'] == 'Present' ? 'success' : 'danger'; ?>">
                                                    <?php echo htmlspecialchars($att['status']); ?>
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
            </div>
        </div>
    </div>

</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Department Bar Chart
const deptCtx = document.getElementById('departmentChart').getContext('2d');
const departmentChart = new Chart(deptCtx, {
    type: 'bar',
    data: {
        labels: <?php echo $dept_labels_json; ?>,
        datasets: [{
            label: 'Number of Employees',
            data: <?php echo $dept_counts_json; ?>,
            backgroundColor: [
                'rgba(2, 99, 255, 0.7)',
                'rgba(0, 255, 170, 0.7)',
                'rgba(255, 162, 0, 0.7)',
                'rgba(255, 0, 0, 0.7)',
                'rgba(76, 0, 255, 0.7)',
                'rgba(0, 135, 120, 0.7)',
                'rgba(157, 65, 0, 0.7)'
            ],
            borderColor: [
                'rgba(0, 0, 0, 1)',
                'rgba(0, 0, 0, 1)',
                'rgba(0, 0, 0, 1)',
                'rgba(0, 0, 0, 1)',
                'rgba(0, 0, 0, 1)',
                'rgba(0, 0, 0, 1)',
                'rgba(0, 0, 0, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>

<style>

.mb-1{
    color : white ;
    text-align: center;    
}
.home-header {
    padding: 20px 0;
    border-bottom: 1px solid #dee2e6;
    margin-bottom: 30px;
    
}
.home-header h1 {
    color: #1E2A38;
    font-weight: 600;
    text-align: center;
}
.home-header img {
    object-fit: cover;
    border: 3px solid #566a8cff;
}
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: transform 0.2s ease-in-out;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
.card-header {
    font-weight: 600;
}
.table th {
    background-color: #212529ff;
    color: white;
}
</style>

<?php require_once 'footer.php'; ?>