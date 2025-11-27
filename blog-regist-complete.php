<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

try {
    $pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';

    $stmt = $pdo->prepare("INSERT INTO blog (title, content) VALUES (?, ?)");
    $stmt->execute([$title, $content]);
    
    $blog_id = $pdo->lastInsertId();
    
    $stmt_users = $pdo->query("SELECT id FROM users");
    $users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

    $stmt_notify = $pdo->prepare("INSERT INTO notification (type, action, related_id, message, user_id, n_read) VALUES ('blog', 'regist', ?, ?, ?, 0)");

    foreach ($users as $u) {
        $stmt_notify->execute([
            $blog_id,
            "ブログ「{$title}」が登録されました。",
            $u['id']
        ]);
    }
} catch (Exception $e) {
    echo "<p style='color:red; font-weight:bold;'>エラーが発生したためブログ登録できませんでした。</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>ブログ登録完了</title>
        <link rel="stylesheet" href="style.css">
        <script src="menu.js" defer></script>
        <style>
            body {
                font-family: sans-serif;
                margin: 0;
                text-align: center;
            }
            
            .header-bar {
                font-size: 24px;
                margin-bottom: 40px;
            }

            .back-link {
                display: inline-block;
                padding: 12px 24px;
                font-size: 16px;
                background-color: #4CAF50;
                color: white;
                text-decoration: none;
                border-radius: 6px;
            }
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <div class="header-bar">
            <h1>ブログが登録されました。</h1>
            <a href="blog.php" class="back-link">ブログへ</a>
        </div>  
    </body>
</html>