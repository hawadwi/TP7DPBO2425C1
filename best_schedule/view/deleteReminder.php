<?php
session_start();
require_once '../config/database.php';
require_once '../class/reminder.php';

// Pastikan ada ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "ID reminder tidak ditemukan!";
    header("Location: reminders.php");
    exit();
}

$id = $_GET['id'];

$reminderObj = new Reminder();

// Eksekusi hapus
$delete = $reminderObj->deleteReminder($id);

if ($delete) {
    $_SESSION['success'] = "Reminder berhasil dihapus!";
} else {
    $_SESSION['error'] = "Gagal menghapus reminder!";
}

// Redirect kembali ke list reminder
header("Location: reminders.php");
exit();
