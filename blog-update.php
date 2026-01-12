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

$id = $_GET['id'] ?? null;

$pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->prepare("SELECT * FROM blog WHERE id = ? AND delete_flag = 0");
$stmt->execute([$id]);
$blog = $stmt->fetch();

if (!$blog) {
    $_SESSION['error'] = "指定されたブログは存在しません。";
    header("Location: blog.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ブログ更新</title>
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
                margin: 20px;
                align-items: center;
            }
            
            .header-bar h1 {
                font-size: 24px;
            }
            
            .back-button {
                font-size: 16px;
                color: #4CAF50;
                text-decoration: none;
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
            
            @media (max-width: 600px) { 
                .header-bar {
                    margin: 10px; 
                }
                .header-bar h1 { 
                    font-size: 20px; 
                }
                .back-button { 
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
            <h1>ブログ更新</h1>
            <a href="blog-info.php?id=<?= htmlspecialchars($blog['id']) ?>" class="back-button">戻る</a>
        </div>
        
        <form action="blog-update-complete.php" method="post">
            <input type="hidden" name="id" value="<?= htmlspecialchars($blog['id']) ?>">
            
            <div class="form-row">
                <label>タイトル</label>
                <input type="text" name="title" maxlength="30" pattern="[\u3040-\u309F\u4E00-\u9FAF\u30A0-\u30FF0-9!-/:-@¥[-`{-~　\s]+" value="<?= htmlspecialchars($blog['title']) ?>" required>
            </div>
            
            <div class="form-row">
                <label>内容</label>
                <textarea name="content" maxlength="500" required oninput="updateCount(this)"><?= htmlspecialchars($blog['content']) ?></textarea>
                <div id="char-count" style="text-align:right; font-size:14px; color:#666;">0/500</div>
            </div>
            
            <div class="submit-area">
                <button type="submit" class="submit-button">更新</button>
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