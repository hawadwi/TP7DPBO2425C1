<?php
$schedules = $schedule->getAllSchedule();
?>

<div class="dashboard-table">
    <table>
        <tr>
            <th>Activity Name</th>
            <th>Description</th>
            <th>Date</th>
            <th>Status</th>
            <th>Attachment</th>
        </tr>

        <?php foreach ($schedules as $s) : ?>
        <tr>
            <td><?= htmlspecialchars($s['nama_aktivitas']); ?></td>
            <td><?= htmlspecialchars($s['deskripsi']); ?></td>
            <td><?= htmlspecialchars($s['tanggal']); ?></td>
            <td>
                <span class="status 
                    <?= $s['status'] == 'belum dimulai' ? 'belum' : ($s['status'] == 'berlangsung' ? 'proses' : 'selesai'); ?>">
                    <?= ucfirst($s['status']); ?>
                </span>
            </td>
            <td>
                <?php if (!empty($s['file_tambahan'])): ?>
                    <a href="<?= htmlspecialchars($s['file_tambahan']); ?>" target="_blank">📎 View</a>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="calendar-side">
    <h3>Today’s Activity</h3>
    <div class="today-list">
        <?php
        $today = date('Y-m-d');
        $todayActivities = array_filter($schedules, fn($a) => $a['tanggal'] == $today);

        if (empty($todayActivities)) {
            echo "<p>No activities today 🎉</p>";
        } else {
            foreach ($todayActivities as $t) {
                echo '<div class="today-item">';
                echo '<h4>' . htmlspecialchars($t['nama_aktivitas']) . '</h4>';
                echo '<p>' . htmlspecialchars($t['deskripsi']) . '</p>';
                echo '</div>';
            }
        }
        ?>
    </div>
</div>
