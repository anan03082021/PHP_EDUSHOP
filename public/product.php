<?php
session_start();
include('../includes/db.php'); 

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Chuyển hướng đến trang đăng nhập
    exit();
}

$userId = $_SESSION['user_id']; 

// Xử lý thêm sản phẩm vào giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $productId = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $quantity = 1;

    // Kiểm tra nếu sản phẩm đã tồn tại trong giỏ hàng
    $sql = "SELECT * FROM gio_hang WHERE user_id = :user_id AND san_pham_id = :product_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':user_id' => $userId, ':product_id' => $productId]);
    $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cartItem) {
        // Nếu đã tồn tại, cập nhật số lượng
        $newQuantity = $cartItem['quantity'] + $quantity;
        $updateSql = "UPDATE gio_hang SET quantity = :quantity WHERE id = :cart_id";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->execute([':quantity' => $newQuantity, ':cart_id' => $cartItem['id']]);
    } else {
        // Nếu chưa tồn tại, thêm mới
        $insertSql = "INSERT INTO gio_hang (user_id, san_pham_id, quantity) VALUES (:user_id, :product_id, :quantity)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->execute([':user_id' => $userId, ':product_id' => $productId, ':quantity' => $quantity]);
    }
}

// Xử lý tìm kiếm sản phẩm
$searchQuery = '%';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchQuery = '%' . $_GET['search'] . '%'; // Thêm ký tự % để hỗ trợ LIKE
}

// Lấy danh mục sản phẩm
$categories = ['Văn phòng phẩm', 'Dụng cụ học tập', 'Dụng cụ mỹ thuật', 'Quà lưu niệm'];

require_once '../admin/models/danh_muc.php';
require_once '../admin/models/san_pham.php';

// Truy vấn sản phẩm theo danh mục và tìm kiếm với phân trang
$limit = 8; // Số sản phẩm trên mỗi trang
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start = ($page - 1) * $limit;

$sql = "SELECT id, name, description, price, category_id, image FROM san_pham WHERE name LIKE :search";

// Thêm điều kiện lọc theo danh mục nếu có
if (isset($_GET['category']) && in_array($_GET['category'], $categories)) {
    $category = $_GET['category'];
    $sql .= " AND category_id = (SELECT id FROM danh_muc WHERE name = :category)";
}

$sql .= " LIMIT :start, :limit";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':search', $searchQuery, PDO::PARAM_STR);
$stmt->bindParam(':start', $start, PDO::PARAM_INT);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);

if (isset($category)) {
    $stmt->bindParam(':category', $category, PDO::PARAM_STR);
}

$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tính tổng số sản phẩm (phục vụ phân trang)
$totalSql = "SELECT COUNT(*) FROM san_pham WHERE name LIKE :search";
if (isset($category)) {
    $totalSql .= " AND category_id = (SELECT id FROM danh_muc WHERE name = :category)";
}
$totalStmt = $conn->prepare($totalSql);
$totalStmt->bindParam(':search', $searchQuery, PDO::PARAM_STR);
if (isset($category)) {
    $totalStmt->bindParam(':category', $category, PDO::PARAM_STR);
}
$totalStmt->execute();
$totalProducts = $totalStmt->fetchColumn();

// Xử lý thêm sản phẩm vào giỏ hàng
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $productId = intval($_POST['product_id']);
    $sql = "SELECT id, name, price FROM san_pham WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $productId, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += 1;
        } else {
            $_SESSION['cart'][$productId] = [
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => 1,
            ];
        }
        $_SESSION['message'] = "Sản phẩm đã được thêm vào giỏ hàng!";
        header("Location: product.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDUSHOP - Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-title {
            font-weight: bold;
            font-size: 1.2rem;
            color: #343a40;
        }
        .card-text {
            color: #6c757d;
        }
        .card img {
            border-radius: 10px 10px 0 0;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .nav-link.active {
            font-weight: bold;
            color: #007bff !important;
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
        <h1 class="text-center">Danh Sách Sản Phẩm</h1>
        <hr>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success text-center"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>

        <div class="search-bar text-center">
            <form method="GET" action="product.php" class="d-flex justify-content-center">
                <input type="text" name="search" class="form-control w-50" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" placeholder="Tìm kiếm sản phẩm...">
                <button type="submit" class="btn btn-primary ms-2">Tìm kiếm</button>
            </form>
        </div>

        <div class="mb-4">
            <ul class="nav justify-content-center">
                <?php foreach ($categories as $cat): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($category) && $category == $cat) ? 'active' : ''; ?>" 
                           href="product.php?category=<?php echo urlencode($cat); ?>">
                           <?php echo $cat; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <?php if (empty($products)): ?>
            <p class="text-center text-muted">Không tìm thấy sản phẩm nào.</p>
        <?php else: ?>
            <div class="row">
                <?php foreach ($products as $row): ?>
                    <div class="col-md-4 col-lg-3">
                        <div class="card mb-4">
                            <img src="../admin/uploads/<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['name']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                                <p class="text-danger"><strong><?php echo number_format($row['price'], 0, ',', '.'); ?> VND</strong></p>
                                <form method="POST" action="product.php">
                                    <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="add_to_cart" class="btn btn-primary btn-block">
                                        <i class="fas fa-shopping-cart"></i> Thêm vào giỏ hàng
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-center mt-4">
            <nav>
                <ul class="pagination">
                    <?php for ($i = 1; $i <= ceil($totalProducts / $limit); $i++): ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="product.php?page=<?php echo $i; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>

    <?php include('includes/footer.php'); ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
