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

$pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
$event_id = $_GET['id'] ?? null;

$stmt = $pdo->prepare("SELECT * FROM event WHERE id = ? AND delete_flag = 0");
$stmt->execute([$event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt_img = $pdo->prepare("SELECT id, image_path FROM event_images WHERE event_id = ? AND delete_flag = 0");
$stmt_img->execute([$event['id']]);
$images = $stmt_img->fetchAll(PDO::FETCH_ASSOC);

if (!$event) {
    $_SESSION['error'] = "指定されたイベントは存在しません。";
    header("Location: event.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>イベント更新</title>
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
            
            .form-row-inline input[type="date"],
            .form-row-inline input[type="time"] {
                width: 160px;
            }

            .form-row input,
            .form-row textarea {
                width: calc(100% - 20px);
                padding: 8px;
                font-size: 16px;
                border-radius: 6px;
                border: 1px solid #ccc;
            }

            .form-row textarea {
                resize: vertical;
            }
            
            .image-list {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
            }
            
            .image-item {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
            }
            
            .image-item label {
                display: flex;
                flex-direction: row;
                align-items: center;
                gap: 4px;
                margin-top: 6px;
                white-space: nowrap;
            }
            
            .image-item img {
                width: 100%;
                height: 300px;
                object-fit: cover;
                border-radius: 6px;
            }

            #previewArea {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 10px;
                margin: 20px;
            }

            #previewArea img {
                width: 100%;
                height: 300px;
                border-radius: 6px;
                object-fit: cover;
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
            
            .submit-area {
                display: flex;
                justify-content: flex-end;
                margin: 20px;
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
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <div class="header-bar">
            <h1>イベント更新</h1>
            <a href="event-info.php?id=<?= htmlspecialchars($event['id']) ?>" class="back-button">戻る</a>
        </div>
        
        <form action="event-update-complete.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= htmlspecialchars($event['id']) ?>">
            
            <div class="form-row">
                <label>タイトル</label>
                <input type="text" name="title" maxlength="30" pattern="[\u3040-\u309F\u4E00-\u9FAF\u30A0-\u30FF0-9!-/:-@¥[-`{-~　\s]+" value="<?= htmlspecialchars($event['title']) ?>" required>
            </div>
            
            <div class="form-row">
                <label>開始日</label>
                <div class="form-row-inline">
                    <input type="date" name="start_date" value="<?= htmlspecialchars($event['start_date']) ?>" required>
                    <input type="time" name="start_time" value="<?= htmlspecialchars($event['start_time']) ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <label>終了日</label>
                <div class="form-row-inline">
                    <input type="date" name="end_date" value="<?= htmlspecialchars($event['end_date']) ?>">
                    <input type="time" name="end_time" value="<?= htmlspecialchars($event['end_time']) ?>">
                </div>
            </div>
            
            <div class="form-row">
                <label>内容</label>
                <textarea name="content" maxlength="500" rows="6" required oninput="updateCount(this)"><?= htmlspecialchars($event['content']) ?></textarea>
                <div id="char-count" style="text-align:right; font-size:14px; color:#666;">0/500</div>
            </div>
            
            <div class="form-row">
                <label>写真</label>
                <div class="image-list">
                    <?php if (!empty($images)): ?>
                        <?php foreach ($images as $img): ?>
                            <div class="image-item">
                                <img src="<?= htmlspecialchars($img['image_path']) ?>" alt="イベント画像">
                                <label><input type="checkbox" name="delete_images[]" value="<?= htmlspecialchars($img['id']) ?>">削除</label>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>画像は登録されていません。</p>
                    <?php endif; ?>
                </div>
            </div>
                
            <div class="form-row">
                <label>新しい写真を追加</label>
                <label class="image-slot">
                    <span class="plus">＋</span>
                    <input type="file" name="image_path[]" accept="image/*" id="imageInput" multiple>
                </label>
            </div>
                
            <div id="previewArea"></div>
            <div id="image-count" style="text-align:right; font-size:14px; color:#555; margin-top:10px;"></div>
            
            <div class="submit-area">
                <button type="submit" class="submit-button">更新</button>
            </div>         
        </form>
        
        <script>
            const imageInput = document.getElementById('imageInput');
            const imageCount = document.getElementById('image-count');
            const previewArea = document.getElementById('previewArea');
            const MAX_IMAGES = 5;
            
            const existingCount = <?= count($images) ?>;
            
            imageInput.addEventListener('change', () => {
                const newFiles = Array.from(imageInput.files);
                const deleteCount = document.querySelectorAll('input[name="delete_images[]"]:checked').length;
                const totalCount = existingCount - deleteCount + newFiles.length;
                
                if (totalCount > MAX_IMAGES) {
                    alert(`最大${MAX_IMAGES}枚までです。現在 ${totalCount} 枚です。`);
                    imageInput.value = '';
                    previewArea.innerHTML = ''; return;
                }
                
                imageCount.textContent = `${newFiles.length}枚 選択されています`;
                previewArea.innerHTML = '';
                
                newFiles.forEach(file => {
                    if (!file.type.startsWith('image/')) return;

                    const reader = new FileReader();
                    reader.onload = e => {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        previewArea.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                });
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