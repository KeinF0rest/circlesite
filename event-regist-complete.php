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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: event.php");
    exit();
}

$data = $_SESSION['regist'];

try{
    $pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "INSERT INTO event (title, start_date, start_time, end_date, end_time, content) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $data['title'],
        $data['start_date'],
        $data['start_time'],
        !empty($data['end_date']) ? $data['end_date'] : null,
        !empty($data['end_time']) ? $data['end_time'] : null,
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
        
        $upload_dir = __DIR__ . 'uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
    
        $imagePaths = [];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024;
        
        foreach ($data['image_paths'] as $path) {
            $filename = basename($path);
            $temp_path = __DIR__ . '/' . $path;
            $new_path = __DIR__ . '/uploads/' . $filename;
            $web_path = 'uploads/' . $filename;
            
             if (file_exists($temp_path)) {
                rename($temp_path, $new_path);
            }
            $stmt_img->execute([$event_id, $web_path]);
        }
    }
    unset($_SESSION['event']);
} catch (Exception $e) {
    error_log($e->getMessage());
    echo "<p style='color:red; font-weight:bold;'>エラーが発生したためイベント登録できませんでした。</p>";
    echo "<p><a href='event-regist.php' style='display:inline-block; padding:10px 20px; background:#4CAF50; color:#fff; text-decoration:none; border-radius:6px;'>イベント登録画面に戻る</a></p>";
    exit;
}
unset($_SESSION['regist']);
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                cursor: pointer;
            }
            
            @media (max-width: 600px) {
                .header-bar { 
                    margin: 20px 10px; 
                }
                .header-bar h1 {
                    font-size: 20px; 
                    margin-bottom: 14px;
                }
                .back-link { 
                    padding: 14px; 
                    font-size: 14px; 
                    box-sizing: border-box; 
                    text-align: center;
                }
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