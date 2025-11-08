<?php
session_start();
try {
    $pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';

    $stmt = $pdo->prepare("INSERT INTO blog (title, content) VALUES (?, ?)");
    $stmt->execute([$title, $content]);
} catch (PDOException $e) {
    echo "<p style='color:red; font-weight:bold;'>エラーが発生したためイベント登録できません。</p>";
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

            .blog-button:hover {
                background-color: #45a049;
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