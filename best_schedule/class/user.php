<?php
require_once '../config/database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = (new Database())->conn;
    }

    // 🔹 Ambil semua user
    public function getAllUsers() {
        $stmt = $this->db->query("SELECT * FROM user ORDER BY id_user ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 Tambah user
    public function addUser($nama, $email, $instansi) {
        $stmt = $this->db->prepare("INSERT INTO user (nama, email, instansi) VALUES (?, ?, ?)");
        return $stmt->execute([$nama, $email, $instansi]);
    }

    // 🔹 Ubah user
    public function updateUser($id_user, $nama, $email, $instansi) {
        $stmt = $this->db->prepare("UPDATE user SET nama = ?, email = ?, instansi = ? WHERE id_user = ?");
        return $stmt->execute([$nama, $email, $instansi, $id_user]);
    }

    // 🔹 Hapus user beserta schedule & reminder-nya (manual)
    public function deleteUser($id_user) {
        $stmt1 = $this->db->prepare("
            DELETE FROM reminder 
            WHERE id_schedule IN (SELECT id_schedule FROM schedule WHERE id_user = ?)
        ");
        $stmt1->execute([$id_user]);

        $stmt2 = $this->db->prepare("DELETE FROM schedule WHERE id_user = ?");
        $stmt2->execute([$id_user]);

        $stmt3 = $this->db->prepare("DELETE FROM user WHERE id_user = ?");
        return $stmt3->execute([$id_user]);
    }

    // Ambil user berdasarkan ID
    public function getUserById($id_user) {
        $stmt = $this->db->prepare("SELECT * FROM user WHERE id_user = :id LIMIT 1");
        $stmt->bindParam(':id', $id_user, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

?>
