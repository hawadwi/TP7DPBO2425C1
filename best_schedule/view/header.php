<?php
// view/header.php
// Pastikan $current_page di-set sebelum include header, misal $current_page = 'reminders';
?>

<aside class="sidebar">
    <div class="sidebar-header">
        <h1 class="brand-title">Best Schedule</h1>
    </div>

    <nav class="sidebar-menu">
        <a href="../index.php" class="menu-item <?= ($current_page == 'dashboard') ? 'active' : '' ?>">
            <img src="../images/dashboard.png" alt="Dashboard" class="menu-icon">
            <span class="menu-text">Dashboard</span>
        </a>
        <a href="view/schedules.php" class="menu-item <?= ($current_page == 'schedules') ? 'active' : '' ?>">
            <img src="../images/schedule.png" alt="Schedule" class="menu-icon">
            <span class="menu-text">Schedule</span>
        </a>
        <a href="view/reminders.php" class="menu-item <?= ($current_page == 'reminders') ? 'active' : '' ?>">
            <img src="../images/reminder.png" alt="Reminder" class="menu-icon">
            <span class="menu-text">Reminder</span>
        </a>
        <a href="view/users.php" class="menu-item <?= ($current_page == 'users') ? 'active' : '' ?>">
            <img src="../images/user.png" alt="User" class="menu-icon">
            <span class="menu-text">Manage Users</span>
        </a>
    </nav>
</aside>