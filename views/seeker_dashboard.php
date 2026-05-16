<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }

// HARDCODED SEEKER DATA
$_SESSION['user_id'] = 4; // Assuming ID 4 is a seeker
$_SESSION['role'] = 'seeker';
$_SESSION['name'] = 'Seeker User';

include_once "../controllers/TrackingController.php";

$seeker_id = $_SESSION['user_id'];
$saved_jobs = $model->getSavedJobs($seeker_id);
$applied_jobs = $model->getAppliedJobs($seeker_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seeker Dashboard</title>
    <link rel="stylesheet" href="css/tracking_dashboard.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
</head>
<body>
    <div class="container">
        <header>
            <h1>Seeker Dashboard</h1>
            <p>Welcome, <?php echo $_SESSION['name']; ?>!</p>
            <nav>
                <a href="employer_tracking.php">Employer Tracking</a> | 
                <a href="seeker_dashboard.php" style="font-weight:bold">My Jobs</a> |
                <a href="admin_panel.php">Admin Panel</a> |
                <a href="../controllers/Logout.php">Logout</a>
            </nav>
        </header>

        <div class="card">
            <h3>My Applications & Status</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Location</th>
                            <th>Applied Date</th>
                            <th>Current Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applied_jobs as $app): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($app['title']); ?></td>
                                <td><?php echo htmlspecialchars($app['location']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($app['applied_date'])); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo strtolower($app['status']); ?>">
                                        <?php echo $app['status']; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($applied_jobs)): ?>
                            <tr><td colspan="4" style="text-align:center">You haven't applied to any jobs yet</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <h3>Saved Jobs</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Category</th>
                            <th>Location</th>
                            <th>Deadline</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($saved_jobs as $job): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($job['title']); ?></td>
                                <td><?php echo htmlspecialchars($job['category_name']); ?></td>
                                <td><?php echo htmlspecialchars($job['location']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($job['deadline'])); ?></td>
                                <td>
                                    <a href="job_details.php?id=<?php echo $job['id']; ?>" class="btn btn-secondary" style="text-decoration:none">View Job</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($saved_jobs)): ?>
                            <tr><td colspan="5" style="text-align:center">No saved jobs found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
