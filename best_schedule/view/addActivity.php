<?php
session_start();
require_once '../config/database.php';
require_once '../class/user.php';
require_once '../class/schedule.php';

$userObj = new User();
$users = $userObj->getAllUsers();

$scheduleObj = new Schedule();


// Jika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_user = $_POST['id_user'];
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $tanggal = $_POST['tanggal'];
    $status = $_POST['status'];

    // File tambahan
    $filePath = null;

    // Jika ada upload file
    if (isset($_FILES['file_tambahan']) && $_FILES['file_tambahan']['error'] === UPLOAD_ERR_OK) {

        $uploadDir = "../uploads/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = time() . "_" . basename($_FILES['file_tambahan']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['file_tambahan']['tmp_name'], $targetPath)) {
            $filePath = $targetPath;
        }
    }

    // Jika user memasukkan link
    if (!empty($_POST['file_link'])) {
        $filePath = $_POST['file_link'];
    }

    // Simpan ke database
    if ($scheduleObj->addSchedule($id_user, $judul, $deskripsi, $tanggal, $status, $filePath)) {

        // ✅ Ambil ID terakhir dari PDO
        $schedule_id = $scheduleObj->getLastInsertId();

        // ✅ Tambahkan reminder otomatis
        require_once '../class/reminder.php';
        $reminderObj = new Reminder();
        $reminderObj->addReminder(
            $schedule_id,
            "Reminder untuk: $judul",
            $tanggal,
            "tertunda"
        );
        $_SESSION['success'] = "Schedule berhasil ditambahkan!";
        header("Location: schedules.php");
        exit();
    } else {
        $_SESSION['error'] = "Gagal menambah schedule!";
        header("Location: addActivity.php");
        exit();
    }

    
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Schedule - Schedule App</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="page-wrapper">

    <div class="add-schedule-header">
        <a href="schedules.php" class="back-button">←</a>
        <h1 class="add-schedule-title">Tambah Schedule</h1>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="form-card">
        <form method="POST" action="addActivity.php" enctype="multipart/form-data" class="activity-form">

            <div class="form-group">
                <label for="id_user" class="form-label">User *</label>
                <select id="id_user" name="id_user" class="form-input" required>
                    <option value="">-- Pilih User --</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= $user['id_user']; ?>">
                            <?= htmlspecialchars($user['nama']) ?> (<?= htmlspecialchars($user['instansi']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="judul" class="form-label">Judul *</label>
                <input type="text" id="judul" name="judul" class="form-input" required>
            </div>

            <div class="form-group">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" class="form-textarea"></textarea>
            </div>

            <div class="form-group">
                <label for="tanggal" class="form-label">Tanggal *</label>
                <input type="date" id="tanggal" name="tanggal" class="form-input" required>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-input">
                    <option value="belum dimulai">Belum Dimulai</option>
                    <option value="berlangsung">Sedang Proses</option>
                    <option value="selesai">Selesai</option>
                </select>
            </div>

            <!-- FILE / LINK TAMBAHAN -->
            <div class="form-group">
                <label class="form-label">File Tambahan (PDF/IMG/DOC)</label>
                <input type="file" name="file_tambahan" class="form-input">

                <p style="text-align:center; margin:8px;">— atau —</p>

                <input type="text" name="file_link" class="form-input" placeholder="Masukkan URL (optional)">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">+ Simpan Schedule</button>
                <a href="schedules.php" class="btn-cancel">Batal</a>
            </div>

        </form>
    </div>
</div>

</body>
</html>
