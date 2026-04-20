<?php
session_start();
require_once __DIR__ . '/../config/db.php';
header('Content-Type: application/json');

if (!is_logged_in() || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $membership_id = (int)($_POST['membership_id'] ?? 0);
    $user_id = (int)($_POST['user_id'] ?? 0);
    $amount = (float)($_POST['amount'] ?? 0);
    $method = $_POST['method'] ?? 'cash';
    $discount_id = !empty($_POST['discount_id']) ? (int)$_POST['discount_id'] : null;
    $discount_applied = 0;

    try {
        $pdo->beginTransaction();

        // Apply discount if provided
        if ($discount_id) {
            $stmt = $pdo->prepare("SELECT * FROM discounts WHERE discount_id = ? AND status = 'active' AND valid_from <= CURDATE() AND valid_until >= CURDATE() AND used_count < max_uses");
            $stmt->execute([$discount_id]);
            $discount = $stmt->fetch();
            if ($discount) {
                $discount_applied = round($amount * ($discount['percentage'] / 100), 2);
                $amount -= $discount_applied;
                $pdo->prepare("UPDATE discounts SET used_count = used_count + 1 WHERE discount_id = ?")->execute([$discount_id]);
            }
        }

        // Generate receipt number
        $receipt_no = 'RCP-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        // Insert payment
        $stmt = $pdo->prepare("INSERT INTO payments (membership_id, user_id, amount, payment_date, method, status, receipt_no, processed_by, discount_applied) VALUES (?, ?, ?, CURDATE(), ?, 'paid', ?, ?, ?)");
        $stmt->execute([$membership_id, $user_id, $amount, $method, $receipt_no, $_SESSION['user_id'], $discount_applied]);

        // Activate membership
        $pdo->prepare("UPDATE member_memberships SET status = 'active' WHERE membership_id = ?")->execute([$membership_id]);

        // Activate user
        $pdo->prepare("UPDATE users SET status = 'active' WHERE user_id = ?")->execute([$user_id]);

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Payment processed successfully', 'receipt_no' => $receipt_no]);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Payment failed: ' . $e->getMessage()]);
    }
}
?>
