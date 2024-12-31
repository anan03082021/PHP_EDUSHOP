<?php
session_start();
include('../includes/db.php'); // Kết nối cơ sở dữ liệu

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Lấy thông tin giỏ hàng
$sql = "SELECT g.id AS cart_id, g.quantity, s.id AS product_id, s.name, s.price, s.image 
        FROM gio_hang g
        JOIN san_pham s ON g.san_pham_id = s.id
        WHERE g.user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->execute([':user_id' => $userId]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tính tổng tiền
$totalPrice = 0;
foreach ($cartItems as $item) {
    $subtotal = $item['price'] * $item['quantity'];
    $totalPrice += $subtotal;
}

// Xử lý thanh toán
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $fullName = $_POST['full_name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $paymentMethod = $_POST['payment_method'];

    // Thêm đơn hàng vào bảng don_hang
    $sql_order = "INSERT INTO don_hang (user_id, total_price, status, created_at) VALUES (?, ?, 'pending', NOW())";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->execute([$userId, $totalPrice]);

    // Lấy ID của đơn hàng vừa tạo
    $orderId = $conn->lastInsertId();

    // Thêm chi tiết đơn hàng vào bảng chi_tiet_don_hang
    foreach ($cartItems as $item) {
        $sql_detail = "INSERT INTO chi_tiet_don_hang (don_hang_id, san_pham_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt_detail = $conn->prepare($sql_detail);
        $stmt_detail->execute([$orderId, $item['product_id'], $item['quantity'], $item['price']]);
    }

    // Thêm địa chỉ giao hàng vào bảng dia_chi_giao_hang
    $sql_address = "INSERT INTO dia_chi_giao_hang (user_id, address, phone) VALUES (?, ?, ?)";
    $stmt_address = $conn->prepare($sql_address);
    $stmt_address->execute([$userId, $address, $phone]);

    // Xóa giỏ hàng sau khi thanh toán
    $sql_clear_cart = "DELETE FROM gio_hang WHERE user_id = ?";
    $stmt_clear_cart = $conn->prepare($sql_clear_cart);
    $stmt_clear_cart->execute([$userId]);

    // Chuyển hướng đến trang xác nhận đơn hàng
    header("Location: order_confirmation.php?order_id=$orderId");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán - EduShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }

        .page-container {
            margin-top: 70px;
        }
    </style>
</head>
<body>
<div class="page-container">
    <?php include('includes/navbar.php'); ?>
<div class="container my-4">
    <h1 class="text-center">Thanh Toán</h1>
    <hr>

    <?php if (empty($cartItems)): ?>
        <div class="alert alert-warning text-center">
            Giỏ hàng của bạn đang trống!
        </div>
    <?php else: ?>
        <form method="POST" action="checkout.php">
            <div class="row">
                <div class="col-md-6">
                    <h4>Thông tin giao hàng</h4>
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Họ tên</label>
                        <input type="text" name="full_name" class="form-control" id="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Địa chỉ</label>
                        <input type="text" name="address" class="form-control" id="address" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control" id="phone" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <h4>Thông tin giỏ hàng</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Giá</th>
                                <th>Số lượng</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td><?php echo number_format($item['price'], 0, ',', '.'); ?> VND</td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> VND</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <h5>Tổng tiền: <?php echo number_format($totalPrice, 0, ',', '.'); ?> VND</h5>

                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Phương thức thanh toán</label>
                        <select name="payment_method" class="form-control" id="payment_method" required>
                            <option value="momo">Momo</option>
                            <option value="cod">Thanh toán khi nhận hàng</option>
                        </select>
                    </div>

                    <button type="submit" name="checkout" class="btn btn-success btn-lg w-100">Xác nhận thanh toán</button>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>
<?php include('includes/footer.php'); ?>
</div>
</body>
</html>
