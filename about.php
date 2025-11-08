<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>情诗画廊 - 关于我们</title>
    <!-- 引入 Font Awesome 图标库 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* 保留原有的导航栏样式 */
        .artistic-title {
            text-decoration: none;
            color: inherit;
            cursor: pointer;
        }

        .menu-toggle {
            display: none;
            font-size: 16px;
            cursor: pointer;
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

        /* 关于我们页面的新样式 */
        .about-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .about-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .about-header h1 {
            font-size: 36px;
            color: #333;
            margin-bottom: 15px;
        }

        .about-header .subtitle {
            font-size: 18px;
            color: #666;
        }

        .about-section {
            margin-bottom: 50px;
        }

        .about-section h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
            position: relative;
            padding-left: 15px;
        }

        .about-section h2::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 20px;
            background: linear-gradient(to bottom, #3c8ce7, #00eaff);
            border-radius: 2px;
        }

        .content-block {
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
        }

        .content-block p {
            color: #666;
            line-height: 1.6;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .feature-card {
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #3c8ce7, #00eaff);
            border-radius: 50%;
            color: #fff;
        }

        .feature-icon i {
            font-size: 24px;
        }

        .feature-card h3 {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
        }

        .feature-card p {
            color: #666;
            font-size: 14px;
        }

        .contact-info {
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .contact-item:last-child {
            margin-bottom: 0;
        }

        .contact-item i {
            width: 24px;
            height: 24px;
            margin-right: 10px;
            color: #3c8ce7;
        }

        .contact-item span {
            color: #666;
        }

        /* 响应式设计 */
        @media screen and (max-width: 768px) {
            .about-header {
                margin-bottom: 40px;
            }

            .about-header h1 {
                font-size: 28px;
            }

            .about-header .subtitle {
                font-size: 16px;
            }

            .about-section h2 {
                font-size: 20px;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .contact-info {
                padding: 20px;
            }
        }

        /* Dark mode styles */
        .dark {
            background-color: #1a1a2e;
            color: #666;
        }

        .dark .about-header h1,
        .dark .about-section h2 {
            color: #f0f0f0;
        }

        .dark .about-header .subtitle {
            color: #999;
        }

        .dark .content-block,
        .dark .feature-card,
        .dark .contact-info {
            background: #202133;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.2);
        }

        .dark .content-block p,
        .dark .feature-card p,
        .dark .contact-item span {
            color: #999;
        }

        .dark .feature-card h3 {
            color: #f0f0f0;
        }

        .ries-translation-highlight {
            background: rgba(53, 181, 170, 0.1);
            border-bottom: 2px solid #35B5AA;
            border-radius: 3px;
            padding: 0 4px;
            transition: all 0.2s ease;
        }
    </style>
</head>
<body class="dark">
    <!-- 保留原有的导航栏 -->
    <header class="navbar">
        <div class="navbar-brand">
            <img src="https://zyj.torgw.com/view.php/79c03704a86ecb49e51a97ff6102cee9.png" alt="情诗画廊 Logo" class="logo-icon">
            <a href="index.php" class="artistic-title">情诗画廊</a>
        </div>
    
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

    <!-- 关于我们内容 -->
    <main class="front-main">
        <div class="container">
            <div class="about-container">
                <div class="about-header">
                    <h1>关于我们</h1>
                    <p class="subtitle">情诗画廊 - 您的专属壁纸资源库</p>
                </div>
                
                <div class="about-content">
                    <div class="about-section">
                        <h2>项目介绍</h2>
                        <div class="content-block">
                            <p>情诗画廊是一个专注于提供高质量壁纸资源的平台，旨在为用户提供美观、实用的个性化壁纸 
                                <span class="ries-translation-highlight">solution(解决方案)</span>。
                            </p>
                        </div>
                    </div>

                    <div class="about-section">
                        <h2>我们的特色</h2>
                        <div class="features-grid">
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="el-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024">
                                            <path fill="currentColor" d="M96 896a32 32 0 0 1-32-32V160a32 32 0 0 1 32-32h832a32 32 0 0 1 32 32v704a32 32 0 0 1-32 32zm315.52-228.48-68.928-68.928a32 32 0 0 0-45.248 0L128 768.064h778.688l-242.112-290.56a32 32 0 0 0-49.216 0L458.752 665.408a32 32 0 0 1-47.232 2.112M256 384a96 96 0 1 0 192.064-.064A96 96 0 0 0 256 384"></path>
                                        </svg>
                                    </i>
                                </div>
                                <h3>高清精选</h3>
                            </div>

                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="el-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024">
                                            <path fill="currentColor" d="m795.904 750.72 124.992 124.928a32 32 0 0 1-45.248 45.248L750.656 795.904a416 416 0 1 1 45.248-45.248zM480 832a352 352 0 1 0 0-704 352 352 0 0 0 0 704"></path>
                                        </svg>
                                    </i>
                                </div>
                                <h3>智能分类</h3>
                            </div>

                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="el-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024">
                                            <path fill="currentColor" d="M784.512 230.272v-50.56a32 32 0 1 1 64 0v149.056a32 32 0 0 1-32 32H667.52a32 32 0 1 1 0-64h92.992A320 320 0 1 0 524.8 833.152a320 320 0 0 0 320-320h64a384 384 0 0 1-384 384 384 384 0 0 1-384-384 384 384 0 0 1 643.712-282.88z"></path>
                                        </svg>
                                    </i>
                                </div>
                                <h3>定期更新</h3>
                            </div>
                        </div>
                    </div>

                    <div class="about-section">
                        <h2>团队介绍</h2>
                        <div class="content-block">
                            <span>选择我们，让您的屏幕成为艺术的载体，让每一次解锁都是美的邂逅.</span>
                        </div>
                    </div>

                    <div class="about-section">
                        <h2>联系我们</h2>
                        <div class="contact-info">
                            <div class="contact-item">
                                <i class="el-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024">
                                        <path fill="currentColor" d="M128 224v512a64 64 0 0 0 64 64h640a64 64 0 0 0 64-64V224zm0-64h768a64 64 0 0 1 64 64v512a128 128 0 0 1-128 128H192A128 128 0 0 1 64 736V224a64 64 0 0 1 64-64"></path>
                                        <path fill="currentColor" d="M904 224 656.512 506.88a192 192 0 0 1-289.024 0L120 224zm-698.944 0 210.56 240.704a128 128 0 0 0 192.704 0L818.944 224H205.056"></path>
                                    </svg>
                                </i>
                                <span>邮箱：</span>
                            </div>
                            <div class="contact-item">
                                <i class="el-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024">
                                        <path fill="currentColor" d="m174.72 855.68 135.296-45.12 23.68 11.84C388.096 849.536 448.576 864 512 864c211.84 0 384-166.784 384-352S723.84 160 512 160 128 326.784 128 512c0 69.12 24.96 139.264 70.848 199.232l22.08 28.8-46.272 115.584zm-45.248 82.56A32 32 0 0 1 89.6 896l58.368-145.92C94.72 680.32 64 596.864 64 512 64 299.904 256 96 512 96s448 203.904 448 416-192 416-448 416a461.056 461.056 0 0 1-206.912-48.384l-175.616 58.56z"></path>
                                        <path fill="currentColor" d="M352 576h320q32 0 32 32t-32 32H352q-32 0-32-32t32-32m32-192h256q32 0 32 32t-32 32H384q-32 0-32-32t32-32"></path>
                                    </svg>
                                </i>
                                <span>社交媒体：</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
<script src="script.js"></script>
    <script>
        // 添加响应式导航菜单功能
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.querySelector('.menu-toggle');
            const navbarNav = document.querySelector('.navbar-nav');

            if (menuToggle) {
                menuToggle.addEventListener('click', function() {
                    navbarNav.classList.toggle('active');
                });
            }

            const menuItems = document.querySelectorAll('.navbar-nav a');
            menuItems.forEach(item => {
                item.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        navbarNav.classList.remove('active');
                    }
                });
            });

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
