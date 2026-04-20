<?php
$page_title = 'Dashboard';
require_once __DIR__ . '/../config/db.php';
check_role('admin');

// Stats
$total_members = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
$active_members = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer' AND status = 'active'")->fetchColumn();
$total_earnings = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'paid'")->fetchColumn();
$total_equipment_cost = $pdo->query("SELECT COALESCE(SUM(total_cost), 0) FROM equipment")->fetchColumn();
$total_maintenance_cost = $pdo->query("SELECT COALESCE(SUM(cost), 0) FROM equipment_maintenance")->fetchColumn();
$total_expenses = $total_equipment_cost + $total_maintenance_cost;

// Expiring this week
$expiring = $pdo->query("
    SELECT u.full_name, u.email, mm.end_date, s.service_name, mp.plan_name
    FROM member_memberships mm
    JOIN users u ON mm.user_id = u.user_id
    JOIN services s ON mm.service_id = s.service_id
    JOIN membership_plans mp ON mm.plan_id = mp.plan_id
    WHERE mm.status = 'active' AND mm.end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    ORDER BY mm.end_date
")->fetchAll();

// Pending approvals
$pending = $pdo->query("SELECT * FROM users WHERE status = 'pending' AND role = 'customer' ORDER BY created_at DESC LIMIT 10")->fetchAll();

// Recent registrations
$recent = $pdo->query("SELECT * FROM users WHERE role = 'customer' ORDER BY created_at DESC LIMIT 5")->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<!-- Stats Row -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card primary animate-in delay-1">
            <div class="stat-label">Total Members</div>
            <div class="stat-value"><?= $total_members ?></div>
            <i class="fas fa-users stat-icon"></i>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card success animate-in delay-2">
            <div class="stat-label">Active Members</div>
            <div class="stat-value"><?= $active_members ?></div>
            <i class="fas fa-user-check stat-icon"></i>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card info animate-in delay-3">
            <div class="stat-label">Total Earnings</div>
            <div class="stat-value">$<?= number_format($total_earnings, 2) ?></div>
            <i class="fas fa-dollar-sign stat-icon"></i>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card danger animate-in delay-4">
            <div class="stat-label">Total Expenses</div>
            <div class="stat-value">$<?= number_format($total_expenses, 2) ?></div>
            <i class="fas fa-receipt stat-icon"></i>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <div class="col-lg-8 mb-3">
        <div class="card-custom">
            <div class="card-header"><h5><i class="fas fa-chart-bar me-2"></i>Monthly Revenue vs Expenses</h5></div>
            <div class="card-body"><canvas id="revenueChart"></canvas></div>
        </div>
    </div>
    <div class="col-lg-4 mb-3">
        <div class="card-custom">
            <div class="card-header"><h5><i class="fas fa-chart-pie me-2"></i>Members per Service</h5></div>
            <div class="card-body"><canvas id="serviceChart"></canvas></div>
        </div>
    </div>
</div>

<!-- Attendance Chart + Expiring -->
<div class="row mb-4">
    <div class="col-lg-7 mb-3">
        <div class="card-custom">
            <div class="card-header"><h5><i class="fas fa-chart-line me-2"></i>Attendance Trend (Last 30 Days)</h5></div>
            <div class="card-body"><canvas id="attendanceChart"></canvas></div>
        </div>
    </div>
    <div class="col-lg-5 mb-3">
        <div class="card-custom">
            <div class="card-header"><h5><i class="fas fa-exclamation-triangle me-2 text-warning"></i>Expiring This Week</h5></div>
            <div class="card-body" style="max-height: 320px; overflow-y: auto;">
                <?php if (empty($expiring)): ?>
                    <div class="empty-state"><i class="fas fa-check-circle text-success"></i><p>No expiring memberships</p></div>
                <?php else: ?>
                    <table class="table table-sm">
                        <thead><tr><th>Name</th><th>Plan</th><th>Expires</th></tr></thead>
                        <tbody>
                        <?php foreach ($expiring as $e): ?>
                            <tr>
                                <td><?= htmlspecialchars($e['full_name']) ?></td>
                                <td><?= htmlspecialchars($e['plan_name']) ?></td>
                                <td><span class="badge bg-warning text-dark"><?= $e['end_date'] ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Pending Approvals + Recent Registrations -->
<div class="row mb-4">
    <div class="col-lg-6 mb-3">
        <div class="card-custom">
            <div class="card-header"><h5><i class="fas fa-user-clock me-2"></i>Pending Approvals</h5></div>
            <div class="card-body">
                <?php if (empty($pending)): ?>
                    <div class="empty-state"><i class="fas fa-check-circle text-success"></i><p>No pending approvals</p></div>
                <?php else: ?>
                    <table class="table table-sm">
                        <thead><tr><th>Name</th><th>Email</th><th>Action</th></tr></thead>
                        <tbody>
                        <?php foreach ($pending as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['full_name']) ?></td>
                                <td><?= htmlspecialchars($p['email']) ?></td>
                                <td>
                                    <a href="members.php?approve=<?= $p['user_id'] ?>" class="btn btn-success btn-sm"><i class="fas fa-check"></i></a>
                                    <a href="members.php?reject=<?= $p['user_id'] ?>" class="btn btn-danger btn-sm"><i class="fas fa-times"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-3">
        <div class="card-custom">
            <div class="card-header"><h5><i class="fas fa-user-plus me-2"></i>Recent Registrations</h5></div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead><tr><th>Name</th><th>Email</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                    <?php foreach ($recent as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['full_name']) ?></td>
                            <td><?= htmlspecialchars($r['email']) ?></td>
                            <td><span class="badge-status badge-<?= $r['status'] ?>"><?= $r['status'] ?></span></td>
                            <td><?= date('M d', strtotime($r['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Load charts via AJAX
document.addEventListener('DOMContentLoaded', function() {
    // Revenue vs Expenses Bar Chart
    fetch('/gym_management/api/get_chart_data.php?type=monthly_revenue')
        .then(r => r.json()).then(data => {
            new Chart(document.getElementById('revenueChart'), {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [
                        { label: 'Revenue', data: data.revenue, backgroundColor: 'rgba(78, 115, 223, 0.8)', borderRadius: 6 },
                        { label: 'Expenses', data: data.expenses, backgroundColor: 'rgba(233, 69, 96, 0.8)', borderRadius: 6 }
                    ]
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true } } }
            });
        });

    // Members per Service Doughnut
    fetch('/gym_management/api/get_chart_data.php?type=members_per_service')
        .then(r => r.json()).then(data => {
            new Chart(document.getElementById('serviceChart'), {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{ data: data.data, backgroundColor: ['#4e73df','#1cc88a','#36b9cc','#f6c23e','#e94560','#858796'] }]
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
            });
        });

    // Attendance Trend Line Chart
    fetch('/gym_management/api/get_chart_data.php?type=attendance_trend')
        .then(r => r.json()).then(data => {
            new Chart(document.getElementById('attendanceChart'), {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{ label: 'Daily Attendance', data: data.data, borderColor: '#1cc88a', backgroundColor: 'rgba(28, 200, 138, 0.1)', fill: true, tension: 0.4 }]
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true } } }
            });
        });
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
