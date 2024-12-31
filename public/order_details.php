<?php
session_start();
include('../includes/db.php'); // Kết nối với cơ sở dữ liệu

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$orderId = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Lấy thông tin đơn hàng
$sql = "SELECT id, total_price, status, created_at FROM don_hang WHERE user_id = :user_id AND id = :order_id";
$stmt = $conn->prepare($sql);
$stmt->execute([':user_id' => $userId, ':order_id' => $orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

// Nếu đơn hàng không tồn tại
if (!$order) {
    header('Location: orders.php');
    exit();
}

// Lấy chi tiết sản phẩm trong đơn hàng
$sql_details = "SELECT s.name, c.quantity, c.price, (c.quantity * c.price) AS subtotal
                FROM chi_tiet_don_hang c
                JOIN san_pham s ON c.san_pham_id = s.id
                WHERE c.don_hang_id = :order_id";
$stmt_details = $conn->prepare($sql_details);
$stmt_details->execute([':order_id' => $orderId]);
$orderDetails = $stmt_details->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn hàng - EduShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    .page-container {
        margin-top: 70px;
    }
</style>
<body>
<div class="page-container">
<div class="container my-4">
    <?php include('includes/navbar.php');?>
    <h1 class="text-center">Chi Tiết Đơn Hàng #<?php echo $order['id']; ?></h1>
    <hr>

    <h4>Tổng tiền: <?php echo number_format($order['total_price'], 0, ',', '.'); ?> VND</h4>
    <h4>Trạng thái: 
        <?php
        if ($order['status'] == 'pending') {
            echo 'Chờ xử lý';
        } elseif ($order['status'] == 'shipping') {
            echo 'Đang giao';
        } else {
            echo 'Hoàn thành';
        }
        ?>
    </h4>
    <h4>Ngày tạo: <?php echo date('d-m-Y', strtotime($order['created_at'])); ?></h4>

    <h3 class="mt-4">Chi Tiết Sản Phẩm</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tên sản phẩm</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orderDetails as $detail): ?>
                <tr>
                    <td><?php echo $detail['name']; ?></td>
                    <td><?php echo $detail['quantity']; ?></td>
                    <td><?php echo number_format($detail['price'], 0, ',', '.'); ?> VND</td>
                    <td><?php echo number_format($detail['subtotal'], 0, ',', '.'); ?> VND</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="orders.php" class="btn btn-secondary">Quay lại</a>
</div>
</div>
<?php include('includes/footer.php');?>
</body>
</html>
