<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$changed = false;
try {
    
    $pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = $_POST['id'] ?? null;
    $title = $_POST['title'] ?? '';
    
    if ($title !== '') {
        $stmt = $pdo->prepare("UPDATE album SET title = ? WHERE id = ?");
        $stmt->execute([$title, $id]);
        $changed = true;
    }

    $stmt_img = $pdo->prepare("SELECT COUNT(*) FROM album_images WHERE album_id = ? AND delete_flag = 0");
    $stmt_img->execute([$id]);
    $current_count = $stmt_img->fetchColumn();

    $delete_count = !empty($_POST['delete_images']) ? count($_POST['delete_images']) : 0;

    $add_count = !empty($_FILES['new_images']['name'][0]) ? count($_FILES['new_images']['name']) : 0;

    $final_count = $current_count - $delete_count + $add_count;
    if ($final_count > 10) {
        echo "<p style='color:red;'>写真は最大10枚までです。削除と追加の枚数を調整してください。</p>";
        exit;
    }

    $stmt = $pdo->prepare("UPDATE album SET title = ? WHERE id = ?");
    $stmt->execute([$title, $id]);

    if (!empty($_POST['delete_images'])) {
        foreach ($_POST['delete_images'] as $img_id) {
            $stmt_del = $pdo->prepare("UPDATE album_images SET delete_flag = 1 WHERE id = ?");
            $stmt_del->execute([$img_id]);
        }
        $changed = true;
    }

    if (!empty($_FILES['new_images']['tmp_name'][0])) {
        $upload_dir = 'uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        foreach ($_FILES['new_images']['tmp_name'] as $index => $tmp_name) {
            if (is_uploaded_file($tmp_name)) {
                $ext = pathinfo($_FILES['new_images']['name'][$index], PATHINFO_EXTENSION);
                $filename = uniqid('img_', true) . '.' . $ext;
                $path = $upload_dir . $filename;
                move_uploaded_file($tmp_name, $path);

                $stmt_img = $pdo->prepare("INSERT INTO album_images (album_id, image_path) VALUES (?, ?)");
                $stmt_img->execute([$id, $path]);
                $changed = true;
            }
        }
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo"<p style='color:red; font-weight:bold;'>エラーが発生したためアルバム更新できません。</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>アルバム更新完了</title>
        <link rel="stylesheet" href="style.css">
        <script src="menu.js" defer></script>
        <style>
            body {
                font-family: sans-serif;
                margin: 0;
            }
            
            .header-bar {
                max-width: 600px;
                margin: 20px;
                text-align: center;
            }
            
            .header-bar h1 {
                margin: 0;
                font-size: 24px;
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
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <div class="header-bar">
            <?php if ($changed): ?>
                <h1>アルバムが更新されました。</h1>
            <?php else: ?>
                <h1>変更はありませんでした。</h1>
            <?php endif; ?>
            <a href="album-info.php?id=<?= htmlspecialchars($id) ?>" class="back-link">アルバムへ</a>
        </div>
    </body>
</html>