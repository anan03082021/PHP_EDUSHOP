<!DOCTYPE html>
<html lang="vi">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Quản lý Sản phẩm</title>
		<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
		<link rel="stylesheet" href="../assets/css/admin.css">
	</head>
    <?php 
    // Kết nối với cơ sở dữ liệu
    require_once '../../includes/db.php';
    require_once '../models/san_pham.php';
    require_once '../includes/navbar.php';
    require_once '../models/danh_muc.php';

    // Khởi tạo model SanPham và DanhMuc
    $sanPham = new SanPham($conn);
    $danhMuc = new DanhMuc($conn);
    // Biến thông báo
    $successMessage = '';
    $errorMessage = '';
    // Lấy danh sách sản phẩm
    $sanPhamList = $sanPham->getAll();

    // Xử lý thêm sản phẩm
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $category_id = $_POST['category_id'];
        $image = null;

        // Kiểm tra và xử lý ảnh tải lên
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['image']['tmp_name'];
            $fileName = time() . '_' . basename($_FILES['image']['name']); // Tạo tên file duy nhất
            $destination = $_SERVER['DOCUMENT_ROOT'] . '/a1/admin/uploads/' . $fileName;

            // Di chuyển file tải lên vào thư mục uploads
            if (move_uploaded_file($fileTmpPath, $destination)) {
                $image = $fileName; // Lưu tên file vào cơ sở dữ liệu
            } else {
                $errorMessage = "Không thể di chuyển file tải lên!";
            }
        }

        // Sử dụng hàm create trong model SanPham để thêm sản phẩm vào cơ sở dữ liệu
        if ($sanPham->create($name, $price, $description, $image, $category_id)) {
            $successMessage = "Thêm sản phẩm thành công!";
        } else {
            $errorMessage = "Lỗi khi thêm sản phẩm!";
        }
        // Reload lại danh sách sản phẩm
        $sanPhamList = $sanPham->getAll();
    }

    // Xử lý sửa sản phẩm
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    	$id = $_POST['id'];
    	$name = $_POST['name'];
    	$description = $_POST['description'];
    	$price = $_POST['price'];
    	$category_id = $_POST['category_id'];
    	$image = $_POST['image']; // Lưu trữ tên ảnh hiện tại

    	// Kiểm tra và xử lý ảnh mới nếu có
    	if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        	$fileTmpPath = $_FILES['image']['tmp_name'];
        	$fileName = time() . '_' . basename($_FILES['image']['name']); // Tạo tên file duy nhất
        	$destination = $_SERVER['DOCUMENT_ROOT'] . '/a1/admin/uploads/' . $fileName;

        	// Di chuyển file tải lên vào thư mục uploads
        	if (move_uploaded_file($fileTmpPath, $destination)) {
            	// Xóa ảnh cũ nếu có
            	if (!empty($image)) {
                	$oldImagePath = $_SERVER['DOCUMENT_ROOT'] . '/a1/admin/uploads/' . $image;
                	if (file_exists($oldImagePath)) {
                    	unlink($oldImagePath);
                	}
            	}
            	$image = $fileName; // Lưu tên file vào cơ sở dữ liệu
        	} else {
            	$errorMessage = "Không thể di chuyển file tải lên!";
        	}
    	}

    	// Cập nhật sản phẩm
    	if ($sanPham->update($id, $name, $price, $description, $image, $category_id)) {
        	$successMessage = "Cập nhật sản phẩm thành công!";
    	} else {
        	$errorMessage = "Lỗi khi cập nhật sản phẩm!";
    	}
    	header("Location: " . $_SERVER['PHP_SELF']);
    	exit();
	}
	
	//Xử lý xóa sản phẩm 
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
		$id = $_POST['id'];
		if ($sanPham->delete($id)) {
			$successMessage = "Xóa sản phẩm thành công!";
		} else {
			$errorMessage = "Lỗi khi xóa sản phẩm!";
		}
		$sanPhamList = $sanPham->getAll();
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
			<h1>Quản lý Sản phẩm</h1>
			<!-- Nút mở modal thêm sản phẩm-->
			<button class="btn btn-primary" onclick="showAddModal()">
				<i class="fas fa-plus"></i> Thêm sản phẩm </button>
			<!-- Modal thêm sản phẩm-->
			<div id="addModal" class="modal">
				<div class="modal-content">
					<span class="modal-close" onclick="closeModal('addModal')">&times;</span>
					<h3 class="modal-title">Thêm Sản Phẩm Mới</h3>
					<form method="POST" action="manage_products.php">
						<input type="hidden" name="action" value="add">
						<div class="form-group">
							<label for="name">Tên Sản Phẩm:</label>
							<input type="text" id="name" name="name" class="form-control" placeholder="Nhập tên sản phẩm" required>
						</div>
						<div class="form-group">
							<label for="description">Mô Tả:</label>
							<textarea id="description" name="description" class="form-control" rows="3" placeholder="Mô tả chi tiết sản phẩm" required></textarea>
						</div>
						<div class="form-group">
							<label for="price">Giá:</label>
							<input type="number" id="price" name="price" class="form-control" placeholder="Nhập giá sản phẩm" required>
						</div>
						<div class="form-group">
							<label for="category_id">Danh Mục:</label>
							<select class="form-control" id="category_id" name="category_id" required>
								<option value="">Chọn danh mục</option> 
                                <?php
                                $danhMuc = new DanhMuc($conn);
                                $categories = $danhMuc->getAll();
                                foreach ($categories as $category) {
                                    echo "<option value='" . $category['id'] . "'>" . $category['name'] . "</option>";
                                }
                                ?>
							</select>
						</div>
						<div class="form-group">
							<label for="image">Ảnh Sản Phẩm:</label>
							<input type="file" class="form-control-file" id="image" name="image" accept="image/*">
						</div>
						<div class="modal-actions">
							<button type="button" class="btn btn-secondary" onclick="closeModal('addModal')">Hủy</button>
							<button type="submit" class="btn btn-primary">Thêm Sản Phẩm</button>
						</div>
					</form>
				</div>
			</div>
			<!-- Modal sửa sản phẩm -->
			<div id="editModal" class="modal">
    			<div class="modal-content">
        			<span class="modal-close" onclick="closeModal('editModal')">&times;</span>
        			<h3>Sửa Sản Phẩm</h3>
        			<form method="POST" action="manage_products.php" enctype="multipart/form-data">
            			<input type="hidden" name="action" value="edit">
            			<input type="hidden" id="edit_id" name="id">
            			<div class="form-group">
                			<label for="edit_name">Tên Sản Phẩm:</label>
                			<input type="text" id="edit_name" name="name" class="form-control" required>
            			</div>
            			<div class="form-group">
                			<label for="edit_description">Mô Tả:</label>
                			<textarea id="edit_description" name="description" class="form-control" rows="3" required></textarea>
            			</div>
            			<div class="form-group">
                			<label for="edit_price">Giá:</label>
                			<input type="number" id="edit_price" name="price" class="form-control" required>
            			</div>
            			<div class="form-group">
                			<label for="edit_category_id">Danh Mục:</label>
                			<select class="form-control" id="edit_category_id" name="category_id" required>
                    			<?php
                    			$categories = $danhMuc->getAll();
                    			foreach ($categories as $category) {
                        			echo "<option value='" . $category['id'] . "'>" . $category['name'] . "</option>";
                    			}
                    			?>
                			</select>
            			</div>
            			<!-- Trường tải ảnh mới -->
            			<div class="form-group">
                			<label for="edit_image">Ảnh Sản Phẩm:</label>
                			<input type="file" class="form-control-file" id="edit_image" name="image" accept="image/*">
                			<!-- Hiển thị ảnh cũ nếu có -->
                			<div class="mt-2">
                    			<img id="current_image" src="" alt="Current Image" width="100" height="100">
                			</div>
            			</div>
            			<div class="modal-actions">
                			<button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Hủy</button>
                			<button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
            			</div>
        			</form>
    			</div>
			</div>
			<!-- Bảng Danh Sách Sản Phẩm -->
			<table class="table table-striped">
				<thead>
					<tr>
						<th>ID</th>
						<th>Tên sản phẩm</th>
						<th>Giá</th>
						<th>Danh mục</th>
						<th>Mô tả</th>
						<th>Hành động</th>
					</tr>
				</thead>
				<tbody> 
                    <?php while ($row = $sanPhamList->fetch(PDO::FETCH_ASSOC)) : ?> 
                        <tr> <!-- Định dạng form bảng bự --> 
						<td> <?php echo $row['id']; ?> </td>
						<td> <?php echo $row['name']; ?> </td>
						<td> <?php echo number_format($row['price'], 0, ',', '.'); ?> VND </td>
						<td> <?php echo $row['category_id']; ?> </td>
						<td> <?php echo $row['description']; ?> </td>
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
			function showEditModal(sanpham) {
				// Điền dữ liệu sản phẩm vào form trong modal
				document.getElementById('edit_id').value = sanpham.id;
				document.getElementById('edit_name').value = sanpham.name;
				document.getElementById('edit_description').value = sanpham.description;
				document.getElementById('edit_price').value = sanpham.price;
				document.getElementById('edit_category_id').value = sanpham.category_id;
				// Hiển thị modal
				const modal = document.getElementById('editModal');
				modal.style.display = 'block'; // Mở modal bằng cách thay đổi style
				setTimeout(() => modal.classList.add('active'), 10); // Thêm class active sau 10ms
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
					form.action = 'manage_products.php'; // URL xử lý xóa sản phẩm

					// Tạo trường ẩn để gửi ID sản phẩm cần xóa
					const input = document.createElement('input');
					input.type = 'hidden';
					input.name = 'action';
					input.value = 'delete'; // Hành động xóa sản phẩm

					const inputId = document.createElement('input');
					inputId.type = 'hidden';
					inputId.name = 'id';
					inputId.value = id;

					// Thêm các trường vào form
					form.appendChild(input);
					form.appendChild(inputId);

					// Thêm form vào body và gửi đi
					document.body.appendChild(form);
					form.submit(); // Gửi form để xóa sản phẩm
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