<?php
session_start();
include('../includes/db.php'); // Kết nối cơ sở dữ liệu

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Xử lý cập nhật số lượng sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    $cartId = intval($_POST['cart_id']);
    $quantity = intval($_POST['quantity']);

    if ($quantity > 0) {
        $sql = "UPDATE gio_hang SET quantity = :quantity WHERE id = :cart_id AND user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':quantity' => $quantity, ':cart_id' => $cartId, ':user_id' => $userId]);
    } else {
        // Nếu số lượng là 0, xóa sản phẩm khỏi giỏ hàng
        $sql = "DELETE FROM gio_hang WHERE id = :cart_id AND user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':cart_id' => $cartId, ':user_id' => $userId]);
    }
    header('Location: cart.php');
    exit();
}

// Xử lý xóa sản phẩm khỏi giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_item'])) {
    $cartId = intval($_POST['cart_id']);
    $sql = "DELETE FROM gio_hang WHERE id = :cart_id AND user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':cart_id' => $cartId, ':user_id' => $userId]);
    header('Location: cart.php');
    exit();
}

// Lấy danh sách sản phẩm trong giỏ hàng
$sql = "SELECT g.id AS cart_id, g.quantity, s.id AS product_id, s.name, s.price, s.image 
        FROM gio_hang g
        JOIN san_pham s ON g.san_pham_id = s.id
        WHERE g.user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->execute([':user_id' => $userId]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng - EduShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }

        .table img {
            width: 60px;
            height: auto;
            border-radius: 5px;
        }

        .table td, .table th {
            vertical-align: middle;
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
        <h1 class="text-center">Giỏ Hàng</h1>
        <hr>

        <?php if (empty($cartItems)): ?>
            <div class="alert alert-warning text-center">
                Giỏ hàng của bạn đang trống!
            </div>
            <hr>
            <a href="product.php" class="btn btn-secondary">Quay lại</a>
        <?php else: ?>
            <form method="POST" action="cart.php">
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>Hình ảnh</th>
                            <th>Sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $totalPrice = 0;
                        foreach ($cartItems as $item): 
                            $subtotal = $item['price'] * $item['quantity'];
                            $totalPrice += $subtotal;
                        ?>
                            <tr>
                                <td><img src="../admin/uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>"></td>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo number_format($item['price'], 0, ',', '.'); ?> VND</td>
                                <td>
                                    <form method="POST" action="cart.php">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="form-control text-center">
                                        <button type="submit" name="update_cart" class="btn btn-sm btn-primary mt-2">Cập nhật</button>
                                    </form>
                                </td>
                                <td><?php echo number_format($subtotal, 0, ',', '.'); ?> VND</td>
                                <td>
                                    <form method="POST" action="cart.php">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                        <button type="submit" name="delete_item" class="btn btn-sm btn-danger">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </form>

            <div class="d-flex justify-content-between">
                <h4>Tổng tiền: <?php echo number_format($totalPrice, 0, ',', '.'); ?> VND</h4>
                <a href="checkout.php" class="btn btn-success">Thanh Toán</a>
            </div>
        <?php endif; ?>
    </div>

    <?php include('includes/footer.php'); ?>
</div>

</body>
</html>
