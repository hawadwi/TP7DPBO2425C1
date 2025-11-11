<?php
session_start();
require_once '../class/user.php';

if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$id_user = $_GET['id'];
$userObj = new User();

if ($userObj->deleteUser($id_user)) {
    $_SESSION['success'] = "User berhasil dihapus!";
} else {
    $_SESSION['error'] = "Gagal menghapus user!";
}

header("Location: users.php");
exit();