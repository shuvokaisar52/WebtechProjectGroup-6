<?php
include_once "../models/ApplicationModel.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $job_id = $_POST['job_id'];
    $seeker_id = $_SESSION['user_id'];
    $cover_letter = $_POST['cover_letter'];
    $file = $_FILES['resume'];

    // SERVER-SIDE MIME TYPE VALIDATION
    $allowed_types = ['application/pdf', 'application/msword'];
    $file_type = mime_content_type($file['tmp_name']);

    if (!in_array($file_type, $allowed_types)) {
        die("Error: Only PDF and Word documents are allowed.");
    }

    // Additional check: File extension
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    if (!in_array(strtolower($extension), ['pdf', 'doc', 'docx'])) {
        die("Error: Invalid file extension.");
    }

    $target_dir = "../uploads/";
    if (!is_dir($target_dir))
        mkdir($target_dir, 0777, true);

    $file_name = time() . "_" . basename($file["name"]);
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        $model = new ApplicationModel();
        $conn = $model->connection();

        // Use prepared statement to prevent SQL injection and handle duplicate application prevention (UNIQUE KEY in DB)
        $sql = "INSERT INTO applications (job_id, seeker_id, cover_letter, resume_path) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiss", $job_id, $seeker_id, $cover_letter, $file_name);

        try {
            if ($stmt->execute()) {
                header("Location: ../views/seeker_dashboard.php?success=Application submitted");
            } else {
                echo "Error submitting application.";
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) { // Duplicate entry
                echo "Error: You have already applied for this job.";
            } else {
                echo "Database error: " . $e->getMessage();
            }
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "Error uploading file.";
    }
}
?>