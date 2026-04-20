<?php
$page_title = 'Equipment';
require_once __DIR__ . '/../config/db.php';
check_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_equipment'])) {
    $stmt = $pdo->prepare("INSERT INTO equipment (name, description, category, quantity, purchase_date, vendor_name, vendor_contact, unit_cost, total_cost, condition_status, zone) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
    $qty = (int)$_POST['quantity'];
    $unit = (float)$_POST['unit_cost'];
    $stmt->execute([
        trim($_POST['name']), trim($_POST['description']), trim($_POST['category']),
        $qty, $_POST['purchase_date'], trim($_POST['vendor_name']),
        trim($_POST['vendor_contact']), $unit, $qty * $unit,
        $_POST['condition_status'], trim($_POST['zone'])
    ]);
    set_flash('success', 'Equipment added.');
    header("Location: equipment.php"); exit();
}

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM equipment WHERE equipment_id = ?")->execute([(int)$_GET['delete']]);
    set_flash('success', 'Equipment deleted.');
    header("Location: equipment.php"); exit();
}

$equipment = $pdo->query("SELECT * FROM equipment ORDER BY created_at DESC")->fetchAll();
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h4><i class="fas fa-cogs me-2"></i>Equipment</h4>
    <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#addEquipModal"><i class="fas fa-plus me-1"></i> Add Equipment</button>
</div>

<div class="card-custom">
    <div class="card-body">
        <table class="table table-custom datatable">
            <thead><tr><th>#</th><th>Name</th><th>Category</th><th>Qty</th><th>Unit Cost</th><th>Total</th><th>Vendor</th><th>Zone</th><th>Condition</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($equipment as $i => $e): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($e['name']) ?></td>
                    <td><?= htmlspecialchars($e['category'] ?? '-') ?></td>
                    <td><?= $e['quantity'] ?></td>
                    <td>$<?= number_format($e['unit_cost'], 2) ?></td>
                    <td>$<?= number_format($e['total_cost'], 2) ?></td>
                    <td><?= htmlspecialchars($e['vendor_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($e['zone'] ?? '-') ?></td>
                    <td><span class="badge-status badge-<?= $e['condition_status'] ?>"><?= $e['condition_status'] ?></span></td>
                    <td><button class="btn btn-danger btn-sm" onclick="confirmDelete('?delete=<?= $e['equipment_id'] ?>', '<?= htmlspecialchars($e['name']) ?>')"><i class="fas fa-trash"></i></button></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addEquipModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="add_equipment" value="1">
                <div class="modal-header"><h5 class="modal-title">Add Equipment</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Name *</label><input type="text" name="name" class="form-control" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Category</label><input type="text" name="category" class="form-control"></div>
                        <div class="col-md-12 mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Quantity *</label><input type="number" name="quantity" class="form-control" value="1" required></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Unit Cost ($)</label><input type="number" name="unit_cost" class="form-control" step="0.01" value="0"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Purchase Date</label><input type="date" name="purchase_date" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Condition</label><select name="condition_status" class="form-select"><option value="good">Good</option><option value="fair">Fair</option><option value="poor">Poor</option></select></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Vendor Name</label><input type="text" name="vendor_name" class="form-control"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Vendor Contact</label><input type="text" name="vendor_contact" class="form-control"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Zone</label><input type="text" name="zone" class="form-control" placeholder="e.g. Cardio Zone"></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-accent">Add</button></div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
