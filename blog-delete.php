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
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
        $delete_id = $_POST['id'] ?? null;
        if ($delete_id) {
            $stmt = $pdo->prepare("UPDATE blog SET delete_flag = 1 WHERE id = ?");
            $stmt->execute([$delete_id]);
    
            $stmt_title = $pdo->prepare("SELECT title FROM blog WHERE id = ?");
            $stmt_title->execute([$delete_id]);
            $blog_title = $stmt_title->fetchColumn();
        
            $stmt_users = $pdo->query("SELECT id FROM users");
            $users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);
        
            $stmt_notify = $pdo->prepare("INSERT INTO notification (type, action, related_id, message, user_id, n_read) VALUES ('blog', 'delete', ?, ?, ?, 0)");

            foreach ($users as $u) {
                $stmt_notify->execute([
                    $id,
                    "ブログ「{$blog_title}」が削除されました。",
                    $u['id']
                ]);
            }
            $_SESSION['delete_complete'] = true;
            header("Location: blog-delete-complete.php");
            exit;
        }
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    $safeId = htmlspecialchars((string)($_POST['id'] ?? ''), ENT_QUOTES, 'UTF-8');
    echo "<p style='color:red; font-weight:bold;'>エラーが発生したためブログ削除ができませんでした。</p>";
    echo "<p><a href='blog-info.php?id=" . $safeId . "' style='display:inline-block; padding:10px 20px; background:#4CAF50; color:#fff; text-decoration:none; border-radius:6px;'>ブログ情報画面に戻る</a></p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>ブログ削除</title>
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
                margin-bottom: 20px;
            }
            
            .back-button {
                position: absolute;
                right: 20px;
                font-size: 16px;
                color: #4CAF50;
                text-decoration: none;
            }
            
            .form-grid {
                margin: 20px;
            }
            
            .form-row {
                display: flex;
                flex-direction: column;
                margin-bottom: 20px;
            }

            .form-row label {
                font-weight: bold;
                margin-bottom: 6px;
                font-size: 15px;
            }
            
            .form-row div {
                padding: 10px;
                background-color: #fff;
                border: 1px solid #ccc;
                border-radius: 6px;
                font-size: 15px;
                line-height: 1.6;
                white-space: pre-wrap;
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
            <h1>ブログを削除しますか？</h1>
            <a href="blog-info.php?id=<?= htmlspecialchars($blog['id']) ?>" class="back-button">戻る</a>
        </div>
        
        <h2>削除すると復元することはできません。</h2>
        
        <div class="form-grid">
            <div class="form-row">
                <label>タイトル</label>
                <div><?= htmlspecialchars($blog['title']) ?></div>
            </div>
            
            <div class="form-row">
                <label>内容</label>
                <div><?= htmlspecialchars($blog['content']) ?></div>
            </div>
        </div>
        
        <div class="submit-area">
            <form method="post">
                <input type="hidden" name="id" value="<?= htmlspecialchars($blog['id']) ?>">
                <button type="submit" name="delete" class="submit-button">削除</button>
            </form>
        </div>
    </body>
</html>