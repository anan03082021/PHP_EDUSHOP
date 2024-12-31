<?php
class DonHang {
    private $conn;
    private $table = 'don_hang';

    // Các thuộc tính của đơn hàng
    public $id;
    public $user_id;
    public $total_price;
    public $status;
    public $created_at;

    // Constructor để khởi tạo kết nối
    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tất cả đơn hàng
    public function getAll() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lấy chi tiết đơn hàng
    public function getById($id) {
        $query = "SELECT don_hang.id, don_hang.created_at, don_hang.status, don_hang.total_price, 
                     users.username, 
                     san_pham.name as san_pham_name, 
                     chi_tiet_don_hang.quantity, san_pham.price 
                FROM " . $this->table . " don_hang
                INNER JOIN users users ON don_hang.user_id = users.id
                INNER JOIN chi_tiet_don_hang ON don_hang.id = chi_tiet_don_hang.don_hang_id
                INNER JOIN san_pham ON chi_tiet_don_hang.san_pham_id = san_pham.id
                WHERE don_hang.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt;
    }

    // Cập nhật trạng thái đơn hàng
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Thêm đơn hàng
    public function create() {
        $query = "INSERT INTO " . $this->table . " (user_id, total_price, status, created_at)
                  VALUES (:user_id, :total_price, :status, :created_at)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':total_price', $this->total_price);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':created_at', $this->created_at);
        return $stmt->execute();
    }

    // Cập nhật đơn hàng
    public function update() {
        $query = "UPDATE " . $this->table . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Xóa đơn hàng
    public function delete($id) {
        try {
            // Xóa chi tiết đơn hàng trước
            $queryDetails = "DELETE FROM chi_tiet_don_hang WHERE don_hang_id = :order_id"; // Kiểm tra lại tên cột
            $stmtDetails = $this->conn->prepare($queryDetails);
            $stmtDetails->bindParam(':order_id', $id);
            if (!$stmtDetails->execute()) {
                throw new Exception("Không thể xóa chi tiết đơn hàng.");
            }
    
            // Xóa đơn hàng chính
            $queryOrder = "DELETE FROM " . $this->table . " WHERE id = :id";
            $stmtOrder = $this->conn->prepare($queryOrder);
            $stmtOrder->bindParam(':id', $id);
            if ($stmtOrder->execute()) {
                return true;
            } else {
                throw new Exception("Không thể xóa đơn hàng.");
            }
        } catch (Exception $e) {
            return false;
        }
    }    

    // Phương thức lấy tổng số đơn hàng
    public function getTotalOrders() {
        $query = "SELECT COUNT(*) AS total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total']; // Trả về tổng số đơn hàng
    }

    // Phương thức lấy tổng doanh thu
    public function getTotalRevenue() {
        $query = "SELECT SUM(total_price) AS total_revenue FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total_revenue']; // Trả về tổng doanh thu
    }
}
?>
