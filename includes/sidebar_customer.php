<!-- Customer Sidebar -->
<nav class="sidebar">
    <div class="sidebar-brand">
        <h3><i class="fas fa-dumbbell"></i> GymPro</h3>
        <small>Member Panel</small>
    </div>
    <ul class="sidebar-menu">
        <li><a href="/gym_management/customer/dashboard.php" class="<?= $current_page === 'dashboard' ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="/gym_management/customer/profile.php" class="<?= $current_page === 'profile' ? 'active' : '' ?>"><i class="fas fa-user"></i> My Profile</a></li>
        <li><a href="/gym_management/customer/todo.php" class="<?= $current_page === 'todo' ? 'active' : '' ?>"><i class="fas fa-tasks"></i> To-Do List</a></li>
        <li><a href="/gym_management/customer/progress.php" class="<?= $current_page === 'progress' ? 'active' : '' ?>"><i class="fas fa-chart-line"></i> My Progress</a></li>
        <li><a href="/gym_management/customer/workout.php" class="<?= $current_page === 'workout' ? 'active' : '' ?>"><i class="fas fa-dumbbell"></i> Workout Plans</a></li>
        <li><a href="/gym_management/customer/diet.php" class="<?= $current_page === 'diet' ? 'active' : '' ?>"><i class="fas fa-utensils"></i> Diet Plans</a></li>
        <li><a href="/gym_management/customer/announcements.php" class="<?= $current_page === 'announcements' ? 'active' : '' ?>"><i class="fas fa-bullhorn"></i> Announcements</a></li>
        <div class="sidebar-divider"></div>
        <li><a href="/gym_management/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</nav>
