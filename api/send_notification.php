<?php
session_start();
require_once __DIR__ . '/../config/db.php';
header('Content-Type: application/json');

if (!is_logged_in() || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = (int)($_POST['user_id'] ?? 0);
    $message = trim($_POST['message'] ?? '');
    $type = $_POST['type'] ?? 'alert';

    if (empty($user_id) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'User ID and message are required']);
        exit();
    }

    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, type, sent_by) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $message, $type, $_SESSION['user_id']]);

    echo json_encode(['success' => true, 'message' => 'Notification sent successfully']);
}
?>
