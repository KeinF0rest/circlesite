<?php
session_start();
$title = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>ブログ新規登録確認</title>
        <link rel="stylesheet" href="style.css">
        <script src="menu.js" defer></script>
        <style>
            body {
                font-family: sans-serif;
                margin: 0;
            }

            .header-bar {
                display: flex;
                justify-content: flex-start;
                align-items: center;
                margin: 20px;
            }

            .header-bar h1 {
                font-size: 24px;
                margin: 0;
            }

            .confirm-row {
                margin: 20px;
            }
            
            .confirm-row .value {
                font-size: 20px;
            }
            
            .confirm-row-content {
                margin: 20px;
            }

            .confirm-row-content .value {
                border: 1px solid #ccc;
                border-radius: 6px;
                padding: 16px;
                background-color: #f9f9f9;
                font-size: 16px;
                line-height: 1.6;
                white-space: pre-wrap;
            }

            .submit-area {
                display: flex;
                justify-content: flex-end;
                gap: 20px;
                margin: 20px;
            }

            .submit-area form {
                display: inline;
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
            <h1>ブログ新規登録確認</h1>
        </div>
        
        <div class="confirm-row">
            <div class="value"><?= htmlspecialchars($title) ?></div>
        </div>
        
        <div class="confirm-row-content">
            <div class="value"><?= htmlspecialchars($content) ?></div>
        </div>
        
        <div class="submit-area">
            <form action="blog-regist.php" method="post">
                <input type="hidden" name="title" value="<?= htmlspecialchars($title) ?>">
                <input type="hidden" name="content" value="<?= htmlspecialchars($content) ?>">
                <button type="submit" class="back-button">戻る</button>
            </form>
            
            <form action="blog-regist-complete.php" method="post">
                <input type="hidden" name="title" value="<?= htmlspecialchars($title) ?>">
                <input type="hidden" name="content" value="<?= htmlspecialchars($content) ?>">
                <button type="submit" class="submit-button">登録する</button>
            </form>
        </div>
    </body>
</html>