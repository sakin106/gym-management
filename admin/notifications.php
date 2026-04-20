<?php
$page_title = 'Notifications';
require_once __DIR__ . '/../config/db.php';
check_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_notification'])) {
    $pdo->prepare("INSERT INTO notifications (user_id, message, type, sent_by) VALUES (?,?,?,?)")
        ->execute([(int)$_POST['user_id'], trim($_POST['message']), $_POST['type'], $_SESSION['user_id']]);
    set_flash('success', 'Notification sent.');
    header("Location: notifications.php"); exit();
}
// One-click fee alert
if (isset($_GET['fee_alert'])) {
    $uid = (int)$_GET['fee_alert'];
    $user = $pdo->prepare("SELECT full_name FROM users WHERE user_id = ?"); $user->execute([$uid]); $u = $user->fetch();
    if ($u) {
        $pdo->prepare("INSERT INTO notifications (user_id, message, type, sent_by) VALUES (?,?,?,?)")
            ->execute([$uid, "Dear {$u['full_name']}, your membership fee is due. Please make payment at the earliest.", 'payment_due', $_SESSION['user_id']]);
        set_flash('success', 'Fee alert sent to ' . $u['full_name']);
    }
    header("Location: notifications.php"); exit();
}

$notifications = $pdo->query("SELECT n.*, u.full_name as to_name, s.full_name as from_name FROM notifications n JOIN users u ON n.user_id = u.user_id LEFT JOIN users s ON n.sent_by = s.user_id ORDER BY n.sent_at DESC LIMIT 100")->fetchAll();
$members = $pdo->query("SELECT user_id, full_name FROM users WHERE role = 'customer' AND status = 'active' ORDER BY full_name")->fetchAll();
// Members with overdue payments
$overdue_members = $pdo->query("SELECT DISTINCT u.user_id, u.full_name FROM payments p JOIN users u ON p.user_id = u.user_id WHERE p.status = 'overdue'")->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <h4><i class="fas fa-bell me-2"></i>Notifications</h4>
    <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#sendModal"><i class="fas fa-paper-plane me-1"></i> Send</button>
</div>

<?php if (!empty($overdue_members)): ?>
<div class="alert alert-warning"><strong>Members with overdue payments:</strong>
    <?php foreach ($overdue_members as $om): ?>
        <a href="?fee_alert=<?= $om['user_id'] ?>" class="btn btn-sm btn-warning ms-2"><i class="fas fa-bell"></i> Alert <?= htmlspecialchars($om['full_name']) ?></a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="card-custom"><div class="card-body">
    <table class="table table-custom datatable">
        <thead><tr><th>#</th><th>To</th><th>Message</th><th>Type</th><th>Read</th><th>From</th><th>Date</th></tr></thead>
        <tbody>
        <?php foreach ($notifications as $i => $n): ?>
        <tr>
            <td><?= $i+1 ?></td><td><?= htmlspecialchars($n['to_name']) ?></td>
            <td><?= htmlspecialchars(substr($n['message'], 0, 80)) ?></td>
            <td><span class="badge bg-<?= $n['type'] === 'payment_due' ? 'danger' : ($n['type'] === 'alert' ? 'warning' : 'info') ?>"><?= $n['type'] ?></span></td>
            <td><?= $n['is_read'] ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-circle text-warning" style="font-size:0.6rem"></i>' ?></td>
            <td><?= htmlspecialchars($n['from_name'] ?? 'System') ?></td>
            <td><?= date('M d, h:i A', strtotime($n['sent_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div></div>

<div class="modal fade" id="sendModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>"><input type="hidden" name="send_notification" value="1">
    <div class="modal-header"><h5 class="modal-title">Send Notification</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <div class="mb-3"><label class="form-label">To Member *</label><select name="user_id" class="form-select" required><option value="">Select</option><?php foreach ($members as $m): ?><option value="<?= $m['user_id'] ?>"><?= htmlspecialchars($m['full_name']) ?></option><?php endforeach; ?></select></div>
        <div class="mb-3"><label class="form-label">Type</label><select name="type" class="form-select"><option value="info">Info</option><option value="alert">Alert</option><option value="payment_due">Payment Due</option></select></div>
        <div class="mb-3"><label class="form-label">Message *</label><textarea name="message" class="form-control" rows="3" required></textarea></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-accent">Send</button></div>
</form></div></div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
