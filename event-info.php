<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$event_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user']['id'];

try {
    $pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("SELECT * FROM event WHERE id = ? AND delete_flag = 0");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$event) {
        $_SESSION['error'] = "指定されたイベントは存在しません。";
        header("Location: event.php");
        exit;
    }
    
    $stmt_img = $pdo->prepare("SELECT image_path FROM event_images WHERE event_id = ?");
    $stmt_img->execute([$event['id']]);
    $images = $stmt_img->fetchAll(PDO::FETCH_ASSOC);

    $stmt_count = $pdo->prepare("SELECT COUNT(*) FROM event_participant ep JOIN users u ON ep.user_id = u.id WHERE ep.event_id = ? AND u.delete_flag = 0");
    $stmt_count->execute([$event_id]);
    $participant_count = $stmt_count->fetchColumn();
    
    $stmt_participants = $pdo->prepare("SELECT u.nickname FROM event_participant ep JOIN users u ON ep.user_id = u.id WHERE ep.event_id = ? AND u.delete_flag = 0");
    $stmt_participants->execute([$event_id]);
    $participants = $stmt_participants->fetchAll(PDO::FETCH_ASSOC);

    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM event_participant WHERE event_id = ? AND user_id = ?");
    $stmt_check->execute([$event_id, $user_id]);
    $already_joined = $stmt_check->fetchColumn() > 0;
} catch (Exception $e) {
    error_log($e->getMessage());
    echo "<p style='color:red; font-weight:bold;'>エラーが発生したためイベント情報が取得できませんでした。</p>";
    echo "<p><a href='event.php' style='display:inline-block; padding:10px 20px; background:#4CAF50; color:#fff; text-decoration:none; border-radius:6px;'>イベントに戻る</a></p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>イベント情報</title>
        <link rel="stylesheet" href="style.css">
        <script src="menu.js" defer></script>
        <style>
            body {
                font-family: sans-serif;
                margin: 0;
            }
            
            .header-bar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin: 20px;
            }

            .header-bar h1 {
                font-size: 24px;
                margin: 0;
            }
            
            .back-button{
                text-decoration: none;
                font-size: 16px;
                color: #4CAF50;
            }
            
            .menu-wrapper {
                position: relative;
                display: flex;
                justify-content: flex-end;
                margin-right: 10px;
            }
            
            .menu-icon {
                font-size: 24px;
                cursor: pointer;
                padding: 6px 10px;
                transition: background-color 0.2s ease;
            }
            
            .menu-popup {
                display: none;
                position: absolute;
                top: 40px;
                right: 0;
                background-color: #fff;
                border: 1px solid #ccc;
                border-radius: 6px;
                box-shadow: 0 2px 6px rgba(0,0,0,0.1);
                z-index: 100;
            }
            
            .menu-popup.visible {
                display: block;
            }
            
            .menu-popup ul {
                list-style: none;
                margin: 0;
                padding: 0;
            }
            
            .menu-popup li {
                margin: 8px 0;
            }
            
            .menu-popup a {
                text-decoration: none;
                color: #333;
                font-size: 14px;
                padding: 4px 8px;
                display: block;
                border-radius: 4px;
            }
            
            .title-bar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-top: 20px;
            }
            
            .event-title {
                font-size: 24px;
                margin: 0;
            }
            
            .menu-icon {
                font-size: 24px;
                cursor: pointer;
            }
            
            .event-container {
                margin: 20px;
            }
            
            .event-image {
                margin: 20px 0;
                text-align: center;
                border: 1px solid #ccc;
                padding: 10px;
                border-radius: 6px;
                background-color: #fff;
            }
            
            .event-image img {
                width: 100%;
                height: auto;
                display: block;
                border-radius: 12px;
            }
            
            .event-image.no-border {
                border: none;
                padding: 0;
                background-color: transparent;
            }
            
            .event-image p {
                color: #999;
                font-size: 16px;
            }
            
            .event-content {
                border: 1px solid #ccc;
                padding-left: 10px;
                border-radius: 6px;
                background-color: #fff;
                margin-top: 20px;
            }
            
            .event-actions {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-top: 10px;
                padding-top: 10px;
            }
            
            .event-actions button {
                margin-right: 10px;
                padding: 6px 12px;
                font-size: 16px;
                background-color: #4CAF50;
                color: white;
                border: none;
                border-radius: 6px;
                cursor: pointer;
            }
            
            .action-buttons button:last-child {
                background-color: #f44336;
            }

            .action-buttons button:hover {
                opacity: 0.85;
            }
            
            .event-footer {
                display: flex;
                justify-content: space-between; 
                align-items: center;
            }
            
            .event-participant {
                font-weight: bold;
            }
            
            .chat-button {
                display: inline-block;
                padding: 6px 12px;
                border-radius: 6px;
            }
            
            .chat-icon {
                width: 35px;
                height: 35px;
                vertical-align: middle;
            }
            
            .participant-list {
                margin-top: 5px;
                padding: 10px;
                background: #f9f9f9;
                border: 1px solid #ddd;
                border-radius: 6px;
            }
            
            .participant-list p {
                margin: 0 0 5px;
                font-weight: bold;
                color: #333;
            }
            
            .participant-list ul {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                list-style: none;
                padding: 0;
                margin: 0;
            }
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <div class="header-bar">
            <h1><?= htmlspecialchars($event['title']) ?></h1>
            <a href="event.php" class="back-button">戻る</a>
        </div>
        
        <?php if ($_SESSION['user']['authority'] != 0): ?>
            <div class="menu-wrapper">
                <div class="menu-icon" onclick="toggleMenu()">⋯</div>
                <div class="menu-popup" id="menu-popup">
                    <ul>
                        <li><a href="event-update.php?id=<?= $event['id'] ?>">更新</a></li>
                        <li><a href="event-delete.php?id=<?= $event['id'] ?>">削除</a></li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="event-container">
            <div class="event-image <?= !empty($images) ? 'no-border' : '' ?>">
                <?php if (!empty($images)):
                    foreach ($images as $img): 
                ?>
                        <img src="<?= htmlspecialchars($img['image_path']) ?>" alt="イベント画像">
                    <?php 
                        endforeach;
                    else: 
                    ?>
                    <p>画像は登録されていません。</p>
                <?php endif; ?>
            </div>
            
            <div class="event-dates">
                <?php if (!empty($event['start_date'])): ?>
                    <p><strong>開始日：</strong><?= htmlspecialchars($event['start_date']) ?></p>
                <?php endif; ?>
                <?php if (!empty($event['end_date'])): ?>
                    <p><strong>終了日：</strong><?= htmlspecialchars($event['end_date']) ?></p>
                <?php endif; ?>
            </div>
            
            <div class="event-content">
                <p><?= nl2br(htmlspecialchars($event['content'])) ?></p>
            </div>
            
            <div class="event-actions">
                <div class="action-button">
                    <button id="participate-btn" <?= $already_joined ? 'style="display:none;"' : '' ?>>参加</button>
                    <button id="cancel-btn" <?= $already_joined ? '' : 'style="display:none;"' ?>>不参加</button>
                </div>

                <div class="event-registered-date">
                    登録日：<?= htmlspecialchars(date('Y/m/d', strtotime($event['registered_time']))) ?>
                </div> 
            </div>
            
            <div class="event-footer">
                <div class="event-participant">
                    参加人数：<span id="participant-count"><?= htmlspecialchars($participant_count) ?></span>人
                </div>
                
                <div class="event-chat">
                    <a href="chat.php?event_id=<?= htmlspecialchars($event['id']) ?>" class="chat-button">
                        <img src="chat_17343583.png" alt="チャット" class="chat-icon">
                    </a>
                </div>
            </div>
            
            <?php if ($_SESSION['user']['authority'] != 0): ?>
                    <div class="participant-list">
                        <?php if (!empty($participants)): ?>
                            <p><strong>参加者</strong></p>
                            <ul>
                                <?php foreach ($participants as $p): ?>
                                    <li><?= htmlspecialchars($p['nickname']) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
        </div>
        <script>
            function toggleMenu(){
                const menu=document.getElementById('menu-popup');
                menu.style.display=(menu.style.display === 'block') ? 'none' : 'block';
            }
            
            document.addEventListener('click', function(e) {
                const menu=document.getElementById('menu-popup');
                const icon=document.querySelector('.menu-icon');
                if (!menu.contains(e.target) && !icon.contains(e.target)){
                    menu.style.display = 'none';
                }
            });
            
            const eventId = <?= json_encode($event['id']) ?>;
            const userId = <?= json_encode($_SESSION['user']['id']) ?>;

            function sendParticipation(status) {
                fetch('event-participation.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ event_id: eventId, status: status, user_id: userId })
                })
                .then(res => res.json())
                .then(data => {
                    document.getElementById('participant-count').textContent = data.count;
                    if (status === '参加') {
                        document.getElementById('participate-btn').style.display = 'none';
                        document.getElementById('cancel-btn').style.display = 'inline-block';
                    } else {
                        document.getElementById('participate-btn').style.display = 'inline-block';
                        document.getElementById('cancel-btn').style.display = 'none';
                    }
                });
            }

            document.getElementById('participate-btn').addEventListener('click', () => sendParticipation('参加'));
            document.getElementById('cancel-btn').addEventListener('click', () => sendParticipation('不参加'));
        </script>     
    </body>
</html>