<?php
session_start();
include_once "../models/db.php";

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';

    if ($email === '')    $errors['email']    = 'Email is required.';
    if ($password === '') $errors['password'] = 'Password is required.';

    if (empty($errors)) {
        $db   = (new db())->connection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();
        $stmt->close();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $errors['general'] = 'Invalid email or password.';
        } else {
            // Build session
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['role']    = $user['role'];

            // Role-based redirect
            switch ($user['role']) {
                case 'employer': header('Location: ../views/EmployerDashboard.php'); break;
                case 'admin':    header('Location: ../views/AdminPanel.php');        break;
                default:         header('Location: ../views/JobBoard.php');          break;
            }
            exit;
        }
    }
}

include "../views/Login.php";
?>
