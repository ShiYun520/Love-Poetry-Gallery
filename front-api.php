<?php
require_once 'front-config.php';
require_once 'front-functions.php';

header('Content-Type: application/json');

if (isset($_GET['action']) && $_GET['action'] == 'refresh') {
    $type = isset($_GET['type']) ? $_GET['type'] : '';
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 8;
    
    if ($type == 'desktop' || $type == 'mobile') {
        $wallpapers = getRandomWallpapers($conn, $type, $limit);
        echo json_encode(['success' => true, 'data' => $wallpapers]);
    } else {
        echo json_encode(['success' => false, 'message' => '无效的壁纸类型']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => '无效的操作']);
