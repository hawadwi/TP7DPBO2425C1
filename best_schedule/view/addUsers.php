<?php
require_once '../class/user.php';
session_start();

$userModel = new User();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama      = $_POST['nama'];
    $email     = $_POST['email'];
    $instansi  = $_POST['instansi'];

    if ($userModel->addUser($nama, $email, $instansi)) {
        header("Location: users.php?success=added");
        exit;
    } else {
        $error = "Gagal menambahkan user.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User - Schedule App</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="page-wrapper">

    <div class="add-reminder-header">
        <a href="users.php" class="back-button">←</a>
        <h1 class="add-reminder-title">Tambah User Baru</h1>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="form-card">
        <form method="POST" class="form">

            <div class="form-group">
                <label for="nama" class="form-label">Nama *</label>
                <input type="text" id="nama" name="nama" class="form-input" required placeholder="Nama user...">
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-input" placeholder="Email user...">
            </div>

            <div class="form-group">
                <label for="instansi" class="form-label">Instansi</label>
                <input type="text" id="instansi" name="instansi" class="form-input" placeholder="Instansi user...">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">+ Simpan User</button>
                <a href="../users.php" class="btn-cancel">Batal</a>
            </div>

        </form>
    </div>

</div>

</body>
</html>
