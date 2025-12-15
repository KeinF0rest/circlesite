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

$id = $_GET['id'] ?? null;

try {
    $pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("SELECT * FROM event WHERE id = ? AND delete_flag = 0");
    $stmt->execute([$id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        $_SESSION['error'] = "指定されたイベントは存在しません。";
        header("Location: event.php");
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
        $delete_id = $_POST['id'] ?? null;
        if ($delete_id) {
            $stmt = $pdo->prepare("UPDATE event SET delete_flag = 1 WHERE id = ?");
            $stmt->execute([$delete_id]);
            
            $stmt_img = $pdo->prepare("DELETE FROM event_images WHERE event_id = ?");
            $stmt_img->execute([$delete_id]);
            
            $stmt_title = $pdo->prepare("SELECT title FROM event WHERE id = ?");
            $stmt_title->execute([$delete_id]);
            $event_title = $stmt_title->fetchColumn();
            
            $stmt_users = $pdo->query("SELECT id FROM users");
            $users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);
            
            $stmt_notify = $pdo->prepare("INSERT INTO notification (type, action, related_id, message, user_id, n_read) VALUES ('event', 'delete', ?, ?, ?, 0)");
            
            foreach ($users as $u) {
                $stmt_notify->execute([
                    $delete_id,
                    "イベント「{$event_title}」が削除されました。",
                    $u['id']
                ]);
            }
            
            header("Location: event-delete-complete.php");
            exit;
        } 
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    $safeId = htmlspecialchars((string)($_POST['id'] ?? ''), ENT_QUOTES, 'UTF-8');
    echo "<p style='color:red; font-weight:bold;'>エラーが発生したためイベント削除ができませんでした。</p>";
    echo "<p><a href='event-info.php?id=" . $safeId . "' style='display:inline-block; padding:10px 20px; background:#4CAF50; color:#fff; text-decoration:none; border-radius:6px;'>イベント情報画面に戻る</a></p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>イベント削除</title>
        <link rel="stylesheet" href="style.css">
        <script src="menu.js" defer></script>
        <style>
            body {
                font-family: sans-serif;    
                margin: 0;
            }
            
            .header-bar {
                position: relative;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .header-bar h1 {
                font-size: 24px;
                margin: 0;
                text-align: center;
            }
            
            .back-button {
                position: absolute;
                right: 20px;
                font-size: 16px;
                color: #4CAF50;
                text-decoration: none;
            }
            
            h2 {
                text-align: center;
                margin-bottom: 20px;
            }
            
            .form-grid {
                margin: 20px;
            }
            
            .form-row {
                display: flex;
                flex-direction: column;
                margin-bottom: 20px;
            }

            .form-row label {
                font-weight: bold;
                margin-bottom: 6px;
                font-size: 15px;
            }
            .form-row div {
                padding: 10px;
                background-color: #fff;
                border: 1px solid #ccc;
                border-radius: 6px;
                font-size: 15px;
                line-height: 1.6;
                white-space: pre-wrap;
            }
            
            .submit-area {
                display: flex;
                justify-content: flex-end;
                margin: 20px;
            }
            
            .submit-button {
                padding: 10px 20px;
                font-size: 16px;
                background-color: #4CAF50;
                color: white;
                border: none;
                border-radius: 6px;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <div class="header-bar">
            <h1>イベントを削除しますか？</h1>
            <a href="event.php" class="back-button">戻る</a>
        </div>
        
        <h2>削除すると復元することはできません。</h2>
        
        <div class="form-grid">
            <div class="form-row">
                <label>タイトル</label>
                <div><?= htmlspecialchars($event['title']) ?></div>
            </div>
        
            <div class="event-dates">
                <?php if (!empty($event['start_date'])): ?>
                    <p><strong>開始日：</strong><?= htmlspecialchars($event['start_date']) ?></p>
                <?php endif; ?>
                <?php if (!empty($event['end_date'])): ?>
                    <p><strong>終了日：</strong><?= htmlspecialchars($event['end_date']) ?></p>
                <?php endif; ?>
            </div>
        
            <div class="form-row">
                <label>内容</label>
                <div><?= nl2br(htmlspecialchars($event['content'])) ?></div>
            </div>
        
            <div class="form-row">
                <label>写真</label>
                <?php
                $stmt_img = $pdo->prepare("SELECT image_path FROM event_images WHERE event_id = ?");
                $stmt_img->execute([$event['id']]);
                $image = $stmt_img->fetch(PDO::FETCH_ASSOC);
                if (!empty($image['image_path'])):
                ?>
                <img src="<?= htmlspecialchars($image['image_path']) ?>" alt="イベント画像">
                <?php else: ?>
                    <div>画像は登録されていません。</div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="submit-area">
            <form method="post">
                <input type="hidden" name="id" value="<?= htmlspecialchars($event['id']) ?>">
                <button type="submit" name="delete" class="submit-button">削除</button>
            </form>
        </div>
        
    </body>
</html>