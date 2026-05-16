<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }

require_once __DIR__ . "/../models/ApplicationModel.php";

// Fallback session variables if not set by the view (for direct access or testing)
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 2; // Default to employer for testing
    $_SESSION['role'] = 'employer';
    $_SESSION['name'] = 'Employer User';
}

$model = new ApplicationModel();

if (isset($_GET['action'])) {
header('Content-Type: application/json');
$action = $_GET['action'];

if ($action === 'get_applications') {
$job_id = $_GET['job_id'] ?? 0;
$applications = $model->getApplicationsByJob($job_id);
echo json_encode($applications);
exit();
}

if ($action === 'update_status') {
// Since standard PHP doesn't handle PUT easily without extra config,
// we use POST or check for REQUEST_METHOD
$data = json_decode(file_get_contents("php://input"), true);
$app_id = $data['id'] ?? 0;
$status = $data['status'] ?? '';

if ($app_id && $status) {
$success = $model->updateApplicationStatus($app_id, $status);
echo json_encode(['success' => $success]);
} else {
echo json_encode(['success' => false, 'error' => 'Invalid data']);
}
exit();
}

if ($action === 'get_stats') {
$job_id = $_GET['job_id'] ?? 0;
$stats = $model->getFunnelStats($job_id);
echo json_encode($stats);
exit();
}

if ($action === 'delete_job') {
$job_id = $_POST['job_id'] ?? 0;
$success = $model->softDeleteJob($job_id);
if ($success) {
header("Location: ../views/admin_panel.php?success=Job closed");
} else {
header("Location: ../views/admin_panel.php?error=Delete failed");
}
exit();
}
}

// Function to get categories for filter
function getCategoriesForFilter() {
$model = new ApplicationModel();
return $model->getCategories();
}

// Function to get all jobs for admin with filters
function getAdminJobs($cat_id, $status) {
$model = new ApplicationModel();
return $model->getAllJobs($cat_id, $status);
}

// Function to get admin summary
function getAdminSummaryStats() {
$model = new ApplicationModel();
return $model->getAdminSummary();
}
?>