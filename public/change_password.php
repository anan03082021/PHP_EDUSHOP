<?php
session_start();
include('../includes/db.php');

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Kiểm tra khi người dùng gửi form thay đổi mật khẩu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Kiểm tra mật khẩu cũ
    $sql = "SELECT password FROM users WHERE id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':user_id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Kiểm tra nếu mật khẩu hiện tại chính xác
    if ($currentPassword !== $user['password']) {
        $error = "Mật khẩu hiện tại không chính xác!";
    } elseif ($newPassword !== $confirmPassword) {
        $error = "Mật khẩu mới và xác nhận mật khẩu không khớp!";
    } else {
        // Cập nhật mật khẩu mới (không mã hóa)
        $updateSql = "UPDATE users SET password = :password WHERE id = :user_id";
        $stmtUpdate = $conn->prepare($updateSql);
        $stmtUpdate->execute([':password' => $newPassword, ':user_id' => $userId]);

        $_SESSION['message'] = 'Mật khẩu đã được thay đổi thành công!';
        header('Location: profile.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đổi Mật Khẩu - EduShop</title>
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

    <h1 class="text-center">Đổi Mật Khẩu</h1>
    <hr>

    <?php
    if (isset($error)) {
        echo '<div class="alert alert-danger">' . $error . '</div>';
    }
    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-success">' . $_SESSION['message'] . '</div>';
        unset($_SESSION['message']);
    }
    ?>

    <form action="change_password.php" method="POST">
        <div class="mb-3">
            <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
            <input type="password" class="form-control" id="current_password" name="current_password" required>
        </div>
        <div class="mb-3">
            <label for="new_password" class="form-label">Mật khẩu mới</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required>
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật mật khẩu</button>
    </form>

    <hr>
    <a href="profile.php" class="btn btn-secondary">Quay lại</a>
</div>
</div>
<?php include('includes/footer.php'); ?>
</body>
</html>
