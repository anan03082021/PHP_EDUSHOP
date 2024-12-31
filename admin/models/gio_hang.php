<?php
class GioHang {
    private $conn;
    private $table = 'gio_hang';

    // Các thuộc tính của giỏ hàng
    public $id;
    public $user_id;
    public $san_pham_id;
    public $quantity;  // Đổi tên từ 'so_luong' thành 'quantity'

    // Constructor để khởi tạo kết nối
    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy giỏ hàng của người dùng
    public function getByUserId($user_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt;
    }

    // Thêm sản phẩm vào giỏ hàng
    public function addToCart() {
        // Kiểm tra nếu sản phẩm đã có trong giỏ hàng của người dùng
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id AND san_pham_id = :san_pham_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':san_pham_id', $this->san_pham_id);
        $stmt->execute();

        // Nếu sản phẩm đã có, thì cập nhật số lượng
        if ($stmt->rowCount() > 0) {
            return $this->updateQuantity();
        } else {
            // Nếu sản phẩm chưa có trong giỏ, thêm mới
            $query = "INSERT INTO " . $this->table . " (user_id, san_pham_id, quantity) VALUES (:user_id, :san_pham_id, :quantity)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $this->user_id);
            $stmt->bindParam(':san_pham_id', $this->san_pham_id);
            $stmt->bindParam(':quantity', $this->quantity); 
            return $stmt->execute();
        }
    }

    // Cập nhật số lượng sản phẩm trong giỏ hàng
    public function updateQuantity() {
        $query = "UPDATE " . $this->table . " SET quantity = :quantity WHERE user_id = :user_id AND san_pham_id = :san_pham_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quantity', $this->quantity);  
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':san_pham_id', $this->san_pham_id);
        return $stmt->execute();
    }

    // Lấy tổng số lượng sản phẩm trong giỏ hàng của người dùng
    public function getCartCount($user_id) {
        $query = "SELECT SUM(quantity) as total FROM " . $this->table . " WHERE user_id = :user_id";  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ? $row['total'] : 0;  
    }

    // Xóa sản phẩm khỏi giỏ hàng
    public function removeFromCart() {
        $query = "DELETE FROM " . $this->table . " WHERE user_id = :user_id AND san_pham_id = :san_pham_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':san_pham_id', $this->san_pham_id);
        return $stmt->execute();
    }
}
?>
