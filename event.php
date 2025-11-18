<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
$sql = "SELECT id, title, registered_time, start_date, end_date FROM event WHERE delete_flag = 0 ORDER BY registered_time DESC";
$stmt = $pdo->query($sql);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
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
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <div class="header-bar">
            <h1>イベント一覧</h1>
            <a href="event-regist.php" class="regist-button">＋</a>
        </div>
        
        <div class="event-grid">
            <?php foreach ($events as $event): ?>
                <?php
                $today = date('Y-m-d');

                $start = !empty($event['start_date']) ? date('Y-m-d', strtotime($event['start_date'])) : null;
                $end = !empty($event['end_date']) ? date('Y-m-d', strtotime($event['end_date'])) : null;
            
                $isEnded = $end !== null && $end < $today;
            
                $isOngoing = $start !== null && $start <= $today && ($end === null || $end >= $today);
            
                if ($start === $today) {
                    $isOngoing = true;
                    $isEnded = false;
                }
            
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