<?php
require_once '../config/database.php';
require_once 'reminder.php';

class Schedule {
    private $db;

    public function __construct() {
        $this->db = (new Database())->conn;
    }

    // 🔹 Ambil semua schedule (gabung dengan user)
    public function getAllSchedule() {
        $stmt = $this->db->query("
            SELECT schedule.*, user.nama AS nama_user, user.instansi
            FROM schedule
            LEFT JOIN user ON schedule.id_user = user.id_user
            ORDER BY tanggal ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 Tambah schedule + reminder otomatis H-3
    public function addSchedule($id_user, $nama_aktivitas, $deskripsi, $tanggal, $status, $file_tambahan = null) {
        $stmt = $this->db->prepare("
            INSERT INTO schedule (id_user, nama_aktivitas, deskripsi, tanggal, status, file_tambahan)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $success = $stmt->execute([$id_user, $nama_aktivitas, $deskripsi, $tanggal, $status, $file_tambahan]);

        if ($success) {
            $id_schedule = $this->db->lastInsertId();
            $tanggalKirim = date('Y-m-d', strtotime($tanggal . ' -3 days'));
            $pesan = "Pengingat: Aktivitas '$nama_aktivitas' akan dilaksanakan pada $tanggal";

            $reminder = new Reminder();
            $reminder->addReminder($id_schedule, $tanggalKirim, $pesan);
        }

        return $success;
    }

    // 🔹 Update schedule
    public function updateSchedule($id_schedule, $nama_aktivitas, $deskripsi, $tanggal, $status, $file_tambahan = null) {
        $stmt = $this->db->prepare("
            UPDATE schedule
            SET nama_aktivitas = ?, deskripsi = ?, tanggal = ?, status = ?, file_tambahan = ?
            WHERE id_schedule = ?
        ");
        return $stmt->execute([$nama_aktivitas, $deskripsi, $tanggal, $status, $file_tambahan, $id_schedule]);
    }

    // 🔹 Hapus schedule + reminder-nya
    public function deleteSchedule($id_schedule) {
        $this->db->prepare("DELETE FROM reminder WHERE id_schedule = ?")->execute([$id_schedule]);
        $stmt = $this->db->prepare("DELETE FROM schedule WHERE id_schedule = ?");
        return $stmt->execute([$id_schedule]);
    }

    // 🔹 Ambil satu data schedule
    public function getScheduleById($id_schedule) {
        $stmt = $this->db->prepare("SELECT * FROM schedule WHERE id_schedule = ?");
        $stmt->execute([$id_schedule]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🔹 Rekap bulanan
    public function getRekapBulanan($bulan) {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) AS total_aktivitas,
                SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) AS total_selesai,
                SUM(CASE WHEN status = 'berlangsung' THEN 1 ELSE 0 END) AS total_berlangsung,
                SUM(CASE WHEN status = 'belum dimulai' THEN 1 ELSE 0 END) AS total_belum
            FROM schedule
            WHERE MONTH(tanggal) = ?
        ");
        $stmt->execute([$bulan]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getLastInsertId() {
        return $this->db->lastInsertId();
    }

}
?>
