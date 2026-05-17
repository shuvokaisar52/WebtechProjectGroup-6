<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../models/ApplicationModel.php";

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 2;
    $_SESSION['role'] = 'employer';
    $_SESSION['name'] = 'Tech Solutions';
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
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            echo json_encode(['success' => false, 'error' => 'Method Not Allowed']);
            exit();
        }
        $data = json_decode(file_get_contents("php://input"), true);
        $app_id = $data['id'] ?? 0;
        $status = $data['status'] ?? '';

        if ($app_id && $status) {
            $success = $model->updateApplicationStatus($app_id, $status);
            if ($success) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Database update failed']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid data: ID=' . $app_id . ', Status=' . $status]);
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

    if ($action === 'get_admin_jobs') {
        $cat_id = $_GET['category'] ?? null;
        if ($cat_id === '') $cat_id = null;
        $status = $_GET['status'] ?? null;
        if ($status === '') $status = null;
        
        $jobs = $model->getAllJobs($cat_id, $status);
        echo json_encode($jobs);
        exit();
    }
}

function getCategoriesForFilter()
{
    $model = new ApplicationModel();
    return $model->getCategories();
}

function getAdminJobs($cat_id, $status)
{
    $model = new ApplicationModel();
    return $model->getAllJobs($cat_id, $status);
}

function getAdminSummaryStats()
{
    $model = new ApplicationModel();
    return $model->getAdminSummary();
}
?>