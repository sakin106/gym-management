<?php
$page_title = 'Diet Plans';
require_once __DIR__ . '/../config/db.php';
check_role('customer');
$uid = $_SESSION['user_id'];

$plans = $pdo->prepare("SELECT dp.*, u.full_name as trainer_name FROM diet_plans dp LEFT JOIN trainers t ON dp.trainer_id = t.trainer_id LEFT JOIN users u ON t.user_id = u.user_id WHERE dp.member_id = ? ORDER BY dp.start_date DESC");
$plans->execute([$uid]); $plans = $plans->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<h4 class="mb-4"><i class="fas fa-utensils me-2"></i>My Diet Plans</h4>
<?php if (empty($plans)): ?>
    <div class="card-custom"><div class="card-body"><div class="empty-state"><i class="fas fa-utensils"></i><p>No diet plans assigned yet</p></div></div></div>
<?php else: foreach ($plans as $p):
    $items = $pdo->prepare("SELECT * FROM diet_items WHERE diet_id = ? ORDER BY FIELD(meal_type,'breakfast','lunch','snack','dinner')");
    $items->execute([$p['diet_id']]); $items = $items->fetchAll();
?>
    <div class="card-custom mb-3">
        <div class="card-header">
            <h5><?= htmlspecialchars($p['plan_name']) ?> <small class="text-muted">(<?= $p['calorie_target'] ?> kcal/day)</small></h5>
            <small class="text-muted">Trainer: <?= htmlspecialchars($p['trainer_name'] ?? 'N/A') ?> | <?= $p['start_date'] ?> to <?= $p['end_date'] ?? 'Ongoing' ?></small>
        </div>
        <div class="card-body">
            <?php if (!empty($items)): ?>
            <table class="table table-sm">
                <thead><tr><th>Meal</th><th>Food</th><th>Qty</th><th>Calories</th><th>Protein</th><th>Carbs</th><th>Fat</th></tr></thead>
                <tbody>
                <?php foreach ($items as $it): ?>
                <tr><td><span class="badge bg-<?= ['breakfast'=>'warning','lunch'=>'success','dinner'=>'primary','snack'=>'info'][$it['meal_type']] ?? 'secondary' ?>"><?= $it['meal_type'] ?></span></td>
                <td><?= htmlspecialchars($it['food_name']) ?></td><td><?= htmlspecialchars($it['quantity'] ?? '-') ?></td><td><?= $it['calories'] ?></td><td><?= $it['protein'] ?>g</td><td><?= $it['carbs'] ?>g</td><td><?= $it['fat'] ?>g</td></tr>
                <?php endforeach; ?></tbody></table>
            <?php else: ?><p class="text-muted">No diet items added yet</p><?php endif; ?>
        </div>
    </div>
<?php endforeach; endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
