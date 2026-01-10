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
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>アルバム登録</title>
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
                right: 20px;
            }
            
            .form-grid {
                margin: 20px;
            }
            
            .form-row {
                display: flex;
                flex-direction: column;
                gap: 5px;
                margin-bottom: 10px;
            }

            .form-row label {
                font-weight: bold;
                font-size: 16px;
            }
            
            .form-row input {
                padding: 8px;
                font-size: 16px;
                border-radius: 6px;
                border: 1px solid #ccc;
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
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 10px;
                margin-top: 10px;
            }
            
            #previewArea img {
                width: 100%;
                height: 300px;
                object-fit: cover;
                border-radius: 6px;
            }
            
            #image-count {
                text-align: right;
                font-size: 14px;
                color: #555;
                margin-top: 10px;
            }
            
            .submit-area {
                display: flex;
                justify-content: flex-end;
                margin: 20px;
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
        			display: flex;
        			justify-content: space-between;
        			align-items: center;
    			}
    			.header-bar h1 {
        			font-size: 20px;
    			}
    			.back-button {
        			right: 10px;
        			font-size: 14px;
    			}
    			.form-grid {
        			margin: 10px;
    			}
    			#previewArea {
        			grid-template-columns: 1fr;
        			gap: 10px;
    			}
    			#previewArea img {
        			height: 200px;
    			}
  				.submit-area {
        			margin: 10px;
                    display: flex;
                	justify-content: flex-end;
    			}
    			.submit-area button {
        			padding: 14px;
        			font-size: 14px;
    			}
			}
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <div class="header-bar">
            <h1>アルバム新規登録</h1>
            <a href="album.php" class="back-button">戻る</a>
        </div>
        
        <form action="album-regist-complete.php" method="post" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-row">
                    <label>タイトル</label>
                    <input type="text" name="title" id="title" maxlength="30" pattern="[\u3040-\u309F\u4E00-\u9FAF\u30A0-\u30FF0-9!-/:-@¥[-`{-~　\s]+" required>
                </div>
            
                <div class="form-row">
                    <label>写真</label>
                    <label class="image-slot">
                        <span class="plus">＋</span>
                        <input type="file" name="image[]" id="image" accept="image/*" multiple required>
                    </label>
                </div>
            
                <div id="previewArea">
                    <?php if (!empty($_SESSION['album']['image_paths'])): ?>
                        <?php foreach ($_SESSION['album']['image_paths'] as $path): ?>
                            <img src="<?= htmlspecialchars($path) ?>" style="width:120px; height:120px; object-fit:cover; border-radius:6px;">
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            
                <div id="image-count" style="text-align:right; font-size:14px; color:#555; margin-top:10px;"></div>
            </div>
            
            <div class="submit-area">
                <button type="submit">登録する</button>
            </div>
        </form>

        <script>
            const imageInput = document.getElementById('image');
            const imageCount = document.getElementById('image-count');
            const previewArea = document.getElementById('previewArea');
            const MAX_IMAGES = 10;

            imageInput.addEventListener('change', () => {
                const files = imageInput.files;
                const count = files.length;

                if (count > MAX_IMAGES) {
                    alert(`最大${MAX_IMAGES}枚まで登録できます。選択された枚数：${count}枚`);
                    imageInput.value = '';
                    imageCount.textContent = '';
                    previewArea.innerHTML = '';
                    return;
                }
                
                imageCount.textContent = `${count}枚 選択されています`;
                previewArea.innerHTML = '';
                
                Array.from(files).forEach(file => {
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
        </script>
    </body>
</html>