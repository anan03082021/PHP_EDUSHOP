<?php
session_start();
include('../includes/db.php'); // Kết nối với cơ sở dữ liệu

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Lấy thông tin người dùng từ cơ sở dữ liệu
$sql = "SELECT * FROM users WHERE id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->execute([':user_id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Cập nhật thông tin người dùng
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullName = $_POST['full_name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];

    // Cập nhật thông tin
    $updateSql = "UPDATE users SET full_name = :full_name, email = :email, address = :address, phone = :phone WHERE id = :user_id";
    $stmtUpdate = $conn->prepare($updateSql);
    $stmtUpdate->execute([
        ':full_name' => $fullName,
        ':email' => $email,
        ':address' => $address,
        ':phone' => $phone,
        ':user_id' => $userId
    ]);

    $_SESSION['message'] = 'Thông tin của bạn đã được cập nhật thành công!';
    header('Location: profile.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Tin Cá Nhân - EduShop</title>
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
    <?php include('includes/navbar.php'); ?>

    <h1 class="text-center">Thông Tin Cá Nhân</h1>
    <hr>

    <?php
    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-success">' . $_SESSION['message'] . '</div>';
        unset($_SESSION['message']);
    }
    ?>

    <form action="profile.php" method="POST">
        <div class="mb-3">
            <label for="full_name" class="form-label">Họ và Tên</label>
            <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Địa Chỉ</label>
            <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Số Điện Thoại</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Cập Nhật Thông Tin</button>
    </form>

    <hr>
    <a href="product.php" class="btn btn-secondary">Quay lại</a>
    <a href="change_password.php" class="btn btn-warning">Đổi Mật Khẩu</a> 
    
</div>
</div>
<?php include('includes/footer.php'); ?>
</body>
</html>
