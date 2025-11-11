<?php
session_start();
require_once '../config/database.php';
require_once '../class/schedule.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "ID Schedule tidak ditemukan!";
    header("Location: schedules.php");
    exit();
}

$id = $_GET['id'];

$scheduleObj = new Schedule();

// Ambil data schedule untuk cek apakah ada file tambahan
$data = $scheduleObj->getScheduleById($id);

if (!$data) {
    $_SESSION['error'] = "Schedule tidak ditemukan!";
    header("Location: schedules.php");
    exit();
}

// ✅ Jika ada file tambahan dan itu file lokal (bukan URL)
if (!empty($data['file_tambahan']) && file_exists($data['file_tambahan'])) {
    unlink($data['file_tambahan']);
}

// ✅ Lakukan delete
$deleted = $scheduleObj->deleteSchedule($id);

if ($deleted) {
    $_SESSION['success'] = "Schedule berhasil dihapus!";
} else {
    $_SESSION['error'] = "Gagal menghapus schedule!";
}

header("Location: schedules.php");
exit();
