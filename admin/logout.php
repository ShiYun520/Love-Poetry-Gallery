<?php
// 启动会话
session_start();

// 销毁所有会话数据
$_SESSION = array();

// 如果使用了会话cookie，将其销毁
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}

// 销毁会话
session_destroy();

// 重定向到登录页面
header("Location: login.php");
exit;
