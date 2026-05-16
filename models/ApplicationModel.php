<?php
include_once "db.php";

class ApplicationModel extends db
{
    private function execute($sql, $params = [], $types = "")
    {
        $conn = $this->connection();
        $result = null;

        if (empty($params)) {
            $result = $conn->query($sql);
        } else {
            $stmt = $conn->prepare($sql);
            if ($types) {
                $stmt->bind_param($types, ...$params);
            }
            if (!$stmt->execute()) {
                $stmt->close();
                $conn->close();
                return false;
            }
            $result = $stmt->get_result();
            
            if ($result === false) {
                $status = $stmt->affected_rows >= 0;
                $stmt->close();
                $conn->close();
                return $status;
            }
            $stmt->close();
        }

        if (is_bool($result)) {
            $conn->close();
            return $result;
        }

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $conn->close();
        return $data;
    }

    public function getEmployerJobs($employer_id)
    {
        $sql = "SELECT id, title FROM jobs WHERE employer_id = ? AND status = 'active'";
        return $this->execute($sql, [$employer_id], "i");
    }

    public function getApplicationsByJob($job_id)
    {
        $sql = "SELECT a.*, u.name as seeker_name, sp.headline 
                FROM applications a 
                JOIN users u ON a.seeker_id = u.id 
                LEFT JOIN seeker_profiles sp ON u.id = sp.user_id 
                WHERE a.job_id = ?";
        return $this->execute($sql, [$job_id], "i");
    }

    public function updateApplicationStatus($app_id, $status)
    {
        $sql = "UPDATE applications SET status = ? WHERE id = ?";
        return $this->execute($sql, [$status, $app_id], "si");
    }

    public function getFunnelStats($job_id)
    {
        $sql = "SELECT status, COUNT(*) as count FROM applications WHERE job_id = ? GROUP BY status";
        $rows = $this->execute($sql, [$job_id], "i");
        
        $stats = [
            'Submitted' => 0,
            'Reviewed' => 0,
            'Shortlisted' => 0,
            'Rejected' => 0
        ];
        
        foreach ($rows as $row) {
            $stats[$row['status']] = $row['count'];
        }
        return $stats;
    }

    public function getAllJobs($category_id = null, $status = null)
    {
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

        return $this->execute($sql, $params, $types);
    }

    public function softDeleteJob($job_id)
    {
        $sql = "UPDATE jobs SET status = 'closed' WHERE id = ?";
        return $this->execute($sql, [$job_id], "i");
    }

    public function getAdminSummary()
    {
        $summary = [];
        
        $totalJobsRow = $this->execute("SELECT COUNT(*) as total FROM jobs");
        $summary['total_jobs'] = $totalJobsRow[0]['total'] ?? 0;

        $totalAppsRow = $this->execute("SELECT COUNT(*) as total FROM applications");
        $summary['total_applications'] = $totalAppsRow[0]['total'] ?? 0;

        $sql = "SELECT c.name, COUNT(a.id) as app_count 
                FROM categories c 
                LEFT JOIN jobs j ON c.id = j.category_id 
                LEFT JOIN applications a ON j.id = a.job_id 
                GROUP BY c.id";
        $summary['category_breakdown'] = $this->execute($sql);

        return $summary;
    }

    public function getCategories()
    {
        return $this->execute("SELECT * FROM categories");
    }

    public function getSavedJobs($user_id)
    {
        $sql = "SELECT j.*, c.name as category_name 
                FROM saved_jobs sj 
                JOIN jobs j ON sj.job_id = j.id 
                JOIN categories c ON j.category_id = c.id 
                WHERE sj.user_id = ?";
        return $this->execute($sql, [$user_id], "i");
    }

    public function getAppliedJobs($user_id)
    {
        $sql = "SELECT j.title, j.location, a.status, a.created_at as applied_date 
                FROM applications a 
                JOIN jobs j ON a.job_id = j.id 
                WHERE a.seeker_id = ?";
        return $this->execute($sql, [$user_id], "i");
    }
}
?>