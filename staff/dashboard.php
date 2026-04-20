<?php
$page_title = 'Dashboard';
require_once __DIR__ . '/../config/db.php';
check_role('staff');

$total_members = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer' AND status = 'active'")->fetchColumn();
$today_attendance = $pdo->query("SELECT COUNT(*) FROM attendance WHERE date = CURDATE()")->fetchColumn();
$pending_payments = $pdo->query("SELECT COUNT(*) FROM payments WHERE status = 'pending'")->fetchColumn();
$total_equipment = $pdo->query("SELECT COUNT(*) FROM equipment")->fetchColumn();

include __DIR__ . '/../includes/header.php';
?>
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3"><div class="stat-card primary animate-in delay-1"><div class="stat-label">Active Members</div><div class="stat-value"><?= $total_members ?></div><i class="fas fa-users stat-icon"></i></div></div>
    <div class="col-xl-3 col-md-6 mb-3"><div class="stat-card success animate-in delay-2"><div class="stat-label">Today's Attendance</div><div class="stat-value"><?= $today_attendance ?></div><i class="fas fa-clipboard-check stat-icon"></i></div></div>
    <div class="col-xl-3 col-md-6 mb-3"><div class="stat-card warning animate-in delay-3"><div class="stat-label">Pending Payments</div><div class="stat-value"><?= $pending_payments ?></div><i class="fas fa-clock stat-icon"></i></div></div>
    <div class="col-xl-3 col-md-6 mb-3"><div class="stat-card info animate-in delay-4"><div class="stat-label">Equipment</div><div class="stat-value"><?= $total_equipment ?></div><i class="fas fa-cogs stat-icon"></i></div></div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <div class="card-custom">
            <div class="card-header"><h5><i class="fas fa-bolt me-2"></i>Quick Actions</h5></div>
            <div class="card-body">
                <a href="attendance.php" class="btn btn-accent mb-2 me-2"><i class="fas fa-clipboard-check me-1"></i> Mark Attendance</a>
                <a href="payments.php" class="btn btn-outline-primary mb-2"><i class="fas fa-credit-card me-1"></i> Process Payment</a>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card-custom">
            <div class="card-header"><h5><i class="fas fa-clock me-2"></i>Recent Check-ins Today</h5></div>
            <div class="card-body">
                <?php
                $recent = $pdo->query("SELECT a.check_in, u.full_name FROM attendance a JOIN users u ON a.user_id = u.user_id WHERE a.date = CURDATE() ORDER BY a.check_in DESC LIMIT 5")->fetchAll();
                if (empty($recent)): ?>
                    <div class="empty-state"><p>No check-ins yet today</p></div>
                <?php else: ?>
                    <table class="table table-sm"><tbody>
                    <?php foreach ($recent as $r): ?>
                        <tr><td><?= htmlspecialchars($r['full_name']) ?></td><td><?= date('h:i A', strtotime($r['check_in'])) ?></td></tr>
                    <?php endforeach; ?></tbody></table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
