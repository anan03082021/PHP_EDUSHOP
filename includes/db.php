<?php

$servername = "sql209.infinityfree.com";  
$username = "if0_38013889";         
$password = "uZrnmaruMnh";             
$dbname = "if0_38013889_edushop";  

try {
    // Tạo kết nối
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    
    // Thiết lập chế độ báo lỗi
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    
} catch(PDOException $e) {
    // Nếu có lỗi xảy ra, thông báo lỗi
    echo "Lỗi kết nối: " . $e->getMessage();
}

// Đóng kết nối
// $conn = null; // Chú ý: không đóng kết nối ở đây nếu bạn muốn dùng kết nối trong các file khác
?>
