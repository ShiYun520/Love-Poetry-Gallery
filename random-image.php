<?php
header('Content-Type: application/json');

// 设置图片目录路径
$directory = 'uploads/mobile/';

// 获取目录中所有图片文件
$files = glob($directory . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);

if(empty($files)) {
    echo json_encode(['success' => false, 'message' => '没有找到图片']);
    exit;
}

// 随机选择一个图片
$randomImage = $files[array_rand($files)];

echo json_encode([
    'success' => true,
    'imageUrl' => $randomImage
]);
?>
