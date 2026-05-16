<?php
// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration
// 1. Load Configuration
if (file_exists(__DIR__ . '/config/db_config.php')) {
    require_once __DIR__ . '/config/db_config.php';
} else {
    die("❌ Error: config/db_config.php not found. Please make sure it exists.");
}

echo "--- Job Portal System Check ---\n";
echo "Attempting to connect as '" . DB_USER . "' to '" . DB_HOST . "'...\n";
echo "Using password: " . (DB_PASSWORD ? "YES (hidden)" : "NO") . "\n\n";

// 2. Check MySQL Connection
$conn = @new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    echo "❌ Database Connection Failed: " . $conn->connect_error . "\n";
    echo "   Tip: Make sure MySQL is running in XAMPP and the database 'job_portal' exists.\n";
} else {
    echo "✅ Database Connection Successful!\n";

    // 2. Check Tables
    $tables = ['users', 'categories', 'jobs', 'applications', 'employer_profiles', 'seeker_profiles', 'saved_jobs'];
    echo "\n--- Table Status ---\n";
    foreach ($tables as $table) {
        $res = $conn->query("SELECT COUNT(*) as count FROM $table");
        if ($res) {
            $row = $res->fetch_assoc();
            echo "📊 Table '$table': " . $row['count'] . " records found.\n";
        } else {
            echo "❌ Table '$table' is missing or has errors.\n";
        }
    }

    // 3. Summary for Dashboards
    echo "\n--- Application Summary ---\n";
    $jobs_res = $conn->query("SELECT COUNT(*) as total FROM jobs");
    $apps_res = $conn->query("SELECT COUNT(*) as total FROM applications");
    echo "Active Jobs: " . $jobs_res->fetch_assoc()['total'] . "\n";
    echo "Total Applications: " . $apps_res->fetch_assoc()['total'] . "\n";

    $conn->close();
}

echo "\nCheck completed.\n";
?>