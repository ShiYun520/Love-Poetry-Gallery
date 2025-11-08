<?php
require_once 'front-config.php';
require_once 'front-functions.php';

// 获取精选壁纸
$desktopWallpapers = getFeaturedWallpapers($conn, 'desktop', 8);
$mobileWallpapers = getFeaturedWallpapers($conn, 'mobile', 10);
?>

<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>情诗画廊 - 品质壁纸</title>
    <!-- 引入 Font Awesome 图标库 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* 新增/修改：艺术标题链接样式 */
        .artistic-title {
            text-decoration: none; /* 移除链接下划线 */
            color: inherit; /* 继承父元素（navbar-brand）的颜色 */
            cursor: pointer; /* 提示可点击 */
            /* 如果原 span 有其他特定样式（如 font-size, font-weight, margin-left 等），请在此处添加 */
            /* 假设它原本就与 logo-icon 对齐，font-size 和 margin-left 可能在 .navbar-brand 或其他地方定义 */
        }

        /* 移动设备导航栏样式 */
        .menu-toggle {
            display: none;
            font-size: 16px;
            cursor: pointer;
        }
    
        /* 响应式设计 */
        @media screen and (max-width: 768px) {
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
        
            /* 电脑壁纸网格样式 */
            .gallery-grid {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        
            .mobile-gallery-grid {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        
            /* 手机端主内容图片调整为9:16比例 */
            .wallpaper-card {
                position: relative;
                width: 100%;
                height: 0;
                padding-bottom: 177.78%; /* 9:16的高宽比 (16/9 * 100%) */
                overflow: hidden;
            }
        
            .wallpaper-card .overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-size: cover;
                background-position: center;
            }
        
            .wallpaper-card .text-content {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 90%;
                text-align: center;
                z-index: 2;
            }
        
            .wallpaper-card .wave-shape {
                position: absolute;
                bottom: 0;
                width: 100%;
            }
        }
    </style>
</head>

<body class=""> 
    <header class="navbar">
        <div class="navbar-brand">
            <img src="https://zyj.com/view.php/79c03704a86ecb49e51a97ff6102cee9.png" alt="情诗画廊 Logo" class="logo-icon">
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
                <li><a href="avatar.php"><i class="fas fa-user-circle"></i> 头像制作</a></li>
                <li><a href="api.php"><i class="fas fa-link"></i> 随机API接口</a></li>
                <li><a href="about.php"><i class="fas fa-info-circle"></i> 关于我们</a></li>
            </ul>
        </nav>
        <div class="navbar-right-icon">
            <i class="far fa-clock"></i>
        </div>
    </header>

    <main class="content-wrapper">
        <div class="wallpaper-card">
            <div class="overlay"></div>
            <div class="text-content">
                <h1 class="glitch-text" data-text="情诗壁纸">情诗壁纸</h1>
                <div class="title-underline"></div>
                <div class="slogan-container">
                    <p>品质壁纸，提升您的视觉享受</p>
                </div>
            </div>
            <div class="wave-shape"></div>
        </div>

        <!-- 电脑壁纸画廊部分 - 使用PHP循环 -->
        <section class="wallpaper-gallery-section">
            <div class="gallery-header">
                <h2 class="gallery-title">电脑壁纸</h2>
                <div class="gallery-buttons">
                    <button class="btn btn-secondary" id="refresh-desktop"><i class="fas fa-redo-alt"></i> 换一换</button>
                    <a href="desktop-wallpaper.php" class="btn btn-primary" style="text-decoration: none;">查看更多</a>
                </div>
            </div>
        
            <div class="gallery-grid">
                <?php if (count($desktopWallpapers) > 0): ?>
                    <?php foreach ($desktopWallpapers as $wallpaper): ?>
                    <div class="gallery-item" data-title="<?php echo htmlspecialchars($wallpaper['title']); ?>" data-desc="<?php echo htmlspecialchars($wallpaper['description']); ?>">
                        <div class="image-container">
                            <img class="lazy-image" 
                                 src="http://fz.cc/bz/uploads/683ec0b07f8ad.png" 
                                 data-src="<?php echo htmlspecialchars($wallpaper['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($wallpaper['title']); ?>">
                        </div>
                        <div class="image-overlay">
                            <h3 class="image-title"><?php echo htmlspecialchars($wallpaper['title']); ?></h3>
                            <p class="image-desc"><?php echo htmlspecialchars($wallpaper['description']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-wallpapers">暂无壁纸</div>
                <?php endif; ?>
            </div>
        </section>

        <!-- 手机壁纸画廊部分 - 使用PHP循环 -->
        <section class="wallpaper-gallery-section">
            <div class="gallery-header">
                <h2 class="gallery-title">手机壁纸</h2>
                <div class="gallery-buttons">
                    <button class="btn btn-secondary" id="refresh-mobile"><i class="fas fa-redo-alt"></i> 换一换</button>
                    <a href="Mobile-wallpaper.php" class="btn btn-primary" style="text-decoration: none;">查看更多</a>
                </div>
            </div>
        
            <div class="gallery-grid mobile-gallery-grid">
                <?php if (count($mobileWallpapers) > 0): ?>
                    <?php foreach ($mobileWallpapers as $wallpaper): ?>
                    <div class="gallery-item mobile-gallery-item" data-title="<?php echo htmlspecialchars($wallpaper['title']); ?>" data-desc="<?php echo htmlspecialchars($wallpaper['description']); ?>">
                        <div class="image-container">
                            <img class="lazy-image" 
                                 src="http://fz.cc/bz/uploads/683ec0b07f8ad.png" 
                                 data-src="<?php echo htmlspecialchars($wallpaper['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($wallpaper['title']); ?>">
                        </div>
                        <div class="image-overlay">
                            <h3 class="image-title"><?php echo htmlspecialchars($wallpaper['title']); ?></h3>
                            <p class="image-desc"><?php echo htmlspecialchars($wallpaper['description']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-wallpapers">暂无壁纸</div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- Lightbox Modal HTML -->
    <div id="imageModal" class="modal">
        <span class="close-button">&times;</span>
        <img class="modal-content" id="img01" alt="">
        <div id="caption">
            <h3 id="modalTitle"></h3>
            <p id="modalDescription"></p>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        // 添加响应式导航菜单功能
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
        });
    </script>
</body>
</html>