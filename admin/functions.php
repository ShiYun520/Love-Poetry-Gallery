<?php
require_once 'config.php';

// 生成缩略图函数
function createThumbnail($source, $destination, $width = 300) {
    $info = getimagesize($source);
    $mime = $info['mime'];
    
    switch ($mime) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $image = imagecreatefrompng($source);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($source);
            break;
        default:
            return false;
    }
    
    $old_width = imagesx($image);
    $old_height = imagesy($image);
    $scale = $width / $old_width;
    $new_height = $old_height * $scale;
    
    $new = imagecreatetruecolor($width, $new_height);
    
    // 保留PNG透明度
    if ($mime == 'image/png') {
        imagealphablending($new, false);
        imagesavealpha($new, true);
        $transparent = imagecolorallocatealpha($new, 255, 255, 255, 127);
        imagefilledrectangle($new, 0, 0, $width, $new_height, $transparent);
    }
    
    imagecopyresampled($new, $image, 0, 0, 0, 0, $width, $new_height, $old_width, $old_height);
    
    switch ($mime) {
        case 'image/jpeg':
            imagejpeg($new, $destination, 90);
            break;
        case 'image/png':
            imagepng($new, $destination);
            break;
        case 'image/gif':
            imagegif($new, $destination);
            break;
    }
    
    imagedestroy($new);
    imagedestroy($image);
    
    return true;
}

// 从外部URL下载图片并保存到本地
function downloadImage($url, $save_path) {
    $content = file_get_contents($url);
    if ($content === false) {
        return false;
    }
    
    return file_put_contents($save_path, $content);
}

// 获取所有壁纸
function getAllWallpapers($conn, $type = null) {
    $sql = "SELECT * FROM wallpapers";
    if ($type) {
        $sql .= " WHERE type = '$type'";
    }
    $sql .= " ORDER BY created_at DESC";
    
    $result = $conn->query($sql);
    $wallpapers = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $wallpapers[] = $row;
        }
    }
    
    return $wallpapers;
}

// 获取单个壁纸
function getWallpaper($conn, $id) {
    $id = $conn->real_escape_string($id);
    $sql = "SELECT * FROM wallpapers WHERE id = '$id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 1) {
        return $result->fetch_assoc();
    }
    
    return null;
}

// 添加壁纸
function addWallpaper($conn, $title, $description, $image_url, $thumbnail_url, $type, $is_external) {
    $title = $conn->real_escape_string($title);
    $description = $conn->real_escape_string($description);
    $image_url = $conn->real_escape_string($image_url);
    $thumbnail_url = $conn->real_escape_string($thumbnail_url);
    $type = $conn->real_escape_string($type);
    $is_external = (int)$is_external;
    
    $sql = "INSERT INTO wallpapers (title, description, image_url, thumbnail_url, type, is_external) VALUES ('$title', '$description', '$image_url', '$thumbnail_url', '$type', '$is_external')";
    
    return $conn->query($sql);
}

// 更新壁纸
function updateWallpaper($conn, $id, $title, $description, $image_url = null, $thumbnail_url = null, $type) {
    $id = $conn->real_escape_string($id);
    $title = $conn->real_escape_string($title);
    $description = $conn->real_escape_string($description);
    $type = $conn->real_escape_string($type);
    
    $sql = "UPDATE wallpapers SET title = '$title', description = '$description', type = '$type'";
    
    if ($image_url) {
        $image_url = $conn->real_escape_string($image_url);
        $thumbnail_url = $conn->real_escape_string($thumbnail_url);
        $sql .= ", image_url = '$image_url', thumbnail_url = '$thumbnail_url'";
    }
    
    $sql .= " WHERE id = '$id'";
    
    return $conn->query($sql);
}

// 删除壁纸
function deleteWallpaper($conn, $id) {
    // 先获取壁纸信息，检查是否是本地文件
    $wallpaper = getWallpaper($conn, $id);
    
    if ($wallpaper && $wallpaper['is_external'] == 0) {
        // 是本地文件，删除文件
        $image_path = $_SERVER['DOCUMENT_ROOT'] . parse_url($wallpaper['image_url'], PHP_URL_PATH);
        $thumbnail_path = $_SERVER['DOCUMENT_ROOT'] . parse_url($wallpaper['thumbnail_url'], PHP_URL_PATH);
        
        if (file_exists($image_path)) {
            unlink($image_path);
        }
        
        if (file_exists($thumbnail_path)) {
            unlink($thumbnail_path);
        }
    }
    
    // 从数据库中删除记录
    $id = $conn->real_escape_string($id);
    $sql = "DELETE FROM wallpapers WHERE id = '$id'";
    
    return $conn->query($sql);

}

/**
 * 获取指定类型的壁纸（分页）
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

/**
 * 获取精选壁纸（首页显示）
 */
function getFeaturedWallpapers($conn, $type, $limit) {
    // 这里可以根据需求修改，例如按点击量排序等
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

/**
 * 统计指定类型壁纸总数
 */
function countWallpapers($conn, $type) {
    $sql = "SELECT COUNT(*) as total FROM wallpapers WHERE type = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $type);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['total'];
}

?>
