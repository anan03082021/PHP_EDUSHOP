<?php
// admin/models/danh_muc.php

class DanhMuc {
    private $conn;
    private $table = 'danh_muc';

    // Các thuộc tính của danh mục
    public $id;
    public $ten_danh_muc;

    // Constructor để khởi tạo kết nối
    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tất cả danh mục
    public function getAll() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lấy danh mục theo ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt;
    }

    // Thêm danh mục
    public function create() {
        $query = "INSERT INTO " . $this->table . " (ten_danh_muc) VALUES (:ten_danh_muc)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ten_danh_muc', $this->ten_danh_muc);
        return $stmt->execute();
    }

    // Cập nhật danh mục
    public function update() {
        $query = "UPDATE " . $this->table . " SET ten_danh_muc = :ten_danh_muc WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ten_danh_muc', $this->ten_danh_muc);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Xóa danh mục
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Lấy tất cả danh mục
    public function getCategories() {
        $query = "SELECT id, name FROM " . $this->table; // Chỉ lấy id và name
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Trả về mảng các danh mục
    }

    
}
?>
