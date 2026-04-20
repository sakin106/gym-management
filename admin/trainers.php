<?php
$page_title = 'Trainers';
require_once __DIR__ . '/../config/db.php';
check_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_trainer'])) {
    $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $pdo->beginTransaction();
    try {
        $pdo->prepare("INSERT INTO users (full_name, email, password, phone, gender, role, status) VALUES (?,?,?,?,?,'staff','active')")
            ->execute([trim($_POST['full_name']), trim($_POST['email']), $hashed, trim($_POST['phone']), $_POST['gender'] ?: null]);
        $uid = $pdo->lastInsertId();
        $pdo->prepare("INSERT INTO trainers (user_id, specialization, hire_date, salary, bio, status) VALUES (?,?,?,?,?,?)")
            ->execute([$uid, trim($_POST['specialization']), $_POST['hire_date'], (float)$_POST['salary'], trim($_POST['bio']), 'active']);
        $pdo->commit();
        set_flash('success', 'Trainer added.');
    } catch (Exception $e) { $pdo->rollBack(); set_flash('danger', 'Error: ' . $e->getMessage()); }
    header("Location: trainers.php"); exit();
}
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM trainers WHERE trainer_id = ?")->execute([(int)$_GET['delete']]);
    set_flash('success', 'Trainer deleted.'); header("Location: trainers.php"); exit();
}

$trainers = $pdo->query("SELECT t.*, u.full_name, u.email, u.phone FROM trainers t JOIN users u ON t.user_id = u.user_id ORDER BY t.trainer_id DESC")->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="page-header"><h4><i class="fas fa-running me-2"></i>Trainers</h4>
    <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#addTrainerModal"><i class="fas fa-plus me-1"></i> Add Trainer</button></div>
<div class="card-custom"><div class="card-body">
    <table class="table table-custom datatable">
        <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Specialization</th><th>Salary</th><th>Hire Date</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($trainers as $i => $t): ?>
        <tr><td><?= $i+1 ?></td><td><?= htmlspecialchars($t['full_name']) ?></td><td><?= htmlspecialchars($t['email']) ?></td><td><?= htmlspecialchars($t['specialization']) ?></td><td>$<?= number_format($t['salary'],2) ?></td><td><?= $t['hire_date'] ?></td><td><span class="badge-status badge-<?= $t['status'] ?>"><?= $t['status'] ?></span></td>
        <td><button class="btn btn-danger btn-sm" onclick="confirmDelete('?delete=<?= $t['trainer_id'] ?>','<?= htmlspecialchars($t['full_name']) ?>')"><i class="fas fa-trash"></i></button></td></tr>
        <?php endforeach; ?>
        </tbody></table></div></div>

<div class="modal fade" id="addTrainerModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content"><form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>"><input type="hidden" name="add_trainer" value="1">
    <div class="modal-header"><h5 class="modal-title">Add Trainer</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body"><div class="row">
        <div class="col-md-6 mb-3"><label class="form-label">Full Name *</label><input type="text" name="full_name" class="form-control" required></div>
        <div class="col-md-6 mb-3"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" required></div>
        <div class="col-md-4 mb-3"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control"></div>
        <div class="col-md-4 mb-3"><label class="form-label">Gender</label><select name="gender" class="form-select"><option value="">Select</option><option value="male">Male</option><option value="female">Female</option></select></div>
        <div class="col-md-4 mb-3"><label class="form-label">Password *</label><input type="password" name="password" class="form-control" required></div>
        <div class="col-md-6 mb-3"><label class="form-label">Specialization *</label><input type="text" name="specialization" class="form-control" placeholder="e.g. Cardio, Yoga, CrossFit" required></div>
        <div class="col-md-3 mb-3"><label class="form-label">Salary</label><input type="number" name="salary" class="form-control" step="0.01"></div>
        <div class="col-md-3 mb-3"><label class="form-label">Hire Date</label><input type="date" name="hire_date" class="form-control" value="<?= date('Y-m-d') ?>"></div>
        <div class="col-12 mb-3"><label class="form-label">Bio</label><textarea name="bio" class="form-control" rows="2"></textarea></div>
    </div></div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-accent">Add Trainer</button></div>
</form></div></div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
