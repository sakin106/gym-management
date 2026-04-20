<?php
$page_title = 'Reports';
require_once __DIR__ . '/../config/db.php';
check_role('admin');

// Members Report
$members_report = $pdo->query("
    SELECT u.full_name, u.email, u.phone, mm.membership_id, s.service_name, mp.plan_name,
           s.monthly_charge, mp.price as plan_price, mm.total_amount, mm.start_date, mm.end_date,
           mm.status as mem_status, p.status as pay_status, p.receipt_no
    FROM users u
    JOIN member_memberships mm ON u.user_id = mm.user_id
    JOIN services s ON mm.service_id = s.service_id
    JOIN membership_plans mp ON mm.plan_id = mp.plan_id
    LEFT JOIN payments p ON mm.membership_id = p.membership_id
    WHERE u.role = 'customer'
    ORDER BY u.full_name
")->fetchAll();

// Progress Report
$progress_report = $pdo->query("
    SELECT u.full_name, cp.recorded_date, cp.initial_weight, cp.current_weight,
           cp.initial_body_type, cp.current_body_type, cp.progress_pct
    FROM customer_progress cp
    JOIN users u ON cp.user_id = u.user_id
    ORDER BY cp.recorded_date DESC
")->fetchAll();

// Earnings & Expenses
$total_earnings = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'paid'")->fetchColumn();
$total_equip = $pdo->query("SELECT COALESCE(SUM(total_cost), 0) FROM equipment")->fetchColumn();
$total_maint = $pdo->query("SELECT COALESCE(SUM(cost), 0) FROM equipment_maintenance")->fetchColumn();

include __DIR__ . '/../includes/header.php';
?>

<h4 class="mb-4"><i class="fas fa-chart-bar me-2"></i>Reports & Analytics</h4>

<!-- Earnings vs Expenses Summary -->
<div class="row mb-4">
    <div class="col-md-4"><div class="stat-card success"><div class="stat-label">Total Earnings</div><div class="stat-value">$<?= number_format($total_earnings, 2) ?></div><i class="fas fa-dollar-sign stat-icon"></i></div></div>
    <div class="col-md-4"><div class="stat-card danger"><div class="stat-label">Total Expenses</div><div class="stat-value">$<?= number_format($total_equip + $total_maint, 2) ?></div><i class="fas fa-receipt stat-icon"></i></div></div>
    <div class="col-md-4"><div class="stat-card info"><div class="stat-label">Net Profit</div><div class="stat-value">$<?= number_format($total_earnings - $total_equip - $total_maint, 2) ?></div><i class="fas fa-chart-line stat-icon"></i></div></div>
</div>

<!-- Earnings vs Expenses Chart -->
<div class="card-custom mb-4">
    <div class="card-header"><h5>Monthly Revenue vs Expenses</h5></div>
    <div class="card-body"><canvas id="reportChart" style="max-height: 300px;"></canvas></div>
</div>

<!-- Members Report -->
<div class="card-custom mb-4">
    <div class="card-header">
        <h5>Members Report</h5>
        <button onclick="window.print()" class="btn btn-sm btn-outline-primary"><i class="fas fa-print me-1"></i> Print</button>
    </div>
    <div class="card-body">
        <table class="table table-custom datatable" id="membersReport">
            <thead><tr><th>Name</th><th>Email</th><th>Service</th><th>Plan</th><th>Charge/Mo</th><th>Total</th><th>Status</th><th>Receipt</th></tr></thead>
            <tbody>
            <?php foreach ($members_report as $m): ?>
                <tr>
                    <td><?= htmlspecialchars($m['full_name']) ?></td>
                    <td><?= htmlspecialchars($m['email']) ?></td>
                    <td><?= htmlspecialchars($m['service_name']) ?></td>
                    <td><?= htmlspecialchars($m['plan_name']) ?></td>
                    <td>$<?= number_format($m['monthly_charge'], 2) ?></td>
                    <td>$<?= number_format($m['total_amount'], 2) ?></td>
                    <td><span class="badge-status badge-<?= $m['mem_status'] ?>"><?= $m['mem_status'] ?></span></td>
                    <td><?= $m['receipt_no'] ?? '-' ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Progress Report -->
<div class="card-custom mb-4">
    <div class="card-header"><h5>Customer Progress Report</h5></div>
    <div class="card-body">
        <table class="table table-custom datatable">
            <thead><tr><th>Member</th><th>Date</th><th>Initial Weight</th><th>Current Weight</th><th>Body Type (Before)</th><th>Body Type (After)</th><th>Progress %</th></tr></thead>
            <tbody>
            <?php foreach ($progress_report as $pr): ?>
                <tr>
                    <td><?= htmlspecialchars($pr['full_name']) ?></td>
                    <td><?= $pr['recorded_date'] ?></td>
                    <td><?= $pr['initial_weight'] ?> kg</td>
                    <td><?= $pr['current_weight'] ?> kg</td>
                    <td><?= htmlspecialchars($pr['initial_body_type'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($pr['current_body_type'] ?? '-') ?></td>
                    <td><span class="badge bg-<?= $pr['progress_pct'] > 0 ? 'success' : 'secondary' ?>"><?= number_format($pr['progress_pct'], 1) ?>%</span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
fetch('/gym_management/api/get_chart_data.php?type=monthly_revenue')
    .then(r => r.json()).then(data => {
        new Chart(document.getElementById('reportChart'), {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [
                    { label: 'Revenue', data: data.revenue, backgroundColor: 'rgba(28, 200, 138, 0.8)', borderRadius: 6 },
                    { label: 'Expenses', data: data.expenses, backgroundColor: 'rgba(233, 69, 96, 0.8)', borderRadius: 6 }
                ]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
    });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
