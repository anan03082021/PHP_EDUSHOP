<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Đơn hàng</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<?php
// Kết nối cơ sở dữ liệu
require_once '../../includes/db.php';
require_once '../models/don_hang.php';
require_once '../models/chi_tiet_don_hang.php';
require_once '../includes/navbar.php';

try {
    // Khởi tạo các model cần thiết
    $donHang = new DonHang($conn);
    $chiTietDonHang = new ChiTietDonHang($conn);

    // Kiểm tra nếu có ID trong URL, thực hiện hành động tương ứng
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $orderId = intval($_GET['id']);
        // Xử lý xem chi tiết đơn hàng
        if (isset($_GET['action']) && $_GET['action'] == 'view') {
            $orderDetails = $donHang->getById($orderId)->fetch(PDO::FETCH_ASSOC);
            if (!$orderDetails) {
                echo "<div class='alert alert-danger'>Không tìm thấy đơn hàng với ID: {$orderId}</div>";
            } else {
                $orderItems = $chiTietDonHang->getByDonHangId($orderId);
            }
        }
        // Xử lý cập nhật trạng thái đơn hàng
        if (isset($_POST['status']) && !empty($_POST['status'])) {
            $newStatus = htmlspecialchars($_POST['status']);
            $donHang->updateStatus($orderId, $newStatus);
            header("Location: manage_orders.php"); // Quay lại trang quản lý sau khi cập nhật
            exit();
        }
        // Xử lý xóa đơn hàng
        if (isset($_GET['action']) && $_GET['action'] == 'delete') {
            $donHang->delete($orderId);
            header("Location: manage_orders.php"); // Quay lại trang quản lý sau khi xóa
            exit();
        }
    } else {
        // Lấy danh sách tất cả đơn hàng nếu không có action cụ thể
        $sql = "SELECT don_hang.id, don_hang.created_at, don_hang.status, don_hang.total_price, 
                   users.username as username
                FROM don_hang
                INNER JOIN users ON don_hang.user_id = users.id";
        $donHangList = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC); // Trả về danh sách
    }
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Lỗi kết nối hoặc xử lý: {$e->getMessage()}</div>";
    die();
}
?>
<body>
    <div class="container">
        <h1>Quản lý Đơn hàng</h1>

        <?php if (isset($orderDetails)) : ?>
            <!-- Hiển thị chi tiết đơn hàng -->
            <h3>Chi tiết Đơn hàng</h3>
            <table class="table table-bordered">
                <tr>
                    <th>ID Đơn Hàng</th>
                    <td><?php echo $orderDetails['id']; ?></td>
                </tr>
                <tr>
                    <th>Người dùng</th>
                    <td><?php echo $orderDetails['username']; ?></td> 
                </tr>
                <tr>
                    <th>Tổng tiền</th>
                    <td><?php echo number_format($orderDetails['total_price'], 2); ?> VNĐ</td>
                </tr>
                <tr>
                    <th>Tình trạng</th>
                    <td><?php echo $orderDetails['status']; ?></td>
                </tr>
                <tr>
                    <th>Ngày đặt</th>
                    <td><?php echo $orderDetails['created_at']; ?></td>
                </tr>
            </table>

            <!-- Hiển thị danh sách sản phẩm trong đơn hàng -->
            <?php
            $orderTotal = 0;
            foreach ($orderItems as $item) {
                $orderTotal += $item['quantity'] * $item['price']; 
            }
            ?>
            <?php if (!empty($orderItems)) : ?>
                <h4>Danh sách sản phẩm</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID Sản phẩm</th>
                            <th>Tên sản phẩm</th>
                            <th>Số lượng</th>
                            <th>Giá</th>
                            <th>Tổng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderItems as $item) : ?>
                            <tr>
                                <td><?php echo $item['san_pham_id']; ?></td>
                                <td><?php echo $item['san_pham_name']; ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td><?php echo number_format($item['price'], 2); ?> VNĐ</td>
                                <td><?php echo number_format($item['quantity'] * $item['price'], 2); ?> VNĐ</td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="4" class="text-right"><strong></strong></td>
                            <td><strong><?php echo number_format($orderTotal, 2); ?> VNĐ</strong></td>
                        </tr>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-warning">Không có sản phẩm nào trong đơn hàng này.</div>
            <?php endif; ?>

            <!-- Form cập nhật trạng thái -->
            <h4>Cập nhật trạng thái đơn hàng</h4>
            <form method="POST">
                <div class="form-group">
                    <label for="status">Trạng thái:</label>
                    <select class="form-control" name="status" id="status">
                        <option value="pending" <?php echo ($orderDetails['status'] == 'pending') ? 'selected' : ''; ?>>Chờ xác nhận</option>
                        <option value="completed" <?php echo ($orderDetails['status'] == 'completed') ? 'selected' : ''; ?>>Đã giao</option>
                        <option value="canceled" <?php echo ($orderDetails['status'] == 'canceled') ? 'selected' : ''; ?>>Hủy</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Cập nhật</button>
            </form>

            <a href="manage_orders.php" class="btn btn-secondary">Quay lại</a>

        <?php else: ?>
            <!-- Hiển thị danh sách đơn hàng -->
            <?php if (!empty($donHangList)) : ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Người dùng</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Tình trạng</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($donHangList as $row) : ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['username']; ?></td> 
                                <td><?php echo $row['created_at']; ?></td>
                                <td><?php echo number_format($row['total_price'], 2); ?> VNĐ</td>
                                <td><?php echo $row['status']; ?></td>
                                <td>
                                    <a href="?id=<?php echo $row['id']; ?>&action=view" class="btn btn-info">Xem</a>
                                    <a href="?id=<?php echo $row['id']; ?>&action=delete" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?');"> <i class="fas fa-trash"></i> Xóa</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-warning">Không có đơn hàng nào trong hệ thống.</div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
