// File: admin.js

// Hiệu ứng hover cho các nút
const btns = document.querySelectorAll('.btn');
btns.forEach(btn => {
    btn.addEventListener('mouseenter', function() {
        this.style.transform = 'scale(1.05)';
    });

    btn.addEventListener('mouseleave', function() {
        this.style.transform = 'scale(1)';
    });
});

// Xóa sản phẩm - xác nhận trước khi xóa
function deleteProduct(id) {
    if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
        // Thực hiện xóa sản phẩm (Gửi yêu cầu Ajax hoặc chuyển hướng đến trang xử lý xóa)
        window.location.href = 'delete_product.php?id=' + id;
    }
}

// Hiển thị modal sửa sản phẩm
function showEditModal(productData) {
    // Giả sử bạn có modal sửa sản phẩm trong HTML, bạn có thể truyền dữ liệu vào để hiển thị
    document.getElementById('product-id').value = productData.id;
    document.getElementById('product-name').value = productData.name;
    document.getElementById('product-price').value = productData.price;
    document.getElementById('product-category').value = productData.category_id;
    document.getElementById('product-description').value = productData.description;

    // Mở modal
    $('#editModal').modal('show');
}

// Thêm hiệu ứng active cho các mục trong navbar
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function() {
        // Loại bỏ lớp active khỏi tất cả các liên kết
        document.querySelectorAll('.nav-link').forEach(item => item.classList.remove('active'));
        // Thêm lớp active vào liên kết đang được click
        this.classList.add('active');
    });
});
