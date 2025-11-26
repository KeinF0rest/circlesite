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

$data = $_SESSION['event'];

try{
    $pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("INSERT INTO event (title, start_date, end_date, content) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $data['title'],
        $data['start_date'],
        !empty($data['end_date']) ? $data['end_date'] : null,
        $data['content'],
    ]);
    
    $event_id = $pdo->lastInsertId();
    
    $stmt_users = $pdo->query("SELECT id FROM users");
    $users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt_notify = $pdo->prepare("INSERT INTO notification (type, action, related_id, message, user_id, n_read) VALUES ('event', 'regist', ?, ?, ?, 0)");
    foreach ($users as $u) {
        $stmt_notify->execute([
            $event_id,
            "イベント「{$data['title']}」が登録されました。",
            $u['id']
        ]);
    }
    
    
    if (!empty($data['image_paths'])) {
        if (count($data['image_paths']) > 5) {
            echo "<p style='color:red;'>画像は最大5枚まで登録できます。</p>";
            exit;
        }
        
        $stmt_img = $pdo->prepare("INSERT INTO event_images(event_id, image_path) VALUES (?, ?)");
        
        $upload_dir = 'uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
    
        $imagePaths = [];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024;
        
        foreach ($data['image_paths'] as $path) {
            $filename = basename($path);
            $new_path = $upload_dir . $filename;
            
             if (file_exists($path)) {
                rename($path, $new_path);
            }
            
            $stmt_img->execute([$event_id, $filename]);
        }
    }
    unset($_SESSION['event']);
} catch (Exception $e){
    error_log($e->getMessage());
    echo"<p style='color:red; font-weight:bold;'>エラーが発生したためイベント登録できませんでした。</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>イベント登録完了</title>
        <link rel="stylesheet" href="style.css">
        <script src="menu.js" defer></script>
        <style>
            body {
                font-family: sans-serif;
                margin: 0;
            }
            
            .header-bar {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                margin: 20px;
                text-align: center;
            }

            .header-bar h1 {
                margin-bottom: 20px;
                font-size: 24px;
            }
            
            .back-link {
                display: inline-block;
                padding: 10px 20px;
                font-size: 16px;
                background-color: #4CAF50;
                color: white;
                text-decoration: none;
                border-radius: 6px;
                transition: background-color 0.3s ease;
                cursor: pointer;
            }

            .back-link:hover {
                background-color: #45a049;
            }
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <div class="header-bar">
            <h1>イベントが登録されました。</h1>
            <a href="event.php" class="back-link">イベントへ</a>
        </div>
    </body>
</html>