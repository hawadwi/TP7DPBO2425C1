<?php
require_once '../config/database.php';

class Reminder {
    private $db;

    public function __construct() {
        $this->db = (new Database())->conn;  // sudah objek PDO
    }

    // Ambil semua reminder
    public function getAllReminders() {
        $stmt = $this->db->query("
            SELECT reminder.*, schedule.nama_aktivitas
            FROM reminder
            LEFT JOIN schedule ON reminder.id_schedule = schedule.id_schedule
            ORDER BY tanggal_kirim ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tambah reminder
    public function addReminder($id_schedule, $tanggal_kirim, $pesan) {
        $stmt = $this->db->prepare("
            INSERT INTO reminder (id_schedule, tanggal_kirim, pesan, status)
            VALUES (?, ?, ?, 'tertunda')
        ");
        return $stmt->execute([$id_schedule, $tanggal_kirim, $pesan]);
    }

    // Update reminder
    public function updateReminder($id_reminder, $tanggal_kirim, $pesan, $status) {
        $stmt = $this->db->prepare("
            UPDATE reminder
            SET tanggal_kirim = ?, pesan = ?, status = ?
            WHERE id_reminder = ?
        ");
        return $stmt->execute([$tanggal_kirim, $pesan, $status, $id_reminder]);
    }

    // Hapus reminder
    public function deleteReminder($id_reminder) {
        $stmt = $this->db->prepare("DELETE FROM reminder WHERE id_reminder = ?");
        return $stmt->execute([$id_reminder]);
    }

    public function generateReminders($id_schedule, $tanggal, $nama_aktivitas) {

        $reminderDates = [
            date('Y-m-d', strtotime("$tanggal -3 days")) => "Reminder: {$nama_aktivitas} will happen in 3 days.",
            date('Y-m-d', strtotime("$tanggal -1 days")) => "Reminder: {$nama_aktivitas} is tomorrow.",
            $tanggal . " 08:00:00" => "Reminder: {$nama_aktivitas} is today at 8:00 AM.",
            date('Y-m-d H:i:s', strtotime("$tanggal -1 hour")) => "Reminder: {$nama_aktivitas} starts in 1 hour."
        ];

        foreach ($reminderDates as $tgl => $pesan) {
            $stmt = $this->db->prepare("
                INSERT INTO reminder (id_schedule, tanggal_kirim, pesan, status)
                VALUES (?, ?, ?, 'tertunda')
            ");
            $stmt->execute([$id_schedule, $tgl, $pesan]);
        }
    }

    public function getRemindersWithSchedule() {
        $stmt = $this->db->prepare("
            SELECT r.*, s.judul, s.deskripsi, s.tanggal
            FROM reminder r
            JOIN schedule s ON r.id_schedule = s.id_schedule
            ORDER BY r.tanggal_kirim DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReminderById($id_reminder) {
        $stmt = $this->db->prepare("SELECT * FROM reminder WHERE id_reminder = ?");
        $stmt->execute([$id_reminder]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ✅ Ini sudah diperbaiki
    public function updateStatus(int $id, string $status) {
        $stmt = $this->db->prepare("UPDATE reminder SET status = ? WHERE id_reminder = ?");
        return $stmt->execute([$status, $id]);
    }

    public function getRemindersByDate($date){
        $stmt = $this->db->prepare("SELECT * FROM reminder WHERE DATE(tanggal_kirim) = :date ORDER BY tanggal_kirim ASC");
        $stmt->execute(['date' => $date]);
        return $stmt->fetchAll();
    }

    public function getRemindersThisWeek(){
        $stmt = $this->db->prepare("
            SELECT * FROM reminder 
            WHERE YEARWEEK(tanggal_kirim, 1) = YEARWEEK(CURDATE(), 1)
            ORDER BY tanggal_kirim ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getRemindersThisMonth(){
        $stmt = $this->db->prepare("
            SELECT * FROM reminder
            WHERE MONTH(tanggal_kirim) = MONTH(CURDATE()) AND YEAR(tanggal_kirim) = YEAR(CURDATE())
            ORDER BY tanggal_kirim ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

?>
