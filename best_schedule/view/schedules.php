<?php
require_once __DIR__ . '/../config/database.php';

$schedules = [];
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$selected_status = isset($_GET['status']) ? $_GET['status'] : 'semua';

try {
    $db = new Database();
    $conn = $db->conn;

    // === Ambil data user aktif ===
    $user_stmt = $conn->prepare("SELECT * FROM user LIMIT 1");
    $user_stmt->execute();
    $current_user = $user_stmt->fetch(PDO::FETCH_ASSOC) ?: ['nama' => 'User', 'instansi' => 'Student'];

    // === Ambil daftar jadwal berdasarkan filter ===
    $query = "
        SELECT s.*, u.nama AS user_name 
        FROM schedule s
        LEFT JOIN user u ON s.id_user = u.id_user
    ";

    $params = [];
    if ($selected_status !== 'semua') {
        $query .= " WHERE s.status = :status ";
        $params[':status'] = strtolower($selected_status);
    }

    $query .= " ORDER BY s.tanggal DESC";

    $schedule_stmt = $conn->prepare($query);
    $schedule_stmt->execute($params);
    $results = $schedule_stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results) {
        foreach ($results as $row) {
            $status_map = [
                'belum dimulai' => 'Belum Dimulai',
                'berlangsung'   => 'Sedang Proses',
                'selesai'       => 'Selesai'
            ];

            $schedules[] = [
                'id'          => $row['id_schedule'],
                'name'        => $row['nama_aktivitas'] ?? $row['judul'] ?? '',
                'description' => $row['deskripsi'] ?? '',
                'location'    => $row['lokasi'] ?? 'Tidak ada lokasi',
                'date'        => date('d-m-Y', strtotime($row['tanggal'])),
                'date_raw'    => $row['tanggal'],
                'status'      => $status_map[$row['status']] ?? $row['status'],
                'status_raw'  => strtolower($row['status'] ?? ''),
                'attachment'  => $row['file_tambahan'] ? basename($row['file_tambahan']) : ''
            ];
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    die();
}

function getStatusBadgeClass($status)
{
    switch ($status) {
        case 'Belum Dimulai': return 'badge-not-started';
        case 'Sedang Proses': return 'badge-in-progress';
        case 'Selesai': return 'badge-completed';
        default: return '';
    }
}

function formatDateIndonesia($date_string)
{
    $date = new DateTime($date_string);
    $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    
    $day_name = $days[$date->format('w')];
    $day = $date->format('d');
    $month_name = $months[$date->format('n') - 1];
    $year = $date->format('Y');
    
    return "$day_name, $day $month_name $year";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Schedule - Best Schedule</title>
    <link rel="stylesheet" href="/../css/style.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h1 class="brand-title">Best Schedule</h1>
            </div>

            <nav class="sidebar-menu">
                <a href="../index.php" class="menu-item">
                    <img src="../images/dashboard.png" alt="Dashboard" class="menu-icon">
                    <span class="menu-text">Dashboard</span>
                </a>
                <a href="schedules.php" class="menu-item active">
                    <img src="../images/schedule.png" alt="Schedule" class="menu-icon">
                    <span class="menu-text">Schedule</span>
                </a>
                <a href="reminders.php" class="menu-item">
                    <img src="../images/reminder.png" alt="Reminder" class="menu-icon">
                    <span class="menu-text">Reminder</span>
                </a>
                <a href="users.php" class="menu-item">
                    <img src="../images/user.png" alt="User" class="menu-icon">
                    <span class="menu-text">Manage Users</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="schedules-header">
                <div class="schedules-title-section">
                    <h1 class="page-title">My Schedule</h1>
                    <p class="current-date"><?php echo formatDateIndonesia($selected_date); ?></p>
                </div>
            </div>

            <!-- Filter Buttons -->
            <div class="filter-section">
                <div class="filter-buttons">
                    <a href="?date=<?php echo $selected_date; ?>&status=semua"
                       class="filter-btn <?php echo $selected_status === 'semua' ? 'active' : ''; ?>">Semua</a>
                    <a href="?date=<?php echo $selected_date; ?>&status=belum%20dimulai"
                       class="filter-btn <?php echo $selected_status === 'belum dimulai' ? 'active' : ''; ?>">Belum Dimulai</a>
                    <a href="?date=<?php echo $selected_date; ?>&status=berlangsung"
                       class="filter-btn <?php echo $selected_status === 'berlangsung' ? 'active' : ''; ?>">Sedang Proses</a>
                    <a href="?date=<?php echo $selected_date; ?>&status=selesai"
                       class="filter-btn <?php echo $selected_status === 'selesai' ? 'active' : ''; ?>">Selesai</a>
                </div>
            </div>

            <!-- Schedules Grid -->
            <section class="schedules-section">
                <?php if (count($schedules) > 0): ?>
                    <div class="schedules-grid">
                        <?php foreach ($schedules as $schedule): ?>
                            <div class="schedule-card">
                                <div class="card-header">
                                    <h3 class="schedule-title"><?php echo htmlspecialchars($schedule['name']); ?></h3>
                                    <span class="status-badge <?php echo getStatusBadgeClass($schedule['status']); ?>">
                                        <?php echo $schedule['status']; ?>
                                    </span>
                                </div>

                                <div class="card-body">
                                    <div class="schedule-info">
                                        <p class="info-item">
                                            <span class="info-label">📅</span>
                                            <span class="info-value"><?php echo $schedule['date']; ?></span>
                                        </p>
                                        <?php if (!empty($schedule['attachment'])): ?>
                                            <p class="info-item">
                                                <span class="info-label">📎</span>
                                                <a href="../uploads/<?php echo htmlspecialchars($schedule['attachment']); ?>" target="_blank" class="info-value">
                                                    <?php echo htmlspecialchars($schedule['attachment']); ?>
                                                </a>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <a href="editSchedule.php?id=<?php echo $schedule['id']; ?>" class="btn btn-edit">Edit</a>
                                    <a href="deleteSchedule.php?id=<?php echo $schedule['id']; ?>" class="btn btn-delete" onclick="return confirm('Hapus jadwal ini?');">Delete</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <p>Tidak ada jadwal untuk ditampilkan</p>
                    </div>
                <?php endif; ?>
            </section>
        </main>
        <?php include 'footer.php'; ?>
    </div>
</body>
</html>
