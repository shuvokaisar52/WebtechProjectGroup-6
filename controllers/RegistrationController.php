<?php
session_start();
include_once "../models/db.php";

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';
    $role     = $_POST['role']          ?? '';

    // ── Validation ──
    if ($name === '')
        $errors['name'] = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors['email'] = 'Enter a valid email.';
    if (strlen($password) < 8)
        $errors['password'] = 'Password must be at least 8 characters.';
    if (!in_array($role, ['employer', 'seeker']))
        $errors['role'] = 'Please select a role.';

    // ── Check duplicate email ──
    if (empty($errors['email'])) {
        $db   = (new db())->connection();
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0)
            $errors['email'] = 'Email already registered.';
        $stmt->close();
    }

    // ── File Upload ──
    $filePath = null;
    if (empty($errors) && isset($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file         = $_FILES['file'];
        $maxSize      = 2 * 1024 * 1024; // 2MB
        $allowedMimes = $role === 'employer'
            ? ['image/jpeg', 'image/png', 'image/gif', 'image/webp']
            : ['application/pdf'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors['file'] = 'File upload failed. Try again.';
        } elseif ($file['size'] > $maxSize) {
            $errors['file'] = 'File must be under 2 MB.';
        } else {
            // Server-side MIME check
            $finfo    = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file['tmp_name']);
            if (!in_array($mimeType, $allowedMimes)) {
                $expected       = $role === 'employer' ? 'JPG/PNG/GIF/WEBP image' : 'PDF';
                $errors['file'] = "File must be a $expected.";
            } else {
                $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = uniqid('upload_', true) . '.' . $ext;
                $dest     = __DIR__ . '/../public/uploads/' . $filename;
                if (!move_uploaded_file($file['tmp_name'], $dest)) {
                    $errors['file'] = 'Could not save file. Check folder permissions.';
                } else {
                    $filePath = 'public/uploads/' . $filename;
                }
            }
        }
    }

    // ── Insert user ──
    if (empty($errors)) {
        $db   = (new db())->connection();
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $db->prepare(
            "INSERT INTO users (name, email, password_hash, role, file_path, created_at)
             VALUES (?, ?, ?, ?, ?, NOW())"
        );
        $stmt->bind_param("sssss", $name, $email, $hash, $role, $filePath);

        if ($stmt->execute()) {
            header('Location: ../views/Login.php?registered=1');
            exit;
        } else {
            $errors['general'] = 'Registration failed. Please try again.';
        }
        $stmt->close();
    }
}

// Show the registration view with any errors
include "../views/Registration.php";
?>
