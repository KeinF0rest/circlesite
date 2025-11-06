<?php
session_start();

?>

<!DOCTYPE>
<html lang="ja">
    <head>
        <meta charset="UTF-8"> 
        <title>イベント登録</title>
        <link rel="stylesheet" href="style.css">
        <script src="menu.js" defer></script>
        <style>
            body {
                font-family: sans-serif;
                margin: 0px;
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

            .form-grid {
                display: grid;
                grid-template-columns: 1fr;
                gap: 20px;
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
                border: 1px solid #ccc;
                border-radius: 12px;
                background-color: #f9f9f9;
            }

            .form-row {
                display: flex;
                flex-direction: column;
                gap: 5px;
                margin: 20px;
            }

            .form-row label {
                font-weight: bold;
                font-size: 16px;
            }

            .form-row input, .form-row textarea {
                width: 100%;
                padding: 8px;
                font-size: 16px;
                border-radius: 6px;
                border: 1px solid #ccc;
            }

            .form-row textarea {
                resize: vertical;
            }

            .form-row:last-child {
                justify-content: flex-end;
            }
            
            .form-row input[type="date"] {
                width: 160px;
            }

            .form-row.image-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .image-slot {
                width: 100%;
                height: 100px;
                border: 2px dashed #ccc;
                border-radius: 6px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                position: relative;
            }

            .image-slot:hover {
                border-color: #007bff;
            }

            .plus {
                font-size: 24px;
                color: #888;
            }

            .image-slot input[type="file"] {
                position: absolute;
                inset: 0;
                opacity: 0;
                cursor: pointer;
            }
            #previewArea {
                display: flex;
                gap: 10px;
                overflow-x: auto;
                padding: 20px;
                scroll-snap-type: x mandatory;
            }
            #previewArea img {
                width: 100%;
                object-fit: cover;
                border-radius: 6px;
                scroll-snap-align: center;
                flex-shrink: 0;
            }
            
            .submit-area{
                display: flex;
                justify-content: flex-end;
                margin-top: 20px;
                margin-right: 20px;
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
            <h1>イベント登録</h1>
            <a href="event.php" class="back-button">戻る</a>
        </div>
        
        <form id="event-form" action="event-regist-confirm.php" method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <label>タイトル</label>
                <input type="text" name="title" maxlength="30" pattern="[\u3040-\u309F\u4E00-\u9FAF\u30A0-\u30FF0-9!-/:-@¥[-`{-~　\s]+" value="<?= htmlspecialchars($_SESSION['event']['title'] ?? '') ?>" required>
            </div>
            
            <div class="form-row">
                <label>開始日</label>
                <input type="date" name="start_date" value="<?= htmlspecialchars($_SESSION['event']['start_date'] ?? '') ?>" required>
            </div>
            
            <div class="form-row">
                <label>終了日</label>
                <input type="date" name="end_date" value="<?= htmlspecialchars($_SESSION['event']['end_date'] ?? '') ?>">
            </div>
            
            <div class="form-row">
                <label>内容</label>
                <textarea name="content" maxlength="500" rows="6" pattern="[\u3040-\u309F\u4E00-\u9FAF\u30A0-\u30FF0-9!-/:-@¥[-`{-~　\s]+" required><?= htmlspecialchars($_SESSION['event']['content'] ?? '') ?></textarea>
            </div>
            
            <div class="form-row">
                <label>写真</label>
                <label class="image-slot">
                    <span class="plus">＋</span>
                    <input type="file" name="image_path[]" accept="image/*" multiple id="imageInput">
                </label>
            </div>
            
            <?php if (!empty($_SESSION['event']['image_paths'])): ?>
                <div id="previewArea">
                    <?php foreach ($_SESSION['event']['image_paths'] as $path): ?>
                        <img src="<?= htmlspecialchars($path) ?>" style="width:120px; height:120px; object-fit:cover; border-radius:6px;">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <div class="submit-area">
                <button type="submit">確認する</button>
            </div>
        </form>
        
        <script>
            document.getElementById('imageInput').addEventListener('change', function(e) {
                const previewArea = document.getElementById('previewArea');
                previewArea.innerHTML = '';

                const files = Array.from(e.target.files);
                if (files.length > 5) {
                    alert("最大5枚まで選択できます");
                    e.target.value = "";
                    return;
                }

                files.forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const img = document.createElement('img');
                        img.src = event.target.result;
                        img.style.width = '100%';
                        img.style.borderRadius = '6px';
                        img.style.objectFit = 'cover';
                        previewArea.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                });
            });
        </script>
    </body>
</html>