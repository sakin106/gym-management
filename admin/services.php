<?php
$page_title = 'Services';
require_once __DIR__ . '/../config/db.php';
check_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_service'])) {
    $pdo->prepare("INSERT INTO services (service_name, description, monthly_charge, status) VALUES (?,?,?,?)")
        ->execute([trim($_POST['service_name']), trim($_POST['description']), (float)$_POST['monthly_charge'], $_POST['status']]);
    set_flash('success', 'Service added.'); header("Location: services.php"); exit();
}
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM services WHERE service_id = ?")->execute([(int)$_GET['delete']]);
    set_flash('success', 'Deleted.'); header("Location: services.php"); exit();
}

$services = $pdo->query("SELECT * FROM services ORDER BY created_at DESC")->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="page-header"><h4><i class="fas fa-concierge-bell me-2"></i>Services</h4>
    <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-1"></i> Add Service</button></div>
<div class="card-custom"><div class="card-body">
    <table class="table table-custom datatable">
        <thead><tr><th>#</th><th>Service Name</th><th>Description</th><th>Monthly Charge</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($services as $i => $s): ?>
        <tr><td><?= $i+1 ?></td><td><?= htmlspecialchars($s['service_name']) ?></td><td><?= htmlspecialchars($s['description'] ?? '-') ?></td><td>$<?= number_format($s['monthly_charge'],2) ?></td><td><span class="badge-status badge-<?= $s['status'] ?>"><?= $s['status'] ?></span></td>
        <td><button class="btn btn-danger btn-sm" onclick="confirmDelete('?delete=<?= $s['service_id'] ?>','<?= htmlspecialchars($s['service_name']) ?>')"><i class="fas fa-trash"></i></button></td></tr>
        <?php endforeach; ?>
        </tbody></table></div></div>

<div class="modal fade" id="addModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>"><input type="hidden" name="add_service" value="1">
    <div class="modal-header"><h5 class="modal-title">Add Service</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <div class="mb-3"><label class="form-label">Name *</label><input type="text" name="service_name" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
        <div class="mb-3"><label class="form-label">Monthly Charge ($) *</label><input type="number" name="monthly_charge" class="form-control" step="0.01" required></div>
        <div class="mb-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-accent">Add</button></div>
</form></div></div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
