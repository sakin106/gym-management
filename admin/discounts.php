<?php
$page_title = 'Discounts';
require_once __DIR__ . '/../config/db.php';
check_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_discount'])) {
    $pdo->prepare("INSERT INTO discounts (code, percentage, valid_from, valid_until, max_uses, status) VALUES (?,?,?,?,?,?)")
        ->execute([strtoupper(trim($_POST['code'])), (float)$_POST['percentage'], $_POST['valid_from'], $_POST['valid_until'], (int)$_POST['max_uses'], 'active']);
    set_flash('success', 'Discount added.'); header("Location: discounts.php"); exit();
}
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM discounts WHERE discount_id = ?")->execute([(int)$_GET['delete']]);
    set_flash('success', 'Deleted.'); header("Location: discounts.php"); exit();
}
$discounts = $pdo->query("SELECT * FROM discounts ORDER BY valid_until DESC")->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="page-header"><h4><i class="fas fa-tags me-2"></i>Discounts</h4>
    <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-1"></i> Add Discount</button></div>
<div class="card-custom"><div class="card-body">
    <table class="table table-custom datatable">
        <thead><tr><th>#</th><th>Code</th><th>Percentage</th><th>Valid From</th><th>Valid Until</th><th>Uses</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($discounts as $i => $d): ?>
        <tr><td><?= $i+1 ?></td><td><code><?= htmlspecialchars($d['code']) ?></code></td><td><?= $d['percentage'] ?>%</td><td><?= $d['valid_from'] ?></td><td><?= $d['valid_until'] ?></td><td><?= $d['used_count'] ?>/<?= $d['max_uses'] ?></td><td><span class="badge-status badge-<?= $d['status'] ?>"><?= $d['status'] ?></span></td>
        <td><button class="btn btn-danger btn-sm" onclick="confirmDelete('?delete=<?= $d['discount_id'] ?>','<?= $d['code'] ?>')"><i class="fas fa-trash"></i></button></td></tr>
        <?php endforeach; ?></tbody></table></div></div>

<div class="modal fade" id="addModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>"><input type="hidden" name="add_discount" value="1">
    <div class="modal-header"><h5 class="modal-title">Add Discount</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <div class="mb-3"><label class="form-label">Code *</label><input type="text" name="code" class="form-control" placeholder="e.g. SUMMER20" required></div>
        <div class="mb-3"><label class="form-label">Percentage *</label><input type="number" name="percentage" class="form-control" step="0.01" min="0" max="100" required></div>
        <div class="row"><div class="col-6 mb-3"><label class="form-label">Valid From *</label><input type="date" name="valid_from" class="form-control" required></div>
        <div class="col-6 mb-3"><label class="form-label">Valid Until *</label><input type="date" name="valid_until" class="form-control" required></div></div>
        <div class="mb-3"><label class="form-label">Max Uses</label><input type="number" name="max_uses" class="form-control" value="10"></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-accent">Add</button></div>
</form></div></div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
