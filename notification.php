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
    SELECT n.*, e.title AS event_title, a.title AS album_title, b.title AS blog_title, e2.title AS chat_event_title 
    FROM notification n
    LEFT JOIN event e ON (n.type='event' AND n.related_id=e.id)
    LEFT JOIN album a ON (n.type='album' AND n.related_id=a.id)
    LEFT JOIN blog b ON (n.type='blog' AND n.related_id=b.id)
    LEFT JOIN event e2 ON (n.type='chat' AND n.related_id=e2.id)
    WHERE n.user_id = ? AND n.n_read = 0
    ORDER BY n.registered_time DESC
    ");
    $stmt->execute([$_SESSION['user']['id']]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "<p style='color:red;'>エラーが発生したため通知の取得ができませんでした。</p>";
    echo "<p><a href='notification.php' style='display:inline-block; padding:10px 20px; background:#4CAF50; color:#fff; text-decoration:none; border-radius:6px;'>通知一覧に戻る</a></p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            
            @media (max-width: 600px) { 
                .header-bar { 
                    margin: 10px; 
                    display: flex; 
                    justify-content: space-between; 
                    align-items: center; 
                }
                .header-bar h1 { 
                    font-size: 20px; 
                } 
                .back-button { 
                    position: static;
                    font-size: 14px; 
                } 
                .notification-grid { 
                    padding: 10px; 
                    gap: 12px; 
                } 
                .notification-card { 
                    padding: 14px; 
                } 
                .notification-card h2 { 
                    font-size: 16px; 
                } 
                .notification-card p { 
                    font-size: 12px; 
                } 
                .read-btn {  
                    padding: 14px;
                    font-size: 14px; 
                    box-sizing: border-box; 
                } 
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
                <?php $msg = $n['message'];
                if ($n['type'] === 'chat') {
                    $redirect = "chat.php?event_id=" . $n['related_id'];
                } else {
                    $redirect = "{$n['type']}-info.php?id={$n['related_id']}";
                }
                ?>
                    
                <?php if ($n['action'] !== 'delete'): ?>
                    <a href="read-notification.php?id=<?= $n['id'] ?>&redirect=<?= $redirect ?>" class="notification-card">
                        <h2><?= htmlspecialchars($msg) ?></h2>
                        <p>日時: <?= htmlspecialchars(date('Y-m-d H:i', strtotime($n['registered_time']))) ?></p>
                    </a>
                <?php else: ?>
                    <div class="notification-card">
                        <h2><?= htmlspecialchars($msg) ?></h2>
                        <p>日時: <?= htmlspecialchars(date('Y-m-d H:i', strtotime($n['registered_time']))) ?></p>
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