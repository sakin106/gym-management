<?php
$page_title = 'Payments';
require_once __DIR__ . '/../config/db.php';
check_role('admin');

// Handle manual payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_payment'])) {
    $membership_id = (int)$_POST['membership_id'];
    $user_id = (int)$_POST['user_id'];
    $amount = (float)$_POST['amount'];
    $method = $_POST['method'];
    $discount_id = !empty($_POST['discount_id']) ? (int)$_POST['discount_id'] : null;
    $discount_applied = 0;

    try {
        $pdo->beginTransaction();
        if ($discount_id) {
            $d = $pdo->prepare("SELECT * FROM discounts WHERE discount_id = ? AND status = 'active' AND valid_from <= CURDATE() AND valid_until >= CURDATE() AND used_count < max_uses");
            $d->execute([$discount_id]);
            $disc = $d->fetch();
            if ($disc) {
                $discount_applied = round($amount * ($disc['percentage'] / 100), 2);
                $amount -= $discount_applied;
                $pdo->prepare("UPDATE discounts SET used_count = used_count + 1 WHERE discount_id = ?")->execute([$discount_id]);
            }
        }
        $receipt_no = 'RCP-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $stmt = $pdo->prepare("INSERT INTO payments (membership_id, user_id, amount, payment_date, method, status, receipt_no, processed_by, discount_applied) VALUES (?, ?, ?, CURDATE(), ?, 'paid', ?, ?, ?)");
        $stmt->execute([$membership_id, $user_id, $amount, $method, $receipt_no, $_SESSION['user_id'], $discount_applied]);
        $pdo->prepare("UPDATE member_memberships SET status = 'active' WHERE membership_id = ?")->execute([$membership_id]);
        $pdo->prepare("UPDATE users SET status = 'active' WHERE user_id = ?")->execute([$user_id]);
        $pdo->commit();
        set_flash('success', "Payment processed. Receipt: $receipt_no");
    } catch (Exception $e) {
        $pdo->rollBack();
        set_flash('danger', 'Payment failed: ' . $e->getMessage());
    }
    header("Location: payments.php"); exit();
}

$payments = $pdo->query("
    SELECT p.*, u.full_name, proc.full_name as processed_name
    FROM payments p
    JOIN users u ON p.user_id = u.user_id
    LEFT JOIN users proc ON p.processed_by = proc.user_id
    ORDER BY p.created_at DESC
")->fetchAll();

$pending_memberships = $pdo->query("
    SELECT mm.*, u.full_name, u.user_id, mp.plan_name, mp.price, s.service_name, s.monthly_charge, mp.duration_months
    FROM member_memberships mm
    JOIN users u ON mm.user_id = u.user_id
    JOIN membership_plans mp ON mm.plan_id = mp.plan_id
    JOIN services s ON mm.service_id = s.service_id
    WHERE mm.status = 'pending'
")->fetchAll();

$discounts = $pdo->query("SELECT * FROM discounts WHERE status = 'active' AND valid_until >= CURDATE() AND used_count < max_uses")->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h4><i class="fas fa-credit-card me-2"></i>Payments</h4>
    <?php if (!empty($pending_memberships)): ?>
    <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#paymentModal"><i class="fas fa-plus me-1"></i> Process Payment</button>
    <?php endif; ?>
</div>

<div class="card-custom">
    <div class="card-body">
        <table class="table table-custom datatable">
            <thead><tr><th>Receipt #</th><th>Member</th><th>Amount</th><th>Method</th><th>Discount</th><th>Status</th><th>Date</th><th>By</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($payments as $p): ?>
                <tr>
                    <td><code><?= htmlspecialchars($p['receipt_no']) ?></code></td>
                    <td><?= htmlspecialchars($p['full_name']) ?></td>
                    <td>$<?= number_format($p['amount'], 2) ?></td>
                    <td><span class="badge bg-secondary"><?= $p['method'] ?></span></td>
                    <td>$<?= number_format($p['discount_applied'], 2) ?></td>
                    <td><span class="badge-status badge-<?= $p['status'] ?>"><?= $p['status'] ?></span></td>
                    <td><?= $p['payment_date'] ?></td>
                    <td><?= htmlspecialchars($p['processed_name'] ?? '-') ?></td>
                    <td><a href="receipt.php?id=<?= $p['payment_id'] ?>" class="btn btn-sm btn-outline-primary" target="_blank"><i class="fas fa-file-pdf"></i></a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Process Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="process_payment" value="1">
                <div class="modal-header"><h5 class="modal-title">Process Payment</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Membership</label>
                        <select name="membership_id" class="form-select" id="membershipSelect" required>
                            <option value="">Select pending membership</option>
                            <?php foreach ($pending_memberships as $pm): ?>
                                <option value="<?= $pm['membership_id'] ?>" data-user="<?= $pm['user_id'] ?>" data-amount="<?= $pm['total_amount'] ?>">
                                    <?= htmlspecialchars($pm['full_name']) ?> — <?= $pm['plan_name'] ?> ($<?= $pm['total_amount'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <input type="hidden" name="user_id" id="paymentUserId">
                    <div class="mb-3">
                        <label class="form-label">Amount ($)</label>
                        <input type="number" name="amount" id="paymentAmount" class="form-control" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="method" class="form-select">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="mobile">Mobile Banking</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Discount Code (Optional)</label>
                        <select name="discount_id" class="form-select">
                            <option value="">No Discount</option>
                            <?php foreach ($discounts as $d): ?>
                                <option value="<?= $d['discount_id'] ?>"><?= $d['code'] ?> (<?= $d['percentage'] ?>%)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-accent">Process Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('membershipSelect')?.addEventListener('change', function() {
    var opt = this.options[this.selectedIndex];
    document.getElementById('paymentUserId').value = opt.dataset.user || '';
    document.getElementById('paymentAmount').value = opt.dataset.amount || '';
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
