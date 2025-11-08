<?php
// 数据库配置
$db_host = "localhost";
$db_user = "bz520";
$db_pass = "bz520";
$db_name = "bz520";

// 创建数据库连接
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 设置字符集
$conn->set_charset("utf8mb4");

// 网站URL（不包含尾部斜杠）
$site_url = "http://fz.torgw.cc/bz";
