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

$pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");

$title = $_POST['title'] ?? '';
$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? null;
if ($end_date === '') {
    $end_date = null;
}
$content = $_POST['content'] ?? '';
$event_id = $_POST['id'] ?? null;
$new_images = [];
if (!empty($_FILES['image_path']['tmp_name'][0])) {
    foreach ($_FILES['image_path']['tmp_name'] as $index => $tmpName) {
        if ($_FILES['image_path']['error'][$index] === UPLOAD_ERR_OK) {
            $mime = mime_content_type($tmpName);
            if (strpos($mime, 'image/') !== 0) {
                continue;
            }
            $filename = uniqid() . '_' . basename($_FILES['image_path']['name'][$index]);
            $targetPath = 'uploads/' . $filename;
            if (move_uploaded_file($tmpName, $targetPath)) {
                $new_images[] = $targetPath;
            }
        }
    }
}

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
        
        if (!empty($new_images)) {
            $pdo->prepare("DELETE FROM event_images WHERE event_id = ?")->execute([$event_id]);
            if (count($new_images) > 5) {
                echo "<p style='color:red;'>画像は最大5枚まで登録できます。</p>";
                exit;
            }
            $stmt_img = $pdo->prepare("INSERT INTO event_images(event_id, image_path) VALUES (?, ?)");
            foreach ($new_images as $path) {
                $stmt_img->execute([$event_id, $path]);
            }
            $changes[] = "写真";
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
        if (!empty($new_images)) {
            $changes[] = "写真";
        }
        if (!empty($changes)) {
            $change_text = implode(".", $changes);
            
            $stmt_users = $pdo->query("SELECT id FROM users");
            $users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);
            
            $stmt_notify = $pdo->prepare("INSERT INTO notification (type, action, related_id, message, user_id, n_read) VALUES ('event', 'update', ?, ?, ?, 0)");
            
            foreach ($users as $u) {
                $stmt_notify->execute([
                    $event_id,
                    "イベント「{$title}」が更新されました。（変更箇所: {$change_text}）",
                    $u['id']
                ]);
            }
        }
    }
} catch (Exception $e) {
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