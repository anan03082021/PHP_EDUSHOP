<?php
// admin/models/chi_tiet_don_hang.php

class ChiTietDonHang {
    private $conn;
    private $table = 'chi_tiet_don_hang';

    // Các thuộc tính của chi tiết đơn hàng
    public $id;
    public $don_hang_id;
    public $san_pham_id;
    public $so_luong;
    public $gia;

    // Constructor để khởi tạo kết nối
    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy chi tiết đơn hàng theo ID đơn hàng
    public function getByDonHangId($don_hang_id) {
        $query = "SELECT chi_tiet_don_hang.don_hang_id, chi_tiet_don_hang.san_pham_id, san_pham.name as san_pham_name, 
                     chi_tiet_don_hang.quantity, san_pham.price
              FROM " . $this->table . " chi_tiet_don_hang
              INNER JOIN san_pham ON chi_tiet_don_hang.san_pham_id = san_pham.id
              WHERE chi_tiet_don_hang.don_hang_id = :don_hang_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':don_hang_id', $don_hang_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm chi tiết đơn hàng
    public function create() {
        $query = "INSERT INTO " . $this->table . " (don_hang_id, san_pham_id, so_luong, gia) VALUES (:don_hang_id, :san_pham_id, :so_luong, :gia)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':don_hang_id', $this->don_hang_id);
        $stmt->bindParam(':san_pham_id', $this->san_pham_id);
        $stmt->bindParam(':so_luong', $this->so_luong);
        $stmt->bindParam(':gia', $this->gia);
        return $stmt->execute();
    }
}
?>
