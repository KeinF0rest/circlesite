<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
$id = $_GET['id'] ?? null;

if($id){
    if ($_SESSION['user']['authority'] == 0 && $id != $_SESSION['user']['id']) {
        $_SESSION['error'] = "アクセス権限がありません。";
        header("Location: index.php");
        exit();
    }
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
    } else {
        $_SESSION['error'] = "ユーザー情報が見つかりません。";
        header("Location: index.php");
        exit();
    }
    
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>マイページ</title>
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
                padding: 20px;
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
            
            .menu-wrapper {
                position: relative;
                display: flex;
                justify-content: flex-end;
                margin-right: 10px;
                margin-bottom: 10px;
            }
            
            .menu-icon {
                font-size: 24px;
                cursor: pointer;
                padding: 6px 10px;
                transition: background-color 0.2s ease;
            }
            
            .menu-popup {
                display: none;
                position: absolute;
                top: 40px;
                right: 0;
                background-color: #fff;
                border: 1px solid #ccc;
                border-radius: 6px;
                box-shadow: 0 2px 6px rgba(0,0,0,0.1);
                z-index: 100;
            }

            .menu-popup.visible {
                display: block;
            }
            
            .menu-popup ul {
                list-style: none;
                margin: 0;
                padding: 10px;
            }

            .menu-popup li {
                margin: 8px 0;
            }

            .menu-popup a {
                text-decoration: none;
                color: #333;
                font-size: 14px;
                padding: 4px 8px;
                display: block;
                border-radius: 4px;
                transition: background-color 0.2s ease;
            }

            .menu-popup a:hover {
                background-color: #f0f0f0;
            }
            
            .profile-image-wrapper {
                width: 120px;
                height: 120px;
                border-radius: 50%;
                overflow: hidden;
                background-color: transparent;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto;
                cursor: pointer;
                border: 2px dashed #aaa; 
            }
            
            .profile-image-wrapper img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            
            .profile-image-wrapper.no-image {
                background-color: #ccc;
            }
            
            .profile-image-wrapper.no-image img {
                display: none;
            }
            
            .form-grid {
                display: grid;
                grid-template-columns: 1fr;
                gap: 16px;
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
                border: 1px solid #ccc;
                border-radius: 12px;
                background-color: #f9f9f9;
            }

            .form-row {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 16px;
                max-width: 500px;
            }

            .form-row label {
                font-weight: bold;
                margin-bottom: 6px;
                color: #333;    
                width: 150px;
            }

            .form-row input,
            .form-row select {
                flex: 1;
                padding: 8px;
                font-size: 16px;
                border: 1px solid #ccc;
                border-radius: 6px;
            }
            
            .back-button{
                text-decoration: none;
            }
            
            .submit-area {
                display: flex;
                justify-content: flex-end;
                max-width: 600px;
                margin: 20px auto;
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
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <div class="header-bar">
            <h1>マイページ</h1>
            <a href="index.php" class="back-button">戻る</a>
        </div>
            
        <?php if ($_SESSION['user']['authority'] != 0): ?>
            <div class="menu-wrapper">
                <div class="menu-icon" onclick="toggleMenu()">⋯</div>
                <div class="menu-popup" id="menu-popup">
                    <ul>
                        <li><a href="account-delete.php?id=<?= $user['id'] ?>">削除</a></li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <form action="mypage-update-complete.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
            
            <div class="form-grid">
                <label for="profile_image" class="profile-image-wrapper <?= empty($user['profile_image']) ? 'no-image' : '' ?>">
                    <img id="preview" src="<?= !empty($user['profile_image']) ? htmlspecialchars($user['profile_image'], ENT_QUOTES, 'UTF-8') : '' ?>" alt="プロフィール画像" style="<?= empty($user['profile_image']) ? 'display:none;' : '' ?>">
                </label>
                <input type="file" name="profile_image" id="profile_image" accept="image/*" style="display:none">
                
                <div class="form-row">
                    <label>名前（姓）</label>
                    <input type="text" name="family_name" maxlength="10" pattern="[\u3040-\u309F\u4E00-\u9FAF]+" value="<?= htmlspecialchars($user['family_name'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                
                <div class="form-row">
                    <label>名前（名）</label>
                    <input type="text" name="last_name" maxlength="10" pattern="[\u3040-\u309F\u4E00-\u9FAF]+" value="<?= htmlspecialchars($user['last_name'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                
                <div class="form-row">
                    <label>ニックネーム</label>
                    <input type="text" name="nickname" maxlength="10" pattern="[\u3040-\u309F\u4E00-\u9FAF\u30A0-\u30FF0-9!-/:-@¥[-`{-~　\s]+" value="<?= htmlspecialchars($user['nickname'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                
                <div class="form-row">
                    <label>メールアドレス</label>
                    <input type="email" name="mail" maxlength="100" pattern="^[a-zA-Z0-9@.\-]+$" value="<?= htmlspecialchars($user['mail'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                
                <div class="form-row">
                    <label>パスワード(変更する場合のみ入力)</label>
                    <input type="password" name="password" maxlength="10" pattern="[A-Za-z0-9]+">
                </div>
                
                <div class="form-row">
                    <label>性別</label>
                    <label>
                        <input type="radio" name="gender" value="0" <?= $user['gender'] == 0 ? 'checked' : '' ?>>男性
                    </label>
                    <label>
                        <input type="radio" name="gender" value="1" <?= $user['gender'] == 1 ? 'checked' : '' ?>>女性
                    </label>
                </div>
                
                <div class="form-row">
                    <label>郵便番号</label>
                    <input type="text" name="postal_code" maxlength="7" pattern="\d{7}" value="<?= htmlspecialchars($user['postal_code'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                
                <div class="form-row">
                    <label>都道府県</label>
                    <select name="prefecture"> 
                        <?php
                        $prefs = ["北海道", "青森県", "岩手県", "宮城県", "秋田県", "山形県", "福島県",
                        "茨城県", "栃木県", "群馬県", "埼玉県", "千葉県", "東京都", "神奈川県",
                        "新潟県", "富山県", "石川県", "福井県", "山梨県", "長野県", "岐阜県", "静岡県", "愛知県",
                        "三重県", "滋賀県", "京都府", "大阪府", "兵庫県", "奈良県", "和歌山県",
                        "鳥取県", "島根県", "岡山県", "広島県", "山口県", "徳島県", "香川県", "愛媛県", "高知県",
                        "福岡県", "佐賀県", "長崎県", "熊本県", "大分県", "宮崎県", "鹿児島県", "沖縄県"];
                        foreach ($prefs as $pref) {
                            $selected = ($user['prefecture'] === $pref) ? 'selected' : '';
                            echo "<option value=\"$pref\" $selected>$pref</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-row">
                    <label>住所（市区町村）</label>
                    <input type="text" name="address1" maxlength="10" pattern="[\u3040-\u309F\u4E00-\u9FAF\u30A1-\u30FA\u3000\u30FC0-9]+" value="<?= htmlspecialchars($user['address1'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                
                <div class="form-row">
                    <label>住所（番地）</label>
                    <input type="text" name="address2" maxlength="100" pattern="[\u3040-\u309F\u4E00-\u9FAF\u30A1-\u30FA\u3000\u30FC0-9]+" value="<?= htmlspecialchars($user['address2'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                
                <div class="form-row">
                    <label>アカウント権限</label>
                    <?php if ($_SESSION['user']['authority'] == 1): ?>
                        <select name="authority">
                            <option value="0" <?= $user['authority'] == 0 ? 'selected' : '' ?>>一般</option>
                            <option value="1" <?= $user['authority'] == 1 ? 'selected' : '' ?>>管理者</option>
                        </select>
                    <?php else: ?>
                        <span><?= $user['authority'] == 0 ? '一般' : '管理者' ?></span>
                        <input type="hidden" name="authority" value="<?= $user['authority'] ?>">
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="submit-area">
                <button type="submit" class="submit-button">変更</button>
            </div>
        </form>
        <script>
            function toggleMenu() {
                const menu = document.getElementById('menu-popup');
                menu.classList.toggle('visible');
            }
            
            document.getElementById('profile_image').addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('preview');
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });
        </script>
    </body>
</html>