<?php
$page_title = 'My Profile';
require_once __DIR__ . '/../config/db.php';
check_role('customer');
$uid = $_SESSION['user_id'];

$user = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$user->execute([$uid]); $user = $user->fetch();

// Attendance history
$attendance = $pdo->prepare("SELECT * FROM attendance WHERE user_id = ? ORDER BY date DESC LIMIT 20");
$attendance->execute([$uid]); $attendance = $attendance->fetchAll();

// Mark notifications as read
$pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0")->execute([$uid]);

include __DIR__ . '/../includes/header.php';
?>
<div class="row">
    <div class="col-lg-4 mb-3">
        <div class="card-custom">
            <div class="card-body text-center">
                <i class="fas fa-user-circle" style="font-size: 5rem; color: var(--accent); opacity: 0.8;"></i>
                <h4 class="mt-3"><?= htmlspecialchars($user['full_name']) ?></h4>
                <p class="text-muted mb-1"><?= htmlspecialchars($user['email']) ?></p>
                <p class="text-muted mb-1"><?= htmlspecialchars($user['phone'] ?? 'No phone') ?></p>
                <span class="badge-status badge-<?= $user['status'] ?>"><?= $user['status'] ?></span>
                <hr>
                <div class="text-start">
                    <p><strong>Gender:</strong> <?= ucfirst($user['gender'] ?? 'N/A') ?></p>
                    <p><strong>DOB:</strong> <?= $user['dob'] ?? 'N/A' ?></p>
                    <p><strong>Address:</strong> <?= htmlspecialchars($user['address'] ?? 'N/A') ?></p>
                    <p><strong>Joined:</strong> <?= date('M d, Y', strtotime($user['created_at'])) ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8 mb-3">
        <div class="card-custom">
            <div class="card-header"><h5><i class="fas fa-clipboard-check me-2"></i>Attendance History</h5></div>
            <div class="card-body">
                <?php if (empty($attendance)): ?>
                    <div class="empty-state"><i class="fas fa-calendar-times"></i><p>No attendance records</p></div>
                <?php else: ?>
                <table class="table table-custom">
                    <thead><tr><th>Date</th><th>Check In</th><th>Check Out</th><th>Hours</th></tr></thead>
                    <tbody>
                    <?php foreach ($attendance as $a): ?>
                    <tr><td><?= $a['date'] ?></td><td><?= date('h:i A', strtotime($a['check_in'])) ?></td><td><?= $a['check_out'] ? date('h:i A', strtotime($a['check_out'])) : '-' ?></td><td><?= $a['working_hours'] ? $a['working_hours'].' hrs' : '-' ?></td></tr>
                    <?php endforeach; ?></tbody></table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
