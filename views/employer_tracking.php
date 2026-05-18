<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

include_once "../controllers/TrackingController.php";

$employer_id = $_SESSION['user_id'];
$jobs = $model->getEmployerJobs($employer_id);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Tracking Dashboard</title>
    <link rel="stylesheet" href="css/tracking_dashboard.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="container">
        <header>
            <h1>Employer Dashboard</h1>
            <p>Welcome back, <?php echo $_SESSION['name']; ?> (Employer ID: <?php echo $employer_id; ?>)</p>
            <nav>
                <a href="EmployerDashboard.php" class="btn" style="padding: 5px 10px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px;">Dashboard</a>
                <a href="../controllers/ProfileController.php" class="btn" style="padding: 5px 10px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px;">Profile</a>
                <a href="../controllers/LogoutController.php" class="btn btn-danger" style="padding: 5px 10px; background: #dc3545; color: white; text-decoration: none; border-radius: 5px;">Logout</a>
            </nav>
        </header>

        <div id="alert-message" class="alert"></div>

        <div class="card">
            <h3>Select Job Listing</h3>
            <div class="form-group">
                <select id="job-selector">
                    <option value="">-- Select a Job --</option>
                    <?php foreach ($jobs as $job): ?>
                        <option value="<?php echo $job['id']; ?>"><?php echo htmlspecialchars($job['title']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="card">
            <h3>Job Applications</h3>
            <div class="table-container">
                <table id="applications-table">
                    <thead>
                        <tr>
                            <th>Seeker Name</th>
                            <th>Headline</th>
                            <th>Date Applied</th>
                            <th>Cover Letter</th>
                            <th>Resume</th>
                            <th>Status Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6" style="text-align:center">Please select a job above to view applications
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <h3>Application Funnel Visualisation</h3>
            <div class="chart-container">
                <canvas id="funnelChart"></canvas>
            </div>
        </div>
    </div>

    <script src="../controllers/js/tracking.js"></script>
</body>

</html>