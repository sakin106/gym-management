<?php
session_start();
require_once __DIR__ . '/../config/db.php';
header('Content-Type: application/json');

if (!is_logged_in() || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $user_id = (int)($_POST['user_id'] ?? 0);

    if ($action === 'checkin') {
        // Check if already checked in today without checkout
        $stmt = $pdo->prepare("SELECT attendance_id FROM attendance WHERE user_id = ? AND date = CURDATE() AND check_out IS NULL");
        $stmt->execute([$user_id]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Member already checked in today']);
            exit();
        }
        $stmt = $pdo->prepare("INSERT INTO attendance (user_id, check_in, date, marked_by) VALUES (?, NOW(), CURDATE(), ?)");
        $stmt->execute([$user_id, $_SESSION['user_id']]);
        echo json_encode(['success' => true, 'message' => 'Check-in recorded successfully']);

    } elseif ($action === 'checkout') {
        $stmt = $pdo->prepare("SELECT attendance_id, check_in FROM attendance WHERE user_id = ? AND date = CURDATE() AND check_out IS NULL ORDER BY attendance_id DESC LIMIT 1");
        $stmt->execute([$user_id]);
        $record = $stmt->fetch();
        if (!$record) {
            echo json_encode(['success' => false, 'message' => 'No active check-in found for today']);
            exit();
        }
        $check_in = new DateTime($record['check_in']);
        $check_out = new DateTime();
        $hours = round(($check_out->getTimestamp() - $check_in->getTimestamp()) / 3600, 2);

        $stmt = $pdo->prepare("UPDATE attendance SET check_out = NOW(), working_hours = ? WHERE attendance_id = ?");
        $stmt->execute([$hours, $record['attendance_id']]);
        echo json_encode(['success' => true, 'message' => "Check-out recorded. Hours: $hours"]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
}
?>
