<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// If already logged in, redirect
if (is_logged_in()) {
    header("Location: /gym_management/" . $_SESSION['role'] . "/dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] !== 'active') {
                $error = 'Your account is ' . $user['status'] . '. Please contact admin.';
            } else {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['status'] = $user['status'];
                $_SESSION['login_time'] = time();

                // Redirect based on role
                switch ($user['role']) {
                    case 'admin':
                        header("Location: /gym_management/admin/dashboard.php");
                        break;
                    case 'staff':
                        header("Location: /gym_management/staff/dashboard.php");
                        break;
                    case 'customer':
                        header("Location: /gym_management/customer/dashboard.php");
                        break;
                }
                exit();
            }
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymPro — Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="/gym_management/assets/css/custom.css" rel="stylesheet">
</head>
<body>
<div class="login-wrapper">
    <div class="login-card animate-in">
        <div class="brand-icon">
            <i class="fas fa-dumbbell"></i>
        </div>
        <h2>Welcome Back</h2>
        <p class="login-subtitle">Sign in to GymPro Management System</p>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <div class="mb-3">
                <label for="email" class="form-label"><i class="fas fa-envelope me-1"></i> Email Address</label>
                <input type="email" class="form-control form-control-lg" id="email" name="email"
                       placeholder="admin@gym.com" value="<?= htmlspecialchars($email ?? '') ?>" required>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label"><i class="fas fa-lock me-1"></i> Password</label>
                <input type="password" class="form-control form-control-lg" id="password" name="password"
                       placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-accent w-100 py-3" style="font-size: 1rem;">
                <i class="fas fa-sign-in-alt me-2"></i> Sign In
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="register.php" class="text-muted" style="font-size: 0.9rem;">
                New member? <span style="color: var(--accent);">Register here</span>
            </a>
        </div>

        <div class="text-center mt-3">
            <small class="text-muted">Demo Logins — admin@gym.com / staff@gym.com / john@email.com (pass: password123)</small>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
