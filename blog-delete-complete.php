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

if (empty($_SESSION['delete_complete'])) {
    header("Location: blog.php");
    exit();
}
unset($_SESSION['delete_complete']);
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ブログ削除完了</title>
        <link rel="stylesheet" href="style.css">
        <script src="menu.js" defer></script>
        <style>
            body {
                font-family: sans-serif;
                margin: 0;
            }
            
            .header-bar {
                text-align: center;
                margin-bottom: 20px;
            }
            
            .header-bar h1 {
                font-size: 24px;
                margin-bottom: 20px;
            }
            
            .back-link {
                display: inline-block;
                padding: 10px 20px;
                font-size: 16px;
                background-color: #4CAF50;
                color: white;
                text-decoration: none;
                border-radius: 6px;
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
            <h1>ブログが削除されました。</h1>
            <a href="blog.php" class="back-link">ブログへ</a>
        </div>
    </body>
</html>