<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'db.php';
// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

if (!isset($_SESSION['loggedin'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access.']);
    exit;
}
// Checking if request_id and status are set and valid
$request_id = isset($_POST['request_id']) ? (int) $_POST['request_id'] : 0;
$status = isset($_POST['status']) ? trim($_POST['status']) : '';
$valid_statuses = ['Approved', 'Denied'];
if ($request_id <= 0 || !in_array($status, $valid_statuses)) {
    echo json_encode(['success' => false, 'error' => 'Invalid request data.']);
    exit;
}
// checking if the leave request exists
$stmt = $conn->prepare("UPDATE leave_requests SET status = ? WHERE request_id = ?");
$stmt->bind_param("si", $status, $request_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database update failed.']);
}
$stmt->close();
$conn->close();
?>