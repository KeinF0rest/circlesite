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
    header("Location: blog.php");
    exit();
}

try {
    $pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $id = $_POST['id'] ?? null;
    
    $newTitle = $_POST['title'] ?? '';
    $newContent = $_POST['content'] ?? '';
    
    $stmt = $pdo->prepare("SELECT * FROM blog WHERE id = ? AND delete_flag = 0");
    $stmt->execute([$id]);
    $blog = $stmt->fetch();
    
    if (!$blog) {
        $_SESSION['error'] = "指定されたブログは存在しません。";
        header("Location: blog.php");
        exit;
    }
    
    $changes = [];
    if ($newTitle !== $blog['title']) {
        $changes[] = 'タイトル';
    }
    if ($newContent !== $blog['content']) {
        $changes[] = '内容';
    }
    
    if (!empty($changes)) {
        $stmt = $pdo->prepare("UPDATE blog SET title = ?, content = ? WHERE id = ?");
        $stmt->execute([$newTitle, $newContent, $id]);
        
        $change_text = implode("・", $changes);
        
        $stmt_users = $pdo->query("SELECT id FROM users");
        $users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt_notify = $pdo->prepare("INSERT INTO notification (type, action, related_id, message, user_id, n_read) VALUES ('blog', 'update', ?, ?, ?, 0)");

        foreach ($users as $u) {
            $stmt_notify->execute([
                $id,
                "ブログ「{$newTitle}」が更新されました。（変更箇所: {$change_text}）",
                $u['id']
            ]);
        }
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo"<p style='color:red; font-weight:bold;'>エラーが発生したためブログ更新ができませんでした。</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>ブログ更新完了</title>
        <link rel="stylesheet" href="style.css">
        <script src="menu.js" defer></script>
        <style>
            body {
                font-family: sans-serif;
                margin: 0;
            }
            
            .header-bar {
                max-width: 600px;
                margin: 20px auto;
                text-align: center;
            }
            
            .change-item {
                margin-bottom: 20px;
                font-size: 24px;
            }
            
            .no-change {
                font-size: 18px;
                margin-bottom: 20px;
            }
            
            .back-link {
                padding: 10px 10px;
                display: inline-block;
                font-size: 16px;
                background-color: #4CAF50;
                color: white;
                text-decoration: none;
                border-radius: 6px;
            }
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <div class="header-bar">
            <?php if (!empty($changes)): ?>
                <p class="change-item"><?= implode('、', $changes) ?>が更新されました。</p>
            <?php else: ?>
                <p class="no-change">変更はありませんでした。</p>
            <?php endif; ?>
            
            <a href="blog-info.php?id=<?= htmlspecialchars($id) ?>" class="back-link">ブログへ</a>
        </div>
    </body>
</html>