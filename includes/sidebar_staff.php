<!-- Staff Sidebar -->
<nav class="sidebar">
    <div class="sidebar-brand">
        <h3><i class="fas fa-dumbbell"></i> GymPro</h3>
        <small>Staff Panel</small>
    </div>
    <ul class="sidebar-menu">
        <li><a href="/gym_management/staff/dashboard.php" class="<?= $current_page === 'dashboard' ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="/gym_management/staff/members.php" class="<?= $current_page === 'members' ? 'active' : '' ?>"><i class="fas fa-users"></i> Members</a></li>
        <li><a href="/gym_management/staff/attendance.php" class="<?= $current_page === 'attendance' ? 'active' : '' ?>"><i class="fas fa-clipboard-check"></i> Attendance</a></li>
        <li><a href="/gym_management/staff/payments.php" class="<?= $current_page === 'payments' ? 'active' : '' ?>"><i class="fas fa-credit-card"></i> Payments</a></li>
        <div class="sidebar-divider"></div>
        <li><a href="/gym_management/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</nav>
