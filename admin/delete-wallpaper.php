<?php
require_once 'config.php';
require_once 'functions.php';

// 获取要删除的壁纸ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 获取壁纸信息
$wallpaper = getWallpaper($conn, $id);

// 如果壁纸不存在，重定向到壁纸列表页
if (!$wallpaper) {
    $_SESSION['message'] = '壁纸不存在或已被删除';
    header('Location: wallpapers.php');
    exit;
}

// 记住壁纸类型，用于删除后重定向
$wallpaper_type = $wallpaper['type'];

// 执行删除操作
if (deleteWallpaper($conn, $id)) {
    $_SESSION['message'] = '壁纸已成功删除';
} else {
    $_SESSION['message'] = '删除壁纸失败: ' . $conn->error;
}

// 重定向回壁纸列表页
header('Location: wallpapers.php?type=' . $wallpaper_type);
exit;
