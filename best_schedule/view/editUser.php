<?php
session_start();
require_once '../class/user.php';

$userObj = new User();

// Ambil ID user dari query string
if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$id_user = $_GET['id'];
$user = $userObj->getUserById($id_user);

if (!$user) {
    $_SESSION['error'] = "User tidak ditemukan!";
    header("Location: users.php");
    exit();
}

$error = '';

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = $_POST['nama'];
    $email    = $_POST['email'];
    $instansi = $_POST['instansi'];

    if ($userObj->updateUser($id_user, $nama, $email, $instansi)) {
        $_SESSION['success'] = "User berhasil diperbarui!";
        header("Location: users.php");
        exit();
    } else {
        $error = "Gagal memperbarui user.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Schedule App</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="page-wrapper">

    <div class="add-reminder-header">
        <a href="users.php" class="back-button">←</a>
        <h1 class="add-reminder-title">Edit User</h1>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="form-card">
        <form method="POST" class="form">

            <div class="form-group">
                <label for="nama" class="form-label">Nama *</label>
                <input type="text" id="nama" name="nama" class="form-input" required value="<?= htmlspecialchars($user['nama']); ?>">
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-input" value="<?= htmlspecialchars($user['email']); ?>">
            </div>

            <div class="form-group">
                <label for="instansi" class="form-label">Instansi</label>
                <input type="text" id="instansi" name="instansi" class="form-input" value="<?= htmlspecialchars($user['instansi']); ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">Simpan Perubahan</button>
                <a href="users.php" class="btn-cancel">Batal</a>
            </div>

        </form>
    </div>

</div>
</body>
</html>