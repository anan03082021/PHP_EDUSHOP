<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<?php
// Kết nối với cơ sở dữ liệu
require_once '../../includes/db.php';
require_once '../models/don_hang.php';
require_once '../models/san_pham.php';
require_once '../includes/navbar.php';

// Khởi tạo model DonHang và SanPham
$donHang = new DonHang($conn);
$sanPham = new SanPham($conn);

// Thống kê số lượng đơn hàng, doanh thu và số sản phẩm
$donHangCount = $donHang->getTotalOrders();
$totalRevenue = $donHang->getTotalRevenue();
$totalProducts = $sanPham->getTotalProducts();  // Lấy tổng số sản phẩm
?>
<body>
    <div class="container">
        <h1>Thống kê</h1>

        <div class="statistics">
            <p><strong>Tổng số đơn hàng:</strong> <?php echo $donHangCount; ?></p>
            <p><strong>Tổng doanh thu:</strong> <?php echo number_format($totalRevenue, 0, ',', '.'); ?> VND</p>
            <p><strong>Tổng số sản phẩm trong cửa hàng:</strong> <?php echo $totalProducts; ?></p>  <!-- Thêm tổng số sản phẩm -->
        </div>
    </div>
</body>
</html>

