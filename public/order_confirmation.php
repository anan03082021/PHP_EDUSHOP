<?php
session_start();
include('../includes/db.php'); // Kết nối cơ sở dữ liệu

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Lấy ID đơn hàng từ query string
if (!isset($_GET['order_id'])) {
    die('Invalid order');
}

$orderId = intval($_GET['order_id']);

// Lấy thông tin đơn hàng
$sql_order = "SELECT * FROM don_hang WHERE id = :order_id AND user_id = :user_id";
$stmt_order = $conn->prepare($sql_order);
$stmt_order->execute([':order_id' => $orderId, ':user_id' => $userId]);
$order = $stmt_order->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die('Order not found');
}

// Lấy chi tiết đơn hàng
$sql_details = "SELECT cdh.*, s.name AS product_name, s.price AS product_price
                FROM chi_tiet_don_hang cdh
                JOIN san_pham s ON cdh.san_pham_id = s.id
                WHERE cdh.don_hang_id = :order_id";
$stmt_details = $conn->prepare($sql_details);
$stmt_details->execute([':order_id' => $orderId]);
$orderDetails = $stmt_details->fetchAll(PDO::FETCH_ASSOC);

// Lấy thông tin địa chỉ giao hàng
$sql_address = "SELECT * FROM dia_chi_giao_hang WHERE user_id = :user_id";
$stmt_address = $conn->prepare($sql_address);
$stmt_address->execute([':user_id' => $userId]);
$address = $stmt_address->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận thanh toán - EduShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-4">
    <h1 class="text-center">Xác Nhận Đơn Hàng</h1>
    <hr>

    <h4>Thông Tin Đơn Hàng</h4>
    <p><strong>Mã đơn hàng:</strong> #<?php echo $order['id']; ?></p>
    <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y', strtotime($order['created_at'])); ?></p>
    <p><strong>Tình trạng:</strong> <?php echo $order['status'] == 'pending' ? 'Đang chờ xử lý' : 'Đã hoàn tất'; ?></p>

    <h4>Chi Tiết Sản Phẩm</h4>
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
            <?php
            $totalPrice = 0;
            foreach ($orderDetails as $item) {
                $subtotal = $item['product_price'] * $item['quantity'];
                $totalPrice += $subtotal;
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td><?php echo number_format($item['product_price'], 0, ',', '.'); ?> VND</td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td><?php echo number_format($subtotal, 0, ',', '.'); ?> VND</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <h4>Tổng Tiền: <?php echo number_format($totalPrice, 0, ',', '.'); ?> VND</h4>

    <h4>Thông Tin Giao Hàng</h4>
    <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($address['address']); ?></p>
    <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($address['phone']); ?></p>

    <div class="text-center">
        <a href="index.php" class="btn btn-primary">Tiếp tục mua sắm</a>
    </div>
</div>
</body>
</html>
