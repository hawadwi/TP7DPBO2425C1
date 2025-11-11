<?php
session_start();
require_once '../config/database.php';
require_once '../class/user.php';

$userObj = new User();
$users = $userObj->getAllUsers();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Schedule App</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="sidebar-header">
                <h1 class="brand-title">Best Schedule</h1>
            </div>
            <nav class="sidebar-menu">
                <a href="../index.php" class="menu-item">
                    <img src="../images/dashboard.png" alt="Dashboard" class="menu-icon">
                    <span class="menu-text">Dashboard</span>
                </a>
                <a href="schedules.php" class="menu-item">
                    <img src="../images/schedule.png" alt="Schedule" class="menu-icon">
                    <span class="menu-text">Schedule</span>
                </a>
                <a href="reminders.php" class="menu-item">
                    <img src="../images/reminder.png" alt="Reminder" class="menu-icon">
                    <span class="menu-text">Reminder</span>
                </a>
                <a href="users.php" class="menu-item active">
                    <img src="../images/user.png" alt="User" class="menu-icon">
                    <span class="menu-text">Manage Users</span>
                </a>
            </nav>
        </div>

        <div class="main-content">
            <div class="schedules-header">
                <div class="schedules-title-section">
                    <h1 class="page-title">Manage Users</h1>
                </div>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success']; ?>
                <?php unset($_SESSION['success']); ?>
            </div>
            <?php endif; ?>

            <div class="reminder-filter-section">
                <div class="filter-group">
                    <label class="filter-label">Search User</label>
                    <input type="text" class="search-input" placeholder="Search by name or email">
                </div>
                <a href="addUsers.php" class="btn-add-user">+ Tambah User</a>
            </div>

            

            <div class="reminders-grid">
                <?php if (empty($users)): ?>
                <div class="empty-state">
                    <p>Tidak ada user. <a href="addUsers.php">Tambah user baru</a></p>
                </div>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                    <div class="reminder-card">
                        <div class="reminder-card-header">
                            <h3 class="reminder-card-title"><?= htmlspecialchars($user['nama']); ?></h3>
                            <span class="reminder-badge badge-success">User</span>
                        </div>
                        <div class="reminder-card-body">
                            <p class="reminder-location">Email: <?= htmlspecialchars($user['email']); ?></p>
                            <p class="reminder-location">Role: <?= htmlspecialchars($user['instansi']); ?></p>
                        </div>
                        <div class="reminder-card-footer">
                            <a href="editUser.php?id=<?= $user['id_user']; ?>" class="btn-edit-reminder">Edit</a>
                            <a href="deleteUser.php?id=<?= $user['id_user']; ?>" class="btn-delete-reminder" onclick="return confirm('Yakin ingin menghapus user ini?')">Delete</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php include 'footer.php'; ?>
    </div>
</body>
</html>