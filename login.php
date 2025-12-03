<?php
session_start();

$error = '';

try {
    $pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $mail = $_POST['mail'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE mail = ? AND delete_flag = 0");
        $stmt->execute([$mail]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'mail' => $user['mail'],
                'authority' => $user['authority'],
                'nickname' => $user['nickname'],
                'profile_image' => $user['profile_image']
            ];
            header("Location: index.php");
            exit();
        } else {
            $error = "メールアドレスまたはパスワードが正しくありません。";
        }
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo"<p style='color:red; font-weight:bold;'>エラーが発生したためログイン情報を取得できませんでした。</p>";
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>ログイン</title>
        <style>
            body {
                font-family: sans-serif;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }
            
            .error-container {
                text-align: center;
                margin-bottom: 20px;
            }
            
            .login-grid {
                background: #fff;
                padding: 30px;
                border: 1px solid #4CAF50;
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(0,255,0,0.3);
                width: 450px;
                text-align: center;
            }

            .login-grid h1 {
                margin-bottom: 20px;
                font-size: 24px;
                color: #333;
            }
            
            .login-grid form {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }
            
            .login-grid label {
                text-align: left;
                font-weight: bold;
                font-size: 16px;
                color: #333;
            }
            
            .login-grid input {
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 6px;
                font-size: 14px;
            }
            
            .submit-button {
                align-self: flex-end;
                width: 100px;
                padding: 12px;
                background-color: #4CAF50;
                color: white;
                border: none;
                border-radius: 6px;
                font-size: 16px;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }

            .submit-button:hover {
                background-color: #45a049;
            }
        </style>
    </head>
    <body>
        <?php if (!empty($error)): ?>
            <div class="error-container">
                <p style="color:red;"><?= htmlspecialchars($error) ?></p>
            </div>
        <?php endif; ?>
        
        <div class="login-grid">
            <h1>ログイン</h1>
            <form action="" method="post">
                <label>メールアドレス</label>
                <input type="email" name="mail" maxlength="100" required>
            
                <label>パスワード</label>
                <input type="password" name="password" maxlength="10" required>
                
                <button type="submit" class="submit-button">ログイン</button>
            </form>
        </div>
    </body>
</html>