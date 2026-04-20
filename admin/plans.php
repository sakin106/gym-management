<?php
$page_title = 'Membership Plans';
require_once __DIR__ . '/../config/db.php';
check_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_plan'])) {
    $pdo->prepare("INSERT INTO membership_plans (plan_name, duration_months, price, description, max_freeze_days, status) VALUES (?,?,?,?,?,?)")
        ->execute([trim($_POST['plan_name']), (int)$_POST['duration_months'], (float)$_POST['price'], trim($_POST['description']), (int)$_POST['max_freeze_days'], $_POST['status']]);
    set_flash('success', 'Plan added.'); header("Location: plans.php"); exit();
}
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM membership_plans WHERE plan_id = ?")->execute([(int)$_GET['delete']]);
    set_flash('success', 'Deleted.'); header("Location: plans.php"); exit();
}
$plans = $pdo->query("SELECT * FROM membership_plans ORDER BY created_at DESC")->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="page-header"><h4><i class="fas fa-clipboard-list me-2"></i>Plans</h4>
    <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-1"></i> Add Plan</button></div>
<div class="card-custom"><div class="card-body">
    <table class="table table-custom datatable">
        <thead><tr><th>#</th><th>Plan</th><th>Duration</th><th>Price</th><th>Max Freeze</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($plans as $i => $p): ?>
        <tr><td><?= $i+1 ?></td><td><?= htmlspecialchars($p['plan_name']) ?></td><td><?= $p['duration_months'] ?> months</td><td>$<?= number_format($p['price'],2) ?></td><td><?= $p['max_freeze_days'] ?> days</td><td><span class="badge-status badge-<?= $p['status'] ?>"><?= $p['status'] ?></span></td>
        <td><button class="btn btn-danger btn-sm" onclick="confirmDelete('?delete=<?= $p['plan_id'] ?>','<?= htmlspecialchars($p['plan_name']) ?>')"><i class="fas fa-trash"></i></button></td></tr>
        <?php endforeach; ?></tbody></table></div></div>

<div class="modal fade" id="addModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>"><input type="hidden" name="add_plan" value="1">
    <div class="modal-header"><h5 class="modal-title">Add Plan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <div class="mb-3"><label class="form-label">Plan Name *</label><input type="text" name="plan_name" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Duration (months) *</label><input type="number" name="duration_months" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Price ($) *</label><input type="number" name="price" class="form-control" step="0.01" required></div>
        <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
        <div class="mb-3"><label class="form-label">Max Freeze Days</label><input type="number" name="max_freeze_days" class="form-control" value="30"></div>
        <div class="mb-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-accent">Add</button></div>
</form></div></div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
