<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #FFCCE1;
            --secondary-color: #E195AB;
            --accent-color: #FAD02E;
            --text-color: #2C3E50;
            --bg-color: #D9EAFD;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, var(--bg-color), var(--bg-color));
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Register Container */
        .register-container {
            background: white;
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            animation: fadeIn 0.5s ease-out;
            margin: 2rem auto;
        }

        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .register-header h2 {
            color: var(--text-color);
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-color);
            font-weight: 500;
        }

        .form-group i {
            position: absolute;
            left: 1rem;
            top: 3.2rem;
            color: #666;
        }

        .form-group input {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 2.5rem;
            border: 2px solid #eee;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        .register-button {
            width: 100%;
            padding: 1rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .register-button:hover {
            background: var(--secondary-color);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 480px) {
            .register-container {
                margin: 1rem;
                padding: 1.5rem;
            }
        }
    </style>
</head>
<?php
include '../includes/db.php';
session_start();

// Kiểm tra nếu đã đăng nhập
if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $full_name = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Kiểm tra tên đăng nhập đã tồn tại
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        $error = "Tên đăng nhập đã tồn tại!";
    } else {
        // Thêm mới
        $stmt = $conn->prepare("INSERT INTO users (username, password, full_name, email, phone, address) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$username, $password, $full_name, $email, $phone, $address])) {
            header("Location: login.php");
            exit();
        } else {
            $error = "Có lỗi xảy ra, vui lòng thử lại!";
        }
    }
}
?>
<body>
    <!-- Navbar if any -->
    <?php include('includes/navbar.php'); ?>

    <!-- Register Form -->
    <div class="register-container">
        <div class="register-header">
            <h2>Đăng ký</h2>
        </div>

        <form method="POST">
            <div class="form-group">
                <label for="fullname">Tên đầy đủ</label>
                <i class="fas fa-user"></i>
                <input type="text" id="fullname" name="fullname" required placeholder="Nhập tên đầy đủ">
            </div>

            <div class="form-group">
                <label for="username">Tên đăng nhập</label>
                <i class="fas fa-user"></i>
                <input type="text" id="username" name="username" required placeholder="Nhập tên đăng nhập">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <i class="fas fa-envelope"></i>
                <input type="email" id="email" name="email" required placeholder="Nhập email">
            </div>

            <div class="form-group">
                <label for="phone">Số điện thoại</label>
                <i class="fas fa-phone"></i>
                <input type="text" id="phone" name="phone" required placeholder="Nhập số điện thoại">
            </div>

            <div class="form-group">
                <label for="address">Địa chỉ</label>
                <i class="fas fa-home"></i>
                <input type="text" id="address" name="address" required placeholder="Nhập địa chỉ">
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <i class="fas fa-lock"></i>
                <input type="password" id="password" name="password" required placeholder="Nhập mật khẩu">
            </div>

            <div class="form-group">
                <label for="confirm-password">Xác nhận mật khẩu</label>
                <i class="fas fa-lock"></i>
                <input type="password" id="confirm-password" name="confirm-password" required placeholder="Nhập lại mật khẩu">
            </div>

            <button type="submit" class="register-button">Đăng ký</button>
        </form>
    </div>

</body>

</html>
