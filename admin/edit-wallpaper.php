<?php
require_once 'config.php';
require_once 'functions.php';

// 获取要编辑的壁纸ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 获取壁纸信息
$wallpaper = getWallpaper($conn, $id);

// 如果壁纸不存在，重定向到壁纸列表页
if (!$wallpaper) {
    $_SESSION['message'] = '壁纸不存在或已被删除';
    header('Location: wallpapers.php');
    exit;
}

// 错误消息
$error = '';
// 成功消息
$success = '';

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $type = $_POST['type'] ?? 'desktop';
    $change_image = isset($_POST['change_image']) && $_POST['change_image'] === 'yes';
    
    if (empty($title)) {
        $error = '请输入壁纸标题';
    } else {
        if ($change_image) {
            $image_source = $_POST['image_source'] ?? 'local';
            
            // 设置目标目录
            $upload_dir = UPLOAD_DIR . '/' . $type . '/';
            $thumbnail_dir = UPLOAD_DIR . '/thumbnails/';
            
            // 确保目录存在
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            if (!file_exists($thumbnail_dir)) {
                mkdir($thumbnail_dir, 0777, true);
            }
            
            if ($image_source === 'local') {
                // 本地上传图片
                if (!isset($_FILES['wallpaper_file']) || $_FILES['wallpaper_file']['error'] !== UPLOAD_ERR_OK) {
                    $error = '请选择要上传的图片文件';
                } else {
                    $file_tmp = $_FILES['wallpaper_file']['tmp_name'];
                    $file_name = $_FILES['wallpaper_file']['name'];
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    
                    // 检查文件类型
                    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
                    if (!in_array($file_ext, $allowed_ext)) {
                        $error = '只允许上传 JPG, JPEG, PNG 或 GIF 图片';
                    } else {
                        // 生成唯一文件名
                        $new_file_name = uniqid() . '.' . $file_ext;
                        $destination = $upload_dir . $new_file_name;
                        $thumbnail_destination = $thumbnail_dir . $new_file_name;
                        
                        // 移动上传的文件
                        if (move_uploaded_file($file_tmp, $destination)) {
                            // 创建缩略图
                            createThumbnail($destination, $thumbnail_destination, 300);
                            
                            // 准备更新壁纸信息
                            $image_url = UPLOAD_URL . '/' . $type . '/' . $new_file_name;
                            $thumbnail_url = UPLOAD_URL . '/thumbnails/' . $new_file_name;
                            
                            // 如果原壁纸是本地图片，尝试删除旧文件
                            if ($wallpaper['is_external'] == 0) {
                                $old_image_path = $_SERVER['DOCUMENT_ROOT'] . parse_url($wallpaper['image_url'], PHP_URL_PATH);
                                $old_thumbnail_path = $_SERVER['DOCUMENT_ROOT'] . parse_url($wallpaper['thumbnail_url'], PHP_URL_PATH);
                                
                                if (file_exists($old_image_path)) {
                                    unlink($old_image_path);
                                }
                                if (file_exists($old_thumbnail_path)) {
                                    unlink($old_thumbnail_path);
                                }
                            }
                            
                            // 更新数据库，设置is_external为0
                            $sql = "UPDATE wallpapers SET title = ?, description = ?, image_url = ?, thumbnail_url = ?, type = ?, is_external = 0 WHERE id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("sssssi", $title, $description, $image_url, $thumbnail_url, $type, $id);
                            
                            if ($stmt->execute()) {
                                $success = '壁纸更新成功！';
                                // 重新获取更新后的壁纸信息
                                $wallpaper = getWallpaper($conn, $id);
                            } else {
                                $error = '更新壁纸信息时出错: ' . $conn->error;
                            }
                        } else {
                            $error = '上传文件时出错';
                        }
                    }
                }
            } else {
                // 外部链接图片
                $external_image_url = $_POST['external_image_url'] ?? '';
                
                if (empty($external_image_url)) {
                    $error = '请输入外部图片链接';
                } else if (!filter_var($external_image_url, FILTER_VALIDATE_URL)) {
                    $error = '请输入有效的图片URL';
                } else {
                    // 生成唯一文件名
                    $file_name = uniqid() . '.jpg';
                    $thumbnail_destination = $thumbnail_dir . $file_name;
                    
                    // 尝试下载图片创建缩略图
                    if (downloadImage($external_image_url, $thumbnail_destination)) {
                        // 创建缩略图
                        createThumbnail($thumbnail_destination, $thumbnail_destination, 300);
                        
                        // 如果原壁纸是本地图片，尝试删除旧文件
                        if ($wallpaper['is_external'] == 0) {
                            $old_image_path = $_SERVER['DOCUMENT_ROOT'] . parse_url($wallpaper['image_url'], PHP_URL_PATH);
                            if (file_exists($old_image_path)) {
                                unlink($old_image_path);
                            }
                        }
                        
                        // 如果存在旧的缩略图，尝试删除
                        $old_thumbnail_path = $_SERVER['DOCUMENT_ROOT'] . parse_url($wallpaper['thumbnail_url'], PHP_URL_PATH);
                        if (file_exists($old_thumbnail_path)) {
                            unlink($old_thumbnail_path);
                        }
                        
                        // 更新数据库，设置is_external为1
                        $image_url = $external_image_url;
                        $thumbnail_url = UPLOAD_URL . '/thumbnails/' . $file_name;
                        
                        $sql = "UPDATE wallpapers SET title = ?, description = ?, image_url = ?, thumbnail_url = ?, type = ?, is_external = 1 WHERE id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("sssssi", $title, $description, $image_url, $thumbnail_url, $type, $id);
                        
                        if ($stmt->execute()) {
                            $success = '壁纸更新成功！';
                            // 重新获取更新后的壁纸信息
                            $wallpaper = getWallpaper($conn, $id);
                        } else {
                            $error = '更新壁纸信息时出错: ' . $conn->error;
                        }
                    } else {
                        $error = '无法从外部URL下载图片';
                    }
                }
            }
        } else {
            // 仅更新文字信息
            if (updateWallpaper($conn, $id, $title, $description, null, null, $type)) {
                $success = '壁纸信息更新成功！';
                // 重新获取更新后的壁纸信息
                $wallpaper = getWallpaper($conn, $id);
            } else {
                $error = '更新壁纸信息时出错: ' . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>情诗壁纸 - 编辑壁纸</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #2c2f3a;
            color: #e4e6eb;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .sidebar {
            background-color: #24262c;
            height: 100%;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            padding: 20px 0;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
        }
        .content {
            margin-left: 250px;
            padding: 20px;
            flex-grow: 1;
        }
        .sidebar-header {
            padding: 0 20px 20px 20px;
            border-bottom: 1px solid #383c47;
            margin-bottom: 20px;
            text-align: center;
        }
        .sidebar-header h3 {
            color: #8a65cc;
        }
        .nav-link {
            color: #b0b3b8;
            padding: 10px 20px;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        .nav-link:hover, .nav-link.active {
            color: #fff;
            background-color: rgba(138, 101, 204, 0.1);
            border-left-color: #8a65cc;
        }
        .nav-link i {
            width: 25px;
            text-align: center;
            margin-right: 10px;
        }
        .user-info {
            padding: 20px;
            border-top: 1px solid #383c47;
            margin-top: auto;
        }
        .btn-primary {
            background-color: #8a65cc;
            border-color: #8a65cc;
        }
        .btn-primary:hover {
            background-color: #7a5ac0;
            border-color: #7a5ac0;
        }
        .form-card {
            background-color: #24262c;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .form-control, .form-select {
            background-color: #383c47;
            border: none;
            color: #fff;
            padding: 12px;
        }
        .form-control:focus, .form-select:focus {
            background-color: #424655;
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(138, 101, 204, 0.25);
        }
        .form-select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23ffffff' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
        }
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.2);
            color: #f8d7da;
            border-color: rgba(220, 53, 69, 0.3);
        }
        .alert-success {
            background-color: rgba(40, 167, 69, 0.2);
            color: #d4edda;
            border-color: rgba(40, 167, 69, 0.3);
        }
        .nav-tabs {
            border-bottom-color: #383c47;
        }
        .nav-tabs .nav-link {
            color: #b0b3b8;
            border: none;
            border-bottom: 2px solid transparent;
            border-radius: 0;
            padding: 10px 20px;
            margin-right: 10px;
        }
        .nav-tabs .nav-link:hover {
            border-color: transparent;
            color: #fff;
        }
        .nav-tabs .nav-link.active {
            background-color: transparent;
            border-bottom-color: #8a65cc;
            color: #fff;
        }
        .preview-image {
            width: 100%;
            max-height: 300px;
            object-fit: contain;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .custom-switch .form-check-input:checked {
            background-color: #8a65cc;
            border-color: #8a65cc;
        }
        .collapse-content {
            background-color: #383c47;
            border-radius: 8px;
            padding: 20px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="sidebar d-flex flex-column">
        <div class="sidebar-header">
            <h3>情诗壁纸</h3>
            <p>后台管理系统</p>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-home"></i> 仪表盘
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $wallpaper['type'] === 'desktop' ? 'active' : ''; ?>" href="wallpapers.php?type=desktop">
                    <i class="fas fa-desktop"></i> 电脑壁纸
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $wallpaper['type'] === 'mobile' ? 'active' : ''; ?>" href="wallpapers.php?type=mobile">
                    <i class="fas fa-mobile-alt"></i> 手机壁纸
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="add-wallpaper.php">
                    <i class="fas fa-plus-circle"></i> 添加壁纸
                </a>
            </li>
        </ul>
        <div class="user-info mt-auto">
            <div class="d-flex align-items-center mb-3">
                <i class="fas fa-user-circle me-2"></i>
                <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </div>
            <a href="logout.php" class="btn btn-outline-danger btn-sm w-100">
                <i class="fas fa-sign-out-alt"></i> 退出登录
            </a>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col">
                    <h2><i class="fas fa-edit me-2"></i> 编辑壁纸</h2>
                    <p>修改壁纸"<?php echo htmlspecialchars($wallpaper['title']); ?>"的信息。</p>
                </div>
            </div>
            
            <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle me-2"></i> <?php echo $success; ?>
            </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="form-card">
                        <form method="post" action="" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="title" class="form-label">壁纸标题 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($wallpaper['title']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">壁纸描述</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($wallpaper['description']); ?></textarea>
                            </div>
                            
                            <div class="mb-4">
                                <label for="type" class="form-label">壁纸类型 <span class="text-danger">*</span></label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="desktop" <?php echo $wallpaper['type'] === 'desktop' ? 'selected' : ''; ?>>电脑壁纸</option>
                                    <option value="mobile" <?php echo $wallpaper['type'] === 'mobile' ? 'selected' : ''; ?>>手机壁纸</option>
                                </select>
                            </div>
                            
                            <div class="mb-3 form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="change_image" name="change_image" value="yes" data-bs-toggle="collapse" data-bs-target="#imageOptionsCollapse">
                                <label class="form-check-label" for="change_image">更换图片</label>
                            </div>
                            
                            <div class="collapse" id="imageOptionsCollapse">
                                <div class="collapse-content">
                                    <div class="mb-3">
                                        <label class="form-label">图片来源</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="image_source" id="local_upload" value="local" checked onchange="toggleImageSource()">
                                            <label class="form-check-label" for="local_upload">
                                                从本地上传
                                            </label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" name="image_source" id="external_url" value="external" onchange="toggleImageSource()">
                                            <label class="form-check-label" for="external_url">
                                                使用外部图片链接
                                            </label>
                                        </div>
                                    </div>

                                    <div id="local_upload_section" class="mb-3">
                                        <label for="wallpaper_file" class="form-label">上传新图片</label>
                                        <input type="file" class="form-control" id="wallpaper_file" name="wallpaper_file">
                                        <div class="form-text text-light">支持 JPG, JPEG, PNG, GIF 格式</div>
                                    </div>

                                    <div id="external_url_section" class="mb-3" style="display:none;">
                                        <label for="external_image_url" class="form-label">外部图片链接</label>
                                        <input type="url" class="form-control" id="external_image_url" name="external_image_url" placeholder="请输入外部图片链接">
                                        <div class="form-text text-light">请确保链接是直接指向图片的URL，并且图片有合法的使用权限</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between mt-4">
                                <a href="wallpapers.php?type=<?php echo $wallpaper['type']; ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i> 返回列表
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i> 保存更改
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="form-card">
                        <h4 class="mb-3"><i class="fas fa-image me-2"></i> 当前图片预览</h4>
                        <img src="<?php echo htmlspecialchars($wallpaper['image_url']); ?>" alt="<?php echo htmlspecialchars($wallpaper['title']); ?>" class="preview-image">
                        
                        <table class="table table-dark table-sm mb-0">
                            <tbody>
                                <tr>
                                    <td>类型</td>
                                    <td><?php echo $wallpaper['type'] === 'desktop' ? '电脑壁纸' : '手机壁纸'; ?></td>
                                </tr>
                                <tr>
                                    <td>来源</td>
                                    <td><?php echo $wallpaper['is_external'] ? '外部链接' : '本地上传'; ?></td>
                                </tr>
                                <tr>
                                    <td>添加时间</td>
                                    <td><?php echo date('Y-m-d H:i:s', strtotime($wallpaper['created_at'])); ?></td>
                                </tr>
                                <tr>
                                    <td>图片链接</td>
                                    <td class="text-truncate" style="max-width: 200px;" title="<?php echo htmlspecialchars($wallpaper['image_url']); ?>"><?php echo htmlspecialchars($wallpaper['image_url']); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // 图片来源切换
        function toggleImageSource() {
            const localSource = document.getElementById('local_upload').checked;
            document.getElementById('local_upload_section').style.display = localSource ? 'block' : 'none';
            document.getElementById('external_url_section').style.display = localSource ? 'none' : 'block';
        }
    </script>
</body>
</html>
