<?php
require_once 'front-config.php';
require_once 'front-functions.php';

// 只添加缺少的getTotalWallpapers函数
if (!function_exists('getTotalWallpapers')) {
    function getTotalWallpapers($conn, $type) {
        $sql = "SELECT COUNT(*) as total FROM wallpapers WHERE type = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $type);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}

// 获取手机壁纸，每页16张
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 16;
$offset = ($page - 1) * $perPage;

// 获取壁纸总数用于分页
$totalWallpapers = getTotalWallpapers($conn, 'mobile');
$totalPages = ceil($totalWallpapers / $perPage);

// 使用已存在的getWallpapers函数
$wallpapers = getWallpapers($conn, 'mobile', $perPage, $offset);

// 页面内容继续...
?>

<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>情诗画廊 - 手机壁纸</title>
    <!-- 引入 Font Awesome 图标库 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-color: #1a1e2e;
            color: #fff;
            font-family: 'Microsoft YaHei', sans-serif;
            margin: 0;
            padding: 0;
        }
  
        /* 调整 wallpaper-container 的顶部内边距，让 page-header 自己控制与顶部的距离 */
        .wallpaper-container {
            width: 95%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 0 20px 0; /* 顶部内边距设为0，底部保持20px */
        }
  
        /* page-header 作为独立的卡片样式 */
        .page-header {
            background-color: #232736;
            padding: 45px;
            margin-bottom: 20px;
            border-radius: 10px;
            text-align: center;
            /* 新增：增加顶部外边距，与导航栏隔离 */
            margin-top: 40px; 
            /* 新增：添加阴影，使其看起来像浮动的卡片 */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.4); 
        }
  
        .page-header h1 {
            margin: 0;
            font-size: 2rem;
            color: #fff;
            /* 新增：为下划线定位做准备 */
            position: relative; 
            /* 新增：让h1的宽度只包裹内容，以便下划线动画是基于文字宽度 */
            display: inline-block; 
            padding-bottom: 10px; /* 给下划线留出空间 */
        }

        /* 定义下划线动画 */
        @keyframes drawUnderline {
            from {
                width: 0;
                left: 50%; /* 从中间开始 */
                transform: translateX(-50%);
            }
            to {
                width: 100%;
                left: 0; /* 扩展到整个宽度 */
                transform: translateX(0);
            }
        }
  
        /* 为 h1 添加下划线伪元素 */
        .page-header h1::after {
            content: '';
            position: absolute;
            bottom: 0; /* 位于 h1 底部 */
            left: 0;
            height: 3px; /* 下划线粗细 */
            background-color: #8A2BE2; /* 紫色 */
            width: 0; /* 初始宽度为0 */
            /* 应用动画 */
            animation: drawUnderline 0.8s ease-out forwards; /* 动画持续0.8秒，缓动，停留在最后一帧 */
            animation-delay: 0.3s; /* 动画开始前的延迟 */
        }

        .desktop-gallery {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-gap: 20px;
            margin-bottom: 30px;
        }
  
        .desktop-gallery-item {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            aspect-ratio: 9 / 16;
        }
  
        .desktop-gallery-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        }
  
        .desktop-gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
  
        .desktop-gallery-item:hover img {
            transform: scale(1.05);
        }
  
        .overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 100%);
            color: #fff;
            padding: 20px;
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s ease;
        }
  
        .desktop-gallery-item:hover .overlay {
            transform: translateY(0);
            opacity: 1;
        }
  
        .overlay h3 {
            margin: 0 0 5px 0;
            font-size: 18px;
        }
  
        .overlay p {
            margin: 0;
            font-size: 14px;
            opacity: 0.8;
        }
  
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 40px;
            flex-wrap: wrap;
        }
  
        .pagination a, .pagination span {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            margin: 0 5px;
            border-radius: 50%;
            background-color: #2a2f45;
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s ease;
        }
  
        .pagination a:hover {
            background-color: #3a4058;
        }
  
        .pagination .active {
            background-color: #4e7eff;
        }
  
        .pagination .go, .pagination .total {
            width: auto;
            padding: 0 15px;
            border-radius: 20px;
            background-color: #2a2f45;
        }
  
        .filters-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
  
        .filter-options select {
            padding: 8px 15px;
            border-radius: 5px;
            background-color: #2a2f45;
            color: #fff;
            border: none;
            outline: none;
            margin-right: 10px;
        }
  
        .sort-options select {
            padding: 8px 15px;
            border-radius: 5px;
            background-color: #2a2f45;
            color: #fff;
            border: none;
            outline: none;
        }

        /* 移动设备导航栏样式 */
        .menu-toggle {
            display: none;
            font-size: 16px;
            cursor: pointer;
        }
  
        /* 响应式设计 */
        @media screen and (max-width: 768px) {
            .desktop-gallery {
                grid-template-columns: repeat(2, 1fr);
            }
      
            /* 导航栏样式 */
            .menu-toggle {
                display: block;
                position: absolute;
                right: 40px; /* 向左移动，避免与右侧图标重叠 */
                top: 12px;
                z-index: 101;
                color: #fff;
                background: rgba(0, 0, 0, 0.2);
                padding: 5px 10px;
                border-radius: 5px;
            }
      
            .navbar-nav {
                display: none;
                width: 90%;
                max-width: 300px;
                position: absolute;
                top: 60px;
                right: 10px; /* 靠右对齐 */
                left: auto;
                background: rgba(255, 255, 255, 0.8); /* 半透明背景 */
                backdrop-filter: blur(10px); /* 模糊玻璃效果 */
                -webkit-backdrop-filter: blur(10px);
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                z-index: 100;
                border-radius: 10px;
                padding: 5px 0;
                font-size: 0.95em; /* 稍微缩小字体 */
            }
      
            .navbar-nav.active {
                display: block;
            }
      
            .navbar-nav ul {
                flex-direction: column;
                padding: 0;
                margin: 0;
            }
      
            .navbar-nav ul li {
                width: 100%;
                text-align: left;
                padding: 12px 20px;
                border-bottom: 1px solid rgba(240, 240, 240, 0.5);
                transition: all 0.2s ease;
            }
      
            .navbar-nav ul li:last-child {
                border-bottom: none;
            }
      
            .navbar-nav ul li:hover {
                background: rgba(255, 255, 255, 0.3);
            }
      
            .navbar-nav ul li a {
                display: flex;
                align-items: center;
                color: #333;
            }
      
            .navbar-nav ul li a i {
                margin-right: 10px;
                width: 20px;
                text-align: center;
            }
        }
  
        @media screen and (max-width: 576px) {
            .desktop-gallery {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>

<body class=""> 
    <header class="navbar">
        <div class="navbar-brand">
            <img src="https://zyj.torgw.com/view.php/79c03704a86ecb49e51a97ff6102cee9.png" alt="情诗画廊 Logo" class="logo-icon">
            <!-- 已将 span 替换为带有 href 的 a 标签 -->
            <a href="index.php" class="artistic-title">情诗画廊</a>
        </div>
  
        <!-- 添加汉堡菜单图标 -->
        <div class="menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
  
        <nav class="navbar-nav">
            <ul>
                <li><a href="desktop-wallpaper.php"><i class="fas fa-desktop"></i> 电脑壁纸</a></li>
                <li><a href="Mobile-wallpaper.php"><i class="fas fa-mobile-alt"></i> 手机壁纸</a></li>
                <li><a href="avatar-maker.php"><i class="fas fa-user-circle"></i> 头像制作</a></li>
                <li><a href="api.php"><i class="fas fa-link"></i> 随机API接口</a></li>
                <li><a href="about.php"><i class="fas fa-info-circle"></i> 关于我们</a></li>
            </ul>
        </nav>
        <div class="navbar-right-icon">
            <i class="far fa-clock"></i>
        </div>
    </header>

    <div class="wallpaper-container">
        <div class="page-header">
            <!-- h1 已添加下划线动画 -->
            <h1>手机壁纸</h1>
        </div>
  
        <div class="filters-bar">
            <div class="filter-options">
                <select name="category">
                    <option value="">全部分类</option>
                    <option value="anime">动漫</option>
                    <option value="landscape">风景</option>
                    <option value="abstract">抽象</option>
                    <option value="games">游戏</option>
                </select>
                <select name="resolution">
                    <option value="">全部分辨率</option>
                    <option value="1080x1920">1080x1920</option>
                    <option value="1440x2560">1440x2560</option>
                    <option value="2160x3840">2160x3840</option>
                </select>
            </div>
      
            <div class="sort-options">
                <select name="sort">
                    <option value="newest">最新上传</option>
                    <option value="popular">最受欢迎</option>
                    <option value="downloads">下载最多</option>
                </select>
            </div>
        </div>
  
        <div class="desktop-gallery">
            <?php if (count($wallpapers) > 0): ?>
                <?php foreach ($wallpapers as $wallpaper): ?>
                <div class="desktop-gallery-item" data-id="<?php echo htmlspecialchars($wallpaper['id']); ?>">
                    <img src="<?php echo htmlspecialchars($wallpaper['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($wallpaper['title']); ?>">
                    <div class="overlay">
                        <h3><?php echo htmlspecialchars($wallpaper['title']); ?></h3>
                        <p><?php echo htmlspecialchars($wallpaper['description']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-wallpapers">暂无壁纸</div>
            <?php endif; ?>
        </div>
  
        <!-- 分页 -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?type=mobile&page=1"><i class="fas fa-angle-double-left"></i></a>
                <a href="?type=mobile&page=<?php echo $page - 1; ?>"><i class="fas fa-angle-left"></i></a>
            <?php endif; ?>
      
            <?php
            $startPage = max(1, $page - 2);
            $endPage = min($totalPages, $page + 2);
      
            for ($i = $startPage; $i <= $endPage; $i++):
            ?>
                <a href="?type=mobile&page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
      
            <?php if ($page < $totalPages): ?>
                <a href="?type=mobile&page=<?php echo $page + 1; ?>"><i class="fas fa-angle-right"></i></a>
                <a href="?type=mobile&page=<?php echo $totalPages; ?>"><i class="fas fa-angle-double-right"></i></a>
            <?php endif; ?>
      
            <span class="total"><?php echo $totalPages; ?> 页</span>
            <a href="#" class="go">GO</a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Lightbox Modal HTML -->
    <div id="imageModal" class="modal">
        <span class="close-button">&times;</span>
        <img class="modal-content" id="img01" alt="">
        <div id="caption">
            <h3 id="modalTitle"></h3>
            <p id="modalDescription"></p>
            <div id="modalActions">
                <a href="#" id="downloadBtn" class="btn-download"><i class="fas fa-download"></i> 下载</a>
                <a href="#" id="favoriteBtn" class="btn-favorite"><i class="far fa-heart"></i> 收藏</a>
                <a href="#" id="shareBtn" class="btn-share"><i class="fas fa-share-alt"></i> 分享</a>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 导航菜单切换
            const menuToggle = document.querySelector('.menu-toggle');
            const navbarNav = document.querySelector('.navbar-nav');
      
            if (menuToggle) {
                menuToggle.addEventListener('click', function() {
                    navbarNav.classList.toggle('active');
                });
            }
      
            // 点击菜单项后关闭菜单
            const menuItems = document.querySelectorAll('.navbar-nav a');
            menuItems.forEach(item => {
                item.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        navbarNav.classList.remove('active');
                    }
                });
            });
      
            // 点击页面其他区域关闭菜单
            document.addEventListener('click', function(event) {
                if (!event.target.closest('.navbar-nav') && 
                    !event.target.closest('.menu-toggle') && 
                    navbarNav.classList.contains('active')) {
                    navbarNav.classList.remove('active');
                }
            });
      
            // 壁纸点击显示大图
            const galleryItems = document.querySelectorAll('.desktop-gallery-item');
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('img01');
            const modalTitle = document.getElementById('modalTitle');
            const modalDesc = document.getElementById('modalDescription');
            const closeBtn = document.querySelector('.close-button');
      
            galleryItems.forEach(item => {
                item.addEventListener('click', function() {
                    modal.style.display = "block";
                    modalImg.src = this.querySelector('img').src;
                    modalTitle.textContent = this.querySelector('.overlay h3').textContent;
                    modalDesc.textContent = this.querySelector('.overlay p').textContent;
                    document.body.style.overflow = 'hidden';
                });
            });
      
            closeBtn.addEventListener('click', function() {
                modal.style.display = "none";
                document.body.style.overflow = 'auto';
            });
        });
    </script>
</body>
</html>