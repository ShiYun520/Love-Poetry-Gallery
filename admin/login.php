<?php
require_once 'config.php';

// 如果用户已登录，重定向到仪表盘
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

// 处理登录表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = '请输入用户名和密码';
    } else {
        // 查询用户
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // 验证密码
            if (password_verify($password, $user['password'])) {
                // 密码正确，创建会话
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                // 重定向到仪表盘
                header('Location: index.php');
                exit;
            } else {
                $error = '密码错误';
            }
        } else {
            $error = '用户不存在';
        }
        
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>情诗壁纸 - 管理员登录</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #2c2f3a;
            color: #fff;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background-color: #24262c;
            border-radius: 10px;
            padding: 30px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h1 {
            font-size: 1.8rem;
            color: #8a65cc;
            margin-top: 10px;
        }
        .form-control {
            background-color: #383c47;
            border: none;
            color: #fff;
            padding: 12px;
        }
        .form-control:focus {
            background-color: #424655;
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(138, 101, 204, 0.25);
        }
        .btn-primary {
            background-color: #8a65cc;
            border: none;
            padding: 10px;
        }
        .btn-primary:hover {
            background-color: #7a5ac0;
        }
        .alert {
            background-color: rgba(220, 53, 69, 0.2);
            color: #f8d7da;
            border-color: rgba(220, 53, 69, 0.3);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fas fa-lock fa-3x text-primary"></i>
            <h1>情诗壁纸后台管理</h1>
            <p>请输入您的登录凭证</p>
        </div>
        
        <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div class="mb-3">
                <label for="username" class="form-label">用户名</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">密码</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">登录</button>
            </div>
        </form>
    </div>
</body>
</html>
