<?php
$page_title = 'Class Enrollments';
require_once __DIR__ . '/../config/db.php';
check_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_enrollment'])) {
    try {
        $pdo->prepare("INSERT INTO class_enrollments (class_id, user_id, enrolled_date, status) VALUES (?,?,CURDATE(),'enrolled')")
            ->execute([(int)$_POST['class_id'], (int)$_POST['user_id']]);
        set_flash('success', 'Enrolled.');
    } catch (PDOException $e) {
        set_flash('danger', strpos($e->getMessage(), '45000') !== false ? 'Class is full!' : 'Already enrolled or error.');
    }
    header("Location: class_enrollments.php"); exit();
}
if (isset($_GET['drop'])) {
    $pdo->prepare("UPDATE class_enrollments SET status = 'dropped' WHERE enrollment_id = ?")->execute([(int)$_GET['drop']]);
    set_flash('info', 'Dropped.'); header("Location: class_enrollments.php"); exit();
}

$enrollments = $pdo->query("SELECT ce.*, c.class_name, c.schedule_day, u.full_name FROM class_enrollments ce JOIN classes c ON ce.class_id = c.class_id JOIN users u ON ce.user_id = u.user_id ORDER BY ce.enrolled_date DESC")->fetchAll();
$classes = $pdo->query("SELECT class_id, class_name, schedule_day FROM classes WHERE status = 'active'")->fetchAll();
$members = $pdo->query("SELECT user_id, full_name FROM users WHERE role = 'customer' AND status = 'active' ORDER BY full_name")->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="page-header"><h4><i class="fas fa-user-plus me-2"></i>Class Enrollments</h4>
    <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-1"></i> Enroll</button></div>
<div class="card-custom"><div class="card-body">
    <table class="table table-custom datatable">
        <thead><tr><th>#</th><th>Member</th><th>Class</th><th>Day</th><th>Enrolled</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($enrollments as $i => $e): ?>
        <tr><td><?= $i+1 ?></td><td><?= htmlspecialchars($e['full_name']) ?></td><td><?= htmlspecialchars($e['class_name']) ?></td><td><?= $e['schedule_day'] ?></td><td><?= $e['enrolled_date'] ?></td>
        <td><span class="badge-status badge-<?= $e['status'] === 'enrolled' ? 'active' : 'expired' ?>"><?= $e['status'] ?></span></td>
        <td><?php if ($e['status'] === 'enrolled'): ?><a href="?drop=<?= $e['enrollment_id'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-times"></i> Drop</a><?php endif; ?></td></tr>
        <?php endforeach; ?></tbody></table></div></div>

<div class="modal fade" id="addModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>"><input type="hidden" name="add_enrollment" value="1">
    <div class="modal-header"><h5 class="modal-title">Enroll Member</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <div class="mb-3"><label class="form-label">Class *</label><select name="class_id" class="form-select" required><option value="">Select</option><?php foreach ($classes as $c): ?><option value="<?= $c['class_id'] ?>"><?= htmlspecialchars($c['class_name']) ?> (<?= $c['schedule_day'] ?>)</option><?php endforeach; ?></select></div>
        <div class="mb-3"><label class="form-label">Member *</label><select name="user_id" class="form-select" required><option value="">Select</option><?php foreach ($members as $m): ?><option value="<?= $m['user_id'] ?>"><?= htmlspecialchars($m['full_name']) ?></option><?php endforeach; ?></select></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-accent">Enroll</button></div>
</form></div></div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
