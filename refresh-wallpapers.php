<?php
require_once 'admin/config.php';
require_once 'admin/functions.php';

header('Content-Type: application/json');

if (!isset($_GET['type'])) {
    echo json_encode(['success' => false, 'message' => '参数错误']);
    exit;
}

$type = $_GET['type'];
if ($type !== 'desktop' && $type !== 'mobile') {
    echo json_encode(['success' => false, 'message' => '壁纸类型错误']);
    exit;
}

$limit = ($type === 'desktop') ? 8 : 10;
$wallpapers = getFeaturedWallpapers($conn, $type, $limit);

if (empty($wallpapers)) {
    echo json_encode(['success' => false, 'message' => '暂无壁纸']);
    exit;
}

echo json_encode(['success' => true, 'wallpapers' => $wallpapers]);
