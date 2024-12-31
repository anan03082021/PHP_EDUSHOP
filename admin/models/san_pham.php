<?php
class SanPham {
    private $conn;
    private $table = 'san_pham';

    // Các thuộc tính của sản phẩm
    private $id;
    private $name;
    private $price;
    private $description;
    private $image;
    public $danh_muc_id;

    // Constructor để khởi tạo kết nối
    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tất cả sản phẩm
    public function getAll() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lấy chi tiết một sản phẩm
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            return $stmt->fetch(PDO::FETCH_ASSOC);  // Trả về một bản ghi dưới dạng mảng kết hợp
        } else {
            return null;  // Nếu có lỗi, trả về null
        }
    }

    // Thêm một sản phẩm
    public function create($name, $price, $description, $image, $category_id) {
        $query = "INSERT INTO " . $this->table . " (name, description, price, category_id, image) 
                  VALUES (:name, :description, :price, :category_id, :image)";
        $stmt = $this->conn->prepare($query);
    
        // Gắn giá trị cho các tham số
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':image', $image);
    
        return $stmt->execute(); // Trả về kết quả thực thi
    }    

    // Cập nhật sản phẩm
    public function update($id, $name, $price, $description, $image, $category_id) {
        $sql = "UPDATE san_pham SET name = ?, description = ?, price = ?, category_id = ?, image = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$name, $description, $price, $category_id, $image, $id]);
    }

    // Xóa sản phẩm
    public function delete($id) {
        $query = "DELETE FROM san_pham WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
    
    //Phương thức đếm tổng sản phẩm trong shop
    public function getTotalProducts() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM san_pham");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
  
}
?>