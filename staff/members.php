<?php
$page_title = 'Members';
require_once __DIR__ . '/../config/db.php';
check_role('staff');

$members = $pdo->query("
    SELECT u.*, mm.status as mem_status, mp.plan_name, s.service_name
    FROM users u
    LEFT JOIN member_memberships mm ON u.user_id = mm.user_id
    LEFT JOIN membership_plans mp ON mm.plan_id = mp.plan_id
    LEFT JOIN services s ON mm.service_id = s.service_id
    WHERE u.role = 'customer'
    ORDER BY u.full_name
")->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<h4 class="mb-4"><i class="fas fa-users me-2"></i>Members</h4>
<div class="card-custom"><div class="card-body">
    <table class="table table-custom datatable">
        <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Plan</th><th>Service</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($members as $i => $m): ?>
        <tr><td><?= $i+1 ?></td><td><?= htmlspecialchars($m['full_name']) ?></td><td><?= htmlspecialchars($m['email']) ?></td><td><?= htmlspecialchars($m['phone'] ?? '-') ?></td><td><?= htmlspecialchars($m['plan_name'] ?? 'N/A') ?></td><td><?= htmlspecialchars($m['service_name'] ?? 'N/A') ?></td><td><span class="badge-status badge-<?= $m['status'] ?>"><?= $m['status'] ?></span></td></tr>
        <?php endforeach; ?>
        </tbody></table></div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
