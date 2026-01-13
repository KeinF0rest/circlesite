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
            
            .form-row-inline {
                display: flex;          
                align-items: center;
                gap: 10px;
            }
            
            .form-row-inline input[type="date"],
            .form-row-inline input[type="time"] {
                width: 160px;
            }

            .form-row input, .form-row textarea {
                width: calc(100% - 20px);
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
                scroll-snap-type: x mandatory;
                margin: 20px;
            }
            
            #previewArea img {
                width: 300px;
                height: 300px;
                object-fit: cover;
                border-radius: 6px;
                scroll-snap-align: center;
                flex-shrink: 0;
            }
            
            .submit-area {
                display: flex;
                justify-content: flex-end;
                margin-top: 20px;
                margin-right: 20px;
            }
            
            .submit-area button {
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
                .form-row label { 
                    font-size: 14px;
                }
                .form-row-inline {
                    gap: 6px; 
                }
                .form-row-inline input[type="date"], .form-row-inline input[type="time"] {
                    width: 100%; 
                }
                input[type="text"], textarea { 
                    width: 100% !important;
                    padding: 10px; 
                    font-size: 14px;
                    box-sizing: border-box;
                }
                #char-count {
                    font-size: 12px;
                }
                .image-slot { 
                    height: 80px; 
                }
                .plus { 
                    font-size: 20px; 
                }
                #previewArea { 
                    margin: 10px;
                    gap: 10px; 
                }
                #previewArea img { 
                    width: 200px;
                    height: 200px; 
                } 
                .submit-area { 
                    margin: 10px; 
                }
                .submit-area button {
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
            <h1>イベント新規登録</h1>
            <a href="event.php" class="back-button">戻る</a>
        </div>
        
        <form id="event-form" action="event-regist-confirm.php" method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-row">
                    <label>タイトル</label>
                    <input type="text" name="title" maxlength="30" pattern="[\u3040-\u309F\u4E00-\u9FAF\u30A0-\u30FF0-9!-/:-@¥[-`{-~　\s]+" value="<?= htmlspecialchars($data['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
            
                <div class="form-row">
                    <label>開始日</label>
                    <div class="form-row-inline"> 
                        <input type="date" name="start_date" value="<?= htmlspecialchars($data['start_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                        <input type="time" name="start_time" value="<?= htmlspecialchars($data['start_time'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                </div>
            
                <div class="form-row">
                    <label>終了日</label>
                    <div class="form-row-inline">
                        <input type="date" name="end_date" value="<?= htmlspecialchars($data['end_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <input type="time" name="end_time" value="<?= htmlspecialchars($data['end_time'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                </div>
            
                <div class="form-row">
                    <label>内容</label>
                    <textarea name="content" maxlength="500" rows="6" required oninput="updateCount(this)"><?= htmlspecialchars($data['content'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    <div id="char-count" style="text-align:right; font-size:14px; color:#666;">0/500</div>
                </div>
            
                <div class="form-row">
                    <label>写真</label>
                    <label class="image-slot">
                        <span class="plus">＋</span>
                        <input type="file" name="image_path[]" accept="image/*" multiple id="imageInput">
                    </label>
                </div>
            
                <div id="previewArea">
                    <?php if (!empty($data['image_paths'])): ?>
                        <?php foreach ($data['image_paths'] as $path): ?>
                            <img src="<?= htmlspecialchars($path, ENT_QUOTES, 'UTF-8') ?>">
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="submit-area">
                <button type="submit">確認する</button>
            </div>
        </form>
        
        <script>
            document.getElementById('imageInput').addEventListener('change', function(e) {
                const previewArea = document.getElementById('previewArea')
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
                        previewArea.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                });
            });
            
            document.getElementById('event-form').addEventListener('submit', function(e) {
                const startDate = document.querySelector('[name="start_date"]').value;
                const startTime = document.querySelector('[name="start_time"]').value;
                const endDate   = document.querySelector('[name="end_date"]').value;
                const endTime   = document.querySelector('[name="end_time"]').value;
                if (startDate && startTime && endDate && endTime) {
                    const start = new Date(`${startDate}T${startTime}`);
                    const end   = new Date(`${endDate}T${endTime}`);
                    if (end < start) {
                        alert("終了日時は開始日時以降を選択してください");
                        e.preventDefault();
                    }
                }
            });
            
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