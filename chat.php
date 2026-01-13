<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$event_id = $_GET['event_id'] ?? null;

if (empty($event_id)) {
    $_SESSION['error'] = "イベントが指定されていません。";
    header("Location: index.php");
    exit();
}

$eventStmt = $pdo->prepare("SELECT title FROM event WHERE id = ? AND delete_flag = 0");
$eventStmt->execute([$event_id]);
$event = $eventStmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    $_SESSION['error'] = "指定されたイベントは存在しません。";
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT m.id, m.event_id, m.user_id, m.message, m.image_path, m.registered_time, u.nickname, u.profile_image, (SELECT COUNT(*) FROM message_read r WHERE r.message_id = m.id) AS read_count FROM message m JOIN users u ON m.user_id = u.id WHERE m.event_id = ? ORDER BY m.registered_time ASC");
$stmt->execute([$event_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("INSERT IGNORE INTO message_read (message_id, user_id) VALUES (?, ?)");
foreach ($messages as $msg) {
    if ($msg['user_id'] != $_SESSION['user']['id']) {
        $stmt->execute([$msg['id'], $_SESSION['user']['id']]);
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                align-items: flex-start;
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
                border-radius: 6px;
                background: #f1f1f1;
            }
            
            .chat-image {
                max-width: 100%;
            }
            
            .chat-image img {
                display: block;
                width: 100%;
                height: auto;
                border-radius: 6px;
            }
            
            .date {
                font-size: 12px;
                color: #888;
            }
            
            .msg.me .msg-body {
                justify-content: flex-end;
            }
            
            .msg.me .bubble {
                margin-left: 8px;
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
            
            @media (max-width: 600px) { 
                .header-bar {
                    margin: 10px; 
                }
                .header-bar h1 { 
                    font-size: 18px; 
                }
                .back-button { 
                    right: 10px; 
                    font-size: 14px; 
                }
                .chat-thread { 
                    padding: 10px; 
                    padding-bottom: 120px;
                }
                .bubble { 
                    max-width: 80%;
                    font-size: 14px;
                    padding: 10px; 
                    box-sizing: border-box;
                } 
                .icon, .default-icon { 
                    width: 32px; 
                    height: 32px; 
                }
                .nickname { 
                    font-size: 12px;
                }
                .date { 
                    font-size: 10px;
                }
                .chat-image img {
                    max-width: 70vw; 
                    height: auto; 
                }
                .form-grid {
                    padding: 10px; 
                }
                .form-grid form { 
                    gap: 6px; 
                }
                .form-grid textarea { 
                    height: 50px; 
                    font-size: 14px; 
                    padding: 10px; 
                    box-sizing: border-box; 
                }
                .plus-button { 
                    width: 28px;
                    height: 28px; 
                    font-size: 20px; 
                }
                .form-grid button {
                    height: 40px;
                    padding: 0 14px; 
                    font-size: 14px; 
                } 
                #preview {
                    max-width: 80px; 
                    margin-left: 10px;
                }
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
                            <?php if (!empty($msg['message'])): ?> 
                                <?= nl2br(htmlspecialchars($msg['message'])) ?>
                            <?php endif; ?>
                            
                            <?php if (!empty($msg['image_path'])): ?>
                                <div class="chat-image">
                                    <img src="<?= htmlspecialchars($msg['image_path']) ?>" style="max-width:200px; margin-top:8px; border-radius:6px;">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="date">
                        <span class="time">
                            <?= date('Y-m-d H:i', strtotime($msg['registered_time'])) ?>
                        </span>
                        <?php if ($msg['user_id'] == $_SESSION['user']['id']): ?>
                            <?php if ($msg['read_count'] > 0): ?>
                                <span class="read-flag">既読 <?= $msg['read_count'] ?></span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="error-message" style="color:red; margin:10px;">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <div class="form-grid">
            <form method="post" action="chat-post.php" enctype="multipart/form-data">
                <input type="hidden" name="event_id" value="<?= htmlspecialchars($event_id) ?>">
                <textarea name="message" placeholder="メッセージを入力"></textarea>
                <input type="file" id="image" name="image" accept="image/*" style="display:none;">
                <label for="image" class="plus-button">＋</label>
                <button type="submit">送信</button>
            </form>
            
            <img id="preview" style="max-width:100px; display:none; margin-top: 10px;">
        </div>
        
        <script>
            document.getElementById("image").addEventListener("change", previewImage);
            function previewImage(event) {
                const file = event.target.files[0];
                
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('preview');
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                }
            }
            document.querySelector(".form-grid form").addEventListener("submit", () => {
                const preview = document.getElementById("preview");
                preview.style.display = "none";
                preview.src = "";
            });
            
            document.addEventListener("DOMContentLoaded", function() {
                const chatThread = document.querySelector(".chat-thread");
                chatThread.scrollTop = chatThread.scrollHeight;
            });
        </script>
    </body>
</html>