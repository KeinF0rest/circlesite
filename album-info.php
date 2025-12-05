<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");

$id = $_GET['id'] ?? null;

$stmt = $pdo->prepare("SELECT * FROM album WHERE id = ? AND delete_flag = 0");
$stmt->execute([$id]);
$album = $stmt->fetch();

$stmt_img = $pdo->prepare("SELECT image_path FROM album_images WHERE album_id = ? AND delete_flag = 0 ORDER BY registered_time ASC");
$stmt_img->execute([$id]);
$images = $stmt_img->fetchAll(PDO::FETCH_ASSOC);

if (!$album) {
    echo "<p style='color:red;'>指定されたアルバムは存在しません。</p>";
    exit;
}

?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>アルバム情報</title>
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
            
            .menu-wrapper {
                position: relative;
                display: flex;
                justify-content: flex-end;
                margin-right: 20px;
            }
            
            .menu-icon {
                font-size: 24px;
                cursor: pointer;
                padding: 6px 10px;
                transition: background-color 0.2s ease;
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
            }
            
            .image-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
                margin: 20px;
            }
            
            .image-grid img {
                width: 100%;
                height: 200px;
                object-fit: cover;
                border-radius: 6px;
            }
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <div class="header-bar">
            <h1><?= htmlspecialchars($album['title']) ?></h1>
            <a href="album.php" class="back-button">戻る</a>
        </div>
        
        <?php if ($_SESSION['user']['authority'] != 0): ?>
            <div class="menu-wrapper">
                <div class="menu-icon" onclick="toggleMenu()">⋯</div>
                <div class="menu-popup" id="menu-popup">
                    <ul>
                        <li><a href="album-update.php?id=<?= $album['id'] ?>">更新</a></li>
                        <li><a href="album-delete.php?id=<?= $album['id'] ?>">削除</a></li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($images)): ?>
            <div class="image-grid">
                <?php foreach ($images as $img): ?>
                    <img src="<?= htmlspecialchars($img['image_path']) ?>" alt="アルバム画像">
                <?php endforeach; ?>
            </div>  
        <?php else: ?>
            <p style="margin: 20px;">画像は登録されていません。</p>
        <?php endif; ?>
        
        <script>
            function toggleMenu() {
                const menu = document.getElementById('menu-popup');
                menu.classList.toggle('visible');
            }
        </script>
    </body>
</html>