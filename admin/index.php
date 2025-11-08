<?php
require_once 'config.php';
require_once 'functions.php';

// 获取统计数据
$desktop_count = $conn->query("SELECT COUNT(*) as count FROM wallpapers WHERE type = 'desktop'")->fetch_assoc()['count'];
$mobile_count = $conn->query("SELECT COUNT(*) as count FROM wallpapers WHERE type = 'mobile'")->fetch_assoc()['count'];
$total_count = $desktop_count + $mobile_count;
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>情诗壁纸 - 管理后台</title>
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
        .dashboard-card {
            background-color: #24262c;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.3s;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
        }
        .dashboard-card i {
            font-size: 2rem;
            margin-bottom: 15px;
            color: #8a65cc;
        }
        .dashboard-card .count {
            font-size: 2.5rem;
            font-weight: bold;
            color: #fff;
        }
        .dashboard-card .title {
            color: #b0b3b8;
            font-size: 0.9rem;
            margin-top: 5px;
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
                <a class="nav-link active" href="index.php">
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
                    <h2><i class="fas fa-tachometer-alt me-2"></i> 仪表盘</h2>
                    <p>欢迎回来，<?php echo htmlspecialchars($_SESSION['username']); ?>！这里是情诗壁纸后台管理系统。</p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-4">
                    <div class="dashboard-card text-center">
                        <i class="fas fa-images"></i>
                        <div class="count"><?php echo $total_count; ?></div>
                        <div class="title">总壁纸数量</div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="dashboard-card text-center">
                        <i class="fas fa-desktop"></i>
                        <div class="count"><?php echo $desktop_count; ?></div>
                        <div class="title">电脑壁纸</div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="dashboard-card text-center">
                        <i class="fas fa-mobile-alt"></i>
                        <div class="count"><?php echo $mobile_count; ?></div>
                        <div class="title">手机壁纸</div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-lg-6">
                    <div class="dashboard-card">
                        <h4 class="mb-3"><i class="fas fa-tasks me-2"></i> 快捷操作</h4>
                        <div class="d-grid gap-2">
                            <a href="add-wallpaper.php" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-2"></i> 添加新壁纸
                            </a>
                            <a href="wallpapers.php?type=desktop" class="btn btn-outline-light">
                                <i class="fas fa-desktop me-2"></i> 管理电脑壁纸
                            </a>
                            <a href="wallpapers.php?type=mobile" class="btn btn-outline-light">
                                <i class="fas fa-mobile-alt me-2"></i> 管理手机壁纸
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="dashboard-card">
                        <h4 class="mb-3"><i class="fas fa-info-circle me-2"></i> 系统信息</h4>
                        <table class="table table-dark table-sm mb-0">
                            <tbody>
                                <tr>
                                    <td>PHP 版本</td>
                                    <td><?php echo phpversion(); ?></td>
                                </tr>
                                <tr>
                                    <td>服务器软件</td>
                                    <td><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
                                </tr>
                                <tr>
                                    <td>MySQL 版本</td>
                                    <td><?php echo $conn->server_info; ?></td>
                                </tr>
                                <tr>
                                    <td>最大上传大小</td>
                                    <td><?php echo ini_get('upload_max_filesize'); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
