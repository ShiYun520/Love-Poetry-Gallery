<?php
require_once 'config.php';
require_once 'functions.php';

// 获取壁纸类型参数（desktop 或 mobile）
$type = isset($_GET['type']) ? $_GET['type'] : 'desktop';

if ($type !== 'desktop' && $type !== 'mobile') {
    $type = 'desktop'; // 默认为电脑壁纸
}

// 获取对应类型的所有壁纸
$wallpapers = getAllWallpapers($conn, $type);

// 处理消息提示
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>情诗壁纸 - <?php echo $type === 'desktop' ? '电脑' : '手机'; ?>壁纸管理</title>
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
        .wallpaper-card {
            background-color: #24262c;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: all 0.3s;
        }
        .wallpaper-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
        }
        .wallpaper-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .wallpaper-info {
            padding: 15px;
            flex-grow: 1;
        }
        .wallpaper-title {
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .wallpaper-desc {
            font-size: 0.9rem;
            color: #b0b3b8;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .alert-success {
            background-color: rgba(40, 167, 69, 0.2);
            color: #d4edda;
            border-color: rgba(40, 167, 69, 0.3);
        }
        .table {
            color: #e4e6eb;
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
                <a class="nav-link <?php echo $type === 'desktop' ? 'active' : ''; ?>" href="wallpapers.php?type=desktop">
                    <i class="fas fa-desktop"></i> 电脑壁纸
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $type === 'mobile' ? 'active' : ''; ?>" href="wallpapers.php?type=mobile">
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
                <div class="col-md-6">
                    <h2><i class="fas <?php echo $type === 'desktop' ? 'fa-desktop' : 'fa-mobile-alt'; ?> me-2"></i> <?php echo $type === 'desktop' ? '电脑' : '手机'; ?>壁纸管理</h2>
                    <p>管理您的<?php echo $type === 'desktop' ? '电脑' : '手机'; ?>壁纸集合。</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="add-wallpaper.php?type=<?php echo $type; ?>" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i> 添加新壁纸
                    </a>
                </div>
            </div>
            
            <?php if (!empty($message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            
            <?php if (empty($wallpapers)): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i> 没有找到<?php echo $type === 'desktop' ? '电脑' : '手机'; ?>壁纸，请添加一些壁纸。
            </div>
            <?php else: ?>
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover">
                            <thead>
                                <tr>
                                    <th width="80">#</th>
                                    <th width="100">预览</th>
                                    <th>标题</th>
                                    <th>描述</th>
                                    <th>来源</th>
                                    <th width="180">添加日期</th>
                                    <th width="220">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($wallpapers as $wallpaper): ?>
                                <tr>
                                    <td><?php echo $wallpaper['id']; ?></td>
                                    <td>
                                        <img src="<?php echo htmlspecialchars($wallpaper['thumbnail_url']); ?>" alt="<?php echo htmlspecialchars($wallpaper['title']); ?>" class="img-fluid rounded" style="max-height: 60px;">
                                    </td>
                                    <td><?php echo htmlspecialchars($wallpaper['title']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($wallpaper['description'], 0, 70)) . (strlen($wallpaper['description']) > 70 ? '...' : ''); ?></td>
                                    <td><?php echo $wallpaper['is_external'] ? '外部链接' : '本地上传'; ?></td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($wallpaper['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="edit-wallpaper.php?id=<?php echo $wallpaper['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i> 编辑
                                            </a>
                                            <a href="copy-wallpaper.php?id=<?php echo $wallpaper['id']; ?>" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-copy"></i> 复制
                                            </a>
                                            <a href="delete-wallpaper.php?id=<?php echo $wallpaper['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('确定要删除这个壁纸吗？');">
                                                <i class="fas fa-trash-alt"></i> 删除
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
