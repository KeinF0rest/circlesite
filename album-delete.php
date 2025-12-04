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

try {
    $pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = $_GET['id'] ?? null;
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
        $stmt = $pdo->prepare("UPDATE album SET delete_flag = 1 WHERE id = ?");
        $stmt->execute([$id]);

        $stmt_img = $pdo->prepare("UPDATE album_images SET delete_flag = 1 WHERE album_id = ?");
        $stmt_img->execute([$id]);
        
        $stmt_title = $pdo->prepare("SELECT title FROM album WHERE id = ?");
        $stmt_title->execute([$id]);
        $album_title = $stmt_title->fetchColumn();
        
        $stmt_users = $pdo->query("SELECT id FROM users");
        $users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt_notify = $pdo->prepare("INSERT INTO notification (type, action, related_id, message, user_id, n_read) VALUES ('album', 'delete', ?, ?, ?, 0)");
        
        foreach ($users as $u) {
            $stmt_notify->execute([
                $id,
                "アルバム「{$album_title}」が削除されました。",
                $u['id']
            ]);
        }
        header("Location: album-delete-complete.php");
        exit;
    }
    $stmt = $pdo->prepare("SELECT * FROM album WHERE id = ? AND delete_flag = 0");
    $stmt->execute([$id]);
    $album = $stmt->fetch();
    
    if (!$album) {
        echo "該当するアルバムが見つかりません。";
        exit;
    }

    $stmt_img = $pdo->prepare("SELECT COUNT(*) FROM album_images WHERE album_id = ? AND delete_flag = 0");
    $stmt_img->execute([$id]);
    $image_count = $stmt_img->fetchColumn();
} catch (Exception $e) {
    error_log($e->getMessage());
    echo "<p style='color:red; font-weight:bold;'>エラーが発生したためアルバム削除できません。</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>アルバム削除</title>
        <link rel="stylesheet" href="style.css">
        <script src="menu.js" defer></script>
        <style>
            body {
                font-family: sans-serif;
                margin: 0;
            }
            
            .header-bar {
                position: relative;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .header-bar h1 {
                font-size: 24px;
                margin: 0;
                text-align: center;
            }
            
            h2 {
                text-align: center;
            }
            .back-button {
                position: absolute;
                right: 20px;
                font-size: 16px;
                color: #4CAF50;
                text-decoration: none;
            }
            
            .form-grid {
                max-width: 400px;
                margin: 0 auto;
                padding: 10px;
                border-radius: 12px;
                border: 1px solid #ccc;
                font-size: 16px;
            }
            
            .form-grid label {
                display: flex;
                flex-direction: column;
                margin: 10px;
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
            <h1>アルバムを削除しますか？</h1>
            <a href="album-info.php?id=<?= htmlspecialchars($album['id']) ?>" class="back-button">戻る</a>
        </div>
        
        <h2>削除すると復元することはできません。</h2>
        
        <div class="form-grid">
            <label><?= htmlspecialchars($album['title']) ?></label>
            <label>登録枚数：<?= $image_count ?> 枚</label>
        </div>
        
        <div class="submit-area">
            <form method="post">
                <button type="submit" name="submit" class="submit-button">削除</button>
            </form>
        </div>
    </body>
</html>