<?php
session_start();
$album_id = $_GET['id'] ?? null;

$pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->prepare("SELECT * FROM album WHERE id = ?");
$stmt->execute([$album_id]);
$album = $stmt->fetch();

$stmt_img = $pdo->prepare("SELECT * FROM album_images WHERE album_id = ? AND delete_flag = 0");
$stmt_img->execute([$album_id]);
$images = $stmt_img->fetchAll();
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>アルバム更新</title>
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
                padding: 20px;
                align-items: center;
            }
            
            .header-bar h1 {
                font-size: 24px;
            }
            
            .back-button {
                font-size: 16px;
                color: #4CAF50;
                text-decoration: none;
            }
            
            .form-row{
                margin: 20px;
            }
            
            label{
                display: block;
                font-weight: bold;
                margin-bottom: 5px;
            }
            
            .image-list {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
            }
            
            .image-item img {
                width: 100%;
                height: auto;
                object-fit: cover;
                border-radius: 6px;
            }
            
            .image-slot {
                width: 100%;
                height: 100px;
                border: 2px dashed #ccc;
                border-radius: 6px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                position: relative;
            }

            .image-slot:hover {
                border-color: #007bff;
            }

            .plus {
                font-size: 24px;
                color: #888;
            }

            .image-slot input[type="file"] {
                position: absolute;
                inset: 0;
                opacity: 0;
                cursor: pointer;
            }
            .submit-area {
                display: flex;
                justify-content: flex-end;
                margin: 20px;
            }
            
            .submit-button {
                padding: 10px 20px;
                font-size: 16px;
                background-color: #4CAF50;
                color: white;
                border: none;
                border-radius: 6px;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <div class="header-bar">
            <h1>アルバム更新</h1>
            <a href="album-info.php?id=<?= htmlspecialchars($album['id']) ?>" class="back-button">戻る</a>
        </div>
        
        <form action="album-update-complete.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= htmlspecialchars($album['id']) ?>">
            
            <div class="form-row">
                <label>タイトル</label>
                <input type="text" name="title" maxlength="30" pattern="[ぁ-んァ-ヶ一-龠A-Za-z0-9ー\s]+" value="<?= htmlspecialchars($album['title']) ?>" required>
            </div>
            
            <div class="form-row">
            <label>写真</label>
            <div class="image-list">
                <?php foreach ($images as $img): ?>
                    <div class="image-item">
                        <img src="<?= htmlspecialchars($img['image_path']) ?>" width="120">
                        <label>
                            <input type="checkbox" name="delete_images[]" value="<?= $img['id'] ?>">削除
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
                </div>

            <div class="form-row">
                <label>新しい画像を追加</label>
                <label class="image-slot">
                    <span class="plus">＋</span>
                    <input type="file" name="image_path[]" accept="image/*" multiple id="imageInput">
                </label>
            </div>
            

            <div class="submit-area">
                <button type="submit" class="submit-button">更新</button>
            </div>
        </form>
    </body>
</html>