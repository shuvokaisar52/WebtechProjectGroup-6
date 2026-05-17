<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: ../views/Login.php');
    exit;
}

include_once "../models/db.php";

$db     = (new db())->connection();
$userId = (int) $_SESSION['user_id'];
$role   = $_SESSION['role'];
$errors = [];

// ── Fetch current user ──
$stmt = $db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// ── Fetch existing profile ──
if ($role === 'employer') {
    $stmt = $db->prepare("SELECT * FROM employer_profiles WHERE user_id = ? LIMIT 1");
} else {
    $stmt = $db->prepare("SELECT * FROM seeker_profiles WHERE user_id = ? LIMIT 1");
}
$stmt->bind_param("i", $userId);
$stmt->execute();
$profile    = $stmt->get_result()->fetch_assoc();
$stmt->close();

$isComplete = $profile !== null && (
    $role === 'employer'
        ? !empty($profile['company_name']) && !empty($profile['industry'])
        : !empty($profile['headline'])     && !empty($profile['skills'])
);

// ── Handle form save ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {

    if ($role === 'employer') {
        $companyName = trim($_POST['company_name'] ?? '');
        $industry    = trim($_POST['industry']     ?? '');
        $description = trim($_POST['description']  ?? '');
        $website     = trim($_POST['website']      ?? '');

        if ($companyName === '') $errors['company_name'] = 'Company name is required.';
        if ($industry    === '') $errors['industry']     = 'Industry is required.';
        if ($website !== '' && !filter_var($website, FILTER_VALIDATE_URL))
            $errors['website'] = 'Enter a valid URL (include https://).';

        if (empty($errors)) {
            // Optional logo re-upload
            if (isset($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE) {
                $uploadResult = handleFileUpload($_FILES['file'], 'employer');
                if (isset($uploadResult['error'])) {
                    $errors['file'] = $uploadResult['error'];
                } else {
                    $stmt = $db->prepare("UPDATE users SET file_path = ? WHERE id = ?");
                    $stmt->bind_param("si", $uploadResult['path'], $userId);
                    $stmt->execute(); $stmt->close();
                }
            }
        }

        if (empty($errors)) {
            $stmt = $db->prepare(
                "INSERT INTO employer_profiles (user_id, company_name, industry, description, website)
                 VALUES (?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE
                   company_name = VALUES(company_name),
                   industry     = VALUES(industry),
                   description  = VALUES(description),
                   website      = VALUES(website)"
            );
            $stmt->bind_param("issss", $userId, $companyName, $industry, $description, $website);
            $stmt->execute(); $stmt->close();
            header('Location: ../views/Profile.php?saved=1');
            exit;
        }

    } else { // seeker
        $headline        = trim($_POST['headline']          ?? '');
        $skills          = trim($_POST['skills']            ?? '');
        $yearsExperience = (int) ($_POST['years_experience'] ?? 0);

        if ($headline === '') $errors['headline'] = 'Headline is required.';
        if ($skills   === '') $errors['skills']   = 'Skills are required.';

        if (empty($errors)) {
            // Optional resume re-upload
            if (isset($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE) {
                $uploadResult = handleFileUpload($_FILES['file'], 'seeker');
                if (isset($uploadResult['error'])) {
                    $errors['file'] = $uploadResult['error'];
                } else {
                    $stmt = $db->prepare("UPDATE users SET file_path = ? WHERE id = ?");
                    $stmt->bind_param("si", $uploadResult['path'], $userId);
                    $stmt->execute(); $stmt->close();
                }
            }
        }

        if (empty($errors)) {
            $stmt = $db->prepare(
                "INSERT INTO seeker_profiles (user_id, headline, skills, years_experience)
                 VALUES (?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE
                   headline         = VALUES(headline),
                   skills           = VALUES(skills),
                   years_experience = VALUES(years_experience)"
            );
            $stmt->bind_param("issi", $userId, $headline, $skills, $yearsExperience);
            $stmt->execute(); $stmt->close();
            header('Location: ../views/Profile.php?saved=1');
            exit;
        }
    }

    // Re-fetch profile after failed save attempt
    if ($role === 'employer') {
        $stmt = $db->prepare("SELECT * FROM employer_profiles WHERE user_id = ? LIMIT 1");
    } else {
        $stmt = $db->prepare("SELECT * FROM seeker_profiles WHERE user_id = ? LIMIT 1");
    }
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $profile = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// ── Handle AJAX password change ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    header('Content-Type: application/json');

    $currentPass = $_POST['current_password'] ?? '';
    $newPass     = $_POST['new_password']      ?? '';
    $confirmPass = $_POST['confirm_password']  ?? '';

    if (!password_verify($currentPass, $user['password_hash'])) {
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
        exit;
    }
    if (strlen($newPass) < 8) {
        echo json_encode(['success' => false, 'message' => 'New password must be at least 8 characters.']);
        exit;
    }
    if ($newPass !== $confirmPass) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
        exit;
    }

    $hash = password_hash($newPass, PASSWORD_BCRYPT);
    $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
    $stmt->bind_param("si", $hash, $userId);
    $stmt->execute(); $stmt->close();

    echo json_encode(['success' => true, 'message' => 'Password updated successfully.']);
    exit;
}

// ── File upload helper ──
function handleFileUpload(array $file, string $role): array {
    $maxSize      = 2 * 1024 * 1024;
    $allowedMimes = $role === 'employer'
        ? ['image/jpeg', 'image/png', 'image/gif', 'image/webp']
        : ['application/pdf'];

    if ($file['error'] !== UPLOAD_ERR_OK)   return ['error' => 'Upload error.'];
    if ($file['size']  >  $maxSize)         return ['error' => 'File must be under 2 MB.'];

    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    if (!in_array($mimeType, $allowedMimes)) {
        return ['error' => $role === 'employer' ? 'Must be JPG/PNG/GIF/WEBP.' : 'Must be a PDF.'];
    }

    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('upload_', true) . '.' . $ext;
    $dest     = __DIR__ . '/../public/uploads/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) return ['error' => 'Could not save file.'];
    return ['path' => 'public/uploads/' . $filename];
}

include "../views/Profile.php";
?>
