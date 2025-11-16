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
    $files = $_FILES['image'] ?? null;

    $stmt = $pdo->prepare("INSERT INTO album (title) VALUES (?)");
    $stmt->execute([$title]);
    $album_id = $pdo->lastInsertId();

    $upload_dir = 'uploads/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $imagePaths = [];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 5 * 1024 * 1024;

    foreach ($files['tmp_name'] as $index => $tmp_name) {
        if (is_uploaded_file($tmp_name)) {
            $type = mime_content_type($tmp_name);
            $size = $files['size'][$index];
            
            $ext = pathinfo($files['name'][$index], PATHINFO_EXTENSION);
            $filename = uniqid('img_', true) . '.' . $ext;
            $path = $upload_dir . $filename;

            move_uploaded_file($tmp_name, $path);
            $imagePaths[] = $path;

            $stmt_img = $pdo->prepare("INSERT INTO album_images (album_id, image_path) VALUES (?, ?)");
            $stmt_img->execute([$album_id, $path]);
        }
    }
} catch(Exception $e) {
    error_log($e->getMessage());
    echo"<p style='color:red; font-weight:bold;'>エラーが発生したためアルバム登録できません。: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>アルバム登録完了</title>
        <link rel="stylesheet" href="style.css">
        <script src="menu.js" defer></script>
        <style>
            body {
                font-family: sans-serif;
                margin: 0;
            }
            
            .header-bar {
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                align-items: center;
                margin: 20px;
                text-align: center;
            }

            .header-bar h1 {
                margin: 0;
                font-size: 24px;
                margin: 20px;
            }
            
            .back-link {
                display: inline-block;
                padding: 10px 20px;
                font-size: 16px;
                background-color: #4CAF50;
                color: white;
                text-decoration: none;
                border-radius: 6px;
                transition: background-color 0.3s ease;
            }

            .back-link:hover {
                background-color: #45a049;
            }
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <div class="header-bar">
            <h1>アルバムが登録されました。</h1>
            <a href="album.php" class="back-link">アルバムへ</a>
        </div>
    </body>
</html>