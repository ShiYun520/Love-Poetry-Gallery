<?php
require_once 'config.php';
require_once 'functions.php';

// 检查用户是否已登录
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// 检查是否有ID参数
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = '无效的壁纸ID';
    header('Location: wallpapers.php');
    exit;
}

$wallpaper_id = (int)$_GET['id'];

// 获取要复制的壁纸信息
$sql = "SELECT * FROM wallpapers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $wallpaper_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['message'] = '找不到要复制的壁纸';
    header('Location: wallpapers.php');
    exit;
}

$wallpaper = $result->fetch_assoc();

// 准备新数据，修改标题以表明这是一个副本
$new_title = $wallpaper['title'] . ' (副本)';
$description = $wallpaper['description'];
$image_url = $wallpaper['image_url'];
$thumbnail_url = $wallpaper['thumbnail_url'];
$type = $wallpaper['type'];
$is_external = $wallpaper['is_external'];
$current_time = date('Y-m-d H:i:s');

// 插入新数据
$sql = "INSERT INTO wallpapers (title, description, image_url, thumbnail_url, type, is_external, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $new_title, $description, $image_url, $thumbnail_url, $type, $is_external, $current_time);

if ($stmt->execute()) {
    $_SESSION['message'] = '壁纸已成功复制';
} else {
    $_SESSION['message'] = '复制壁纸时出错: ' . $conn->error;
}

// 重定向回壁纸列表页面，保持原来的壁纸类型筛选
header('Location: wallpapers.php?type=' . $type);
exit;
?>
