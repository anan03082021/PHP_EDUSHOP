<nav class="navbar">
    <ul class="navbar-list">
        <li><a href="dashboard.php" class="nav-link">Trang Chủ</a></li>
        <li><a href="manage_products.php" class="nav-link">Quản lý Sản phẩm</a></li>
        <li><a href="manage_orders.php" class="nav-link">Quản lý Đơn hàng</a></li>
        <li><a href="manage_users.php" class="nav-link">Quản lý Người dùng</a></li>
        <li><a href="statistics.php" class="nav-link">Thống kê</a></li>
        <li><a href="logout.php" class="nav-link">Đăng xuất</a></li>
    </ul>
</nav>

<style>
    /* Cấu trúc và kiểu dáng cho navbar */
    .navbar {
        background-color: #81BFDA; /* Thay đổi màu nền navbar cho thống nhất với admin.css */
        padding: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 1000;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .navbar-list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
    }

    .navbar-list li {
        margin: 0 20px;
    }

    .nav-link {
        color: white;
        text-decoration: none;
        font-size: 1rem;
        font-weight: 600;
        padding: 8px 20px;
        border-radius: 5px;
        transition: all 0.3s ease;
    }

    /* Hover effect */
    .nav-link:hover {
        background-color: #D9EAFD;
        color: #4e73df; 
        transform: scale(1.1);
    }

    /* Active link style */
    .nav-link.active {
        background-color: #28a745;
        color: white;
        transform: scale(1.1);
    }

    /* Responsive mobile view */
    @media (max-width: 768px) {
        .navbar-list {
            flex-direction: column;
            text-align: center;
        }

        .navbar-list li {
            margin: 10px 0;
        }
    }
</style>

<script>
    // JavaScript để thêm hiệu ứng active vào các mục khi click
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function() {
            // Loại bỏ lớp active khỏi tất cả các liên kết
            document.querySelectorAll('.nav-link').forEach(item => item.classList.remove('active'));
            // Thêm lớp active vào liên kết đang được click
            this.classList.add('active');
        });
    });
</script>
