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

$image_paths = $_SESSION['event']['image_paths'] ?? [];

$_SESSION['event'] = [
    'title' => $_POST['title'],
    'start_date' => $_POST['start_date'],
    'end_date' => $_POST['end_date'],
    'content' => $_POST['content'],
    'image_paths' => $image_paths
];

if(!empty($_FILES['image_path']['name'][0])) {
    $_SESSION['event']['image_paths'] = [];
    
    foreach ($_FILES['image_path']['name'] as $index => $name) {
        $tmp_name = $_FILES['image_path']['tmp_name'][$index];
        $error = $_FILES['image_path']['error'][$index];
        
        if ($error === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array(mime_content_type($tmp_name), $allowed_types)) {
                $filename = uniqid('', true) . '_' . basename($name);
                $filepath = 'temp/' . $filename;
            
                move_uploaded_file($tmp_name, $filepath);
                $_SESSION['event']['image_paths'][] = $filepath;
            }  
        }
    }
} elseif (!empty($_SESSION['event']['image_paths'])) {
} else {
    $_SESSION['event']['image_paths'] = [];
}

?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>イベント登録確認</title>
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
            
            .confirm-grid {
                margin: 20px;
                background-color: #fff;
                border-radius: 12px;
                display: grid;
                gap: 20px;
            }
            
            .confirm-row {
                display: flex;
                flex-direction: row;
                align-items: flex-start;
                gap: 20px;
            }

            .confirm-row label {
                font-weight: bold;
                font-size: 16px;
                width: 100px;
                flex-shrink: 0;
            }

            .confirm-row .value {
                font-size: 16px;
                line-height: 1.6;
                word-break: break-word;
                flex: 1;
            }

            .confirm-row img {
                max-width: 300px;
                border-radius: 6px;
            }
            
            .confirm-row.content,
            .confirm-row.image {
                flex-direction: column;
                border: 1px solid #ccc;
                border-radius: 6px;
                background-color: #f9f9f9;
                padding: 20px;
            }
            
            label {
                font-size: 16px;
                font-weight: bold;
            }

            .submit-area {
                display: flex;
                justify-content: flex-end;
                margin: 20px;
                gap: 10px;
            }

            .submit-area button {
                padding: 10px 20px;
                font-size: 16px;
                border: none;
                border-radius: 6px;
                cursor: pointer;
            }

            .back-button {
                background-color: #ccc;
                color: #333;
            }

            .submit-button {
                background-color: #4CAF50;
                color: white;
            }
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <div class="header-bar">
            <h1>イベント内容の確認</h1>
        </div>
        
        <div class="confirm-grid">
            <div class="confirm-row">
                <label>タイトル</label>
                <div class="value"><?= htmlspecialchars($_SESSION['event']['title']) ?></div>
            </div>
            
            <div class="confirm-row">
                <label>開始日</label>
                <div class="value"><?= htmlspecialchars($_SESSION['event']['start_date']) ?></div>
            </div>
            
            <div class="confirm-row">
                <label>終了日</label>
                <div class="value"><?= htmlspecialchars($_SESSION['event']['end_date']) ?></div>
            </div>
            
            <label>内容</label>
            <div class="confirm-row content">
                <div class="value"><?= nl2br(htmlspecialchars($_SESSION['event']['content'])) ?></div>
            </div>
            
            <label>写真</label>
            <div class="confirm-row image">
                <?php if(!empty($_SESSION['event']['image_paths'])): ?>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <?php foreach ($_SESSION['event']['image_paths'] as $path): ?>
                            <img src="<?= htmlspecialchars($path) ?>" width="150">
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="value">写真の登録なし</div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="submit-area">
            <form action="event-regist.php" method="POST">
                <button type="submit" class="back-button">戻る</button>
            </form>
        
            <form action="event-regist-complete.php" method="POST">
                <button type="submit" class="submit-button">登録する</button>
            </form>
        </div>
    </body>
</html>