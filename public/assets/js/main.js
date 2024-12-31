function switchTab(tabName) {
    const loginForm = document.getElementById('login');
    const signupForm = document.getElementById('signup');
    const loginTab = document.getElementById('login-tab');
    const signupTab = document.getElementById('signup-tab');
  
    // Ẩn tất cả các form
    loginForm.classList.remove('active');
    signupForm.classList.remove('active');
  
    // Thêm lớp active cho tab và form tương ứng
    if (tabName === 'login') {
      loginForm.classList.add('active');
      loginTab.classList.add('active');
      signupTab.classList.remove('active');
    } else {
      signupForm.classList.add('active');
      signupTab.classList.add('active');
      loginTab.classList.remove('active');
    }
  }
  
  // Tự động chọn tab Đăng Nhập khi tải trang
  document.addEventListener('DOMContentLoaded', () => {
    switchTab('login');
  });

  function addToCart(productId) {
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ product_id: productId }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Sản phẩm đã được thêm vào giỏ hàng!');
        } else {
            alert('Có lỗi xảy ra, vui lòng thử lại.');
        }
    })
    .catch(error => {
        console.error('Lỗi:', error);
    });
}
