<!-- Admin Sidebar -->
<nav class="sidebar">
    <div class="sidebar-brand">
        <h3><i class="fas fa-dumbbell"></i> GymPro</h3>
        <small>Admin Panel</small>
    </div>
    <ul class="sidebar-menu">
        <li><a href="/gym_management/admin/dashboard.php" class="<?= $current_page === 'dashboard' ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>

        <div class="sidebar-heading">Members</div>
        <li><a href="/gym_management/admin/members.php" class="<?= $current_page === 'members' ? 'active' : '' ?>"><i class="fas fa-users"></i> Members</a></li>
        <li><a href="/gym_management/admin/staff.php" class="<?= $current_page === 'staff' ? 'active' : '' ?>"><i class="fas fa-user-tie"></i> Staff</a></li>
        <li><a href="/gym_management/admin/trainers.php" class="<?= $current_page === 'trainers' ? 'active' : '' ?>"><i class="fas fa-running"></i> Trainers</a></li>
        <li><a href="/gym_management/admin/trainer_assignments.php" class="<?= $current_page === 'trainer_assignments' ? 'active' : '' ?>"><i class="fas fa-user-check"></i> Assignments</a></li>

        <div class="sidebar-divider"></div>
        <div class="sidebar-heading">Operations</div>
        <li><a href="/gym_management/admin/attendance.php" class="<?= $current_page === 'attendance' ? 'active' : '' ?>"><i class="fas fa-clipboard-check"></i> Attendance</a></li>
        <li><a href="/gym_management/admin/payments.php" class="<?= $current_page === 'payments' ? 'active' : '' ?>"><i class="fas fa-credit-card"></i> Payments</a></li>
        <li><a href="/gym_management/admin/services.php" class="<?= $current_page === 'services' ? 'active' : '' ?>"><i class="fas fa-concierge-bell"></i> Services</a></li>
        <li><a href="/gym_management/admin/plans.php" class="<?= $current_page === 'plans' ? 'active' : '' ?>"><i class="fas fa-clipboard-list"></i> Plans</a></li>
        <li><a href="/gym_management/admin/discounts.php" class="<?= $current_page === 'discounts' ? 'active' : '' ?>"><i class="fas fa-tags"></i> Discounts</a></li>

        <div class="sidebar-divider"></div>
        <div class="sidebar-heading">Fitness</div>
        <li><a href="/gym_management/admin/classes.php" class="<?= $current_page === 'classes' ? 'active' : '' ?>"><i class="fas fa-calendar-alt"></i> Classes</a></li>
        <li><a href="/gym_management/admin/class_enrollments.php" class="<?= $current_page === 'class_enrollments' ? 'active' : '' ?>"><i class="fas fa-user-plus"></i> Enrollments</a></li>
        <li><a href="/gym_management/admin/workout_plans.php" class="<?= $current_page === 'workout_plans' ? 'active' : '' ?>"><i class="fas fa-dumbbell"></i> Workout Plans</a></li>
        <li><a href="/gym_management/admin/diet_plans.php" class="<?= $current_page === 'diet_plans' ? 'active' : '' ?>"><i class="fas fa-utensils"></i> Diet Plans</a></li>
        <li><a href="/gym_management/admin/progress.php" class="<?= $current_page === 'progress' ? 'active' : '' ?>"><i class="fas fa-chart-line"></i> Progress</a></li>

        <div class="sidebar-divider"></div>
        <div class="sidebar-heading">Facility</div>
        <li><a href="/gym_management/admin/equipment.php" class="<?= $current_page === 'equipment' ? 'active' : '' ?>"><i class="fas fa-cogs"></i> Equipment</a></li>
        <li><a href="/gym_management/admin/maintenance.php" class="<?= $current_page === 'maintenance' ? 'active' : '' ?>"><i class="fas fa-wrench"></i> Maintenance</a></li>

        <div class="sidebar-divider"></div>
        <div class="sidebar-heading">Communication</div>
        <li><a href="/gym_management/admin/announcements.php" class="<?= $current_page === 'announcements' ? 'active' : '' ?>"><i class="fas fa-bullhorn"></i> Announcements</a></li>
        <li><a href="/gym_management/admin/notifications.php" class="<?= $current_page === 'notifications' ? 'active' : '' ?>"><i class="fas fa-bell"></i> Notifications</a></li>
        <li><a href="/gym_management/admin/reports.php" class="<?= $current_page === 'reports' ? 'active' : '' ?>"><i class="fas fa-chart-bar"></i> Reports</a></li>

        <div class="sidebar-divider"></div>
        <li><a href="/gym_management/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</nav>
