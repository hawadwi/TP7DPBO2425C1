<?php
session_start();
require_once '../config/database.php';
require_once '../class/user.php';
require_once '../class/schedule.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID tidak ditemukan!");
}

$id = $_GET['id'];

$scheduleObj = new Schedule();
$userObj = new User();

$users = $userObj->getAllUsers();
$data = $scheduleObj->getScheduleById($id);

if (!$data) {
    die("Schedule tidak ditemukan.");
}

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_user = $_POST['id_user'];
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $tanggal = $_POST['tanggal'];
    $status = $_POST['status'];

    // File tambahan
    $filePath = $data['file_tambahan']; // default file lama

    // Upload baru
    if (isset($_FILES['file_tambahan']) && $_FILES['file_tambahan']['error'] === UPLOAD_ERR_OK) {

        $uploadDir = "../uploads/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES['file_tambahan']['name']);
        $target = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['file_tambahan']['tmp_name'], $target)) {
            $filePath = $target;
        }
    }

    // Link baru
    if (!empty($_POST['file_link'])) {
        $filePath = $_POST['file_link'];
    }

    if ($scheduleObj->updateSchedule($id, $judul, $deskripsi, $tanggal, $status, $filePath)) {
    

        $_SESSION['success'] = "Schedule berhasil diperbarui!";
        header("Location: schedules.php");
        exit();
    } else {
        $_SESSION['error'] = "Gagal mengupdate schedule!";
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Schedule</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="page-wrapper">

    <!-- Header -->
    <div class="add-schedule-header">
        <a href="schedules.php" class="back-button">←</a>
        <h1 class="add-schedule-title">Edit Schedule</h1>
    </div>

    <div id="alert-container">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Form -->
    <div class="form-card">

        <form method="POST" action="" enctype="multipart/form-data" class="activity-form">

            <!-- User -->
            <div class="form-group">
                <label class="form-label">User *</label>
                <select name="id_user" class="form-input" required>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= $user['id_user']; ?>"
                            <?= $user['id_user'] == $data['id_user'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($user['nama']) . " (" . htmlspecialchars($user['instansi']) . ")" ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Judul -->
            <div class="form-group">
                <label class="form-label">Judul *</label>
                <input type="text" name="judul" value="<?= htmlspecialchars($data['nama_aktivitas']); ?>" class="form-input" required>
            </div>

            <!-- Deskripsi -->
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-textarea"><?= htmlspecialchars($data['deskripsi']); ?></textarea>
            </div>

            <!-- Tanggal -->
            <div class="form-group">
                <label class="form-label">Tanggal *</label>
                <input type="date" name="tanggal" class="form-input"
                       value="<?= $data['tanggal']; ?>" required>
            </div>

            <!-- Status -->
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-input">
                    <option value="belum dimulai" <?= $data['status'] == 'belum dimulai' ? 'selected' : '' ?>>Belum Dimulai</option>
                    <option value="berlangsung" <?= $data['status'] == 'berlangsung' ? 'selected' : '' ?>>Sedang Proses</option>
                    <option value="selesai" <?= $data['status'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                </select>
            </div>

            <!-- File -->
            <div class="form-group">
                <label class="form-label">File Tambahan</label>
                <input type="file" name="file_tambahan" class="form-input">

                <?php if (!empty($data['file_tambahan'])): ?>
                    <p style="font-size: 14px; color:#555;">File saat ini:  
                        <a href="<?= $data['file_tambahan']; ?>" target="_blank">
                            <?= basename($data['file_tambahan']); ?>
                        </a>
                    </p>
                <?php endif; ?>

                <p style="text-align:center; margin:8px;">— atau —</p>

                <input type="text" name="file_link" class="form-input"
                       placeholder="Masukkan URL (optional)">
            </div>

            <!-- Aksi -->
            <div class="form-actions">
                <button type="submit" class="btn-submit">💾 Simpan Perubahan</button>
                <a href="schedules.php" class="btn-cancel">Batal</a>
            </div>

        </form>
    </div>
</div>

</body>
</html>
