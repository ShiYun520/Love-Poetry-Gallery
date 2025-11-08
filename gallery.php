<?php
require_once 'admin/config.php';
require_once 'admin/functions.php';

// 获取壁纸类型参数
$type = isset($_GET['type']) ? $_GET['type'] : 'desktop';
if ($type !== 'desktop' && $type !== 'mobile') {
    $type = 'desktop';
}

// 分页参数
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 12; // 每页显示数量
$offset = ($page - 1) * $perPage;

// 获取壁纸和总数
$wallpapers = getWallpapers($conn, $type, $perPage, $offset);
$totalWallpapers = countWallpapers($conn, $type);
$totalPages = ceil($totalWallpapers / $perPage);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>情诗画廊 - <?php echo $type === 'desktop' ? '电脑壁纸' : '手机壁纸'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- 导航栏与首页相同 -->
    <header class="navbar">
        <!-- 同上... -->
    </header>

    <main class="content-wrapper gallery-page">
        <div class="page-header">
            <h1><?php echo $type === 'desktop' ? '电脑壁纸' : '手机壁纸'; ?></h1>
            <p>共 <?php echo $totalWallpapers; ?> 张壁纸</p>
        </div>

        <div class="gallery-grid <?php echo $type === 'mobile' ? 'mobile-gallery-grid' : ''; ?>">
            <?php if (count($wallpapers) > 0): ?>
                <?php foreach ($wallpapers as $wallpaper): ?>
                <div class="gallery-item <?php echo $type === 'mobile' ? 'mobile-gallery-item' : ''; ?>" 
                     data-title="<?php echo htmlspecialchars($wallpaper['title']); ?>" 
                     data-desc="<?php echo htmlspecialchars($wallpaper['description']); ?>">
                    <div class="image-container">
                        <img class="lazy-image" 
                             src="http://fz.torgw.cc/bz/uploads/683ec0b07f8ad.png" 
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

        <!-- 分页导航 -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?type=<?php echo $type; ?>&page=<?php echo ($page - 1); ?>" class="page-link">
                    <i class="fas fa-chevron-left"></i> 上一页
                </a>
            <?php endif; ?>
            
            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                <a href="?type=<?php echo $type; ?>&page=<?php echo $i; ?>" 
                   class="page-link <?php echo ($i == $page) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
                <a href="?type=<?php echo $type; ?>&page=<?php echo ($page + 1); ?>" class="page-link">
                    下一页 <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </main>

    <!-- Lightbox Modal HTML -->
    <div id="imageModal" class="modal">
        <!-- 同上... -->
    </div>

    <script src="script.js"></script>
</body>
</html>
