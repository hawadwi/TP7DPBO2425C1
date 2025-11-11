<?php
session_start();
require_once '../config/database.php';
require_once '../class/schedule.php';
require_once '../class/reminder.php';

// Ambil semua schedule untuk dropdown
$scheduleObj = new Schedule();
$schedules = $scheduleObj->getAllSchedule();

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_schedule      = $_POST['id_schedule'];
    $deskripsi        = $_POST['deskripsi'];
    $waktu_pengingat  = $_POST['waktu_pengingat'];

    // Validasi minimal
    if (empty($id_schedule) || empty($waktu_pengingat)) {
        $_SESSION['error'] = "Pastikan semua field wajib diisi!";
        header("Location: addReminder.php");
        exit();
    }

    $reminderObj = new Reminder();

    // NOTE: addReminder hanya menerima 3 parameter -> id_schedule, tanggal_kirim, pesan
    $simpan = $reminderObj->addReminder(
        $id_schedule,
        $waktu_pengingat,
        $deskripsi
    );

    if ($simpan) {
        $_SESSION['success'] = "Reminder berhasil ditambahkan!";
        header("Location: reminders.php");
        exit();
    } else {
        $_SESSION['error'] = "Gagal menyimpan reminder.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Reminder - Schedule App</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="page-wrapper">

        <div class="add-reminder-header">
            <a href="reminders.php" class="back-button">←</a>
            <h1 class="add-reminder-title">Tambah Reminder Baru</h1>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST" action="addReminder.php" class="form">

                <!-- Schedule Dropdown -->
                <div class="form-group">
                    <label for="id_schedule" class="form-label">Schedule *</label>
                    <select id="id_schedule" name="id_schedule" class="form-input" required>
                        <option value="">-- Pilih Schedule --</option>
                        <?php foreach ($schedules as $schedule): ?>
                            <option value="<?= $schedule['id_schedule']; ?>">
                                <?= htmlspecialchars($schedule['nama_aktivitas']); ?> - 
                                <?= htmlspecialchars($schedule['nama_user']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Deskripsi -->
                <div class="form-group">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" class="form-textarea"
                        placeholder="Tambahkan deskripsi atau catatan"></textarea>
                </div>

                <!-- Waktu Pengingat -->
                <div class="form-group">
                    <label for="waktu_pengingat" class="form-label">Waktu Pengingat *</label>
                    <input type="datetime-local" id="waktu_pengingat" name="waktu_pengingat" class="form-date" required>
                </div>

                <!-- Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn-submit">+ Simpan Reminder</button>
                    <a href="reminders.php" class="btn-cancel">Batal</a>
                </div>

            </form>
        </div>

    </div>
</body>
</html>