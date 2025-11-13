<?php
session_start();

$data = $_SESSION['event'];

try{
    $pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    $stmt = $pdo->prepare("INSERT INTO event (title, start_date, end_date, content) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $data['title'],
        $data['start_date'],
        $data['end_date'],
        $data['content'],
    ]);
    
    $event_id = $pdo->lastInsertId();
    
    if (!empty($data['image_paths'])) {
        if (count($data['image_paths']) > 5) {
            echo "<p style='color:red;'>画像は最大5枚まで登録できます。</p>";
            exit;
        }
        
        $stmt_img = $pdo->prepare("INSERT INTO event_images(event_id, image_path) VALUES (?, ?)");
        foreach ($data['image_paths'] as $path) {
            $stmt_img->execute([$event_id, $path]);
        }
    }
} catch(PDOException $e){
    error_log($e->getMessage());
    echo"<p style='color:red; font-weight:bold;'>エラーが発生したためイベント登録できません。</p>";
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
                margin: 40px;
            }
            
            .header-bar {
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                align-items: center;
                margin: 20px;
                text-align: center;
            }

            .header-bar h1 {
                margin: 0;
                font-size: 24px;
                margin: 20px;
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