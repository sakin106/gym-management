<?php
$page_title = 'Manage Members';
require_once __DIR__ . '/../config/db.php';
check_role('admin');

// Handle actions
if (isset($_GET['approve'])) {
    $pdo->prepare("UPDATE users SET status = 'active' WHERE user_id = ? AND status = 'pending'")->execute([(int)$_GET['approve']]);
    set_flash('success', 'Member approved successfully.');
    header("Location: members.php"); exit();
}
if (isset($_GET['reject'])) {
    $pdo->prepare("DELETE FROM users WHERE user_id = ? AND status = 'pending'")->execute([(int)$_GET['reject']]);
    set_flash('success', 'Registration rejected.');
    header("Location: members.php"); exit();
}
if (isset($_GET['freeze'])) {
    $pdo->prepare("UPDATE users SET status = 'frozen' WHERE user_id = ?")->execute([(int)$_GET['freeze']]);
    $pdo->prepare("UPDATE member_memberships SET status = 'frozen' WHERE user_id = ? AND status = 'active'")->execute([(int)$_GET['freeze']]);
    set_flash('info', 'Membership frozen.');
    header("Location: members.php"); exit();
}
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM users WHERE user_id = ? AND role = 'customer'")->execute([(int)$_GET['delete']]);
    set_flash('success', 'Member deleted.');
    header("Location: members.php"); exit();
}

// Handle add member form (admin direct registration)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_member'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone'] ?? '');
    $gender = $_POST['gender'] ?? null;
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $plan_id = (int)$_POST['plan_id'];
    $service_id = (int)$_POST['service_id'];

    // Check duplicate
    $check = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $check->execute([$email]);
    if ($check->fetch()) {
        set_flash('danger', 'Email already exists.');
    } else {
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, phone, gender, role, status) VALUES (?, ?, ?, ?, ?, 'customer', 'active')");
            $stmt->execute([$full_name, $email, $password, $phone, $gender]);
            $user_id = $pdo->lastInsertId();

            // Get plan & service details
            $plan = $pdo->prepare("SELECT * FROM membership_plans WHERE plan_id = ?");
            $plan->execute([$plan_id]);
            $plan = $plan->fetch();

            $service = $pdo->prepare("SELECT * FROM services WHERE service_id = ?");
            $service->execute([$service_id]);
            $service = $service->fetch();

            $total = $plan['price'] + ($service['monthly_charge'] * $plan['duration_months']);
            $start = date('Y-m-d');
            $end = date('Y-m-d', strtotime("+{$plan['duration_months']} months"));

            $stmt = $pdo->prepare("INSERT INTO member_memberships (user_id, plan_id, service_id, start_date, end_date, status, total_amount, registered_by) VALUES (?, ?, ?, ?, ?, 'active', ?, 'admin')");
            $stmt->execute([$user_id, $plan_id, $service_id, $start, $end, $total]);

            $pdo->commit();
            set_flash('success', 'Member added successfully.');
        } catch (Exception $e) {
            $pdo->rollBack();
            set_flash('danger', 'Error: ' . $e->getMessage());
        }
    }
    header("Location: members.php"); exit();
}

// Fetch members
$members = $pdo->query("
    SELECT u.*, mm.plan_id, mm.service_id, mm.start_date, mm.end_date, mm.status as mem_status,
           mp.plan_name, s.service_name
    FROM users u
    LEFT JOIN member_memberships mm ON u.user_id = mm.user_id
    LEFT JOIN membership_plans mp ON mm.plan_id = mp.plan_id
    LEFT JOIN services s ON mm.service_id = s.service_id
    WHERE u.role = 'customer'
    ORDER BY u.created_at DESC
")->fetchAll();

$plans = $pdo->query("SELECT * FROM membership_plans WHERE status = 'active'")->fetchAll();
$services = $pdo->query("SELECT * FROM services WHERE status = 'active'")->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h4><i class="fas fa-users me-2"></i>Members</h4>
    <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#addMemberModal">
        <i class="fas fa-plus me-1"></i> Add Member
    </button>
</div>

<div class="card-custom">
    <div class="card-body">
        <table class="table table-custom datatable">
            <thead>
                <tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Plan</th><th>Service</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php foreach ($members as $i => $m): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($m['full_name']) ?></td>
                    <td><?= htmlspecialchars($m['email']) ?></td>
                    <td><?= htmlspecialchars($m['phone'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($m['plan_name'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($m['service_name'] ?? 'N/A') ?></td>
                    <td><span class="badge-status badge-<?= $m['status'] ?>"><?= $m['status'] ?></span></td>
                    <td>
                        <?php if ($m['status'] === 'pending'): ?>
                            <a href="?approve=<?= $m['user_id'] ?>" class="btn btn-success btn-sm" title="Approve"><i class="fas fa-check"></i></a>
                            <a href="?reject=<?= $m['user_id'] ?>" class="btn btn-warning btn-sm" title="Reject"><i class="fas fa-times"></i></a>
                        <?php endif; ?>
                        <?php if ($m['status'] === 'active'): ?>
                            <a href="?freeze=<?= $m['user_id'] ?>" class="btn btn-info btn-sm" title="Freeze"><i class="fas fa-snowflake"></i></a>
                        <?php endif; ?>
                        <button class="btn btn-danger btn-sm" onclick="confirmDelete('?delete=<?= $m['user_id'] ?>', '<?= htmlspecialchars($m['full_name']) ?>')"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="add_member" value="1">
                <div class="modal-header"><h5 class="modal-title">Add New Member</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="">Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Password *</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Membership Plan *</label>
                            <select name="plan_id" class="form-select" required>
                                <option value="">Select Plan</option>
                                <?php foreach ($plans as $p): ?>
                                    <option value="<?= $p['plan_id'] ?>"><?= htmlspecialchars($p['plan_name']) ?> — $<?= $p['price'] ?> (<?= $p['duration_months'] ?> months)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Service *</label>
                            <select name="service_id" class="form-select" required>
                                <option value="">Select Service</option>
                                <?php foreach ($services as $s): ?>
                                    <option value="<?= $s['service_id'] ?>"><?= htmlspecialchars($s['service_name']) ?> — $<?= $s['monthly_charge'] ?>/mo</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-accent">Add Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
