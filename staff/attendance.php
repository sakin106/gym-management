<?php
$page_title = 'Attendance';
require_once __DIR__ . '/../config/db.php';
check_role('staff');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = (int)$_POST['user_id'];
    $action = $_POST['action'];
    if ($action === 'checkin') {
        $check = $pdo->prepare("SELECT attendance_id FROM attendance WHERE user_id = ? AND date = CURDATE() AND check_out IS NULL");
        $check->execute([$user_id]);
        if ($check->fetch()) { set_flash('warning', 'Already checked in.'); }
        else {
            $pdo->prepare("INSERT INTO attendance (user_id, check_in, date, marked_by) VALUES (?, NOW(), CURDATE(), ?)")->execute([$user_id, $_SESSION['user_id']]);
            set_flash('success', 'Check-in recorded.');
        }
    } elseif ($action === 'checkout') {
        $rec = $pdo->prepare("SELECT attendance_id, check_in FROM attendance WHERE user_id = ? AND date = CURDATE() AND check_out IS NULL LIMIT 1");
        $rec->execute([$user_id]); $r = $rec->fetch();
        if ($r) {
            $hours = round((time() - strtotime($r['check_in'])) / 3600, 2);
            $pdo->prepare("UPDATE attendance SET check_out = NOW(), working_hours = ? WHERE attendance_id = ?")->execute([$hours, $r['attendance_id']]);
            set_flash('success', "Check-out recorded. Hours: $hours");
        } else { set_flash('warning', 'No check-in found.'); }
    }
    header("Location: attendance.php"); exit();
}

$date_filter = $_GET['date'] ?? date('Y-m-d');
$records = $pdo->prepare("SELECT a.*, u.full_name FROM attendance a JOIN users u ON a.user_id = u.user_id WHERE a.date = ? ORDER BY a.check_in DESC");
$records->execute([$date_filter]); $records = $records->fetchAll();
$active_members = $pdo->query("SELECT user_id, full_name FROM users WHERE role = 'customer' AND status = 'active' ORDER BY full_name")->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="page-header"><h4><i class="fas fa-clipboard-check me-2"></i>Attendance</h4>
    <form class="d-flex gap-2" method="GET"><input type="date" name="date" class="form-control" value="<?= $date_filter ?>"><button class="btn btn-accent btn-sm">Filter</button></form></div>

<div class="card-custom mb-4"><div class="card-header"><h5>Mark Attendance</h5></div><div class="card-body">
    <form method="POST" class="row g-3 align-items-end">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <div class="col-md-5"><label class="form-label">Member</label><select name="user_id" class="form-select" required><option value="">Select</option><?php foreach ($active_members as $am): ?><option value="<?= $am['user_id'] ?>"><?= htmlspecialchars($am['full_name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-3"><select name="action" class="form-select"><option value="checkin">Check In</option><option value="checkout">Check Out</option></select></div>
        <div class="col-md-4"><button type="submit" class="btn btn-accent">Mark</button></div>
    </form>
</div></div>

<div class="card-custom"><div class="card-body">
    <table class="table table-custom datatable">
        <thead><tr><th>#</th><th>Member</th><th>Check In</th><th>Check Out</th><th>Hours</th></tr></thead>
        <tbody>
        <?php foreach ($records as $i => $r): ?>
        <tr><td><?= $i+1 ?></td><td><?= htmlspecialchars($r['full_name']) ?></td><td><?= date('h:i A', strtotime($r['check_in'])) ?></td><td><?= $r['check_out'] ? date('h:i A', strtotime($r['check_out'])) : '<span class="badge bg-warning">Active</span>' ?></td><td><?= $r['working_hours'] ?? '-' ?></td></tr>
        <?php endforeach; ?></tbody></table></div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
