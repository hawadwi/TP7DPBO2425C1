<?php
require_once  'config/database.php';

$activities = [];
$today_activities = [];
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$selected_status = isset($_GET['status']) ? $_GET['status'] : 'semua';

try {
    $db = new Database();
    $conn = $db->conn;

    // Ambil data user aktif
    $user_stmt = $conn->prepare("SELECT * FROM user LIMIT 1");
    $user_stmt->execute();
    $current_user = $user_stmt->fetch(PDO::FETCH_ASSOC) ?: ['nama' => 'User', 'instansi' => 'Student'];

    // Ambil daftar aktivitas
    $query = "SELECT s.*, u.nama AS user_name FROM schedule s LEFT JOIN user u ON s.id_user = u.id_user";
    $params = [];
    if ($selected_status !== 'semua') {
        $query .= " WHERE s.status = :status";
        $params[':status'] = strtolower($selected_status);
    }
    $query .= " ORDER BY s.tanggal DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results) {
        foreach ($results as $row) {
            $status_map = [
                'belum dimulai' => 'Belum Dimulai',
                'berlangsung'   => 'Sedang Proses',
                'selesai'       => 'Selesai'
            ];

            $activities[] = [
                'id'              => $row['id_schedule'],
                'name'            => $row['nama_aktivitas'] ?? $row['judul'] ?? '',
                'description'     => $row['deskripsi'] ?? '',
                'date'            => date('d-m-Y', strtotime($row['tanggal'])),
                'status'          => $status_map[$row['status']] ?? $row['status'],
                'attachment'      => $row['file_tambahan'] ? basename($row['file_tambahan']) : '',
                'attachment_icon' => $row['file_tambahan'] ? '📎' : '🔗'
            ];
        }
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

function getStatusClass($status)
{
    switch ($status) {
        case 'Belum Dimulai': return 'status-not-started';
        case 'Sedang Proses': return 'status-in-progress';
        case 'Selesai': return 'status-completed';
        default: return '';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Best Schedule - Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <?php include 'view/header.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <header class="header">
            <div class="filter-buttons">
                <a href="?date=<?= $selected_date ?>&status=semua"
                   class="filter-btn <?= $selected_status==='semua'?'active':'' ?>">Semua</a>
                <a href="?date=<?= $selected_date ?>&status=belum%20dimulai"
                   class="filter-btn <?= $selected_status==='belum dimulai'?'active':'' ?>">Belum Dimulai</a>
                <a href="?date=<?= $selected_date ?>&status=berlangsung"
                   class="filter-btn <?= $selected_status==='berlangsung'?'active':'' ?>">Sedang Proses</a>
                <a href="?date=<?= $selected_date ?>&status=selesai"
                   class="filter-btn <?= $selected_status==='selesai'?'active':'' ?>">Selesai</a>
                <a href="view/addActivity.php" class="create-btn">+ Create New Activity</a>
            </div>
        </header>

        <section class="activity-section">
            <table class="activity-table">
                <thead>
                    <tr>
                        <th>Activity Name</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Attachment</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($activities)): ?>
                        <?php foreach ($activities as $activity): ?>
                            <tr>
                                <td><?= htmlspecialchars($activity['name']) ?></td>
                                <td><?= htmlspecialchars(substr($activity['description'],0,50)) ?></td>
                                <td><?= $activity['date'] ?></td>
                                <td><span class="status-badge <?= getStatusClass($activity['status']) ?>"><?= $activity['status'] ?></span></td>
                                <td>
                                    <?php if($activity['attachment']): ?>
                                        <span class="attachment-icon"><?= $activity['attachment_icon'] ?></span>
                                        <?= htmlspecialchars($activity['attachment']) ?>
                                    <?php else: ?>
                                        <span style="color:#999">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align:center; color:#999; padding:40px;">Tidak ada aktivitas</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>

    <?php include 'view/footer.php'; ?>
</div>
</body>
</html>