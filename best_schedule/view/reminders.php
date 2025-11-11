<?php
session_start();
require_once '../config/database.php';
require_once '../class/reminder.php';

$reminderObj = new Reminder();

// Tangkap filter period
$period = isset($_GET['period']) ? $_GET['period'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// PROSES SEND
if (isset($_GET['send'])) {
    $id = intval($_GET['send']);
    $reminderObj->updateStatus($id, "terkirim");
    $_SESSION['success'] = "Reminder berhasil dikirim!";
    header("Location: reminders.php?period=$period&search=" . urlencode($search));
    exit;
}

// PROSES DELETE
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $reminderObj->deleteReminder($id);
    $_SESSION['success'] = "Reminder berhasil dihapus!";
    header("Location: reminders.php?period=$period&search=" . urlencode($search));
    exit;
}

// Ambil reminder sesuai filter
switch($period){
    case 'today':
        $reminders = $reminderObj->getRemindersByDate(date('Y-m-d'));
        break;
    case 'week':
        $reminders = $reminderObj->getRemindersThisWeek();
        break;
    case 'month':
        $reminders = $reminderObj->getRemindersThisMonth();
        break;
    default:
        $reminders = $reminderObj->getAllReminders();
}

// Filter search (nama/judul reminder)
if (!empty($search)) {
    $reminders = array_filter($reminders, function($r) use ($search){
        return str_contains(strtolower($r['pesan']), strtolower($search));
    });
}

// Hitung status
$terkirim_count = 0;
$tertunda_count = 0;
foreach ($reminders as $r) {
    if ($r['status'] === 'terkirim') $terkirim_count++;
    if ($r['status'] === 'tertunda') $tertunda_count++;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reminders - Schedule App</title>
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
            <a href="reminders.php" class="menu-item active">
                <img src="../images/reminder.png" alt="Reminder" class="menu-icon">
                <span class="menu-text">Reminder</span>
            </a>
            <a href="users.php" class="menu-item">
                <img src="../images/user.png" alt="User" class="menu-icon">
                <span class="menu-text">Manage Users</span>
            </a>
        </nav>
    </div>

    <div class="main-content">

        <!-- Header -->
        <div class="schedules-header">
            <div class="schedules-title-section">
                <h1 class="page-title">Reminder</h1>
            </div>
        </div>

        <!-- Notif -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success'] ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <!-- Status cards -->
        <div class="status-cards-container">
            <div class="status-card">
                <div class="status-card-title">Terkirim</div>
                <div class="status-card-number" style="color: #6b4fb8;"><?= $terkirim_count ?></div>
                <div class="status-card-subtitle">Pengingat Sudah Dikirim</div>
            </div>
            <div class="status-card">
                <div class="status-card-title">Tertunda</div>
                <div class="status-card-number" style="color: #22c55e;"><?= $tertunda_count ?></div>
                <div class="status-card-subtitle">Pengingat Belum Terkirim</div>
            </div>
        </div>

        <!-- Filter -->
        <form method="GET" class="reminder-filter-section">
            <div class="filter-group">
                <label class="filter-label">Search Activity</label>
                <input type="text" class="search-input" name="search" placeholder="Search Activity" value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="filter-group">
                <label class="filter-label">Period</label>
                <select name="period" class="filter-select" onchange="this.form.submit()">
                    <option value="all" <?= $period=='all' ? 'selected' : '' ?>>All</option>
                    <option value="today" <?= $period=='today' ? 'selected' : '' ?>>Today</option>
                    <option value="week" <?= $period=='week' ? 'selected' : '' ?>>This Week</option>
                    <option value="month" <?= $period=='month' ? 'selected' : '' ?>>This Month</option>
                </select>
            </div>
            <div class="filter-group">
                <button type="submit" class="btn-add-reminder">Filter</button>
            </div>
        </form>

        <a href="addReminder.php" class="btn-add-reminder">+ Tambah Reminder</a>

        <!-- Grid tampil reminders -->
        <div class="reminders-grid">
            <?php if (empty($reminders)): ?>
                <div class="empty-state">
                    <p>Tidak ada reminder. <a href="addReminder.php">Buat yang baru</a></p>
                </div>
            <?php else: ?>
                <?php foreach ($reminders as $r): ?>
                    <div class="reminder-card">
                        <div class="reminder-card-header">
                            <h3 class="reminder-card-title"><?= htmlspecialchars($r['pesan']) ?></h3>
                            <span class="reminder-badge <?= $r['status'] === 'terkirim' ? 'badge-success' : 'badge-pending' ?>">
                                <?= ucfirst($r['status']) ?>
                            </span>
                        </div>
                        <div class="reminder-card-body">
                            <div class="reminder-date-info">
                                📅 <?= date('d F Y H:i', strtotime($r['tanggal_kirim'])) ?>
                            </div>
                        </div>
                        <div class="reminder-card-footer">
                            <a href="editReminder.php?id=<?= $r['id_reminder'] ?>" class="btn-edit-reminder">Edit</a>
                            <a href="reminders.php?send=<?= $r['id_reminder'] ?>" class="btn-send-reminder">Kirim</a>
                            <a href="reminders.php?delete=<?= $r['id_reminder'] ?>" onclick="return confirm('Yakin ingin menghapus?')" class="btn-delete-reminder">Hapus</a>
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