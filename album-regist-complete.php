<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['user']['authority'] == 0) {
    $_SESSION['error'] = "アクセス権限がありません。";
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: album.php");
    exit();
}

try {
    $pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $title = $_POST['title'] ?? '';

    $sql_album = "INSERT INTO album (title) VALUES (?)";
    $stmt_album = $pdo->prepare($sql_album);
    $stmt_album->execute([$title]);
    $album_id = $pdo->lastInsertId();
    
    $stmt_users = $pdo->query("SELECT id FROM users");
    $users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt_notify = $pdo->prepare("INSERT INTO notification (type, action, related_id, message, user_id, n_read) VALUES ('album', 'regist', ?, ?, ?, 0)");
    
    foreach ($users as $u) {
        $stmt_notify->execute([
            $album_id,
            "アルバム「{$title}」が登録されました。",
            $u['id']
        ]);
    }

    $upload_dir = __DIR__ . '/uploads/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $imagePaths = [];
    $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];
    $maxSize = 5 * 1024 * 1024;

    $files = $_FILES['image'] ?? null;
    if ($files && isset($files['tmp_name'])) {
        foreach ($files['tmp_name'] as $index => $tmp_name) {
            if (!is_uploaded_file($tmp_name)) {
                continue;
            }
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $type = finfo_file($finfo, $tmp_name);
            finfo_close($finfo);
    
            $size = $files['size'][$index];
            if (!array_key_exists($type, $allowedTypes)) {
                continue;
            }
            if ($size > $maxSize) {
                continue;
            }
            
            $ext = $allowedTypes[$type];
            $filename = uniqid('img_', true) . '.' . $ext;
            $path = $upload_dir . $filename;

            if (move_uploaded_file($tmp_name, $path)) {
                $stmt_img = $pdo->prepare("INSERT INTO album_images (album_id, image_path) VALUES (?, ?)");
                $stmt_img->execute([$album_id, 'uploads/' . $filename]);
                $imagePaths[] = 'uploads/' . $filename;
            } else {
                error_log("ファイル移動失敗: {$files['name'][$index]}");
            }
        }
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo "<p style='color:red; font-weight:bold;'>エラーが発生したためアルバム登録できませんでした。</p>";
    echo "<p><a href='album-regist.php' style='display:inline-block; padding:10px 20px; background:#4CAF50; color:#fff; text-decoration:none; border-radius:6px;'>アルバム登録画面に戻る</a></p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            }
            
            @media (max-width: 600px) {
    			.header-bar {
        			margin: 10px;
    			}
    			.header-bar h1 {
        			font-size: 20px;
        			margin: 10px 0;
    			}
    			.back-link {
        			padding: 14px 20px;
            		font-size: 14px;
        			text-align: center;
       				box-sizing: border-box;
    			}
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