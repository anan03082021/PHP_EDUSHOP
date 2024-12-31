<?php
// Kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "root"; // Tên người dùng MySQL
$password = "";     // Mật khẩu MySQL (nếu có)
$dbname = "edushop"; // Tên cơ sở dữ liệu

try {
    // Tạo kết nối
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Thiết lập chế độ lỗi
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Câu lệnh SQL để tạo bảng users
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        full_name VARCHAR(255) NOT NULL,
        address TEXT,
        phone VARCHAR(20),
        role ENUM('user', 'admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);
    echo "Bảng 'users' đã được tạo thành công!<br>";

    // Tạo bảng san_pham (sản phẩm)
    $sql = "CREATE TABLE IF NOT EXISTS san_pham (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10, 2) NOT NULL,
        category_id INT NOT NULL,
        image VARCHAR(255),
        stock INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);
    echo "Bảng 'san_pham' đã được tạo thành công!<br>";

    // Tạo bảng danh_muc (danh mục sản phẩm)
    $sql = "CREATE TABLE IF NOT EXISTS danh_muc (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);
    echo "Bảng 'danh_muc' đã được tạo thành công!<br>";

    if ($categoryCount == 0) {
        // Danh mục mặc định
        $defaultCategories = [
            ['name' => 'Văn phòng phẩm', 'description' => 'Các sản phẩm văn phòng phẩm.'],
            ['name' => 'Dụng cụ học tập', 'description' => 'Các sản phẩm dụng cụ học tập.'],
            ['name' => 'Dụng cụ mỹ thuật', 'description' => 'Các sản phẩm dụng cụ mỹ thuật.'],
            ['name' => 'Quà lưu niệm', 'description' => 'Các sản phẩm quà lưu niệm.']
        ];

        // Thêm các danh mục mặc định vào bảng 'danh_muc'
        $query = "INSERT INTO danh_muc (name, description) VALUES (:name, :description)";
        $stmt = $conn->prepare($query);

        foreach ($defaultCategories as $category) {
            $stmt->bindParam(':name', $category['name']);
            $stmt->bindParam(':description', $category['description']);
            $stmt->execute();
        }

        echo "Các danh mục mặc định đã được thêm vào bảng 'danh_muc'.<br>";
    } else {
        echo "Danh mục đã có trong cơ sở dữ liệu.<br>";
    }

    // Tạo bảng don_hang (đơn hàng)
    $sql = "CREATE TABLE IF NOT EXISTS don_hang (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        total_price DECIMAL(10, 2) NOT NULL,
        status ENUM('pending', 'completed', 'canceled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    $conn->exec($sql);
    echo "Bảng 'don_hang' đã được tạo thành công!<br>";

    // Tạo bảng chi_tiet_don_hang (chi tiết đơn hàng)
    $sql = "CREATE TABLE IF NOT EXISTS chi_tiet_don_hang (
        id INT AUTO_INCREMENT PRIMARY KEY,
        don_hang_id INT NOT NULL,
        san_pham_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        FOREIGN KEY (don_hang_id) REFERENCES don_hang(id),
        FOREIGN KEY (san_pham_id) REFERENCES san_pham(id)
    )";
    $conn->exec($sql);
    echo "Bảng 'chi_tiet_don_hang' đã được tạo thành công!<br>";

    // Tạo bảng gio_hang (giỏ hàng)
    $sql = "CREATE TABLE IF NOT EXISTS gio_hang (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        san_pham_id INT NOT NULL,
        quantity INT NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (san_pham_id) REFERENCES san_pham(id)
    )";
    $conn->exec($sql);
    echo "Bảng 'gio_hang' đã được tạo thành công!<br>";

    // Tạo bảng dia_chi_giao_hang (địa chỉ giao hàng)
    $sql = "CREATE TABLE IF NOT EXISTS dia_chi_giao_hang (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        address TEXT NOT NULL,
        phone VARCHAR(20) NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    $conn->exec($sql);
    echo "Bảng 'dia_chi_giao_hang' đã được tạo thành công!<br>";
    
} catch(PDOException $e) {
    echo "Lỗi: " . $e->getMessage();
}

// Đóng kết nối
$conn = null;
?>
