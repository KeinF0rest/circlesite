<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>ブログ新規登録</title>
        <link rel="stylesheet" href="style.css">
        <script src="menu.js" defer></script>
        <style>
            body{
                font-family: sans-serif;
                margin: 0;
            }
            .header-bar{
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin: 20px;
            }
            .header-bar h1{
                margin: 0;
                font-size: 24px;
            }
            .back-button{
                font-size: 16px;
                text-decoration: none;
                color: #4CAF50;
                position: absolute;
                right: 40px; 
            }
            form {
                padding: 0 20px;
            }
            .form-row{
                margin-bottom: 20px;
            }
            label{
                display: block;
                font-weight: bold;
                margin-bottom: 5px;
            }
            input[type="text"], textarea{
                width: 100%;
                padding: 10px;
                font-size: 16px;
                border-radius: 6px;
                border: 1px solid #ccc;
                box-sizing: border-box;
            }
            textarea{
                height: 200px;
                resize: vertical;
            }
            .submit-area{
                display: flex;
                justify-content: flex-end;
            }
            .submit-area button{
                padding: 10px 20px;
                font-size: 16px;
                border: none;
                border-radius: 6px;
                background-color: #4CAF50;
                color: white;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <div class="header-bar">
            <h1>ブログ新規登録</h1>
            <a href="index.php" class="back-button">戻る</a>
        </div>
        
        <form action="blog-regist-confirm.php" method="post">
            <div class="form-row">
                <label>タイトル</label>
                <input type="text" name="title" maxlength="30" pattern="[ぁ-んァ-ヶ一-龠A-Za-z0-9ー\s]+" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
            </div>
            
            <div class="form-row">
                <label>内容</label>
                <textarea name="content" maxlength="500" required><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea> 
            </div>
            
            <div class="submit-area">
                <button type="submit">確認する</button>
            </div>
        </form>
    </body>
</html>