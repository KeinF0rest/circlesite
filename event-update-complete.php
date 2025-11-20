<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");

$title = $_POST['title'] ?? '';
$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? '';
$content = $_POST['content'] ?? '';
$event_id = $_POST['id'] ?? null;
$new_images = $_POST['image_path'] ?? [];

try {
    if ($event_id) {
        $stmt_old = $pdo->prepare("SELECT title, start_date, end_date, content FROM event WHERE id = ?");
        $stmt_old->execute([$event_id]);
        $old = $stmt_old->fetch(PDO::FETCH_ASSOC);
        
        $stmt_old_img = $pdo->prepare("SELECT image_path FROM event_images WHERE event_id = ?");
        $stmt_old_img->execute([$event_id]);
        $old_images = $stmt_old_img->fetchAll(PDO::FETCH_COLUMN);
        
        $stmt = $pdo->prepare("UPDATE event SET title = ?, start_date = ?, end_date = ?, content = ? WHERE id = ?");
        $stmt->execute([$title, $start_date, $end_date, $content, $event_id]);
        
        $pdo->prepare("DELETE FROM event_images WHERE event_id = ?")->execute([$event_id]);
        if (!empty($new_images)) {
            if (count($new_images) > 5) {
                echo "<p style='color:red;'>画像は最大5枚まで登録できます。</p>";
                exit;
            }
            $stmt_img = $pdo->prepare("INSERT INTO event_images(event_id, image_path) VALUES (?, ?)");
            foreach ($new_images as $path) {
                $stmt_img->execute([$event_id, $path]);
            }
        }
        
        $changes = [];
        if ($old['title'] !== $title) {
            $changes[] = "タイトル";
        }
        if ($old['start_date'] !== $start_date) {
            $changes[] = "開始日";
        }
        if ($old['end_date'] !== $end_date) {
            $changes[] = "終了日";
        }
        if ($old['content'] !== $content) {
            $changes[] = "内容";
        }
        if ($old_images !== $new_images) {
            $changes[] = "写真";
        }
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo"<p style='color:red; font-weight:bold;'>エラーが発生したためイベント更新ができませんでした。</p>";
    exit;
}

?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>イベント更新完了</title>
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
            
            <a href="event-info.php?id=<?= htmlspecialchars($event_id) ?>" class="back-link">イベントへ</a>
        </div>
    </body>
</html>