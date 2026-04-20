<?php
$page_title = 'Maintenance Log';
require_once __DIR__ . '/../config/db.php';
check_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_maintenance'])) {
    $stmt = $pdo->prepare("INSERT INTO equipment_maintenance (equipment_id, maintenance_date, description, cost, performed_by) VALUES (?,?,?,?,?)");
    $stmt->execute([(int)$_POST['equipment_id'], $_POST['maintenance_date'], trim($_POST['description']), (float)$_POST['cost'], trim($_POST['performed_by'])]);
    set_flash('success', 'Maintenance record added.');
    header("Location: maintenance.php"); exit();
}

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM equipment_maintenance WHERE maintenance_id = ?")->execute([(int)$_GET['delete']]);
    set_flash('success', 'Record deleted.');
    header("Location: maintenance.php"); exit();
}

$logs = $pdo->query("SELECT em.*, e.name as equip_name FROM equipment_maintenance em JOIN equipment e ON em.equipment_id = e.equipment_id ORDER BY em.maintenance_date DESC")->fetchAll();
$equipment = $pdo->query("SELECT equipment_id, name FROM equipment ORDER BY name")->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h4><i class="fas fa-wrench me-2"></i>Maintenance Log</h4>
    <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#addMaintenanceModal"><i class="fas fa-plus me-1"></i> Add Record</button>
</div>

<div class="card-custom">
    <div class="card-body">
        <table class="table table-custom datatable">
            <thead><tr><th>#</th><th>Equipment</th><th>Date</th><th>Description</th><th>Cost</th><th>Performed By</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($logs as $i => $l): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($l['equip_name']) ?></td>
                    <td><?= $l['maintenance_date'] ?></td>
                    <td><?= htmlspecialchars($l['description'] ?? '-') ?></td>
                    <td>$<?= number_format($l['cost'], 2) ?></td>
                    <td><?= htmlspecialchars($l['performed_by'] ?? '-') ?></td>
                    <td><button class="btn btn-danger btn-sm" onclick="confirmDelete('?delete=<?= $l['maintenance_id'] ?>', 'this record')"><i class="fas fa-trash"></i></button></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addMaintenanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="add_maintenance" value="1">
                <div class="modal-header"><h5 class="modal-title">Add Maintenance Record</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Equipment *</label><select name="equipment_id" class="form-select" required><option value="">Select</option><?php foreach ($equipment as $e): ?><option value="<?= $e['equipment_id'] ?>"><?= htmlspecialchars($e['name']) ?></option><?php endforeach; ?></select></div>
                    <div class="mb-3"><label class="form-label">Date *</label><input type="date" name="maintenance_date" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
                    <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
                    <div class="mb-3"><label class="form-label">Cost ($)</label><input type="number" name="cost" class="form-control" step="0.01" value="0"></div>
                    <div class="mb-3"><label class="form-label">Performed By</label><input type="text" name="performed_by" class="form-control"></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-accent">Add</button></div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
