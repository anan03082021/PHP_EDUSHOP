<?php
// admin/models/dia_chi_giao_hang.php

class DiaChiGiaoHang {
    private $conn;
    private $table = 'dia_chi_giao_hang';

    // Các thuộc tính của địa chỉ giao hàng
    public $id;
    public $user_id;
    public $dia_chi;
    public $sdt;
    public $thanh_pho;

    // Constructor để khởi tạo kết nối
    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy địa chỉ giao hàng của người dùng
    public function getByUserId($user_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt;
    }

    // Thêm địa chỉ giao hàng
    public function create() {
        $query = "INSERT INTO " . $this->table . " (user_id, dia_chi, sdt, thanh_pho) VALUES (:user_id, :dia_chi, :sdt, :thanh_pho)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':dia_chi', $this->dia_chi);
        $stmt->bindParam(':sdt', $this->sdt);
        $stmt->bindParam(':thanh_pho', $this->thanh_pho);
        return $stmt->execute();
    }

    // Cập nhật địa chỉ giao hàng
    public function update() {
        $query = "UPDATE " . $this->table . " SET dia_chi = :dia_chi, sdt = :sdt, thanh_pho = :thanh_pho WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':dia_chi', $this->dia_chi);
        $stmt->bindParam(':sdt', $this->sdt);
        $stmt->bindParam(':thanh_pho', $this->thanh_pho);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }
}
?>
