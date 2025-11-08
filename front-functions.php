<?php
/**
 * 获取壁纸列表函数
 */
function getWallpapers($conn, $type, $limit, $offset) {
    $sql = "SELECT * FROM wallpapers WHERE type = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $type, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $wallpapers = [];
    while ($row = $result->fetch_assoc()) {
        $wallpapers[] = $row;
    }
    
    return $wallpapers;
}

// 其他前端所需函数...
function getFeaturedWallpapers($conn, $type, $limit) {
    $sql = "SELECT * FROM wallpapers WHERE type = ? ORDER BY RAND() LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $type, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $wallpapers = [];
    while ($row = $result->fetch_assoc()) {
        $wallpapers[] = $row;
    }
    
    return $wallpapers;
}

function countWallpapers($conn, $type) {
    $sql = "SELECT COUNT(*) as total FROM wallpapers WHERE type = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $type);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['total'];
}

// 添加到front-functions.php
function getRandomWallpapers($conn, $type, $limit) {
    $sql = "SELECT * FROM wallpapers WHERE type = ? ORDER BY RAND() LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $type, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $wallpapers = [];
    while ($row = $result->fetch_assoc()) {
        $wallpapers[] = $row;
    }
    
    return $wallpapers;
}
