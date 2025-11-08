<!DOCTYPE html>
<html lang="zh-CN" class="dark">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>情诗画廊 - 头像制作</title>
    <meta name="description" content="免费在线头像制作工具，轻松制作个性化头像，支持多种风格和效果。">
    <!-- 引入 Font Awesome 图标库 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
    <style>
        /* 新增/修改：艺术标题链接样式 */
        .artistic-title {
            text-decoration: none; /* 移除链接下划线 */
            color: inherit; /* 继承父元素（navbar-brand）的颜色 */
            cursor: pointer; /* 提示可点击 */
            /* 如果原 span 有其他特定样式（如 font-size, font-weight, margin-left 等），请在此处添加 */
            /* 假设它原本就与 logo-icon 对齐，font-size 和 margin-left 可能在 .navbar-brand 或其他地方定义 */
        }

/* 调整 wallpaper-container 的顶部内边距，让 page-header 自己控制与顶部的距离 */
        .wallpaper-container {
            width: 95%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 0 20px 0; /* 顶部内边距设为0，底部保持20px */
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
      
        /* 头像制作样式 */
        html.dark, body.dark, #app {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            background-color: #1a1a2e;
            color: #f0f0f0;
        }
      
        /* 全局样式 */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
      
        /* 头像制作样式 */
        .avatar-maker {
            padding: 20px 0;
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
      
        .avatar-maker h1 {
            font-size: 36px;
            margin-bottom: 10px;
            color: #f0f0f0;
        }
      
        .header-divider {
            display: flex;
            justify-content: center;
            margin: 15px 0;
        }
      
        .header-divider span {
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, #3c8ce7, #00eaff);
            border-radius: 2px;
        }
      
        .subtitle {
            font-size: 18px;
            color: #999;
        }
      
        .avatar-editor-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
      
        .card {
            background-color: #202133;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            flex: 1;
            min-width: 300px;
        }
      
        .card-header {
            padding: 15px 20px;
            border-bottom: 1px solid #303348;
        }
      
        .card-header h3 {
            margin: 0;
            color: #f0f0f0;
            font-size: 18px;
        }
      
        .cropper-container {
            flex: 1;
            min-height: 500px;
            width: 100%;
            height: 100%;
        }
      
        .cropper-view {
            width: 100%;
            height: 100%;
            position: relative;
        }
      
        .cropper-view img {
            max-width: 100%;
            max-height: 100%;
        }
      
        .cropper-content {
            padding: 20px;
            height: 460px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
      
        .upload-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #999;
            text-align: center;
            border: 2px dashed #555;
            border-radius: 8px;
        }
      
        .upload-icon {
            font-size: 48px;
            color: #555;
            margin-bottom: 15px;
        }
      
        .upload-primary-text {
            font-size: 18px;
            margin-bottom: 35px;
        }
      
        .upload-secondary-text {
            font-size: 14px;
            margin-bottom: 20px;
        }
      
        .upload-buttons {
            display: flex;
            gap: 15px;
        }
      
        .el-button {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            line-height: 1;
            white-space: nowrap;
            cursor: pointer;
            color: #fff;
            text-align: center;
            box-sizing: border-box;
            outline: none;
            transition: .1s;
            font-weight: 500;
            user-select: none;
            vertical-align: middle;
            border: 1px solid transparent;
            border-radius: 4px;
            padding: 12px 20px;
            font-size: 14px;
            background-color: transparent;
        }
      
        .el-button--primary {
            color: #fff;
            background-color: #3c8ce7;
            border-color: #3c8ce7;
        }
      
        .el-button--info {
            color: #fff;
            background-color: #909399;
            border-color: #909399;
        }
      
        .el-button--success {
            color: #fff;
            background-color: #67c23a;
            border-color: #67c23a;
        }
      
        .el-button--large {
            padding: 12px 20px;
            font-size: 14px;
            border-radius: 4px;
        }
      
        .el-button .el-icon {
            margin-right: 6px;
        }
      
        .el-icon {
            width: 1em;
            height: 1em;
            line-height: 1em;
            display: inline-flex;
            justify-content: center;
            align-items: center;
        }
      
        .el-icon svg {
            width: 1em;
            height: 1em;
            fill: currentColor;
        }
      
        .preview-container {
            flex: 1;
        }
      
        .preview-content {
            padding: 20px;
        }
      
        .style-options {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
        }
      
        .option-group {
            flex: 1;
            min-width: 200px;
        }
      
        .option-title {
            font-size: 14px;
            color: #aaa;
            margin-bottom: 10px;
        }
      
        .el-radio-group {
            display: flex;
            gap: 10px;
        }
      
        .el-radio-button {
            position: relative;
            display: inline-block;
            outline: none;
            cursor: pointer;
        }
      
        .el-radio-button__inner {
            display: inline-block;
            line-height: 1;
            white-space: nowrap;
            vertical-align: middle;
            background: #303348;
            border: 1px solid #303348;
            color: #ddd;
            font-weight: 500;
            border-radius: 4px;
            padding: 12px 20px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.15s;
            user-select: none;
        }
      
        .el-radio-button.is-active .el-radio-button__inner {
            color: #fff;
            background-color: #3c8ce7;
            border-color: #3c8ce7;
        }
      
        .el-radio-button__original-radio {
            opacity: 0;
            outline: none;
            position: absolute;
            z-index: -1;
        }
      
        .el-select {
            position: relative;
            width: 100%;
        }
      
        .scene-selector {
            margin-bottom: 30px;
        }
      
        .selector-title {
            font-size: 14px;
            color: #aaa;
            margin-bottom: 10px;
        }
      
        .el-tabs {
            border-radius: 8px;
            overflow: hidden;
        }
      
        .el-tabs__header {
            background-color: #303348;
            margin: 0;
        }
      
        .el-tabs__nav {
            display: flex;
            width: 100%;
        }
      
        .el-tabs__item {
            flex: 1;
            text-align: center;
            padding: 12px 0;
            font-size: 14px;
            color: #aaa;
            cursor: pointer;
            transition: all 0.3s;
        }
      
        .el-tabs__item.is-active {
            color: #fff;
            background-color: #3c8ce7;
        }
      
        .preview-scene-container {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            height: 300px;
        }
      
        /* Phone Scene Styles */
        .phone-scene {
            width: 220px;
            height: 100%;
            border: 10px solid #444;
            border-radius: 30px;
            overflow: hidden;
            background-color: #fff;
            position: relative;
        }
      
        .phone-notch {
            position: absolute;
            width: 60px;
            height: 20px;
            background-color: #444;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
            z-index: 2;
        }
      
        .phone-screen {
            width: 100%;
            height: 100%;
            background-color: #f5f5f5;
            overflow: hidden;
        }
      
        .app-header {
            background-color: #1da1f2;
            color: #fff;
            padding: 10px 15px;
            display: flex;
            align-items: center;
        }
      
        .app-logo {
            width: 24px;
            height: 24px;
            background-color: #fff;
            border-radius: 50%;
            margin-right: 10px;
        }
      
        .app-title {
            font-weight: bold;
        }
      
        .profile-section {
            position: relative;
            height: 100%;
        }
      
        .cover-image {
            height: 80px;
            background: linear-gradient(120deg, #a1c4fd 0%, #c2e9fb 100%);
        }
      
        .user-profile {
            padding: 0 15px;
            margin-top: -40px;
            display: flex;
        }
      
        .user-avatar {
            width: 80px;
            height: 80px;
            border: 4px solid #fff;
            background-color: #ddd;
            background-size: cover;
            background-position: center;
        }
      
        .avatar-square {
            border-radius: 0;
        }
      
        .avatar-rounded {
            border-radius: 16px;
        }
      
        .avatar-circle {
            border-radius: 50%;
        }
      
        .user-info {
            margin-left: 15px;
            padding-top: 45px;
        }
      
        .user-name {
            font-weight: bold;
            margin-bottom: 3px;
        }
      
        .user-status {
            color: #65BB5D;
            font-size: 14px;
        }
      
        .profile-stats {
            margin-top: 20px;
            display: flex;
            justify-content: space-around;
            padding: 10px 15px;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
        }
      
        .stat-item {
            text-align: center;
        }
      
        .stat-number {
            font-weight: bold;
            color: #000000;
        }
      
        .stat-label {
            font-size: 12px;
            color: #777;
        }
      
        /* ID Card Scene */
        .id-card-scene {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }
      
        .id-card {
            width: 320px;
            height: 200px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
      
        .id-card-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
      
        .company-logo {
            width: 30px;
            height: 30px;
            background-color: #3c8ce7;
            border-radius: 50%;
            margin-right: 10px;
        }
      
        .company-name {
            font-weight: bold;
            color: #333;
        }
      
        .id-photo {
            width: 100px;
            height: 120px;
            background-color: #ddd;
            background-size: cover;
            background-position: center;
            align-self: center;
        }
      
        .id-info {
            margin-top: 15px;
            text-align: center;
        }
      
        .id-name {
            font-weight: bold;
            margin-bottom: 5px;
            color: #000000;
        }
      
        .id-title {
            color: #666;
            margin-bottom: 5px;
        }
      
        .id-number {
            font-size: 12px;
            color: #999;
        }
      
        /* Chat Scene */
        .chat-scene {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
        }
      
        .chat-window {
            width: 320px;
            height: 100%;
            background-color: #f5f5f5;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }
      
        .chat-header {
            background-color: #075e54;
            color: #fff;
            padding: 15px;
            text-align: center;
        }
      
        .chat-title {
            font-weight: bold;
        }
      
        .chat-messages {
            padding: 15px;
            height: calc(100% - 50px);
            overflow-y: auto;
        }
      
        .message {
            display: flex;
            margin-bottom: 15px;
        }
      
        .message.received {
            justify-content: flex-start;
        }
      
        .message.sent {
            justify-content: flex-end;
        }
      
        .message-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #ddd;
            background-size: cover;
            background-position: center;
        }
      
        .message-bubble {
            max-width: 200px;
            padding: 10px;
            border-radius: 10px;
            margin: 0 10px;
        }
      
        .message.received .message-bubble {
            background-color: #fff;
        }
      
        .message.sent .message-bubble {
            background-color: #dcf8c6;
        }
      
        .message-text {
            margin-bottom: 5px;
            color: #000000;  /* 或者可以简写为 #000 */
        }
      
        .message-time {
            font-size: 12px;
            color: #999;
            text-align: right;
        }
      
        /* Standard Size Scene */
        .standard-container {
            display: flex;
            justify-content: space-around;
            align-items: center;
            width: 100%;
        }
      
        .standard-scene {
            text-align: center;
        }
      
        .size-indicator {
            margin-bottom: 10px;
            color: #999;
            font-size: 14px;
        }
      
        .avatar-large {
            width: 128px;
            height: 128px;
            background-color: #ddd;
            margin: 0 auto;
            background-size: cover;
            background-position: center;
        }
      
        .avatar-medium {
            width: 64px;
            height: 64px;
            background-color: #ddd;
            margin: 0 auto;
            background-size: cover;
            background-position: center;
        }
      
        .avatar-small {
            width: 32px;
            height: 32px;
            background-color: #ddd;
            margin: 0 auto;
            background-size: cover;
            background-position: center;
        }
      
        .el-alert {
            width: 100%;
            padding: 8px px;
            margin: 20px 0;
            border-radius: 4px;
            position: relative;
            background-color: #f4f4f5;
            overflow: hidden;
            display: flex;
            align-items: center;
        }
      
        .el-alert--info.is-light {
            background-color: #f4f4f5;
            color: #909399;
        }
      
        .el-alert__content {
            display: flex;
            flex: 1;
        }
      
        .el-alert__description {
            font-size: 12px;
            margin: 0;
        }
      
        .tip-content {
            display: flex;
            align-items: center;
        }
      
        .tip-icon {
            margin-right: 8px;
            color: #909399;
        }
      
        .download-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
      
        .download-button {
            width: 200px;
        }
      
        .file-input {
            display: none;
        }
      
        /* 响应式设计 */
        @media (max-width: 768px) {
            .avatar-editor-container {
                flex-direction: column;
            }
          
            .upload-buttons {
                flex-direction: column;
            }
          
            .preview-scene-container {
                height: auto;
                flex-direction: column;
                align-items: center;
                gap: 20px;
            }
          
            .standard-container {
                flex-direction: column;
                gap: 20px;
            }
        }
    </style>
</head>

<body class="dark"> 
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
            <h1>头像制作</h1>
        </div>
                    <div data-v-956b05b4="" class="avatar-editor-container">
                        <div data-v-956b05b4="" class="card cropper-container">
                            <div data-v-956b05b4="" class="cropper-view" style="display: none;">
                                <img id="cropper-image" src="" alt="裁剪图片">
                            </div>
                            <div data-v-956b05b4="" class="cropper-content">
                                <div data-v-956b05b4="" class="upload-placeholder" id="upload-area">
                                    <div data-v-956b05b4="" class="upload-icon">
                                        <i data-v-956b05b4="" class="el-icon">
                                            <svg data-v-956b05b4="" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024">
                                                <path fill="currentColor" d="M160 160v704h704V160zm-32-64h768a32 32 0 0 1 32 32v768a32 32 0 0 1-32 32H128a32 32 0 0 1-32-32V128a32 32 0 0 1 32-32"></path>
                                                <path fill="currentColor" d="M384 288q64 0 64 64t-64 64q-64 0-64-64t64-64M185.408 876.992l-50.816-38.912L350.72 556.032a96 96 0 0 1 134.592-17.856l1.856 1.472 122.88 99.136a32 32 0 0 0 44.992-4.864l216-269.888 49.92 39.936-215.808 269.824-.256.32a96 96 0 0 1-135.04 14.464l-122.88-99.072-.64-.512a32 32 0 0 0-44.8 5.952z"></path>
                                            </svg>
                                        </i>
                                    </div>
                                    <p data-v-956b05b4="" class="upload-primary-text">选择或拖拽图片到此处</p>
                                    <p data-v-956b05b4="" class="upload-secondary-text">开始制作您的专属头像</p>
                                    <div data-v-956b05b4="" class="upload-buttons">
                                        <button data-v-956b05b4="" type="button" class="el-button el-button--primary el-button--large upload-action-button">
                                            <span>
                                                <i data-v-956b05b4="" class="el-icon">
                                                    <svg data-v-956b05b4="" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024">
                                                        <path fill="currentColor" d="M160 832h704a32 32 0 1 1 0 64H160a32 32 0 1 1 0-64m384-578.304V704h-64V247.296L237.248 490.048 192 444.8 508.8 128l316.8 316.8-45.312 45.248z"></path>
                                                                                                            </svg>
                                                </i>
                                                <span data-v-956b05b4="" class="button-text">选择本地图片</span>
                                            </span>
                                        </button>
                                        <button data-v-956b05b4="" type="button" class="el-button el-button--info el-button--large upload-action-button">
                                            <span>
                                                <i data-v-956b05b4="" class="el-icon">
                                                    <svg data-v-956b05b4="" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024">
                                                        <path fill="currentColor" d="M512 128a384 384 0 1 0 0 768 384 384 0 0 0 0-768m0-64a448 448 0 1 1 0 896 448 448 0 0 1 0-896"></path>
                                                        <path fill="currentColor" d="M640 288q64 0 64 64t-64 64q-64 0-64-64t64-64M214.656 790.656l-45.312-45.312 185.664-185.6a96 96 0 0 1 123.712-10.24l138.24 98.688a32 32 0 0 0 39.872-2.176L906.688 422.4l42.624 47.744L699.52 693.696a96 96 0 0 1-119.808 6.592l-138.24-98.752a32 32 0 0 0-41.152 3.456l-185.664 185.6z"></path>
                                                    </svg>
                                                </i>
                                                <span data-v-956b05b4="" class="button-text">随机站内图片</span>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div data-v-956b05b4="" class="card preview-container">
                            <div data-v-956b05b4="" class="card-header">
                                <h3 data-v-956b05b4="">效果预览</h3>
                            </div>
                            <div data-v-956b05b4="" class="preview-content">
                                <div data-v-956b05b4="" class="style-options">
                                    <div data-v-956b05b4="" class="option-group">
                                        <div data-v-956b05b4="" class="option-title">形状</div>
                                        <div data-v-956b05b4="" class="el-radio-group" role="radiogroup">
                                            <label data-v-956b05b4="" class="el-radio-button is-active">
                                                <input type="radio" name="shape" value="square" checked class="el-radio-button__original-radio">
                                                <span class="el-radio-button__inner">方形</span>
                                            </label>
                                            <label data-v-956b05b4="" class="el-radio-button">
                                                <input type="radio" name="shape" value="rounded" class="el-radio-button__original-radio">
                                                <span class="el-radio-button__inner">圆角</span>
                                            </label>
                                            <label data-v-956b05b4="" class="el-radio-button">
                                                <input type="radio" name="shape" value="circle" class="el-radio-button__original-radio">
                                                <span class="el-radio-button__inner">圆形</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div data-v-956b05b4="" class="option-group">
                                        <div data-v-956b05b4="" class="option-title">滤镜</div>
                                        <select id="filter-select" class="el-select">
                                            <option value="none">无滤镜</option>
                                            <option value="grayscale">黑白</option>
                                            <option value="sepia">怀旧</option>
                                            <option value="saturate">鲜艳</option>
                                            <option value="cool">冷色调</option>
                                            <option value="warm">暖色调</option>
                                            <option value="contrast">高对比度</option>
                                            <option value="vintage">复古</option>
                                            <option value="soft">柔和</option>
                                            <option value="neon">霓虹</option>
                                            <option value="shadow">阴影加深</option>
                                            <option value="bright">明亮</option>
                                        </select>
                                    </div>
                                </div>
                                <div data-v-956b05b4="" class="scene-selector">
                                    <div data-v-956b05b4="" class="selector-title">预览场景</div>
                                    <div data-v-956b05b4="" class="el-tabs">
                                        <div class="el-tabs__header">
                                            <div class="el-tabs__nav">
                                                <div id="tab-social" class="el-tabs__item is-active" onclick="switchTab('social')">社交媒体</div>
                                                <div id="tab-id" class="el-tabs__item" onclick="switchTab('id')">工作证件</div>
                                                <div id="tab-chat" class="el-tabs__item" onclick="switchTab('chat')">聊天软件</div>
                                                <div id="tab-standard" class="el-tabs__item" onclick="switchTab('standard')">标准尺寸</div>
                                            </div>
                                        </div>
                                        
                                        <!-- 预览场景内容 -->
                                        <div class="el-tabs__content">
                                            <!-- 社交媒体场景 -->
                                            <div id="pane-social" class="el-tab-pane">
                                                <div class="preview-scene-container">
                                                    <div class="phone-scene">
                                                        <div class="phone-notch"></div>
                                                        <div class="phone-screen">
                                                            <div class="app-header">
                                                                <div class="app-logo"></div>
                                                                <div class="app-title">社交App</div>
                                                            </div>
                                                            <div class="profile-section">
                                                                <div class="cover-image"></div>
                                                                <div class="user-profile">
                                                                    <div id="social-avatar" class="user-avatar avatar-square"></div>
                                                                    <div class="user-info">
                                                                        <div class="user-name">用户名</div>
                                                                        <div class="user-status">在线</div>
                                                                    </div>
                                                                </div>
                                                                <div class="profile-stats">
                                                                    <div class="stat-item">
                                                                        <div class="stat-number">256</div>
                                                                        <div class="stat-label">关注</div>
                                                                    </div>
                                                                    <div class="stat-item">
                                                                        <div class="stat-number">1.2k</div>
                                                                        <div class="stat-label">粉丝</div>
                                                                    </div>
                                                                    <div class="stat-item">
                                                                        <div class="stat-number">48</div>
                                                                        <div class="stat-label">动态</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- 工作证件场景 -->
                                            <div id="pane-id" class="el-tab-pane" style="display: none;">
                                                <div class="preview-scene-container">
                                                    <div class="id-card-scene">
                                                        <div class="id-card">
                                                            <div class="id-card-header">
                                                                <div class="company-logo"></div>
                                                                <div class="company-name">科技有限公司</div>
                                                            </div>
                                                            <div id="id-avatar" class="id-photo avatar-square"></div>
                                                            <div class="id-info">
                                                                <div class="id-name">张三</div>
                                                                <div class="id-title">高级工程师</div>
                                                                <div class="id-number">员工编号: 10086</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- 聊天软件场景 -->
                                            <div id="pane-chat" class="el-tab-pane" style="display: none;">
                                                <div class="preview-scene-container">
                                                    <div class="chat-scene">
                                                        <div class="chat-window">
                                                            <div class="chat-header">
                                                                <div class="chat-title">对话</div>
                                                            </div>
                                                            <div class="chat-messages">
                                                                <div class="message received">
                                                                    <div class="message-avatar avatar-square"></div>
                                                                    <div class="message-bubble">
                                                                        <div class="message-text">你好！最近在忙什么？</div>
                                                                        <div class="message-time">10:24</div>
                                                                    </div>
                                                                </div>
                                                                <div class="message sent">
                                                                    <div class="message-bubble">
                                                                        <div class="message-text">我在制作我的新头像！</div>
                                                                        <div class="message-time">10:25</div>
                                                                    </div>
                                                                    <div id="chat-avatar" class="message-avatar avatar-square"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- 标准尺寸场景 -->
                                            <div id="pane-standard" class="el-tab-pane" style="display: none;">
                                                <div class="preview-scene-container standard-container">
                                                    <div class="standard-scene">
                                                        <div class="size-indicator">大尺寸 (128×128)</div>
                                                        <div id="large-avatar" class="avatar-large avatar-square"></div>
                                                    </div>
                                                    <div class="standard-scene">
                                                        <div class="size-indicator">中尺寸 (64×64)</div>
                                                        <div id="medium-avatar" class="avatar-medium avatar-square"></div>
                                                    </div>
                                                    <div class="standard-scene">
                                                        <div class="size-indicator">小尺寸 (32×32)</div>
                                                        <div id="small-avatar" class="avatar-small avatar-square"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 提示信息 -->
                                <div class="el-alert el-alert--info is-light">
                                    <div class="el-alert__content">
                                        <p class="el-alert__description">
                                            <div class="tip-content">
                                                <i class="el-icon tip-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024">
                                                        <path fill="currentColor" d="M512 64a448 448 0 1 1 0 896.064A448 448 0 0 1 512 64m67.2 275.072c33.28 0 60.288-23.104 60.288-57.344s-27.072-57.344-60.288-57.344c-33.28 0-60.16 23.104-60.16 57.344s26.88 57.344 60.16 57.344M590.912 699.2c0-6.848 2.368-24.64 1.024-34.752l-52.608 60.544c-10.88 11.456-24.512 19.392-30.912 17.28a12.992 12.992 0 0 1-8.256-14.72l87.68-276.992c7.168-35.136-12.544-67.2-54.336-71.296-44.096 0-108.992 44.736-148.48 101.504 0 6.784-1.28 23.68.064 33.792l52.544-60.608c10.88-11.328 23.552-19.328 29.952-17.152a12.8 12.8 0 0 1 7.808 16.128L388.48 728.576c-10.048 32.256 8.96 63.872 55.04 71.04 67.84 0 107.904-43.648 147.456-100.416z"></path>
                                                    </svg>
                                                </i>
                                                <span>tips：使用"更多选项"可以选择不同尺寸和格式，效果更佳。</span>
                                            </div>
                                        </p>
                                    </div>
                                </div>

                                <!-- 下载按钮 -->
                                <div class="download-container">
                                    <button type="button" class="el-button el-button--success el-button--large download-button" disabled>
                                        <span>
                                            <i class="el-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024">
                                                    <path fill="currentColor" d="M160 832h704a32 32 0 1 1 0 64H160a32 32 0 1 1 0-64m384-253.696 236.288-236.352 45.248 45.248L508.8 704 192 387.2l45.248-45.248L480 584.704V128h64z"></path>
                                                </svg>
                                            </i>
                                            <span class="button-text">下载头像</span>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="file" class="file-input" accept="image/*">
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <script>
        // 页面加载完成后执行
        document.addEventListener('DOMContentLoaded', function() {
            let cropper = null;
            const uploadArea = document.getElementById('upload-area');
            const cropperView = document.querySelector('.cropper-view');
            const cropperImage = document.getElementById('cropper-image');
            const fileInput = document.querySelector('.file-input');

            // 初始化裁剪器
            function initCropper(imageUrl) {
                cropperView.style.display = 'block';
                uploadArea.style.display = 'none';
                cropperImage.src = imageUrl;
                
                if (cropper) {
                    cropper.destroy();
                }
                
                cropper = new Cropper(cropperImage, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 0.8,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                    ready: function() {
                        cropperView.addEventListener('wheel', function(e) {
                            e.preventDefault();
                            if (e.deltaY > 0) {
                                cropper.zoom(-0.1);
                            } else {
                                cropper.zoom(0.1);
                            }
                        });
                    },
                    crop: updatePreviews
                });
            }

            // 更新预览
            function updatePreviews() {
                if (!cropper) return;
                
                const canvas = cropper.getCroppedCanvas({
                    width: 256,
                    height: 256
                });
                
                if (canvas) {
                    const imageUrl = canvas.toDataURL();
                    const avatarElements = document.querySelectorAll(
                        '#social-avatar, #id-avatar, #chat-avatar, #large-avatar, #medium-avatar, #small-avatar'
                    );
                    
                    avatarElements.forEach(avatar => {
                        avatar.style.backgroundImage = `url(${imageUrl})`;
                    });
                }
            }

            // 选择本地图片按钮点击事件
            document.querySelector('.upload-action-button').addEventListener('click', function() {
                fileInput.click();
            });
            
// 找到随机站内图片按钮并修改其事件处理
document.querySelectorAll('.upload-action-button')[1].addEventListener('click', function() {
    // 发起AJAX请求获取随机图片
    fetch('random-image.php')
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // 使用获取到的随机图片初始化裁剪器
                initCropper(data.imageUrl);
                enableDownloadButton();
            } else {
                alert('获取随机图片失败: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('获取随机图片时发生错误');
        });
});



            // 文件选择处理
            fileInput.addEventListener('change', function(e) {
                if (e.target.files.length > 0) {
                    const file = e.target.files[0];
                    const reader = new FileReader();
                    
                    reader.onload = function(event) {
                        initCropper(event.target.result);
                        enableDownloadButton();
                    };
                    
                    reader.readAsDataURL(file);
                }
            });

            // 启用下载按钮
            function enableDownloadButton() {
                const downloadButton = document.querySelector('.download-button');
                downloadButton.disabled = false;
                downloadButton.classList.remove('is-disabled');
                
                downloadButton.addEventListener('click', function() {
                    if (!cropper) return;
                    
                    const canvas = cropper.getCroppedCanvas({
                        width: 256,
                        height: 256
                    });
                    
                    if (canvas) {
                        const link = document.createElement('a');
                        link.download = 'avatar.png';
                        link.href = canvas.toDataURL('image/png');
                        link.click();
                    }
                });
            }

            // 切换标签页
            window.switchTab = function(tabId) {
                const tabPanes = document.querySelectorAll('.el-tab-pane');
                const tabItems = document.querySelectorAll('.el-tabs__item');
                
                tabPanes.forEach(pane => {
                    pane.style.display = 'none';
                });
                
                tabItems.forEach(item => {
                    item.classList.remove('is-active');
                });
                
                document.getElementById('pane-' + tabId).style.display = 'block';
                document.getElementById('tab-' + tabId).classList.add('is-active');
            };

            // 形状切换
            const shapeRadios = document.querySelectorAll('input[name="shape"]');
            shapeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    const radioButtons = document.querySelectorAll('.el-radio-button');
                    radioButtons.forEach(button => {
                        button.classList.remove('is-active');
                    });
                    
                    this.parentElement.classList.add('is-active');
                    
                    const avatarElements = document.querySelectorAll(
                        '.user-avatar, .id-photo, .message-avatar, .avatar-large, .avatar-medium, .avatar-small'
                    );
                    
                    avatarElements.forEach(avatar => {
                        avatar.classList.remove('avatar-square', 'avatar-rounded', 'avatar-circle');
                        avatar.classList.add('avatar-' + this.value);
                    });
                });
            });

            // 滤镜切换
            const filterSelect = document.getElementById('filter-select');
            filterSelect.addEventListener('change', function() {
                const avatarElements = document.querySelectorAll(
                    '.user-avatar, .id-photo, .message-avatar, .avatar-large, .avatar-medium, .avatar-small'
                );
                
                let filterStyle = '';
                switch (this.value) {
                    case 'grayscale':
                        filterStyle = 'grayscale(100%)';
                        break;
                    case 'sepia':
                        filterStyle = 'sepia(0.7)';
                        break;
                    case 'saturate':
                        filterStyle = 'saturate(2)';
                        break;
                    case 'cool':
                        filterStyle = 'hue-rotate(180deg)';
                        break;
                    case 'warm':
                        filterStyle = 'sepia(0.3) saturate(1.5)';
                        break;
                    case 'contrast':
                        filterStyle = 'contrast(1.5)';
                        break;
                    case 'vintage':
                        filterStyle = 'sepia(0.5) contrast(1.2)';
                        break;
                    case 'soft':
                        filterStyle = 'contrast(0.9) brightness(1.1)';
                        break;
                    case 'neon':
                        filterStyle = 'brightness(1.2) saturate(1.8) contrast(1.4)';
                        break;
                    case 'shadow':
                        filterStyle = 'brightness(0.8) contrast(1.2)';
                        break;
                    case 'bright':
                        filterStyle = 'brightness(1.3)';
                        break;
                    default:
                        filterStyle = 'none';
                }
                
                avatarElements.forEach(avatar => {
                    avatar.style.filter = filterStyle;
                });
            });

            // 支持拖拽上传
            const dropArea = document.querySelector('.cropper-content');
            
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, preventDefaults, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                dropArea.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, unhighlight, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            function highlight() {
                dropArea.classList.add('highlight');
            }

            function unhighlight() {
                dropArea.classList.remove('highlight');
            }

            dropArea.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;

                if (files.length > 0) {
                    const file = files[0];
                    const reader = new FileReader();

                    reader.onload = function(event) {
                        initCropper(event.target.result);
                        enableDownloadButton();
                    };

                    reader.readAsDataURL(file);
                }
            }

            // 添加响应式导航菜单功能
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
