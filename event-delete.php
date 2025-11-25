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

$event_id = $_GET['id'] ?? null;

$pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $delete_id = $_POST['id'] ?? null;
    if ($delete_id) {
        try {
            $stmt = $pdo->prepare("UPDATE event SET delete_flag = 1 WHERE id = ?");
            $stmt->execute([$delete_id]);
            
            $stmt_img = $pdo->prepare("DELETE FROM event_images WHERE event_id = ?");
            $stmt_img->execute([$delete_id]);
            
            header("Location: event-delete-complete.php");
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            echo"<p style='color:red; font-weight:bold;'>エラーが発生したためイベント削除ができません。</p>";
            exit;
        }
    }
}
$stmt = $pdo->prepare("SELECT * FROM event WHERE id = ? AND delete_flag = 0");
$stmt->execute([$event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    echo "該当するイベントが見つかりません。";
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
                <button type="submit" name="delete" class="submit-button">削除</button>
            </form>
        </div>
        
    </body>
</html>