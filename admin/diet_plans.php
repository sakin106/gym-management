<?php
$page_title = 'Diet Plans';
require_once __DIR__ . '/../config/db.php';
check_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_diet'])) {
    $pdo->prepare("INSERT INTO diet_plans (member_id, trainer_id, plan_name, calorie_target, start_date, end_date) VALUES (?,?,?,?,?,?)")
        ->execute([(int)$_POST['member_id'], (int)$_POST['trainer_id'], trim($_POST['plan_name']), (int)$_POST['calorie_target'], $_POST['start_date'], $_POST['end_date'] ?: null]);
    $diet_id = $pdo->lastInsertId();
    if (!empty($_POST['food_name'])) {
        for ($j = 0; $j < count($_POST['food_name']); $j++) {
            if (!empty($_POST['food_name'][$j])) {
                $pdo->prepare("INSERT INTO diet_items (diet_id, meal_type, food_name, quantity, calories, protein, carbs, fat) VALUES (?,?,?,?,?,?,?,?)")
                    ->execute([$diet_id, $_POST['meal_type'][$j], trim($_POST['food_name'][$j]), trim($_POST['quantity'][$j] ?? ''), (int)($_POST['cal'][$j] ?? 0), (float)($_POST['protein'][$j] ?? 0), (float)($_POST['carbs'][$j] ?? 0), (float)($_POST['fat'][$j] ?? 0)]);
            }
        }
    }
    set_flash('success', 'Diet plan created.'); header("Location: diet_plans.php"); exit();
}
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM diet_items WHERE diet_id = ?")->execute([(int)$_GET['delete']]);
    $pdo->prepare("DELETE FROM diet_plans WHERE diet_id = ?")->execute([(int)$_GET['delete']]);
    set_flash('success', 'Deleted.'); header("Location: diet_plans.php"); exit();
}

$plans = $pdo->query("SELECT dp.*, u.full_name as member_name, tu.full_name as trainer_name FROM diet_plans dp JOIN users u ON dp.member_id = u.user_id LEFT JOIN trainers t ON dp.trainer_id = t.trainer_id LEFT JOIN users tu ON t.user_id = tu.user_id ORDER BY dp.created_at DESC")->fetchAll();
$members = $pdo->query("SELECT user_id, full_name FROM users WHERE role = 'customer' AND status = 'active' ORDER BY full_name")->fetchAll();
$trainers = $pdo->query("SELECT t.trainer_id, u.full_name FROM trainers t JOIN users u ON t.user_id = u.user_id WHERE t.status = 'active'")->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="page-header"><h4><i class="fas fa-utensils me-2"></i>Diet Plans</h4>
    <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-1"></i> Create Plan</button></div>
<div class="card-custom"><div class="card-body">
    <table class="table table-custom datatable">
        <thead><tr><th>#</th><th>Plan</th><th>Member</th><th>Trainer</th><th>Calories</th><th>Start</th><th>End</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($plans as $i => $p): ?>
        <tr><td><?= $i+1 ?></td><td><?= htmlspecialchars($p['plan_name']) ?></td><td><?= htmlspecialchars($p['member_name']) ?></td><td><?= htmlspecialchars($p['trainer_name'] ?? 'N/A') ?></td><td><?= $p['calorie_target'] ?> kcal</td><td><?= $p['start_date'] ?></td><td><?= $p['end_date'] ?? 'Ongoing' ?></td>
        <td><button class="btn btn-danger btn-sm" onclick="confirmDelete('?delete=<?= $p['diet_id'] ?>','<?= htmlspecialchars($p['plan_name']) ?>')"><i class="fas fa-trash"></i></button></td></tr>
        <?php endforeach; ?></tbody></table></div></div>

<div class="modal fade" id="addModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content"><form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>"><input type="hidden" name="add_diet" value="1">
    <div class="modal-header"><h5 class="modal-title">Create Diet Plan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label">Plan Name *</label><input type="text" name="plan_name" class="form-control" required></div>
            <div class="col-md-6 mb-3"><label class="form-label">Member *</label><select name="member_id" class="form-select" required><option value="">Select</option><?php foreach ($members as $m): ?><option value="<?= $m['user_id'] ?>"><?= htmlspecialchars($m['full_name']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-3 mb-3"><label class="form-label">Trainer</label><select name="trainer_id" class="form-select"><option value="">Select</option><?php foreach ($trainers as $t): ?><option value="<?= $t['trainer_id'] ?>"><?= htmlspecialchars($t['full_name']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-3 mb-3"><label class="form-label">Calorie Target</label><input type="number" name="calorie_target" class="form-control" value="2000"></div>
            <div class="col-md-3 mb-3"><label class="form-label">Start</label><input type="date" name="start_date" class="form-control" value="<?= date('Y-m-d') ?>"></div>
            <div class="col-md-3 mb-3"><label class="form-label">End</label><input type="date" name="end_date" class="form-control"></div>
        </div>
        <hr><h6>Diet Items</h6>
        <?php for ($j = 0; $j < 4; $j++): ?>
        <div class="row mb-2">
            <div class="col-2"><select name="meal_type[]" class="form-select form-select-sm"><option value="breakfast">Breakfast</option><option value="lunch">Lunch</option><option value="dinner">Dinner</option><option value="snack">Snack</option></select></div>
            <div class="col-2"><input type="text" name="food_name[]" class="form-control form-control-sm" placeholder="Food"></div>
            <div class="col-2"><input type="text" name="quantity[]" class="form-control form-control-sm" placeholder="Qty"></div>
            <div class="col-1"><input type="number" name="cal[]" class="form-control form-control-sm" placeholder="Cal"></div>
            <div class="col-2"><input type="number" name="protein[]" class="form-control form-control-sm" placeholder="Protein" step="0.01"></div>
            <div class="col-2"><input type="number" name="carbs[]" class="form-control form-control-sm" placeholder="Carbs" step="0.01"></div>
            <div class="col-1"><input type="number" name="fat[]" class="form-control form-control-sm" placeholder="Fat" step="0.01"></div>
        </div>
        <?php endfor; ?>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-accent">Create</button></div>
</form></div></div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
