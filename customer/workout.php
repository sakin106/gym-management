<?php
$page_title = 'Workout Plans';
require_once __DIR__ . '/../config/db.php';
check_role('customer');
$uid = $_SESSION['user_id'];

$plans = $pdo->prepare("SELECT wp.*, u.full_name as trainer_name FROM workout_plans wp LEFT JOIN trainers t ON wp.trainer_id = t.trainer_id LEFT JOIN users u ON t.user_id = u.user_id WHERE wp.member_id = ? ORDER BY wp.start_date DESC");
$plans->execute([$uid]); $plans = $plans->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<h4 class="mb-4"><i class="fas fa-dumbbell me-2"></i>My Workout Plans</h4>
<?php if (empty($plans)): ?>
    <div class="card-custom"><div class="card-body"><div class="empty-state"><i class="fas fa-dumbbell"></i><p>No workout plans assigned yet</p></div></div></div>
<?php else: foreach ($plans as $p):
    $exercises = $pdo->prepare("SELECT * FROM workout_exercises WHERE plan_id = ? ORDER BY FIELD(day_of_week,'Mon','Tue','Wed','Thu','Fri','Sat','Sun')");
    $exercises->execute([$p['plan_id']]); $exercises = $exercises->fetchAll();
?>
    <div class="card-custom mb-3">
        <div class="card-header">
            <h5><?= htmlspecialchars($p['plan_name']) ?></h5>
            <small class="text-muted">Trainer: <?= htmlspecialchars($p['trainer_name'] ?? 'N/A') ?> | <?= $p['start_date'] ?> to <?= $p['end_date'] ?? 'Ongoing' ?></small>
        </div>
        <div class="card-body">
            <?php if ($p['goal']): ?><p><strong>Goal:</strong> <?= htmlspecialchars($p['goal']) ?></p><?php endif; ?>
            <?php if (!empty($exercises)): ?>
            <table class="table table-sm">
                <thead><tr><th>Exercise</th><th>Sets</th><th>Reps</th><th>Duration</th><th>Day</th></tr></thead>
                <tbody>
                <?php foreach ($exercises as $e): ?>
                <tr><td><?= htmlspecialchars($e['exercise_name']) ?></td><td><?= $e['sets'] ?></td><td><?= $e['reps'] ?></td><td><?= $e['duration_minutes'] ? $e['duration_minutes'].'min' : '-' ?></td><td><span class="badge bg-secondary"><?= $e['day_of_week'] ?></span></td></tr>
                <?php endforeach; ?></tbody></table>
            <?php else: ?><p class="text-muted">No exercises added yet</p><?php endif; ?>
        </div>
    </div>
<?php endforeach; endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
