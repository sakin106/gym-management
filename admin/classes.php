<?php
$page_title = 'Classes';
require_once __DIR__ . '/../config/db.php';
check_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_class'])) {
    $pdo->prepare("INSERT INTO classes (class_name, trainer_id, schedule_day, start_time, end_time, capacity, room, status) VALUES (?,?,?,?,?,?,?,?)")
        ->execute([trim($_POST['class_name']), (int)$_POST['trainer_id'], $_POST['schedule_day'], $_POST['start_time'], $_POST['end_time'], (int)$_POST['capacity'], trim($_POST['room']), 'active']);
    set_flash('success', 'Class added.'); header("Location: classes.php"); exit();
}
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM classes WHERE class_id = ?")->execute([(int)$_GET['delete']]);
    set_flash('success', 'Deleted.'); header("Location: classes.php"); exit();
}

$classes = $pdo->query("SELECT c.*, u.full_name as trainer_name, (SELECT COUNT(*) FROM class_enrollments ce WHERE ce.class_id = c.class_id AND ce.status = 'enrolled') as enrolled FROM classes c LEFT JOIN trainers t ON c.trainer_id = t.trainer_id LEFT JOIN users u ON t.user_id = u.user_id ORDER BY FIELD(c.schedule_day,'Mon','Tue','Wed','Thu','Fri','Sat','Sun')")->fetchAll();
$trainers = $pdo->query("SELECT t.trainer_id, u.full_name FROM trainers t JOIN users u ON t.user_id = u.user_id WHERE t.status = 'active'")->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="page-header"><h4><i class="fas fa-calendar-alt me-2"></i>Classes</h4>
    <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-1"></i> Add Class</button></div>
<div class="card-custom"><div class="card-body">
    <table class="table table-custom datatable">
        <thead><tr><th>#</th><th>Class</th><th>Trainer</th><th>Day</th><th>Time</th><th>Room</th><th>Enrolled/Cap</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($classes as $i => $c): ?>
        <tr><td><?= $i+1 ?></td><td><?= htmlspecialchars($c['class_name']) ?></td><td><?= htmlspecialchars($c['trainer_name'] ?? 'TBD') ?></td><td><?= $c['schedule_day'] ?></td><td><?= date('h:i A', strtotime($c['start_time'])) ?> - <?= date('h:i A', strtotime($c['end_time'])) ?></td><td><?= htmlspecialchars($c['room'] ?? '-') ?></td>
        <td><span class="badge bg-<?= $c['enrolled'] >= $c['capacity'] ? 'danger' : 'success' ?>"><?= $c['enrolled'] ?>/<?= $c['capacity'] ?></span></td>
        <td><span class="badge-status badge-<?= $c['status'] ?>"><?= $c['status'] ?></span></td>
        <td><button class="btn btn-danger btn-sm" onclick="confirmDelete('?delete=<?= $c['class_id'] ?>','<?= htmlspecialchars($c['class_name']) ?>')"><i class="fas fa-trash"></i></button></td></tr>
        <?php endforeach; ?></tbody></table></div></div>

<div class="modal fade" id="addModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>"><input type="hidden" name="add_class" value="1">
    <div class="modal-header"><h5 class="modal-title">Add Class</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <div class="mb-3"><label class="form-label">Class Name *</label><input type="text" name="class_name" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Trainer *</label><select name="trainer_id" class="form-select" required><option value="">Select</option><?php foreach ($trainers as $t): ?><option value="<?= $t['trainer_id'] ?>"><?= htmlspecialchars($t['full_name']) ?></option><?php endforeach; ?></select></div>
        <div class="row"><div class="col-4 mb-3"><label class="form-label">Day</label><select name="schedule_day" class="form-select"><option>Mon</option><option>Tue</option><option>Wed</option><option>Thu</option><option>Fri</option><option>Sat</option><option>Sun</option></select></div>
        <div class="col-4 mb-3"><label class="form-label">Start</label><input type="time" name="start_time" class="form-control" required></div>
        <div class="col-4 mb-3"><label class="form-label">End</label><input type="time" name="end_time" class="form-control" required></div></div>
        <div class="row"><div class="col-6 mb-3"><label class="form-label">Capacity *</label><input type="number" name="capacity" class="form-control" required></div>
        <div class="col-6 mb-3"><label class="form-label">Room</label><input type="text" name="room" class="form-control"></div></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-accent">Add</button></div>
</form></div></div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
