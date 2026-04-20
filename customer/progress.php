<?php
$page_title = 'My Progress';
require_once __DIR__ . '/../config/db.php';
check_role('customer');
$uid = $_SESSION['user_id'];

$progress = $pdo->prepare("SELECT * FROM customer_progress WHERE user_id = ? ORDER BY recorded_date DESC");
$progress->execute([$uid]); $progress = $progress->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<h4 class="mb-4"><i class="fas fa-chart-line me-2"></i>My Progress</h4>
<?php if (empty($progress)): ?>
    <div class="card-custom"><div class="card-body"><div class="empty-state"><i class="fas fa-chart-area"></i><p>No progress records yet. Ask your trainer to record your progress!</p></div></div></div>
<?php else: ?>
<div class="card-custom"><div class="card-body">
    <table class="table table-custom">
        <thead><tr><th>Date</th><th>Initial Weight</th><th>Current Weight</th><th>Body (Before)</th><th>Body (After)</th><th>Progress</th><th>Notes</th></tr></thead>
        <tbody>
        <?php foreach ($progress as $p): ?>
        <tr><td><?= $p['recorded_date'] ?></td><td><?= $p['initial_weight'] ?> kg</td><td><?= $p['current_weight'] ?> kg</td><td><?= htmlspecialchars($p['initial_body_type'] ?? '-') ?></td><td><?= htmlspecialchars($p['current_body_type'] ?? '-') ?></td>
        <td><span class="badge bg-<?= $p['progress_pct'] > 0 ? 'success' : 'secondary' ?>"><?= number_format($p['progress_pct'],1) ?>%</span></td><td><?= htmlspecialchars($p['notes'] ?? '-') ?></td></tr>
        <?php endforeach; ?></tbody></table></div></div>
<?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
