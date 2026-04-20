<?php
$page_title = 'Customer Progress';
require_once __DIR__ . '/../config/db.php';
check_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_progress'])) {
    $pdo->prepare("INSERT INTO customer_progress (user_id, recorded_date, initial_weight, current_weight, initial_body_type, current_body_type, notes) VALUES (?,?,?,?,?,?,?)")
        ->execute([(int)$_POST['user_id'], $_POST['recorded_date'], (float)$_POST['initial_weight'], (float)$_POST['current_weight'], trim($_POST['initial_body_type']), trim($_POST['current_body_type']), trim($_POST['notes'])]);
    set_flash('success', 'Progress recorded.'); header("Location: progress.php"); exit();
}
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM customer_progress WHERE progress_id = ?")->execute([(int)$_GET['delete']]);
    set_flash('success', 'Deleted.'); header("Location: progress.php"); exit();
}

$progress = $pdo->query("SELECT cp.*, u.full_name FROM customer_progress cp JOIN users u ON cp.user_id = u.user_id ORDER BY cp.recorded_date DESC")->fetchAll();
$members = $pdo->query("SELECT user_id, full_name FROM users WHERE role = 'customer' AND status = 'active' ORDER BY full_name")->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="page-header"><h4><i class="fas fa-chart-line me-2"></i>Customer Progress</h4>
    <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-1"></i> Record</button></div>
<div class="card-custom"><div class="card-body">
    <table class="table table-custom datatable">
        <thead><tr><th>#</th><th>Member</th><th>Date</th><th>Initial Wt</th><th>Current Wt</th><th>Body (Before)</th><th>Body (After)</th><th>Progress</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($progress as $i => $p): ?>
        <tr><td><?= $i+1 ?></td><td><?= htmlspecialchars($p['full_name']) ?></td><td><?= $p['recorded_date'] ?></td><td><?= $p['initial_weight'] ?> kg</td><td><?= $p['current_weight'] ?> kg</td><td><?= htmlspecialchars($p['initial_body_type'] ?? '-') ?></td><td><?= htmlspecialchars($p['current_body_type'] ?? '-') ?></td>
        <td><span class="badge bg-<?= $p['progress_pct'] > 0 ? 'success' : 'secondary' ?>"><?= number_format($p['progress_pct'], 1) ?>%</span></td>
        <td><button class="btn btn-danger btn-sm" onclick="confirmDelete('?delete=<?= $p['progress_id'] ?>','this record')"><i class="fas fa-trash"></i></button></td></tr>
        <?php endforeach; ?></tbody></table></div></div>

<div class="modal fade" id="addModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>"><input type="hidden" name="add_progress" value="1">
    <div class="modal-header"><h5 class="modal-title">Record Progress</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <div class="mb-3"><label class="form-label">Member *</label><select name="user_id" class="form-select" required><option value="">Select</option><?php foreach ($members as $m): ?><option value="<?= $m['user_id'] ?>"><?= htmlspecialchars($m['full_name']) ?></option><?php endforeach; ?></select></div>
        <div class="mb-3"><label class="form-label">Date *</label><input type="date" name="recorded_date" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
        <div class="row"><div class="col-6 mb-3"><label class="form-label">Initial Weight (kg)</label><input type="number" name="initial_weight" class="form-control" step="0.01" required></div>
        <div class="col-6 mb-3"><label class="form-label">Current Weight (kg)</label><input type="number" name="current_weight" class="form-control" step="0.01" required></div></div>
        <div class="row"><div class="col-6 mb-3"><label class="form-label">Initial Body Type</label><input type="text" name="initial_body_type" class="form-control" placeholder="e.g. Overweight"></div>
        <div class="col-6 mb-3"><label class="form-label">Current Body Type</label><input type="text" name="current_body_type" class="form-control" placeholder="e.g. Athletic"></div></div>
        <div class="mb-3"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-accent">Record</button></div>
</form></div></div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
