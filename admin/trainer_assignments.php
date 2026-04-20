<?php
$page_title = 'Trainer Assignments';
require_once __DIR__ . '/../config/db.php';
check_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_assignment'])) {
    $pdo->prepare("INSERT INTO trainer_assignments (trainer_id, member_id, assigned_date, end_date, notes) VALUES (?,?,?,?,?)")
        ->execute([(int)$_POST['trainer_id'], (int)$_POST['member_id'], $_POST['assigned_date'], $_POST['end_date'] ?: null, trim($_POST['notes'])]);
    set_flash('success', 'Assignment created.');
    header("Location: trainer_assignments.php"); exit();
}
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM trainer_assignments WHERE assignment_id = ?")->execute([(int)$_GET['delete']]);
    set_flash('success', 'Deleted.'); header("Location: trainer_assignments.php"); exit();
}

$assignments = $pdo->query("SELECT ta.*, u.full_name as trainer_name, m.full_name as member_name FROM trainer_assignments ta JOIN trainers t ON ta.trainer_id = t.trainer_id JOIN users u ON t.user_id = u.user_id JOIN users m ON ta.member_id = m.user_id ORDER BY ta.assigned_date DESC")->fetchAll();
$trainers = $pdo->query("SELECT t.trainer_id, u.full_name FROM trainers t JOIN users u ON t.user_id = u.user_id WHERE t.status = 'active'")->fetchAll();
$members = $pdo->query("SELECT user_id, full_name FROM users WHERE role = 'customer' AND status = 'active' ORDER BY full_name")->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="page-header"><h4><i class="fas fa-user-check me-2"></i>Trainer Assignments</h4>
    <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-1"></i> Assign</button></div>
<div class="card-custom"><div class="card-body">
    <table class="table table-custom datatable">
        <thead><tr><th>#</th><th>Trainer</th><th>Member</th><th>Start</th><th>End</th><th>Notes</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($assignments as $i => $a): ?>
        <tr><td><?= $i+1 ?></td><td><?= htmlspecialchars($a['trainer_name']) ?></td><td><?= htmlspecialchars($a['member_name']) ?></td><td><?= $a['assigned_date'] ?></td><td><?= $a['end_date'] ?? 'Ongoing' ?></td><td><?= htmlspecialchars($a['notes'] ?? '-') ?></td>
        <td><button class="btn btn-danger btn-sm" onclick="confirmDelete('?delete=<?= $a['assignment_id'] ?>','this assignment')"><i class="fas fa-trash"></i></button></td></tr>
        <?php endforeach; ?>
        </tbody></table></div></div>

<div class="modal fade" id="addModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>"><input type="hidden" name="add_assignment" value="1">
    <div class="modal-header"><h5 class="modal-title">Assign Trainer</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <div class="mb-3"><label class="form-label">Trainer *</label><select name="trainer_id" class="form-select" required><option value="">Select</option><?php foreach ($trainers as $t): ?><option value="<?= $t['trainer_id'] ?>"><?= htmlspecialchars($t['full_name']) ?></option><?php endforeach; ?></select></div>
        <div class="mb-3"><label class="form-label">Member *</label><select name="member_id" class="form-select" required><option value="">Select</option><?php foreach ($members as $m): ?><option value="<?= $m['user_id'] ?>"><?= htmlspecialchars($m['full_name']) ?></option><?php endforeach; ?></select></div>
        <div class="mb-3"><label class="form-label">Start Date *</label><input type="date" name="assigned_date" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
        <div class="mb-3"><label class="form-label">End Date</label><input type="date" name="end_date" class="form-control"></div>
        <div class="mb-3"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-accent">Assign</button></div>
</form></div></div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
