<?php
require_once 'config.php';
require_once 'functions.php';

// 获取壁纸类型参数（默认为desktop）
$type = isset($_GET['type']) ? $_GET['type'] : 'desktop';
if ($type !== 'desktop' && $type !== 'mobile') {
    $type = 'desktop';
}

// 错误消息
$error = '';
// 成功消息
$success = '';

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $upload_type = $_POST['upload_type'] ?? 'local';
    $type = $_POST['type'] ?? 'desktop';
    
    if (empty($title) && $upload_type !== 'batch') {
        $error = '请输入壁纸标题';
    } else {
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
        
        if ($upload_type === 'local') {
            // 本地上传单个图片
            if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $error = '请选择要上传的图片文件';
            } else {
                $file_tmp = $_FILES['image']['tmp_name'];
                $file_name = $_FILES['image']['name'];
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
                        
                        // 保存到数据库
                        $image_url = UPLOAD_URL . '/' . $type . '/' . $new_file_name;
                        $thumbnail_url = UPLOAD_URL . '/thumbnails/' . $new_file_name;
                        
                        if (addWallpaper($conn, $title, $description, $image_url, $thumbnail_url, $type, 0)) {
                            // 设置成功消息并重定向
                            $_SESSION['message'] = '壁纸添加成功！';
                            header('Location: wallpapers.php?type=' . $type);
                            exit;
                        } else {
                            $error = '保存壁纸到数据库时出错: ' . $conn->error;
                        }
                    } else {
                        $error = '上传文件时出错';
                    }
                }
            }
        } elseif ($upload_type === 'batch') {
            // 批量上传图片
            if (!isset($_FILES['images']) || empty($_FILES['images']['name'][0])) {
                $error = '请选择要批量上传的图片文件';
            } else {
                $uploadCount = 0;
                $errorCount = 0;
                $fileCount = count($_FILES['images']['name']);
                
                // 通用标题
                $batch_title = empty($title) ? '壁纸' : $title;
                
                for ($i = 0; $i < $fileCount; $i++) {
                    if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) {
                        $errorCount++;
                        continue;
                    }
                    
                    $file_tmp = $_FILES['images']['tmp_name'][$i];
                    $file_name = $_FILES['images']['name'][$i];
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    
                    // 检查文件类型
                    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
                    if (!in_array($file_ext, $allowed_ext)) {
                        $errorCount++;
                        continue;
                    }
                    
                    // 生成唯一文件名
                    $new_file_name = uniqid() . '.' . $file_ext;
                    $destination = $upload_dir . $new_file_name;
                    $thumbnail_destination = $thumbnail_dir . $new_file_name;
                    
                    // 移动上传的文件
                    if (move_uploaded_file($file_tmp, $destination)) {
                        // 创建缩略图
                        createThumbnail($destination, $thumbnail_destination, 300);
                        
                        // 保存到数据库
                        $image_url = UPLOAD_URL . '/' . $type . '/' . $new_file_name;
                        $thumbnail_url = UPLOAD_URL . '/thumbnails/' . $new_file_name;
                        
                        if (addWallpaper($conn, $batch_title, $description, $image_url, $thumbnail_url, $type, 0)) {
                            $uploadCount++;
                        } else {
                            $errorCount++;
                        }
                    } else {
                        $errorCount++;
                    }
                }
                
                if ($uploadCount > 0) {
                    // 设置成功消息并重定向
                    $_SESSION['message'] = "成功上传 {$uploadCount} 张壁纸" . ($errorCount > 0 ? "，{$errorCount} 张上传失败" : "");
                    header('Location: wallpapers.php?type=' . $type);
                    exit;
                } else {
                    $error = '所有文件上传失败';
                }
            }
        } else {
            // 外部链接图片
            $external_url = $_POST['external_url'] ?? '';
            
            if (empty($external_url)) {
                $error = '请输入外部图片链接';
            } else if (!filter_var($external_url, FILTER_VALIDATE_URL)) {
                $error = '请输入有效的图片URL';
            } else {
                // 生成唯一文件名
                $file_name = uniqid() . '.jpg';
                $destination = $upload_dir . $file_name;
                $thumbnail_destination = $thumbnail_dir . $file_name;
                
                // 尝试下载图片
                if (downloadImage($external_url, $destination)) {
                    // 创建缩略图
                    createThumbnail($destination, $thumbnail_destination, 300);
                    
                    // 保存到数据库
                    $image_url = $external_url; // 使用原始外部URL
                    $thumbnail_url = UPLOAD_URL . '/thumbnails/' . $file_name;
                    
                    if (addWallpaper($conn, $title, $description, $image_url, $thumbnail_url, $type, 1)) {
                        // 设置成功消息并重定向
                        $_SESSION['message'] = '壁纸添加成功！';
                        header('Location: wallpapers.php?type=' . $type);
                        exit;
                    } else {
                        $error = '保存壁纸到数据库时出错: ' . $conn->error;
                    }
                } else {
                    $error = '无法从外部URL下载图片';
                }
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
    <title>情诗壁纸 - 添加壁纸</title>
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
                <a class="nav-link" href="wallpapers.php?type=desktop">
                    <i class="fas fa-desktop"></i> 电脑壁纸
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="wallpapers.php?type=mobile">
                    <i class="fas fa-mobile-alt"></i> 手机壁纸
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="add-wallpaper.php">
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
                    <h2><i class="fas fa-plus-circle me-2"></i> 添加新壁纸</h2>
                    <p>添加新的壁纸到您的壁纸集合中。</p>
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
                        <ul class="nav nav-tabs mb-4" id="uploadTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="local-tab" data-bs-toggle="tab" data-bs-target="#local" type="button" role="tab" aria-controls="local" aria-selected="true">
                                    <i class="fas fa-upload me-2"></i> 本地上传
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="batch-tab" data-bs-toggle="tab" data-bs-target="#batch" type="button" role="tab" aria-controls="batch" aria-selected="false">
                                    <i class="fas fa-images me-2"></i> 批量上传
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="external-tab" data-bs-toggle="tab" data-bs-target="#external" type="button" role="tab" aria-controls="external" aria-selected="false">
                                    <i class="fas fa-link me-2"></i> 外部链接
                                </button>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="uploadTabContent">
                            <div class="tab-pane fade show active" id="local" role="tabpanel" aria-labelledby="local-tab">
                                <form method="post" action="" enctype="multipart/form-data">
                                    <input type="hidden" name="upload_type" value="local">
                                    
                                    <div class="mb-3">
                                        <label for="title" class="form-label">壁纸标题 <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="title" name="title" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">壁纸描述</label>
                                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="type" class="form-label">壁纸类型 <span class="text-danger">*</span></label>
                                        <select class="form-select" id="type" name="type" required>
                                            <option value="desktop" <?php echo $type === 'desktop' ? 'selected' : ''; ?>>电脑壁纸</option>
                                            <option value="mobile" <?php echo $type === 'mobile' ? 'selected' : ''; ?>>手机壁纸</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="image" class="form-label">选择图片 <span class="text-danger">*</span></label>
                                        <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                                        <div class="form-text text-light">支持 JPG, JPEG, PNG, GIF 格式，最大文件大小：<?php echo ini_get('upload_max_filesize'); ?></div>
                                    </div>
                                    
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-upload me-2"></i> 上传壁纸
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- 批量上传表单 -->
                            <div class="tab-pane fade" id="batch" role="tabpanel" aria-labelledby="batch-tab">
                                <form method="post" action="" enctype="multipart/form-data">
                                    <input type="hidden" name="upload_type" value="batch">
                                    
                                    <div class="mb-3">
                                        <label for="batch_title" class="form-label">壁纸标题</label>
                                        <input type="text" class="form-control" id="batch_title" name="title" placeholder="例如：风景壁纸">
                                        <div class="form-text text-light">此标题将应用于所有上传的图片</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="batch_description" class="form-label">壁纸描述</label>
                                        <textarea class="form-control" id="batch_description" name="description" rows="3"></textarea>
                                        <div class="form-text text-light">此描述将应用于所有上传的图片</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="batch_type" class="form-label">壁纸类型 <span class="text-danger">*</span></label>
                                        <select class="form-select" id="batch_type" name="type" required>
                                            <option value="desktop" <?php echo $type === 'desktop' ? 'selected' : ''; ?>>电脑壁纸</option>
                                            <option value="mobile" <?php echo $type === 'mobile' ? 'selected' : ''; ?>>手机壁纸</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="images" class="form-label">选择多张图片 <span class="text-danger">*</span></label>
                                        <input type="file" class="form-control" id="images" name="images[]" accept="image/*" multiple required>
                                        <div class="form-text text-light">
                                            支持 JPG, JPEG, PNG, GIF 格式，可以一次选择多张图片<br>
                                            最大文件大小：<?php echo ini_get('upload_max_filesize'); ?> / 每个文件
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-upload me-2"></i> 批量上传壁纸
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <div class="tab-pane fade" id="external" role="tabpanel" aria-labelledby="external-tab">
                                <form method="post" action="">
                                    <input type="hidden" name="upload_type" value="external">
                                    
                                    <div class="mb-3">
                                        <label for="external_title" class="form-label">壁纸标题 <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="external_title" name="title" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="external_description" class="form-label">壁纸描述</label>
                                        <textarea class="form-control" id="external_description" name="description" rows="3"></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="external_type" class="form-label">壁纸类型 <span class="text-danger">*</span></label>
                                        <select class="form-select" id="external_type" name="type" required>
                                            <option value="desktop" <?php echo $type === 'desktop' ? 'selected' : ''; ?>>电脑壁纸</option>
                                            <option value="mobile" <?php echo $type === 'mobile' ? 'selected' : ''; ?>>手机壁纸</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="external_url" class="form-label">图片URL <span class="text-danger">*</span></label>
                                        <input type="url" class="form-control" id="external_url" name="external_url" required placeholder="https://example.com/image.jpg">
                                        <div class="form-text text-light">输入图片的完整URL地址，例如: https://example.com/image.jpg</div>
                                    </div>
                                    
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-plus-circle me-2"></i> 添加外部壁纸
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="form-card">
                        <h4 class="mb-3"><i class="fas fa-info-circle me-2"></i> 提示说明</h4>
                        <div class="alert alert-secondary bg-dark text-light border-secondary mb-3">
                            <h5><i class="fas fa-upload me-2"></i> 本地上传</h5>
                            <ul class="mb-0">
                                <li>支持JPG、JPEG、PNG、GIF格式</li>
                                <li>文件大小限制: <?php echo ini_get('upload_max_filesize'); ?></li>
                                <li>电脑壁纸建议分辨率: 1920×1080或更高</li>
                                <li>手机壁纸建议分辨率: 1080×1920或更高</li>
                            </ul>
                        </div>
                        
                        <div class="alert alert-secondary bg-dark text-light border-secondary mb-3">
                            <h5><i class="fas fa-images me-2"></i> 批量上传</h5>
                            <ul class="mb-0">
                                <li>一次可选择多张图片上传</li>
                                <li>所有图片将使用相同的标题和描述</li>
                                <li>如遇上传失败，请减少批量数量或压缩图片尺寸</li>
                            </ul>
                        </div>
                        
                        <div class="alert alert-secondary bg-dark text-light border-secondary">
                            <h5><i class="fas fa-link me-2"></i> 外部链接</h5>
                            <ul class="mb-0">
                                <li>支持添加互联网上的图片链接</li>
                                <li>请确保链接直接指向图片文件</li>
                                <li>确保您拥有图片的使用权或符合版权要求</li>
                                <li>系统会创建缩略图，但使用原始URL显示</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
