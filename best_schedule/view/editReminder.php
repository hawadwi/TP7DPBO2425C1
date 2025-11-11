<?php
session_start();
require_once '../config/database.php';
require_once '../class/reminder.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID tidak ditemukan!");
}

$id = $_GET['id'];

$reminderObj = new Reminder();
$data = $reminderObj->getReminderById($id);

if (!$data) {
    die("Reminder tidak ditemukan!");
}

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tanggal_kirim = $_POST['tanggal_kirim'];
    $pesan = $_POST['pesan'];
    $status = $_POST['status'];

    $update = $reminderObj->updateReminder($id, $tanggal_kirim, $pesan, $status);

    if ($update) {
        $_SESSION['success'] = "Reminder berhasil diperbarui!";
        header("Location: reminders.php");
        exit();
    } else {
        $_SESSION['error'] = "Gagal mengupdate reminder!";
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Reminder</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="page-wrapper">

    <div class="add-schedule-header">
        <a href="reminders.php" class="back-button">←</a>
        <h1 class="add-schedule-title">Edit Reminder</h1>
    </div>

    <div id="alert-container">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
    </div>

    <div class="form-card">

        <form method="POST" class="activity-form">

            <!-- Tanggal Kirim -->
            <div class="form-group">
                <label class="form-label">Tanggal Kirim *</label>
                <input type="datetime-local" 
                       name="tanggal_kirim" 
                       class="form-input"
                       value="<?= date('Y-m-d\TH:i', strtotime($data['tanggal_kirim'])); ?>"
                       required>
            </div>

            <!-- Pesan -->
            <div class="form-group">
                <label class="form-label">Pesan *</label>
                <textarea name="pesan" class="form-textarea" required><?= htmlspecialchars($data['pesan']); ?></textarea>
            </div>

            <!-- Status -->
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-input">
                    <option value="pending" <?= $data['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="terkirim" <?= $data['status'] == 'terkirim' ? 'selected' : '' ?>>Terkirim</option>
                    <option value="gagal" <?= $data['status'] == 'gagal' ? 'selected' : '' ?>>Gagal</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">💾 Simpan Perubahan</button>
                <a href="reminders.php" class="btn-cancel">Batal</a>
            </div>

        </form>
    </div>

</div>

</body>
</html>
