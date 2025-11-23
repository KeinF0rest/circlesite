<?php
session_start();

$pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$event_id = $_GET['event_id'];

$stmt = $pdo->prepare("SELECT m.id, m.event_id, m.user_id, m.message, m.registered_time, u.nickname, u.profile_image FROM message m JOIN users u ON m.user_id = u.id WHERE m.event_id = ? ORDER BY m.registered_time ASC");
$stmt->execute([$event_id]);
$messages = $stmt->fetchAll();
    
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>チャット</title>
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
            
            .chat-thread {
                padding-bottom: 80px;
                overflow-y: auto;
            }
            
            .chat-input {
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                background: #fff;
                border-top: 1px solid #ddd;
                padding: 10px;
            }
            
            .chat-input form {
                display: flex;
                gap: 10px;
            }

            .chat-input textarea {
                flex: 1;
                resize: none;
                height: 40px;
            }

            .chat-input button {
                padding: 0 16px;
            }
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <div class="header-bar">
            <h1 class=""></h1>
            <a href="event-info.php?id=<= htmlspecialchars($event_id) ?>" class="back-button">戻る</a>
        </div>
        
        <div class="chat-thread">
            <?php foreach ($messages as $msg): ?>
                <div class="msg <?= $msg['user_id'] == $_SESSION['user_id'] ? 'me' : 'other' ?>">
                    <?php if ($msg['user_id'] != $_SESSION['user_id']): ?>
                        <img class="icon" src="<?= htmlspecialchars($msg['icon']) ?>" alt="">
                    <?php endif; ?>
                        <div class="bubble">
                            <p>
                                <strong><?= htmlspecialchars($msg['nickname'] ?? $msg['name']) ?>:</strong>
                                <?= nl2br(htmlspecialchars($msg['message'])) ?>
                            </p>
                            <div class="meta">
                                <span class="time"><?= $msg['registered_time'] ?></span>
                            </div>
                        </div>
                    <?php if ($msg['user_id'] == $_SESSION['user_id']): ?>
                        <img class="icon" src="<?= htmlspecialchars($msg['icon']) ?>" alt="">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="chat-form">
            <form method="post" action="chat-post.php" enctype="multipart/form-data">
                <input type="hidden" name="event_id" value="<?= htmlspecialchars($event_id) ?>">
                <textarea name="message" placeholder="メッセージを入力" required></textarea>
                <input type="file" name="image" accept="image/*">
                <button type="submit">送信</button>
            </form>
        </div>
    </body>
</html>