<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");

$stmt = $pdo->prepare("SELECT * FROM album WHERE delete_flag = 0 ORDER BY registered_time DESC");
$stmt->execute();
$albums = $stmt->fetchAll(PDO::FETCH_ASSOC);

$album_thumbnails = [];
foreach ($albums as $album) {
    $stmt_img = $pdo->prepare("SELECT image_path FROM album_images WHERE album_id = ? AND delete_flag = 0 ORDER BY registered_time ASC LIMIT 1");
    $stmt_img->execute([$album['id']]);
    $image = $stmt_img->fetch(PDO::FETCH_ASSOC);
    $album_thumbnails[$album['id']] = $image['image_path'] ?? null;
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>アルバム</title>
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
                margin: 0;
                font-size: 24px;
            }
            .regist-button {
                right: 0;
                top: 0;
                font-size: 24px;
                padding: 6px 12px;
                color: #333;
                text-decoration: none;
                transition: background-color 0.3s ease;
                line-height: 1;
            }
            
            .album-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 30px;
                margin: 20px;
            }
            
            a.album-card {
                text-decoration: none;
            }
            
            .album-card img {
                width: 100%;
                max-width: 100%;
                height: auto;
                object-fit: cover;
                border-radius: 6px;
            }
            
            .album-card h2 {
                font-size: 20px;
                color: #333;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <div class="header-bar">
            <h1>アルバム</h1>
            <a href="album-regist.php" class="regist-button">＋</a>
        </div>
        
        <div class="album-grid">
            <?php foreach ($albums as $album): ?>
            <a href="album-info.php?id=<?= htmlspecialchars($album['id']) ?>" class="album-card">
                <h2><?= htmlspecialchars($album['title']) ?></h2>
                <img src="<?= htmlspecialchars($album_thumbnails[$album['id']]) ?>" alt="サムネイル">
            </a>
            <?php endforeach; ?>
        </div>
    </body>
</html>