<?php
session_start();
require_once __DIR__ . '/../config/db.php';
header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$type = $_GET['type'] ?? '';

switch ($type) {
    case 'monthly_revenue':
        // Monthly Revenue vs Expenses for current year
        $year = date('Y');
        $stmt = $pdo->prepare("
            SELECT MONTH(payment_date) as month, SUM(amount) as revenue
            FROM payments WHERE status = 'paid' AND YEAR(payment_date) = ?
            GROUP BY MONTH(payment_date) ORDER BY month
        ");
        $stmt->execute([$year]);
        $revenue = array_fill(1, 12, 0);
        foreach ($stmt->fetchAll() as $row) {
            $revenue[(int)$row['month']] = (float)$row['revenue'];
        }

        // Expenses = equipment cost + maintenance cost
        $stmt2 = $pdo->prepare("
            SELECT MONTH(purchase_date) as month, SUM(total_cost) as cost
            FROM equipment WHERE YEAR(purchase_date) = ?
            GROUP BY MONTH(purchase_date)
        ");
        $stmt2->execute([$year]);
        $expenses = array_fill(1, 12, 0);
        foreach ($stmt2->fetchAll() as $row) {
            $expenses[(int)$row['month']] += (float)$row['cost'];
        }

        $stmt3 = $pdo->prepare("
            SELECT MONTH(maintenance_date) as month, SUM(cost) as cost
            FROM equipment_maintenance WHERE YEAR(maintenance_date) = ?
            GROUP BY MONTH(maintenance_date)
        ");
        $stmt3->execute([$year]);
        foreach ($stmt3->fetchAll() as $row) {
            $expenses[(int)$row['month']] += (float)$row['cost'];
        }

        echo json_encode([
            'labels' => ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
            'revenue' => array_values($revenue),
            'expenses' => array_values($expenses)
        ]);
        break;

    case 'members_per_service':
        $stmt = $pdo->query("
            SELECT s.service_name, COUNT(mm.membership_id) as count
            FROM services s
            LEFT JOIN member_memberships mm ON s.service_id = mm.service_id AND mm.status = 'active'
            GROUP BY s.service_id, s.service_name
        ");
        $data = $stmt->fetchAll();
        echo json_encode([
            'labels' => array_column($data, 'service_name'),
            'data' => array_map('intval', array_column($data, 'count'))
        ]);
        break;

    case 'attendance_trend':
        $stmt = $pdo->query("
            SELECT date, COUNT(*) as count
            FROM attendance
            WHERE date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY date ORDER BY date
        ");
        $data = $stmt->fetchAll();
        echo json_encode([
            'labels' => array_column($data, 'date'),
            'data' => array_map('intval', array_column($data, 'count'))
        ]);
        break;

    default:
        echo json_encode(['error' => 'Invalid chart type']);
}
?>
