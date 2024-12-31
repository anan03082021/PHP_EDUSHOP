<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('../includes/db.php'); // Kết nối cơ sở dữ liệu

// Khởi tạo biến $cart_count
$cart_count = 0;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Truy vấn số lượng sản phẩm trong giỏ hàng
    $query = "SELECT SUM(quantity) AS total_quantity FROM gio_hang WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC); // Lấy kết quả
    $cart_count = $result['total_quantity'] ?: 0; // Nếu không có sản phẩm, mặc định là 0
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <!-- Link to Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        /* Cấu trúc và kiểu dáng cho navbar */
        .navbar {
            background-color: #ffffff; /* Màu nền trắng */
            padding: 0.8rem 1rem;
            border-bottom: 2px solid #f0f0f0;
            position: fixed; /* Đặt navbar cố định */
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000; /* Đảm bảo navbar ở trên cùng */
        }

        .navbar .navbar-nav {
            margin-left: auto;
        }

        .navbar .nav-item {
            margin-left: 20px;
        }

        .navbar .nav-link {
            font-weight: 600;
            font-size: 1.1rem;
            padding: 8px 20px;
            transition: all 0.3s;
        }

        /* Màu sắc của các mục khi hover */
        .nav-link:hover {
            color: #007bff;
        }

        .badge {
            font-size: 0.8rem;
            position: relative;
            top: -3px;
        }

        .dropdown-menu {
            border-radius: 10px;
        }

        /* Đảm bảo nội dung trang không bị che khuất bởi navbar */
        body {
            padding-top: 70px; /* Tạo khoảng cách dưới navbar */
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-white shadow">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand text-primary fw-bold" href="index.php" style="font-size: 1.8rem; letter-spacing: 1px;">
            <i class="fas fa-graduation-cap me-2"></i>EduShop
        </a>

        <!-- dùng cho điện thoại -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-center">
                <!-- Home -->
                <li class="nav-item">
                    <a class="nav-link text-dark px-3 py-2" href="index.php">
                        <i class="fas fa-home me-1"></i>Trang chủ
                    </a>
                </li>

                <!-- Cart -->
                <li class="nav-item">
                        <a class="nav-link text-dark px-3 py-2" href="cart.php">
                            <i class="fas fa-shopping-cart me-1"></i>Giỏ hàng
                            <span class="badge bg-danger ms-1" id="cart-count"><?php echo $cart_count; ?></span>
                        </a>
                </li>

                <!-- User Account -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-dark px-3 py-2" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i>
                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="profile.php">Thông tin cá nhân</a></li>
                            <li><a class="dropdown-item" href="orders.php">Đơn hàng</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php">Đăng xuất</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <!-- Login -->
                    <li class="nav-item">
                        <a class="nav-link text-dark px-3 py-2" href="login.php">
                            <i class="fas fa-sign-in-alt me-1"></i>Đăng nhập
                        </a>
                    </li>
                    <!-- Register -->
                    <li class="nav-item">
                        <a class="nav-link text-dark px-3 py-2" href="register.php">
                            <i class="fas fa-user-plus me-1"></i>Đăng ký
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Link to Bootstrap JS (for responsive behavior) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
