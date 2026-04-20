<?php
$page_title = 'To-Do List';
require_once __DIR__ . '/../config/db.php';
check_role('customer');
$uid = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_todo'])) {
    $pdo->prepare("INSERT INTO todo_list (user_id, task, due_date) VALUES (?,?,?)")
        ->execute([$uid, trim($_POST['task']), $_POST['due_date'] ?: null]);
    set_flash('success', 'Task added.'); header("Location: todo.php"); exit();
}
if (isset($_GET['complete'])) {
    $pdo->prepare("UPDATE todo_list SET status = 'done' WHERE todo_id = ? AND user_id = ?")->execute([(int)$_GET['complete'], $uid]);
    set_flash('success', 'Task completed!'); header("Location: todo.php"); exit();
}
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM todo_list WHERE todo_id = ? AND user_id = ?")->execute([(int)$_GET['delete'], $uid]);
    set_flash('success', 'Deleted.'); header("Location: todo.php"); exit();
}

$todos = $pdo->prepare("SELECT * FROM todo_list WHERE user_id = ? ORDER BY status, due_date");
$todos->execute([$uid]); $todos = $todos->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="page-header"><h4><i class="fas fa-tasks me-2"></i>My To-Do List</h4>
    <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-1"></i> Add Task</button></div>
<div class="card-custom"><div class="card-body">
    <?php if (empty($todos)): ?>
        <div class="empty-state"><i class="fas fa-clipboard-check"></i><p>No tasks yet. Add one!</p></div>
    <?php else: ?>
    <table class="table table-custom">
        <thead><tr><th>#</th><th>Task</th><th>Due Date</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($todos as $i => $t): ?>
        <tr style="<?= $t['status'] === 'done' ? 'opacity: 0.5; text-decoration: line-through;' : '' ?>">
            <td><?= $i+1 ?></td><td><?= htmlspecialchars($t['task']) ?></td><td><?= $t['due_date'] ?? '-' ?></td>
            <td><span class="badge-status badge-<?= $t['status'] === 'done' ? 'active' : 'pending' ?>"><?= $t['status'] ?></span></td>
            <td>
                <?php if ($t['status'] === 'pending'): ?><a href="?complete=<?= $t['todo_id'] ?>" class="btn btn-success btn-sm"><i class="fas fa-check"></i></a><?php endif; ?>
                <button class="btn btn-danger btn-sm" onclick="confirmDelete('?delete=<?= $t['todo_id'] ?>','this task')"><i class="fas fa-trash"></i></button>
            </td>
        </tr>
        <?php endforeach; ?></tbody></table>
    <?php endif; ?>
</div></div>

<div class="modal fade" id="addModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>"><input type="hidden" name="add_todo" value="1">
    <div class="modal-header"><h5 class="modal-title">Add Task</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <div class="mb-3"><label class="form-label">Task *</label><textarea name="task" class="form-control" rows="2" required></textarea></div>
        <div class="mb-3"><label class="form-label">Due Date</label><input type="date" name="due_date" class="form-control"></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-accent">Add</button></div>
</form></div></div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
