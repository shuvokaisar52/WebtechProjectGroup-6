<?php
// AJAX endpoint — returns JSON: {"exists": true/false}
header('Content-Type: application/json');
include_once "../models/db.php";

$email = trim($_GET['email'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['exists' => false]);
    exit;
}

$db   = (new db())->connection();
$stmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

echo json_encode(['exists' => $stmt->num_rows > 0]);
$stmt->close();
?>
