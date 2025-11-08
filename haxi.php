<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>密码哈希工具</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 20px;
            background-color: #f4f7f6;
            color: #333;
            line-height: 1.6;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 30px auto;
        }
        h1, h2 {
            color: #0056b3;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        form input[type="password"],
        form input[type="text"] {
            width: calc(100% - 22px);
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box; /* 包含 padding 和 border 在宽度内 */
        }
        form input[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 17px;
            transition: background-color 0.3s ease;
        }
        form input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .result-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px dashed #e0e0e0;
        }
        p {
            margin-bottom: 10px;
        }
        code {
            background-color: #e9e9e9;
            padding: 3px 6px;
            border-radius: 4px;
            font-family: 'Courier New', Courier, monospace;
            display: inline-block; /* 保持代码块在行内 */
            word-break: break-all; /* 防止长代码溢出 */
        }
        .success {
            color: #28a745; /* 绿色 */
            font-weight: bold;
            background-color: #e6ffe6;
            padding: 10px;
            border-radius: 5px;
        }
        .error {
            color: #dc3545; /* 红色 */
            font-weight: bold;
            background-color: #ffe6e6;
            padding: 10px;
            border-radius: 5px;
        }
        .warning {
            color: #ffc107; /* 橙色 */
            background-color: #fff3cd;
            padding: 8px;
            border-radius: 5px;
            font-size: 0.9em;
        }
        .info {
            color: #17a2b8; /* 蓝色 */
            background-color: #e0f7fa;
            padding: 8px;
            border-radius: 5px;
            font-size: 0.9em;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>密码哈希生成器与验证器</h1>

        <form action="" method="post">
            <label for="password">输入密码:</label>
            <input type="password" id="password" name="password" placeholder="输入要哈希或验证的密码" required>

            <label for="stored_hash">（可选）输入已存储的哈希值进行验证:</label>
            <input type="text" id="stored_hash" name="stored_hash" placeholder="例如：$2y$10$......................................................">
            <span class="info">如果你只输入密码，将生成其哈希值。如果你同时输入哈希值，将进行密码验证。</span>
            <br><br>

            <input type="submit" name="process" value="处理">
        </form>

        <div class="result-section">
            <?php
            // 检查表单是否被提交
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // 获取用户输入的密码，并进行HTML实体转义以防止XSS攻击
                $inputPassword = $_POST['password'] ?? '';
                $inputPassword = htmlspecialchars($inputPassword, ENT_QUOTES, 'UTF-8');

                // 获取用户输入的已存储哈希值，并进行HTML实体转义
                $storedHash = $_POST['stored_hash'] ?? '';
                $storedHash = htmlspecialchars($storedHash, ENT_QUOTES, 'UTF-8');

                if (empty($inputPassword)) {
                    echo "<p class='error'>请输入密码！</p>";
                } else {
                    echo "<h2>处理结果</h2>";

                    // 1. 生成密码哈希
                    $hashedPassword = password_hash($inputPassword, PASSWORD_DEFAULT);
                    echo "<p><strong>原始密码:</strong> <code>" . $inputPassword . "</code></p>";
                    echo "<p><strong>哈希后的密码:</strong> <code style='font-size: 0.9em;'>" . $hashedPassword . "</code></p>";
                    echo "<p class='info'><em>请复制此哈希值。在实际应用中，你会将此值存储在数据库中。</em></p>";

                    // 2. 如果提供了已存储的哈希值，则进行密码验证
                    if (!empty($storedHash)) {
                        echo "<h3>密码验证</h3>";
                        echo "<p><strong>用户输入的密码:</strong> <code>" . $inputPassword . "</code></p>";
                        echo "<p><strong>用于验证的哈希值:</strong> <code style='font-size: 0.9em;'>" . $storedHash . "</code></p>";

                        if (password_verify($inputPassword, $storedHash)) {
                            echo "<p class='success'><strong>✅ 验证成功！</strong> 输入的密码与哈希值匹配。</p>";

                            // 检查是否需要重新哈希（比如成本因子或算法更新了）
                            if (password_needs_rehash($storedHash, PASSWORD_DEFAULT)) {
                                echo "<p class='warning'>⚠️ <strong>注意：</strong> 此哈希值是使用旧的参数或算法生成的，建议在用户下次登录时重新哈希并更新它。</p>";
                                // 在实际应用中，你可以在这里执行数据库更新操作，例如：
                                // update_user_password_in_database($user_id, password_hash($inputPassword, PASSWORD_DEFAULT));
                            }
                        } else {
                            echo "<p class='error'><strong>❌ 验证失败！</strong> 输入的密码与哈希值不匹配。</p>";
                        }
                    } else {
                        echo "<p class='info'>未提供已存储的哈希值，仅生成了新密码的哈希。</p>";
                    }
                }
            }
            ?>
        </div>
    </div>
</body>
</html>
