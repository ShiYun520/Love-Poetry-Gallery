<?php
// 数据库连接配置
define('DB_HOST', 'localhost');
define('DB_USER', 'bz520');
define('DB_PASS', 'bz520');
define('DB_NAME', 'bz520');

// 创建连接
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 设置字符集
$conn->set_charset("utf8");

// 站点URL
define('SITE_URL', 'http://fz.torgw.cc/bz');
define('ADMIN_URL', SITE_URL . '/admin');
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/bz/uploads');
define('UPLOAD_URL', SITE_URL . '/uploads');

// 启动会话
session_start();

// 检查用户是否登录
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// 如果用户未登录且当前页面不是login.php，则重定向到登录页面
$current_file = basename($_SERVER['PHP_SELF']);
if (!isLoggedIn() && $current_file != 'login.php') {
    header('Location: login.php');
    exit;
}
?>
