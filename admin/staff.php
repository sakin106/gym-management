<?php
$page_title = 'Staff Management';
require_once __DIR__ . '/../config/db.php';
check_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_staff'])) {
    $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $pdo->prepare("INSERT INTO users (full_name, email, password, phone, gender, role, status) VALUES (?,?,?,?,?,'staff','active')")
        ->execute([trim($_POST['full_name']), trim($_POST['email']), $hashed, trim($_POST['phone']), $_POST['gender'] ?: null]);
    set_flash('success', 'Staff member added.');
    header("Location: staff.php"); exit();
}
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM users WHERE user_id = ? AND role = 'staff'")->execute([(int)$_GET['delete']]);
    set_flash('success', 'Staff deleted.'); header("Location: staff.php"); exit();
}

$staff = $pdo->query("SELECT * FROM users WHERE role = 'staff' ORDER BY created_at DESC")->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <h4><i class="fas fa-user-tie me-2"></i>Staff</h4>
    <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#addStaffModal"><i class="fas fa-plus me-1"></i> Add Staff</button>
</div>
<div class="card-custom"><div class="card-body">
    <table class="table table-custom datatable">
        <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Gender</th><th>Status</th><th>Joined</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($staff as $i => $s): ?>
        <tr><td><?= $i+1 ?></td><td><?= htmlspecialchars($s['full_name']) ?></td><td><?= htmlspecialchars($s['email']) ?></td><td><?= htmlspecialchars($s['phone'] ?? '-') ?></td><td><?= ucfirst($s['gender'] ?? '-') ?></td><td><span class="badge-status badge-<?= $s['status'] ?>"><?= $s['status'] ?></span></td><td><?= date('M d, Y', strtotime($s['created_at'])) ?></td>
        <td><button class="btn btn-danger btn-sm" onclick="confirmDelete('?delete=<?= $s['user_id'] ?>','<?= htmlspecialchars($s['full_name']) ?>')"><i class="fas fa-trash"></i></button></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div></div>
<div class="modal fade" id="addStaffModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>"><input type="hidden" name="add_staff" value="1">
    <div class="modal-header"><h5 class="modal-title">Add Staff</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <div class="mb-3"><label class="form-label">Full Name *</label><input type="text" name="full_name" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control"></div>
        <div class="mb-3"><label class="form-label">Gender</label><select name="gender" class="form-select"><option value="">Select</option><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select></div>
        <div class="mb-3"><label class="form-label">Password *</label><input type="password" name="password" class="form-control" required></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-accent">Add Staff</button></div>
</form></div></div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
