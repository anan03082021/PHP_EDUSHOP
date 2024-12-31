<?php
// admin/models/users.php

class Users {
    private $conn;
    private $table = 'users';

    // Các thuộc tính của người dùng
    public $id;
    public $username;
    public $password;
    public $email;
    public $full_name;
    public $phone;
    public $address;
    public $created_at;

    // Constructor để khởi tạo kết nối
    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tất cả người dùng
    public function getAll() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lấy thông tin người dùng theo ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt;
    }

    // Thêm người dùng mới
    public function create($username, $password, $email, $full_name, $phone) {
        $query = "INSERT INTO users (username, password, email, full_name, phone, created_at)
                  VALUES (:username, :password, :email, :full_name, :phone, :created_at)";
        $stmt = $this->conn->prepare($query);
    
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':phone', $phone);
        $created_at = date('Y-m-d H:i:s');
        $stmt->bindParam(':created_at', $created_at);
    
        return $stmt->execute();
    }
    

    // Cập nhật thông tin người dùng
    public function update($id, $full_name, $email, $phone, $password) {
        $query = "UPDATE " . $this->table . " SET full_name = :full_name, email = :email, phone = :phone, password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':id', $id);
    
        return $stmt->execute();
    }    

    // Xóa người dùng
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Lấy số lượng người dùng
    public function getCount($table) {
        $query = "SELECT COUNT(*) as total FROM " . $table;
        $stmt = $this->conn->prepare($query);

        if ($stmt->execute()) {
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        } else {
            die("Lỗi truy vấn bảng $table: " . $stmt->errorInfo()[2]);
        }
    }
}
?>
