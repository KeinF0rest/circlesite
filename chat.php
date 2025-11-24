<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$event_id = $_GET['event_id'] ?? null;

$eventStmt = $pdo->prepare("SELECT title FROM event WHERE id = ?");
$eventStmt->execute([$event_id]);
$event = $eventStmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT m.id, m.event_id, m.user_id, m.message, m.registered_time, u.nickname, u.profile_image FROM message m JOIN users u ON m.user_id = u.id WHERE m.event_id = ? ORDER BY m.registered_time ASC");
$stmt->execute([$event_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
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
                padding: 20px;
                padding-bottom: 80px;
                overflow-y: auto;
            }
            
            .msg {
                display: flex;
                flex-direction: column;
                align-items: flex-end;
                margin-bottom: 10px;
            }

            .msg.me {
                align-items: flex-end;
            }
            
            .icon {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                object-fit: cover;
                margin-bottom: 4px;
            }
            
            .default-icon {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background-color: #ccc;
            }

            .nickname {
                font-size: 14px;
                margin-bottom: 4px;
            }
            
            .msg-body {
                display: flex;
                flex-direction: row;
                align-items: flex-start;
                gap: 8px;
            }
            
            .bubble {
                max-width: 60%;
                padding: 10px;
                border-radius: 10px;
                background: #f1f1f1;
            }
            
            .date {
                font-size: 12px;
                color: #888;
            }
            
            .msg.me .bubble {
                background: #DCF8C6;
            }
            
            .form-grid {
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                background: #fff;
                box-sizing: border-box;
                border-top: 1px solid #333;
                padding: 10px;
            }
            
            .form-grid form {
                display: flex;
                gap: 10px;
                align-items: center;
                margin: 0;
            }

            .form-grid textarea {
                flex: 1;
                resize: none;
                height: 40px;
                border-radius: 6px;
                border: 1px solid #ccc;
                padding: 10px;
            }

            .form-grid button {
                flex-shrink: 0;
                height: 40px;
                padding: 0 16px;
                background: #4CAF50;
                color: #fff;
                border: none;
                border-radius: 6px;
                cursor: pointer;
            }
            
            .plus-button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 32px;
                height: 32px;
                font-size: 24px;
                font-weight: bold;
                color: #333;
                border-radius: 50%;
                cursor: pointer;
                transition: background 0.2s;
            }
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <div class="header-bar">
            <h1><?= htmlspecialchars($event['title'] ?? 'イベント') ?></h1>
            <a href="event-info.php?id=<?= htmlspecialchars($event_id) ?>" class="back-button">戻る</a>
        </div>
        
        <div class="chat-thread">
            <?php foreach ($messages as $msg): ?>
                <div class="msg <?= $msg['user_id'] == $_SESSION['user']['id'] ? 'me' : 'other' ?>">
                    
                    <div class="nickname">
                        <?= htmlspecialchars($msg['nickname']) ?>
                    </div>
                    
                    <div class="msg-body">
                        <?php if ($msg['user_id'] == $_SESSION['user']['id']): ?>
                            <?php if (!empty($_SESSION['user']['profile_image'])): ?>
                                <img class="icon" src="<?= htmlspecialchars($_SESSION['user']['profile_image']) ?>" alt="">
                            <?php else: ?>
                                <div class="icon default-icon"></div>
                            <?php endif; ?>
                            <?php else: ?>
                            <?php if (!empty($msg['profile_image'])): ?>
                                <img class="icon" src="<?= htmlspecialchars($msg['profile_image']) ?>" alt="">
                            <?php else: ?>
                                <div class="icon default-icon"></div>
                            <?php endif; ?>
                        <?php endif; ?>
    
                        <div class="bubble">
                            <?= nl2br(htmlspecialchars($msg['message'])) ?>
                        </div>
                    </div>
                    
                    <div class="date">
                        <span class="time">
                            <?= date('Y-m-d H:i', strtotime($msg['registered_time'])) ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="form-grid">
            <form method="post" action="chat-post.php" enctype="multipart/form-data">
                <input type="hidden" name="event_id" value="<?= htmlspecialchars($event_id) ?>">
                <textarea name="message" placeholder="メッセージを入力" required></textarea>
                <input type="file" id="image" name="image" accept="image/*" style="display:none;">
                <label for="image" class="plus-button">＋</label>
                <button type="submit">送信</button>
            </form>
        </div>
    </body>
</html>