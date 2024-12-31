<?php session_start(); 
// Kiểm tra xem người dùng đã đăng nhập hay chưa
if (isset($_SESSION['user_id'])) {
    // Nếu đã đăng nhập, chuyển hướng đến trang product.php
    header("Location: product.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDUSHOP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://img.zcool.cn/community/0132856242c1680002c4212ce5974a.jpg?x-oss-process=image/auto-orient,1/resize,m_lfit,w_1280,limit_1/sharpen,100/quality,q_100') no-repeat center center;
            background-size: cover;
            padding: 180px 0;
            color: white;
            border-radius: 0;
            margin-bottom: 0;
            position: relative;
            overflow: hidden;
        }
        
        .feature-box {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
            padding: 2.5rem !important;
        }
        
        .feature-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }

        .feature-icon {
            background: #B3C8CF;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            transition: all 0.3s ease;
        }

        .stats-section {
            background: linear-gradient(rgba(0,0,0,0.8), rgba(0,0,0,0.8)), url('https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?ixlib=rb-4.0.3') fixed;
            background-size: cover;
            color: white;
            padding: 80px 0;
        }

        .stat-box {
            background: rgba(255,255,255,0.1);
            padding: 40px 30px;
            border-radius: 15px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            transition: all 0.3s ease;
        }

        .stat-box:hover {
            transform: translateY(-5px);
            background: rgba(255,255,255,0.15);
        }

        .footer {
            background: #78B3CE;
            padding: 70px 0 20px;
        }

        .social-links a {
            display: inline-block;
            width: 40px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            margin-right: 10px;
            color: white;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            background: #fff;
            color: #0d47a1;
        }

        .footer-links a {
            color: #fff;
            text-decoration: none;
            padding: 8px 0;
            display: block;
            transition: all 0.3s ease;
        }

        .footer-links a:hover {
            color: #605EA1;
            padding-left: 10px;
        }

        .footer-info li {
            color: #fff;
            margin-bottom: 15px;
        }

        .scroll-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: #C5D3E8;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #608BC1;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .scroll-to-top.visible {
            opacity: 1;
            visibility: visible;
        }

        

        .hero-title {
            font-size: 4rem;
            font-weight: 800;
            margin-bottom: 25px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .hero-subtitle {
            font-size: 1.5rem;
            margin-bottom: 30px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }

        .btn-light {
            padding: 15px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }

        .btn-light:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }

        .counter {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<?php include('includes/navbar.php'); ?>
<body>
    <main>
    <section class="hero-section text-center" data-aos="fade-up">
        <div class="container">
            <h1 class="hero-title">Chào Mừng Đến Với EduShop</h1>
            <p class="hero-subtitle">Mua sắm văn phòng phẩm, dụng cụ học tập chất lượng - Giá cả hợp lý</p>
            <div class="mt-4">
                <a href="login.php" class="btn btn-light btn-lg me-3">
                    <i class="fas fa-shopping-cart me-2"></i> Bắt Đầu Mua Sắm
                </a>
            </div>
        </div>
    </section>

    <section class="features-section py-5">
        <div class="container">
            <h2 class="section-title text-center mb-5" data-aos="fade-up">Tại Sao Chọn EduShop?</h2>
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-box text-center p-4">
                        <div class="feature-icon mb-4">
                            <i class="fas fa-boxes fa-3x text-primary"></i>
                        </div>
                        <h3 class="h4 mb-3">Sản Phẩm Đa Dạng</h3>
                        <p>Với hàng loạt sản phẩm văn phòng phẩm và dụng cụ học tập từ các thương hiệu uy tín.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-box text-center p-4">
                        <div class="feature-icon mb-4">
                            <i class="fas fa-truck fa-3x text-primary"></i>
                        </div>
                        <h3 class="h4 mb-3">Giao Hàng Nhanh Chóng</h3>
                        <p>Chúng tôi đảm bảo giao hàng nhanh chóng và an toàn đến tay khách hàng.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-box text-center p-4">
                        <div class="feature-icon mb-4">
                            <i class="fas fa-tags fa-3x text-primary"></i>
                        </div>
                        <h3 class="h4 mb-3">Giá Cả Hợp Lý</h3>
                        <p>Giá cả luôn hợp lý và có nhiều chương trình khuyến mãi hấp dẫn cho khách hàng.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="stats-section py-5">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-md-3" data-aos="fade-up">
                    <div class="stat-box">
                        <i class="fas fa-users fa-2x text-primary mb-3"></i>
                        <h3 class="counter">10000+</h3>
                        <p>Khách Hàng Hài Lòng</p>
                    </div>
                </div>
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-box">
                        <i class="fas fa-cogs fa-2x text-primary mb-3"></i>
                        <h3 class="counter">200+</h3>
                        <p>Sản Phẩm</p>
                    </div>
                </div>
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-box">
                        <i class="fas fa-store fa-2x text-primary mb-3"></i>
                        <h3 class="counter">50+</h3>
                        <p>Cửa Hàng</p>
                    </div>
                </div>
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="stat-box">
                        <i class="fas fa-star fa-2x text-primary mb-3"></i>
                        <h3 class="counter">98%</h3>
                        <p>Đánh Giá Tốt</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>


    <div class="scroll-to-top">
        <i class="fas fa-arrow-up"></i>
    </div>

    <?php include('includes/footer.php') ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });

        window.addEventListener('scroll', function() {
            var scrollToTop = document.querySelector('.scroll-to-top');
            if (window.pageYOffset > 300) {
                scrollToTop.classList.add('visible');
            } else {
                scrollToTop.classList.remove('visible');
            }
        });

        document.querySelector('.scroll-to-top').addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Counter animation
        const counters = document.querySelectorAll('.counter');
        const speed = 200;

        counters.forEach(counter => {
            const updateCount = () => {
                const target = parseInt(counter.getAttribute('data-target'));
                const count = parseInt(counter.innerText);
                const inc = target / speed;

                if (count < target) {
                    counter.innerText = Math.ceil(count + inc);
                    setTimeout(updateCount, 1);
                } else {
                    counter.innerText = target;
                }
            };

            counter.setAttribute('data-target', counter.innerText);
            counter.innerText = '0';
            updateCount();
        });

        // Thêm hiệu ứng navbar khi scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>

</body>
</html>