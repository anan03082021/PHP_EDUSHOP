<?php
session_start();
session_unset(); // Hủy tất cả các biến 
session_destroy(); 
header('Location: ../../public/index.php'); // Quay lại trang chủ
exit();
?>
