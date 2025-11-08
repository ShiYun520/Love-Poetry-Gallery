<?php
require_once 'admin/config.php';
require_once 'admin/functions.php';

// 如果是API调用
if (isset($_GET['random'])) {
    header('Content-Type: application/json');
    
    $type = isset($_GET['type']) ? $_GET['type'] : null;
    $typeClause = $type ? "WHERE type = '$type'" : "";
    
    $sql = "SELECT * FROM wallpapers $typeClause ORDER BY RAND() LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $wallpaper = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'wallpaper' => [
                'id' => $wallpaper['id'],
                'title' => $wallpaper['title'],
                'description' => $wallpaper['description'],
                'image_url' => $wallpaper['image_url'],
                'thumbnail_url' => $wallpaper['thumbnail_url'],
                'type' => $wallpaper['type']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => '没有找到壁纸']);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>情诗画廊 - 随机壁纸API</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <!-- 导航栏与首页相同 -->
    </header>
    
    <main class="content-wrapper">
        <div class="api-section">
            <h1>随机壁纸API</h1>
            <p>本API提供随机壁纸获取功能，支持指定壁纸类型。</p>
            
            <div class="api-docs">
                <h2>接口说明</h2>
                <div class="code-block">
                    <code>GET <?php echo $_SERVER['HTTP_HOST']; ?>/api.php?random=1&type=[desktop|mobile]</code>
                </div>
                
                <h3>参数说明</h3>
                <ul>
                    <li><code>random=1</code> - 必填，表示请求随机壁纸</li>
                    <li><code>type</code> - 可选，壁纸类型，可选值：desktop(电脑壁纸)、mobile(手机壁纸)</li>
                </ul>
                
                <h3>返回示例</h3>
                <div class="code-block">
<pre>{
    "success": true,
    "wallpaper": {
        "id": 1,
        "title": "壁纸标题",
        "description": "壁纸描述",
        "image_url": "https://example.com/wallpaper.jpg",
        "thumbnail_url": "https://example.com/thumbnail.jpg",
        "type": "desktop"
    }
}</pre>
                </div>
            </div>
            
            <div class="api-test">
                <h2>接口测试</h2>
                <button id="test-api-desktop" class="btn btn-primary">获取随机电脑壁纸</button>
                <button id="test-api-mobile" class="btn btn-primary">获取随机手机壁纸</button>
                <button id="test-api-all" class="btn btn-primary">获取任意壁纸</button>
                
                <div id="api-result" class="api-result">
                    <h3>测试结果</h3>
                    <pre id="result-json"></pre>
                    <div id="result-image"></div>
                </div>
            </div>
        </div>
    </main>
    
    <script>
        document.getElementById('test-api-desktop').addEventListener('click', function() {
            testAPI('desktop');
        });
        
        document.getElementById('test-api-mobile').addEventListener('click', function() {
            testAPI('mobile');
        });
        
        document.getElementById('test-api-all').addEventListener('click', function() {
            testAPI();
        });
        
        function testAPI(type) {
            const url = type ? `api.php?random=1&type=${type}` : 'api.php?random=1';
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('result-json').textContent = JSON.stringify(data, null, 2);
                    
                    if (data.success) {
                        document.getElementById('result-image').innerHTML = `
                            <h4>${data.wallpaper.title}</h4>
                            <p>${data.wallpaper.description}</p>
                            <img src="${data.wallpaper.thumbnail_url}" alt="${data.wallpaper.title}">
                        `;
                    } else {
                        document.getElementById('result-image').innerHTML = '';
                    }
                })
                .catch(error => {
                    document.getElementById('result-json').textContent = `错误: ${error.message}`;
                    document.getElementById('result-image').innerHTML = '';
                });
        }
    </script>
</body>
</html>
