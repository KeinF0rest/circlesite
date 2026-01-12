<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

try {
    $pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "SELECT id, title, registered_time, start_date, start_time, end_date, end_time FROM event WHERE delete_flag = 0 ORDER BY registered_time DESC";
    $stmt = $pdo->query($sql);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log($e->getMessage());
    echo "<p style='color:red; font-weight:bold;'>エラーが発生したためイベント一覧画面が閲覧できません。</p>";
    echo "<p><a href='index.php' style='display:inline-block; padding:10px 20px; background:#4CAF50; color:#fff; text-decoration:none; border-radius:6px;'>トップ画面に戻る</a></p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>イベント</title>
        <link rel="stylesheet" href="style.css">
        <script src="menu.js" defer></script>
        <style>
            body {
                font-family: sans-serif;
                margin: 0;
            }
            
            .header-bar {
                margin: 20px;
                display: flex;
                justify-content: space-between;
            }
            
            .header-bar h1 {
                margin: 0;
                font-size: 24px;
            }
            
            .regist-button {
                font-size: 24px;
                padding: 6px 12px;
                color: #333;
                text-decoration: none;
                transition: background-color 0.3s ease;
                line-height: 1;
            }
            
            .event-grid {
                display: grid;
                grid-template-columns: 1fr;
                gap: 20px;
                padding: 20px;
            }
            
            .fc-event-time {
                display: none !important;
            }
            
            .fc-daygrid-event-dot {
                display: none !important;
            }
            
            .fc-daygrid-event:hover {
                background-color: #4CAF50 !important;
                color: #fff !important; 
            }
            
            .event-card {
                background-color: #f9f9f9;
                border: 1px solid #ccc;
                border-radius: 12px;
                padding: 16px;
                display: block;
                text-decoration: none;
                color: inherit;
                transition: box-shadow 0.2s ease;
            }
            
            .event-card.ended {
                background-color: #999;
            }
            
            .event-card:hover {
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                transform: scale(1.02);
                transition: all 0.2s ease;
            }
            
            .event-title {
                font-size: 18px;
                font-weight: bold;
                margin-bottom: 10px;
            }
            
            .event-date {
                font-size: 14px;
                color: #666;
                justify-self: end;
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
                .regist-button { 
                    font-size: 20px; 
                    padding: 4px 10px;
                }
                .event-grid {
                    padding: 10px; 
                    gap: 12px;
                }
                .event-card { 
                    padding: 14px;
                }
                .event-title { 
                    font-size: 16px; 
                    margin-bottom: 6px; 
                }
                .event-date { 
                    font-size: 12px;
                }
            }
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="error-message" style="color:red; margin:10px;">
                <?= htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <div class="header-bar">
            <h1>イベント一覧</h1>
            <?php if ($_SESSION['user']['authority'] != 0): ?>
                <a href="event-regist.php" class="regist-button">＋</a>
            <?php endif; ?>
        </div>
        
        <div class="event-grid">
            <?php foreach ($events as $event): ?>
                <?php
                $now = new DateTime();
            
                $startDate = $event['start_date'];
                $startTime = $event['start_time'];
                $startDateTime = new DateTime("$startDate $startTime");

                $endDate = !empty($event['end_date']) ? $event['end_date'] : null;
                $endTime = !empty($event['end_time']) ? $event['end_time'] : null;
                $endDateTime = null;
            
                if ($endDate && $endTime) {
                    $endDateTime = new DateTime("$endDate $endTime");
                } elseif ($endDate && !$endTime) {
                    $endDateTime = new DateTime("$endDate 23:59:59");
                } else {
                    $endDateTime = new DateTime("$startDate 23:59:59");
                }
            
                $isOngoing = ($now >= $startDateTime && $now <= $endDateTime);
                $isEnded = ($now > $endDateTime);
            
                $cardClass = $isEnded ? "event-card ended" : "event-card";
                ?>
                <a href="event-info.php?id=<?= htmlspecialchars($event['id']) ?>" class="<?= $cardClass ?>">
                    <div class="event-title"><?= htmlspecialchars($event['title']) ?></div>
                    <div class="event-date">登録日：<?= htmlspecialchars(date('Y/m/d', strtotime($event['registered_time']))) ?></div>
                </a>
            <?php endforeach; ?>
        </div>
    </body>
</html>