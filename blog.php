<?php
$pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
$stmt = $pdo->query("SELECT * FROM blog ORDER BY registered_time DESC");
$blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF=8">
        <title>ブログ一覧</title>
        <link rel="stylesheet" href="style.css">
        <script src="menu.js" defer></script>
        <style>
            body {
                font-family: sans-serif;
                margin: 0;
            }
            
            .header-bar {
                margin: 20px;
                display: flex;
                justify-content: space-between;
            }
            
            .header-bar h1 {
                margin: 0;
                font-size: 24px;
            }
            .blog-card {
                background-color: #f9f9f9;
                border: 1px solid #ccc;
                border-radius: 12px;
                padding: 16px;
                display: block;
                text-decoration: none;
                color: inherit;
                transition: box-shadow 0.2s ease;
            }
            
            .blog-card:hover {
                box-shadow: 0 0 8px rgba(0,0,0,0.1);
            }
            
            .regist-button {
                right: 0;
                top: 0;
                font-size: 24px;
                padding: 6px 12px;
                color: black;
                text-decoration: none;
                transition: background-color 0.3s ease;
                line-height: 1;
            }
            
            .blog-grid {
                display: grid;
                grid-template-columns: 1fr;
                gap: 20px;
                padding: 20px;
            }

            .blog-card {
                background-color: #f9f9f9;
                border: 1px solid #ccc;
                border-radius: 12px;
                padding: 16px;
                display: grid;
                grid-template-rows: auto auto;
                grid-template-columns: 1fr;
                position: relative;
            }

            .blog-title {
                font-size: 18px;
                font-weight: bold;
                margin-bottom: 10px;
                justify-self: start;
            }

            .blog-date {
                font-size: 14px;
                color: #666;
                justify-self: end;
            }
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <div class="header-bar">
            <h1>ブログ一覧</h1>
            <a href="blog-regist.php" class="regist-button">＋</a>
        </div>
        
        <div class="blog-grid">
            <?php foreach ($blogs as $blog): ?>
            <a href="blog-info.php?id=<?= htmlspecialchars($blog['id']) ?>" class="blog-card">
                <div class="blog-title"><?= htmlspecialchars($blog['title']) ?></div>
                <div class="blog-date">登録日:<?= htmlspecialchars(date('Y/m/d', strtotime($blog['registered_time']))) ?></div>
            </a>
            <?php endforeach; ?>
        </div>
    </body>
</html>