<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background: #f8f9fc;
            font-family: 'Arial', sans-serif;
        }

        .navbar {
            background-color: #4e73df;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar .nav-link {
            color: white;
            text-decoration: none;
            font-size: 1.1rem;
            font-weight: 600;
            padding: 8px 20px;
            border-radius: 8px;
            transition: background 0.3s ease, transform 0.2s;
        }

        .content {
            margin-top: 100px;
            padding: 30px;
        }

        .overview-card {
            background: linear-gradient(45deg, #6c9bcf, #81BFDA);
            color: white;
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .overview-card h3 {
            font-size: 2.5rem;
            margin: 0;
            font-weight: 700;
        }

        .overview-card p {
            font-size: 1.1rem;
            color: #dcdde1;
        }

        .overview-card .icon {
            font-size: 3.5rem;
            margin-bottom: 15px;
            color: #ffffff;
        }

        .overview-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }

        .btn {
            background-color: #4e73df;
            color: white;
            padding: 10px 20px;
            border-radius: 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s, transform 0.2s ease-in-out;
        }

        .btn:hover {
            background-color: #81BFDA;
            transform: scale(1.05);
        }
    </style>
</head>
<?php
// Kết nối cơ sở dữ liệu
require_once '../../includes/db.php';
require_once '../includes/navbar.php';
require_once '../models/don_hang.php';
require_once '../models/san_pham.php';
require_once '../models/users.php';

// Tạo các đối tượng
$donHang = new DonHang($conn);
$sanPham = new SanPham($conn);
$user = new Users($conn);

// Sử dụng phương thức getCount từ từng model
$total_products = $sanPham->getTotalProducts(); // Đếm số sản phẩm
$total_orders = $donHang->getTotalOrders(); // Đếm số đơn hàng
$total_users = $user->getCount('users'); // Đếm số người dùng
?>
<body>

    <!-- Nội dung chính -->
    <div class="container content">
        <h1 class="text-center my-3">Trang Chủ</h1>
        <p class="text-center mb-4">Chào mừng bạn đến với trang quản lý EduShop.</p>

        <!-- Các thống kê nhanh -->
        <div class="row">
            <div class="col-md-4">
                <div class="overview-card">
                    <div class="icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <h3><?php echo $total_products; ?></h3>
                    <p>Sản phẩm</p>
                    <a href="manage_products.php" class="btn">Quản lý Sản phẩm</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="overview-card">
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h3><?php echo $total_orders; ?></h3>
                    <p>Đơn hàng</p>
                    <a href="manage_orders.php" class="btn">Quản lý Đơn hàng</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="overview-card">
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3><?php echo $total_users; ?></h3>
                    <p>Người dùng</p>
                    <a href="manage_users.php" class="btn">Quản lý Người dùng</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Liên kết JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
