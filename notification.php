<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

try {
    $pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("
    SELECT n.*, e.title AS event_title, a.title AS album_title, b.title AS blog_title 
    FROM notification n
    LEFT JOIN event e ON (n.type='event' AND n.related_id=e.id)
    LEFT JOIN album a ON (n.type='album' AND n.related_id=a.id)
    LEFT JOIN blog b ON (n.type='blog' AND n.related_id=b.id)
    WHERE n.user_id = ? AND n.n_read = 0
    ORDER BY n.registered_time DESC
    ");
    $stmt->execute([$_SESSION['user']['id']]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log($e->getMessage());
    echo "<p style='color:red;'>通知の取得に失敗しました。</p>";
    exit; 
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>通知</title>
        <link rel="stylesheet" href="style.css">
        <script src="menu.js" defer></script>
        <style>
            body {
                font-family: sans-serif;
                margin: 0px;
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

            .back-button {
                font-size: 16px;
                text-decoration: none;
                color: #4CAF50;
                position: absolute;
                right: 40px;  
            }
            
            .notification-grid {
                display: grid;
                grid-template-columns: 1fr;
                gap: 20px;
                padding: 20px;
            }
            
            .notification-card {
                background-color: #f9f9f9;
                border: 1px solid #ccc;
                border-radius: 12px;
                padding: 16px;
                display: block;
                text-decoration: none;
                color: inherit;
                transition: box-shadow 0.2s ease;
            }
            
            .notification-card:hover {
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                transform: scale(1.02);
                transition: all 0.2s ease;
            }
            
            .notification-card h2 {
                margin: 0 0 10px;
                font-size: 18px;
                color: #333;
            }
            
            .notification-card p {
                margin: 0;
                font-size: 14px;
                color: #666;
            }
            
            .read-btn {
                margin-top: 12px;
                padding: 8px 14px;
                background: #4CAF50;
                color: white;
                border: none;
                border-radius: 6px;
                cursor: pointer;
                font-size: 14px;
            }
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <div class="header-bar">
            <h1>通知一覧</h1>
            <a href="index.php" class="back-button">戻る</a>
        </div>
        
        <div class="notification-grid">
            <?php foreach ($notifications as $n): ?>
                <?php
                $title = $n['event_title'] ?? $n['album_title'] ?? $n['blog_title'] ?? '';
                if ($n['type'] === 'event') {
                    if ($n['action'] === 'regist') $msg = "イベント「{$title}」が登録されました。";
                    if ($n['action'] === 'update') $msg = "イベント「{$title}」が更新されました。";
                    if ($n['action'] === 'delete') $msg = "イベント「{$title}」が削除されました。";
                } elseif ($n['type'] === 'album') {
                    if ($n['action'] === 'regist') $msg = "アルバム「{$title}」が登録されました。";
                    if ($n['action'] === 'update') $msg = "アルバム「{$title}」が更新されました。";
                    if ($n['action'] === 'delete') $msg = "アルバム「{$title}」が削除されました。";
                } elseif ($n['type'] === 'blog') {
                    if ($n['action'] === 'regist') $msg = "ブログ「{$title}」が登録されました。";
                    if ($n['action'] === 'update') $msg = "ブログ「{$title}」が更新されました。";
                    if ($n['action'] === 'delete') $msg = "ブログ「{$title}」が削除されました。";
                }
                ?>
                    
                <?php if ($n['action'] !== 'delete'): ?>
                    <a href="read-notification.php?id=<?= $n['id'] ?>&redirect=<?= $n['type'] ?>-info.php?id=<?= $n['related_id'] ?>" class="notification-card">
                        <h2><?= htmlspecialchars($msg) ?></h2>
                        <p>日時: <?= htmlspecialchars($n['registered_time']) ?></p>
                    </a>
                <?php else: ?>
                    <div class="notification-card">
                        <h2><?= htmlspecialchars($msg) ?></h2>
                        <p>日時: <?= htmlspecialchars($n['created_at']) ?></p>
                        <form method="post" action="read-notification.php">
                            <input type="hidden" name="id" value="<?= $n['id'] ?>">
                            <button type="submit" class="read-btn">既読にする</button>
                        </form>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </body>
</html>