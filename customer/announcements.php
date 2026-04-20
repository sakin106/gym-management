<?php
$page_title = 'Announcements';
require_once __DIR__ . '/../config/db.php';
check_role('customer');

$announcements = $pdo->query("SELECT a.*, u.full_name FROM announcements a JOIN users u ON a.posted_by = u.user_id WHERE a.status = 'active' ORDER BY a.applied_date DESC")->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<h4 class="mb-4"><i class="fas fa-bullhorn me-2"></i>Announcements</h4>
<?php if (empty($announcements)): ?>
    <div class="card-custom"><div class="card-body"><div class="empty-state"><i class="fas fa-bullhorn"></i><p>No announcements at this time</p></div></div></div>
<?php else: foreach ($announcements as $a): ?>
    <div class="card-custom mb-3 animate-in">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <h5 class="mb-1"><?= htmlspecialchars($a['title']) ?></h5>
                <small class="text-muted"><?= date('M d, Y', strtotime($a['applied_date'])) ?></small>
            </div>
            <p class="mb-1"><?= nl2br(htmlspecialchars($a['message'])) ?></p>
            <small class="text-muted">Posted by <?= htmlspecialchars($a['full_name']) ?></small>
        </div>
    </div>
<?php endforeach; endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
