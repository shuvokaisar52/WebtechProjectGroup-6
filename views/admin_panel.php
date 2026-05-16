<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }

// HARDCODED ADMIN DATA FOR TESTING
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';
$_SESSION['name'] = 'Admin User';

include_once "../controllers/TrackingController.php";

$cat_filter = $_GET['category'] ?? null;
$status_filter = $_GET['status'] ?? null;

$jobs = getAdminJobs($cat_filter, $status_filter);
$categories = getCategoriesForFilter();
$summary = getAdminSummaryStats();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Management Panel</title>
    <link rel="stylesheet" href="css/tracking_dashboard.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
</head>
<body>
    <div class="container">
        <header>
            <h1>Admin Panel - Job Management</h1>
            <nav>
                <a href="employer_tracking.php">Employer Dashboard</a> | 
                <a href="admin_panel.php" style="font-weight:bold">Admin Panel</a> |
                <a href="../controllers/Logout.php">Logout</a>
            </nav>
        </header>

        <section class="admin-summary">
            <div class="stat-box">
                <div class="stat-value"><?php echo $summary['total_jobs']; ?></div>
                <div class="stat-label">Total Jobs</div>
            </div>
            <div class="stat-box">
                <div class="stat-value"><?php echo $summary['total_applications']; ?></div>
                <div class="stat-label">Total Applications</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Applications per Category</div>
                <table style="font-size: 0.85rem; margin-top: 10px;">
                    <?php foreach ($summary['category_breakdown'] as $cat): ?>
                        <tr>
                            <td><?php echo $cat['name']; ?></td>
                            <td style="text-align: right; font-weight: bold;"><?php echo $cat['app_count']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </section>

        <div class="card">
            <h3>Filters</h3>
            <form method="GET" class="filter-form" style="display: flex; gap: 15px; align-items: flex-end;">
                <div class="form-group" style="flex: 1; margin-bottom: 0;">
                    <label>Category</label>
                    <select name="category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo $cat_filter == $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo $cat['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="flex: 1; margin-bottom: 0;">
                    <label>Status</label>
                    <select name="status">
                        <option value="">All Statuses</option>
                        <option value="active" <?php echo $status_filter == 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="closed" <?php echo $status_filter == 'closed' ? 'selected' : ''; ?>>Closed</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-secondary">Apply Filters</button>
                <a href="admin_panel.php" class="btn" style="text-decoration:none; background:#eee; color:#333;">Clear</a>
            </form>
        </div>

        <div class="card">
            <h3>All Job Listings</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Employer</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jobs as $job): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($job['title']); ?></td>
                                <td><?php echo htmlspecialchars($job['employer_name']); ?></td>
                                <td><?php echo htmlspecialchars($job['category_name']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $job['status']; ?>">
                                        <?php echo $job['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($job['status'] === 'active'): ?>
                                        <form action="../controllers/TrackingController.php?action=delete_job" method="POST" onsubmit="return confirm('Close this job listing?');">
                                            <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
                                            <button type="submit" class="btn btn-danger">Close Job</button>
                                        </form>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted); font-style: italic;">No actions</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($jobs)): ?>
                            <tr><td colspan="5" style="text-align:center">No jobs found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
