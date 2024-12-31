<!DOCTYPE html>
<html lang="vi">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Quản lý Người dùng</title>
		<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
		<link rel="stylesheet" href="../assets/css/admin.css">
	</head>
    <?php
// Kết nối với cơ sở dữ liệu
require_once '../../includes/db.php';
require_once '../models/users.php';
require_once '../includes/navbar.php';

// Khởi tạo model user
$user = new Users($conn);

// Biến thông báo
$successMessage = '';
$errorMessage = '';

// Lấy danh sách user
$UsersList = $user->getAll();

// Xử lý thêm người dùng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
	$username= $_POST['username'];
    $password = $_POST['password'];

    // Sử dụng hàm create trong model users để thêm người dùng vào cơ sở dữ liệu
    if ($user->create($username, $password, $email,$full_name,$phone)) {
        $successMessage = "Thêm người dùng thành công!";
    } else {
        $errorMessage = "Lỗi khi thêm người dùng!";
    }

    // Reload lại danh sách user
    $UsersList = $user->getAll();
}

// Xử lý sửa người dùng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = $_POST['id'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    // Sử dụng hàm update trong model users để cập nhật người dùng
    if ($user->update($id, $full_name, $email, $phone, $password)) {
        $successMessage = "Cập nhật người dùng thành công!";
    } else {
        $errorMessage = "Lỗi khi cập nhật người dùng!";
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Xử lý xóa người dùng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteId'])) {
    $deleteId = $_POST['deleteId'];

    // Kiểm tra xem người dùng có tồn tại trong cơ sở dữ liệu không trước khi xóa
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$deleteId]);
    $userRecord = $stmt->fetch();

    if ($userRecord) {
        // Xóa người dùng khỏi cơ sở dữ liệu
        $deleteStmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $deleteStmt->execute([$deleteId]);
    } else {
        $_SESSION['error_message'] = "Người dùng không tồn tại!";
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>
    <style>
		/* Modal styles */
		.modal {
			display: none;
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background-color: rgba(0, 0, 0, 0.5);
			/* Nền tối */
			z-index: 9999;
			/* Đặt mức cao để modal luôn nổi */
			opacity: 0;
			/* Modal ban đầu ẩn */
			transition: opacity 0.3s ease, transform 0.3s ease;
			/* Hiệu ứng nổi lên */
			backdrop-filter: blur(5px);
			/* Hiệu ứng làm mờ nền */
		}

		.modal.active {
			display: block;
			opacity: 1;
			/* Modal hiển thị */
			transform: scale(1);
			/* Giảm hiệu ứng phóng to */
		}

		.modal-content {
			background-color: #fff;
			margin: auto;
			/* Căn giữa ngang */
			position: relative;
			top: 50%;
			/* Căn giữa dọc */
			transform: translateY(-50%);
			/* Dịch modal lên giữa màn hình */
			padding: 20px;
			border-radius: 8px;
			width: 90%;
			max-width: 500px;
			box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
		}

		.modal-close {
			position: absolute;
			right: 15px;
			top: 10px;
			font-size: 24px;
			color: #333;
			cursor: pointer;
		}

		.form-group {
			margin-bottom: 15px;
		}

		.form-group label {
			display: block;
			margin-bottom: 5px;
		}

		.form-group input {
			width: 100%;
			padding: 8px;
			border: 1px solid #ddd;
			border-radius: 4px;
		}

		.modal-actions {
			text-align: right;
			margin-top: 20px;
		}

		.alert {
			padding: 15px;
			margin-bottom: 20px;
			border-radius: 4px;
		}

		.alert-success {
			background-color: #dff0d8;
			border-color: #d6e9c6;
			color: #3c763d;
		}
	</style>
	<body>
		<div class="container">
			<h1>Quản lý Người dùng</h1>
			<!-- Nút mở modal thêm sản phẩm-->
			<button class="btn btn-primary" onclick="showAddModal()">
				<i class="fas fa-plus"></i> Thêm người dùng </button>
			<!-- Modal thêm người dùng-->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal('addModal')">&times;</span>
        <h3 class="modal-title">Thêm Người Dùng Mới</h3>
        <form method="POST" action="manage_users.php">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <label for="full_name">Tên Người Dùng:</label>
                <input type="text" id="full_name" name="full_name" class="form-control" placeholder="Nhập tên người dùng" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Nhập email người dùng" required>
            </div>
            <div class="form-group">
                <label for="phone">Số Điện Thoại:</label>
                <input type="text" id="phone" name="phone" class="form-control" placeholder="Nhập số điện thoại người dùng" required>
            </div>
			<div class="form-group">
                <label for="username">Tên đăng nhập:</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="Nhập tên tài khoản" required>
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addModal')">Hủy</button>
                <button type="submit" class="btn btn-primary">Thêm Người Dùng</button>
            </div>
        </form>
    </div>
</div>
			<!-- Modal sửa người dùng-->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal('editModal')">&times;</span>
        <h3>Sửa Người Dùng</h3>
        <form method="POST" action="manage_users.php">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" id="edit_id" name="id">
            <div class="form-group">
                <label for="edit_full_name">Tên Người Dùng:</label>
                <input type="text" id="edit_full_name" name="full_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="edit_email">Email:</label>
                <input type="email" id="edit_email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="edit_phone">Số Điện Thoại:</label>
                <input type="text" id="edit_phone" name="phone" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="edit_password">Mật khẩu:</label>
                <input type="password" id="edit_password" name="password" class="form-control" required>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
            </div>
        </form>
    </div>
</div>

			<!-- Bảng Danh Sách Người dùng -->
			<table class="table table-striped">
				<thead>
					<tr>
						<th>ID</th>
						<th>Tên người dùng</th>
						<th>Email</th>
						<th>Phone</th>
						<th>Ngày tạo</th>
						<th>Hành động</th>
					</tr>
				</thead>
				<tbody> 
                    <?php while ($row = $UsersList->fetch(PDO::FETCH_ASSOC)) : ?> 
                        <tr> <!-- Định dạng form bảng bự --> 
						<td> <?php echo $row['id']; ?> </td>
						<td> <?php echo $row['full_name']; ?> </td>
						<td> <?php echo $row['email']; ?> </td>
						<td> <?php echo $row['phone']; ?> </td>
						<td> <?php echo $row['created_at']; ?> </td>
						<td>
							<button class="btn btn-edit" onclick="showEditModal(<?php echo htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8'); ?>)">
								<i class="fas fa-edit"></i> Sửa 
                            </button>
							<button class="btn btn-delete" onclick="deleteSanPham(<?php echo $row['id']; ?>)">
								<i class="fas fa-trash"></i> Xóa 
                            </button>
						</td>
					    </tr> 
                    <?php endwhile; ?> 
                </tbody>
			</table>
		</div>
		<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
		<script>
			// Hàm mở modal thêm sản phẩm
			function showAddModal() {
				const modal = document.getElementById('addModal');
				modal.style.display = 'block';
				setTimeout(() => modal.classList.add('active'), 10);
			}
			// Mở modal sửa thông tin sản phẩm
			function showEditModal(user) {
    document.getElementById('edit_id').value = user.id;
    document.getElementById('edit_full_name').value = user.full_name;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_phone').value = user.phone;
    document.getElementById('edit_password').value = user.password;

    const modal = document.getElementById('editModal');
    modal.style.display = 'block';
    setTimeout(() => modal.classList.add('active'), 10);
}

			// Đóng modal
			function closeModal(modalId) {
				const modal = document.getElementById(modalId);
				modal.style.display = 'none';
			}
			// Xóa sản phẩm
			function deleteSanPham(id) {
				if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
					const form = document.createElement('form');
					form.method = 'POST';
					form.innerHTML = `<input type="hidden" name="deleteId" value="${id}">`;
					document.body.appendChild(form);
					form.submit();
				}
			}
			// Cập nhật event listener cho click outside để đóng modal
			window.onclick = function(event) {
				// Nếu click ra ngoài modal, sẽ đóng modal
				if (event.target.classList.contains('modal')) {
					closeModal(event.target.id);
				}
			}
		</script>
	</body>
</html>