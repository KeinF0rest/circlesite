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

$data = $_SESSION['regist'] ?? [];
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ブログ新規登録</title>
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
                margin: 0;
                font-size: 24px;
            }
            
            .back-button {
                font-size: 16px;
                text-decoration: none;
                color: #4CAF50;
                position: absolute;
                right: 40px; 
            }
            
            .form-row {
                display: flex;
                flex-direction: column;
                gap: 5px;
                margin: 20px;
            }
            
            label {
                display: block;
                font-weight: bold;
                margin-bottom: 5px;
            }
            
            input[type="text"], textarea {
                width: 100%;
                padding: 10px;
                font-size: 16px;
                border-radius: 6px;
                border: 1px solid #ccc;
                box-sizing: border-box;
            }
            
            textarea {
                height: 200px;
                resize: vertical;
            }
            
            .submit-area {
                display: flex;
                justify-content: flex-end;
                margin-top: 20px;
                margin-right: 20px;
            }
            
            .submit-button {
                padding: 10px 20px;
                font-size: 16px;
                border: none;
                border-radius: 6px;
                background-color: #4CAF50;
                color: white;
                cursor: pointer;
            }
            
            @media (max-width: 600px) { 
                .header-bar {
                    margin: 10px;
                }
                .header-bar h1 {
                    font-size: 20px;
                }
                .back-button {
                    right: 10px;
                    font-size: 14px;
                }
                .form-row {
                    margin: 10px;
                    gap: 4px; 
                }
                label { 
                    font-size: 14px;
                }
                input[type="text"], textarea { 
                    font-size: 14px;
                    padding: 10px; 
                }
                #char-count { 
                    font-size: 12px; 
                }
                .submit-area { 
                    margin: 10px;  
                }
                .submit-button { 
                    padding: 14px; 
                    font-size: 14px; 
                    box-sizing: border-box;
                }
            }
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <div class="header-bar">
            <h1>ブログ新規登録</h1>
            <a href="blog.php" class="back-button">戻る</a>
        </div>
        
        <form action="blog-regist-confirm.php" method="post">
            <div class="form-row">
                <label>タイトル</label>
                <input type="text" name="title" maxlength="30" pattern="[\u3040-\u309F\u4E00-\u9FAF\u30A0-\u30FF0-9!-/:-@¥[-`{-~　\s]+" value="<?= htmlspecialchars($data['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            
            <div class="form-row">
                <label>内容</label>
                <textarea name="content" maxlength="500" required oninput="updateCount(this)"><?= htmlspecialchars($data['content'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea> 
                <div id="char-count" style="text-align:right; font-size:14px; color:#666;">0/500</div>
            </div>
            
            <div class="submit-area">
                <button type="submit" class="submit-button">確認する</button>
            </div>
        </form>
        
        <script>
            function updateCount(el) {
                const count = el.value.length;
                document.getElementById('char-count').textContent = count + "/500";
            }
            document.addEventListener("DOMContentLoaded", function() {
                const textarea = document.querySelector('textarea[name="content"]');
                updateCount(textarea);
            });
        </script>
    </body>
</html>