<?php
session_start();
include('../includes/db.php'); // Kết nối với cơ sở dữ liệu

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Lấy danh sách đơn hàng của người dùng
$sql = "SELECT id, total_price, status, created_at FROM don_hang WHERE user_id = :user_id ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute([':user_id' => $userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn hàng của bạn - EduShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
        <h1 class="text-center">Đơn Hàng Của Bạn</h1>
        <hr>

        <?php if (empty($orders)): ?>
            <div class="alert alert-warning text-center">
                Bạn chưa có đơn hàng nào!
            </div>
        <?php else: ?>
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>Mã đơn hàng</th>
                        <th>Tổng tiền</th>
                        <th>Ngày tạo</th>
                        <th>Trạng thái</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo $order['id']; ?></td>
                            <td><?php echo number_format($order['total_price'], 0, ',', '.'); ?> VND</td>
                            <td><?php echo date('d-m-Y', strtotime($order['created_at'])); ?></td>
                            <td>
                                <?php
                                if ($order['status'] == 'pending') {
                                    echo 'Chờ xử lý';
                                } elseif ($order['status'] == 'shipping') {
                                    echo 'Đang giao';
                                } else {
                                    echo 'Hoàn thành';
                                }
                                ?>
                            </td>
                            <td>
                                <a href="order_details.php?order_id=<?php echo $order['id']; ?>" class="btn btn-info btn-sm">Xem chi tiết</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <?php include('includes/footer.php'); ?>
</div>

</body>
</html>
