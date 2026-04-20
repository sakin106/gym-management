<?php
$page_title = 'Workout Plans';
require_once __DIR__ . '/../config/db.php';
check_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_plan'])) {
    $pdo->prepare("INSERT INTO workout_plans (member_id, trainer_id, plan_name, goal, start_date, end_date) VALUES (?,?,?,?,?,?)")
        ->execute([(int)$_POST['member_id'], (int)$_POST['trainer_id'], trim($_POST['plan_name']), trim($_POST['goal']), $_POST['start_date'], $_POST['end_date'] ?: null]);
    $plan_id = $pdo->lastInsertId();
    // Add exercises if provided
    if (!empty($_POST['exercise_name'])) {
        for ($j = 0; $j < count($_POST['exercise_name']); $j++) {
            if (!empty($_POST['exercise_name'][$j])) {
                $pdo->prepare("INSERT INTO workout_exercises (plan_id, exercise_name, sets, reps, duration_minutes, day_of_week) VALUES (?,?,?,?,?,?)")
                    ->execute([$plan_id, trim($_POST['exercise_name'][$j]), (int)($_POST['sets'][$j] ?? 3), (int)($_POST['reps'][$j] ?? 10), (int)($_POST['duration'][$j] ?? 0), $_POST['day'][$j] ?? 'Mon']);
            }
        }
    }
    set_flash('success', 'Workout plan created.'); header("Location: workout_plans.php"); exit();
}
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM workout_exercises WHERE plan_id = ?")->execute([(int)$_GET['delete']]);
    $pdo->prepare("DELETE FROM workout_plans WHERE plan_id = ?")->execute([(int)$_GET['delete']]);
    set_flash('success', 'Deleted.'); header("Location: workout_plans.php"); exit();
}

$plans = $pdo->query("SELECT wp.*, u.full_name as member_name, tu.full_name as trainer_name FROM workout_plans wp JOIN users u ON wp.member_id = u.user_id LEFT JOIN trainers t ON wp.trainer_id = t.trainer_id LEFT JOIN users tu ON t.user_id = tu.user_id ORDER BY wp.created_at DESC")->fetchAll();
$members = $pdo->query("SELECT user_id, full_name FROM users WHERE role = 'customer' AND status = 'active' ORDER BY full_name")->fetchAll();
$trainers = $pdo->query("SELECT t.trainer_id, u.full_name FROM trainers t JOIN users u ON t.user_id = u.user_id WHERE t.status = 'active'")->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="page-header"><h4><i class="fas fa-dumbbell me-2"></i>Workout Plans</h4>
    <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-1"></i> Create Plan</button></div>
<div class="card-custom"><div class="card-body">
    <table class="table table-custom datatable">
        <thead><tr><th>#</th><th>Plan</th><th>Member</th><th>Trainer</th><th>Goal</th><th>Start</th><th>End</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($plans as $i => $p): ?>
        <tr><td><?= $i+1 ?></td><td><?= htmlspecialchars($p['plan_name']) ?></td><td><?= htmlspecialchars($p['member_name']) ?></td><td><?= htmlspecialchars($p['trainer_name'] ?? 'N/A') ?></td><td><?= htmlspecialchars(substr($p['goal'] ?? '',0,40)) ?></td><td><?= $p['start_date'] ?></td><td><?= $p['end_date'] ?? 'Ongoing' ?></td>
        <td><button class="btn btn-danger btn-sm" onclick="confirmDelete('?delete=<?= $p['plan_id'] ?>','<?= htmlspecialchars($p['plan_name']) ?>')"><i class="fas fa-trash"></i></button></td></tr>
        <?php endforeach; ?></tbody></table></div></div>

<div class="modal fade" id="addModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content"><form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>"><input type="hidden" name="add_plan" value="1">
    <div class="modal-header"><h5 class="modal-title">Create Workout Plan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label">Plan Name *</label><input type="text" name="plan_name" class="form-control" required></div>
            <div class="col-md-6 mb-3"><label class="form-label">Member *</label><select name="member_id" class="form-select" required><option value="">Select</option><?php foreach ($members as $m): ?><option value="<?= $m['user_id'] ?>"><?= htmlspecialchars($m['full_name']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-4 mb-3"><label class="form-label">Trainer</label><select name="trainer_id" class="form-select"><option value="">Select</option><?php foreach ($trainers as $t): ?><option value="<?= $t['trainer_id'] ?>"><?= htmlspecialchars($t['full_name']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-4 mb-3"><label class="form-label">Start Date</label><input type="date" name="start_date" class="form-control" value="<?= date('Y-m-d') ?>"></div>
            <div class="col-md-4 mb-3"><label class="form-label">End Date</label><input type="date" name="end_date" class="form-control"></div>
            <div class="col-12 mb-3"><label class="form-label">Goal</label><textarea name="goal" class="form-control" rows="2"></textarea></div>
        </div>
        <hr><h6>Exercises</h6>
        <div class="row mb-2"><div class="col-3"><strong>Exercise</strong></div><div class="col-2"><strong>Sets</strong></div><div class="col-2"><strong>Reps</strong></div><div class="col-2"><strong>Duration</strong></div><div class="col-3"><strong>Day</strong></div></div>
        <?php for ($j = 0; $j < 5; $j++): ?>
        <div class="row mb-2">
            <div class="col-3"><input type="text" name="exercise_name[]" class="form-control form-control-sm"></div>
            <div class="col-2"><input type="number" name="sets[]" class="form-control form-control-sm" value="3"></div>
            <div class="col-2"><input type="number" name="reps[]" class="form-control form-control-sm" value="10"></div>
            <div class="col-2"><input type="number" name="duration[]" class="form-control form-control-sm" value="0"></div>
            <div class="col-3"><select name="day[]" class="form-select form-select-sm"><option>Mon</option><option>Tue</option><option>Wed</option><option>Thu</option><option>Fri</option><option>Sat</option><option>Sun</option></select></div>
        </div>
        <?php endfor; ?>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-accent">Create</button></div>
</form></div></div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
