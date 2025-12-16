<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? null;

try {
    $pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("SELECT * FROM blog WHERE id = ? AND delete_flag = 0");
    $stmt->execute([$id]);
    $blog = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$blog) {
        $_SESSION['error'] = "指定されたブログは存在しません。";
        header("Location: blog.php");
        exit;
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo "<p style='color:red; font-weight:bold;'>エラーが発生したためブログ情報が取得できませんでした。</p>";
    echo "<p><a href='blog.php' style='display:inline-block; padding:10px 20px; background:#4CAF50; color:#fff; text-decoration:none; border-radius:6px;'>ブログに戻る</a></p>";
    exit;
}







?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>ブログ</title>
        <link rel="stylesheet" href="style.css">
        <script src="menu.js" defer></script>
        <style>
            body {
                font-family: sans-serif;
                margin: 0;
            }
            
            .header-bar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin: 20px;
            }

            .header-bar h1 {
                font-size: 24px;
                margin: 0;
            }
            
            .back-button {
                text-decoration: none;
                font-size: 16px;
                color: #4CAF50;
            }

            .menu-icon {
                font-size: 24px;
                cursor: pointer;
                padding: 6px 10px;
                transition: background-color 0.2s ease;
            }

            .menu-wrapper {
                position: relative;
                display: flex;
                justify-content: flex-end;
                margin-right: 20px;
            }
            
            .menu-popup {
                display: none;
                position: absolute;
                top: 40px;
                right: 0;
                background-color: #fff;
                border: 1px solid #ccc;
                border-radius: 6px;
                box-shadow: 0 2px 6px rgba(0,0,0,0.1);
                z-index: 100;
            }

            .menu-popup.visible {
                display: block;
            }
            
            .menu-popup ul {
                list-style: none;
                margin: 0;
                padding: 10px;
            }

            .menu-popup li {
                margin: 8px 0;
            }
            .menu-popup ul {
                list-style: none;
                margin: 0;
                padding: 10px;
            }

            .menu-popup li {
                margin: 8px 0;
            }

            .menu-popup a {
                text-decoration: none;
                color: #333;
                font-size: 14px;
                padding: 4px 8px;
                display: block;
                border-radius: 4px;
                transition: background-color 0.2s ease;
            }

            .menu-popup a:hover {
                background-color: #f0f0f0;
            }
            
            .content {
                border: 1px solid #ccc;
                border-radius: 6px;
                padding: 20px;
                background-color: #f9f9f9;
                font-size: 16px;
                line-height: 1.6;
                margin: 20px;
                color: #333;
                line-height: 1.8;
            }
            
            .blog-date {
                font-size: 14px;
                color: #666;
                justify-self: end;
                margin-right: 20px;
            }
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <div class="header-bar">
            <h1><?= htmlspecialchars($blog['title']) ?></h1>
            <a href="blog.php" class="back-button">戻る</a>
        </div>
        
       <?php if ($_SESSION['user']['authority'] != 0): ?>
            <div class="menu-wrapper">
                <div class="menu-icon" onclick="toggleMenu()">⋯</div>
                <div class="menu-popup" id="menu-popup">
                    <ul>
                        <li><a href="blog-update.php?id=<?= $blog['id'] ?>">更新</a></li>
                        <li><a href="blog-delete.php?id=<?= $blog['id'] ?>">削除</a></li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="content">
            <?= nl2br(htmlspecialchars(preg_replace('/^[\p{Z}\s]+/u', '', $blog['content']))) ?>
        </div>
        
        <div class="blog-date">
            登録日:<?= htmlspecialchars(date('Y/m/d', strtotime($blog['registered_time']))) ?>
        </div>
        
        <script>
            function toggleMenu() {
                const menu = document.getElementById('menu-popup');
                menu.classList.toggle('visible');
            }
        </script>
    </body>
</html>