<?php
include_once "db.php";

class ApplicationModel extends db {
    
    // Fetch all jobs belonging to a specific employer
    public function getEmployerJobs($employer_id) {
        $conn = $this->connection();
        $sql = "SELECT id, title FROM jobs WHERE employer_id = ? AND status = 'active'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $employer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $jobs = [];
        while ($row = $result->fetch_assoc()) {
            $jobs[] = $row;
        }
        $stmt->close();
        $conn->close();
        return $jobs;
    }

    // Fetch all applications for a specific job
    public function getApplicationsByJob($job_id) {
        $conn = $this->connection();
        $sql = "SELECT a.*, u.name as seeker_name, sp.headline 
                FROM applications a 
                JOIN users u ON a.seeker_id = u.id 
                LEFT JOIN seeker_profiles sp ON u.id = sp.user_id 
                WHERE a.job_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $job_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $apps = [];
        while ($row = $result->fetch_assoc()) {
            $apps[] = $row;
        }
        $stmt->close();
        $conn->close();
        return $apps;
    }

    // Update application status
    public function updateApplicationStatus($app_id, $status) {
        $conn = $this->connection();
        $sql = "UPDATE applications SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $app_id);
        $success = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $success;
    }

    // Get stats for the funnel chart
    public function getFunnelStats($job_id) {
        $conn = $this->connection();
        $sql = "SELECT status, COUNT(*) as count FROM applications WHERE job_id = ? GROUP BY status";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $job_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stats = [
            'Submitted' => 0,
            'Reviewed' => 0,
            'Shortlisted' => 0,
            'Rejected' => 0
        ];
        while ($row = $result->fetch_assoc()) {
            $stats[$row['status']] = $row['count'];
        }
        $stmt->close();
        $conn->close();
        return $stats;
    }

    // Admin: Get all jobs with filters
    public function getAllJobs($category_id = null, $status = null) {
        $conn = $this->connection();
        $sql = "SELECT j.*, u.name as employer_name, c.name as category_name 
                FROM jobs j 
                JOIN users u ON j.employer_id = u.id 
                JOIN categories c ON j.category_id = c.id WHERE 1=1";
        
        $params = [];
        $types = "";
        
        if ($category_id) {
            $sql .= " AND j.category_id = ?";
            $params[] = $category_id;
            $types .= "i";
        }
        if ($status) {
            $sql .= " AND j.status = ?";
            $params[] = $status;
            $types .= "s";
        }

        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $jobs = [];
        while ($row = $result->fetch_assoc()) {
            $jobs[] = $row;
        }
        $stmt->close();
        $conn->close();
        return $jobs;
    }

    // Admin: Soft delete job
    public function softDeleteJob($job_id) {
        $conn = $this->connection();
        $sql = "UPDATE jobs SET status = 'closed' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $job_id);
        $success = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $success;
    }

    // Admin: Summary Stats
    public function getAdminSummary() {
        $conn = $this->connection();
        $summary = [];
        
        $res = $conn->query("SELECT COUNT(*) as total FROM jobs");
        $summary['total_jobs'] = $res->fetch_assoc()['total'];
        
        $res = $conn->query("SELECT COUNT(*) as total FROM applications");
        $summary['total_applications'] = $res->fetch_assoc()['total'];
        
        $sql = "SELECT c.name, COUNT(a.id) as app_count 
                FROM categories c 
                LEFT JOIN jobs j ON c.id = j.category_id 
                LEFT JOIN applications a ON j.id = a.job_id 
                GROUP BY c.id";
        $res = $conn->query($sql);
        $summary['category_breakdown'] = [];
        while ($row = $res->fetch_assoc()) {
            $summary['category_breakdown'][] = $row;
        }
        
        $conn->close();
        return $summary;
    }

    public function getCategories() {
        $conn = $this->connection();
        $res = $conn->query("SELECT * FROM categories");
        $categories = [];
        while ($row = $res->fetch_assoc()) {
            $categories[] = $row;
        }
        $conn->close();
        return $categories;
    }

    // Seeker: Get saved jobs
    public function getSavedJobs($user_id) {
        $conn = $this->connection();
        $sql = "SELECT j.*, c.name as category_name 
                FROM saved_jobs sj 
                JOIN jobs j ON sj.job_id = j.id 
                JOIN categories c ON j.category_id = c.id 
                WHERE sj.user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $jobs = [];
        while ($row = $result->fetch_assoc()) {
            $jobs[] = $row;
        }
        $stmt->close();
        $conn->close();
        return $jobs;
    }

    // Seeker: Get applied jobs with status
    public function getAppliedJobs($user_id) {
        $conn = $this->connection();
        $sql = "SELECT j.title, j.location, a.status, a.created_at as applied_date 
                FROM applications a 
                JOIN jobs j ON a.job_id = j.id 
                WHERE a.seeker_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $apps = [];
        while ($row = $result->fetch_assoc()) {
            $apps[] = $row;
        }
        $stmt->close();
        $conn->close();
        return $apps;
    }
}
?>
