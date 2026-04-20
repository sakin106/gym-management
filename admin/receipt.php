<?php
$page_title = 'Receipt';
require_once __DIR__ . '/../config/db.php';
check_role('admin');

$id = (int)($_GET['id'] ?? 0);
$payment = $pdo->prepare("
    SELECT p.*, u.full_name, u.email, u.phone, proc.full_name as processed_name,
           mm.start_date, mm.end_date, mp.plan_name, mp.duration_months, s.service_name, s.monthly_charge
    FROM payments p
    JOIN users u ON p.user_id = u.user_id
    LEFT JOIN users proc ON p.processed_by = proc.user_id
    LEFT JOIN member_memberships mm ON p.membership_id = mm.membership_id
    LEFT JOIN membership_plans mp ON mm.plan_id = mp.plan_id
    LEFT JOIN services s ON mm.service_id = s.service_id
    WHERE p.payment_id = ?
");
$payment->execute([$id]);
$p = $payment->fetch();

if (!$p) { echo "Receipt not found."; exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt — <?= $p['receipt_no'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f4f6f9; }
        .receipt { max-width: 600px; margin: 30px auto; background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .receipt-header { text-align: center; border-bottom: 2px solid #e94560; padding-bottom: 20px; margin-bottom: 20px; }
        .receipt-header h2 { color: #e94560; margin: 0; }
        .receipt-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f0f0f0; }
        .receipt-row .label { color: #7f8c8d; font-weight: 600; font-size: 0.85rem; }
        .receipt-row .value { font-weight: 600; color: #2c3e50; }
        .total-row { font-size: 1.2rem; border-top: 2px solid #2c3e50; padding-top: 12px; margin-top: 12px; }
        .no-print { text-align: center; margin-top: 20px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
<div class="receipt">
    <div class="receipt-header">
        <h2>💪 GymPro</h2>
        <p style="color: #7f8c8d; margin: 4px 0;">Payment Receipt</p>
        <p style="font-size: 0.8rem; color: #aaa;">Receipt No: <strong><?= $p['receipt_no'] ?></strong></p>
    </div>

    <div class="receipt-row"><span class="label">Member Name</span><span class="value"><?= htmlspecialchars($p['full_name']) ?></span></div>
    <div class="receipt-row"><span class="label">Email</span><span class="value"><?= htmlspecialchars($p['email']) ?></span></div>
    <div class="receipt-row"><span class="label">Service</span><span class="value"><?= htmlspecialchars($p['service_name'] ?? 'N/A') ?></span></div>
    <div class="receipt-row"><span class="label">Plan</span><span class="value"><?= htmlspecialchars($p['plan_name'] ?? 'N/A') ?> (<?= $p['duration_months'] ?? '-' ?> months)</span></div>
    <div class="receipt-row"><span class="label">Charge/Month</span><span class="value">$<?= number_format($p['monthly_charge'] ?? 0, 2) ?></span></div>
    <div class="receipt-row"><span class="label">Period</span><span class="value"><?= $p['start_date'] ?? '-' ?> to <?= $p['end_date'] ?? '-' ?></span></div>
    <div class="receipt-row"><span class="label">Payment Method</span><span class="value"><?= ucfirst($p['method']) ?></span></div>
    <div class="receipt-row"><span class="label">Payment Date</span><span class="value"><?= $p['payment_date'] ?></span></div>
    <div class="receipt-row"><span class="label">Discount</span><span class="value">-$<?= number_format($p['discount_applied'], 2) ?></span></div>
    <div class="receipt-row"><span class="label">Processed By</span><span class="value"><?= htmlspecialchars($p['processed_name'] ?? 'System') ?></span></div>
    <div class="receipt-row total-row"><span class="label">Total Amount Paid</span><span class="value" style="color: #1cc88a;">$<?= number_format($p['amount'], 2) ?></span></div>

    <div class="no-print">
        <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print me-1"></i> Print Receipt</button>
        <a href="payments.php" class="btn btn-secondary">Back</a>
    </div>
</div>
</body>
</html>
