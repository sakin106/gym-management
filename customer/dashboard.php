<?php
$page_title = 'Dashboard';
require_once __DIR__ . '/../config/db.php';
check_role('customer');
$uid = $_SESSION['user_id'];

// Membership info
$membership = $pdo->prepare("SELECT mm.*, mp.plan_name, s.service_name FROM member_memberships mm JOIN membership_plans mp ON mm.plan_id = mp.plan_id JOIN services s ON mm.service_id = s.service_id WHERE mm.user_id = ? ORDER BY mm.created_at DESC LIMIT 1");
$membership->execute([$uid]); $mem = $membership->fetch();

// Unread notifications
$unread = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
$unread->execute([$uid]); $unread_count = $unread->fetchColumn();

// Latest announcements
$announcements = $pdo->query("SELECT * FROM announcements WHERE status = 'active' ORDER BY created_at DESC LIMIT 3")->fetchAll();

// Upcoming todos
$todos = $pdo->prepare("SELECT * FROM todo_list WHERE user_id = ? AND status = 'pending' ORDER BY due_date LIMIT 5");
$todos->execute([$uid]); $todos = $todos->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<div class="row mb-4">
    <!-- Membership Card -->
    <div class="col-lg-6 mb-3">
        <div class="card-custom animate-in">
            <div class="card-header"><h5><i class="fas fa-id-card me-2"></i>My Membership</h5></div>
            <div class="card-body">
                <?php if ($mem): ?>
                    <div class="d-flex justify-content-between mb-2"><span class="text-muted">Plan:</span><strong><?= htmlspecialchars($mem['plan_name']) ?></strong></div>
                    <div class="d-flex justify-content-between mb-2"><span class="text-muted">Service:</span><strong><?= htmlspecialchars($mem['service_name']) ?></strong></div>
                    <div class="d-flex justify-content-between mb-2"><span class="text-muted">Start:</span><strong><?= $mem['start_date'] ?></strong></div>
                    <div class="d-flex justify-content-between mb-2"><span class="text-muted">Expires:</span><strong><?= $mem['end_date'] ?></strong></div>
                    <div class="d-flex justify-content-between mb-2"><span class="text-muted">Status:</span><span class="badge-status badge-<?= $mem['status'] ?>"><?= $mem['status'] ?></span></div>
                    <div class="d-flex justify-content-between"><span class="text-muted">Amount:</span><strong>$<?= number_format($mem['total_amount'], 2) ?></strong></div>
                <?php else: ?>
                    <div class="empty-state"><i class="fas fa-info-circle"></i><p>No active membership</p></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="col-lg-6 mb-3">
        <div class="row">
            <div class="col-6 mb-3">
                <div class="stat-card warning animate-in delay-1">
                    <div class="stat-label">Notifications</div>
                    <div class="stat-value"><?= $unread_count ?></div>
                    <i class="fas fa-bell stat-icon"></i>
                </div>
            </div>
            <div class="col-6 mb-3">
                <div class="stat-card info animate-in delay-2">
                    <div class="stat-label">Pending Tasks</div>
                    <div class="stat-value"><?= count($todos) ?></div>
                    <i class="fas fa-tasks stat-icon"></i>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        <div class="card-custom animate-in delay-3">
            <div class="card-header"><h5><i class="fas fa-bell me-2"></i>Notifications</h5></div>
            <div class="card-body" style="max-height: 200px; overflow-y: auto;">
                <?php
                $notifs = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY sent_at DESC LIMIT 5");
                $notifs->execute([$uid]); $notifs = $notifs->fetchAll();
                if (empty($notifs)): ?>
                    <p class="text-muted mb-0">No notifications</p>
                <?php else: foreach ($notifs as $n): ?>
                    <div class="d-flex align-items-start mb-2 pb-2 border-bottom">
                        <i class="fas fa-<?= $n['type'] === 'payment_due' ? 'exclamation-circle text-danger' : 'info-circle text-info' ?> me-2 mt-1"></i>
                        <div><small class="text-muted"><?= date('M d', strtotime($n['sent_at'])) ?></small><br><?= htmlspecialchars($n['message']) ?></div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Announcements & Todos -->
<div class="row">
    <div class="col-lg-6 mb-3">
        <div class="card-custom">
            <div class="card-header"><h5><i class="fas fa-bullhorn me-2"></i>Announcements</h5></div>
            <div class="card-body">
                <?php if (empty($announcements)): ?>
                    <p class="text-muted">No announcements</p>
                <?php else: foreach ($announcements as $a): ?>
                    <div class="mb-3 pb-3 border-bottom">
                        <h6 class="mb-1"><?= htmlspecialchars($a['title']) ?></h6>
                        <small class="text-muted"><?= date('M d, Y', strtotime($a['applied_date'])) ?></small>
                        <p class="mb-0 mt-1" style="font-size: 0.9rem;"><?= htmlspecialchars($a['message']) ?></p>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-3">
        <div class="card-custom">
            <div class="card-header"><h5><i class="fas fa-tasks me-2"></i>Upcoming Tasks</h5></div>
            <div class="card-body">
                <?php if (empty($todos)): ?>
                    <p class="text-muted">No pending tasks. <a href="todo.php">Add one!</a></p>
                <?php else: foreach ($todos as $t): ?>
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <span><?= htmlspecialchars($t['task']) ?></span>
                        <small class="text-muted"><?= $t['due_date'] ? date('M d', strtotime($t['due_date'])) : '' ?></small>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
