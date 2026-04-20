<?php
$page_title = 'Announcements';
require_once __DIR__ . '/../config/db.php';
check_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_announcement'])) {
    $pdo->prepare("INSERT INTO announcements (title, message, posted_by, applied_date, status) VALUES (?,?,?,?,?)")
        ->execute([trim($_POST['title']), trim($_POST['message']), $_SESSION['user_id'], $_POST['applied_date'], $_POST['status']]);
    set_flash('success', 'Announcement posted.');
    header("Location: announcements.php"); exit();
}
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM announcements WHERE announcement_id = ?")->execute([(int)$_GET['delete']]);
    set_flash('success', 'Deleted.'); header("Location: announcements.php"); exit();
}
if (isset($_GET['toggle'])) {
    $pdo->prepare("UPDATE announcements SET status = IF(status='active','inactive','active') WHERE announcement_id = ?")->execute([(int)$_GET['toggle']]);
    header("Location: announcements.php"); exit();
}
$announcements = $pdo->query("SELECT a.*, u.full_name FROM announcements a JOIN users u ON a.posted_by = u.user_id ORDER BY a.created_at DESC")->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <h4><i class="fas fa-bullhorn me-2"></i>Announcements</h4>
    <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-1"></i> New</button>
</div>
<div class="card-custom"><div class="card-body">
    <table class="table table-custom datatable">
        <thead><tr><th>#</th><th>Title</th><th>Message</th><th>Date</th><th>Status</th><th>By</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($announcements as $i => $a): ?>
        <tr>
            <td><?= $i+1 ?></td><td><?= htmlspecialchars($a['title']) ?></td>
            <td><?= htmlspecialchars(substr($a['message'], 0, 60)) ?>...</td>
            <td><?= $a['applied_date'] ?></td>
            <td><span class="badge-status badge-<?= $a['status'] ?>"><?= $a['status'] ?></span></td>
            <td><?= htmlspecialchars($a['full_name']) ?></td>
            <td>
                <a href="?toggle=<?= $a['announcement_id'] ?>" class="btn btn-sm btn-outline-info"><i class="fas fa-sync"></i></a>
                <button class="btn btn-danger btn-sm" onclick="confirmDelete('?delete=<?= $a['announcement_id'] ?>','this announcement')"><i class="fas fa-trash"></i></button>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div></div>
<div class="modal fade" id="addModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>"><input type="hidden" name="add_announcement" value="1">
    <div class="modal-header"><h5 class="modal-title">New Announcement</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <div class="mb-3"><label class="form-label">Title *</label><input type="text" name="title" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Message *</label><textarea name="message" class="form-control" rows="4" required></textarea></div>
        <div class="mb-3"><label class="form-label">Applied Date *</label><input type="date" name="applied_date" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
        <div class="mb-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-accent">Post</button></div>
</form></div></div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
