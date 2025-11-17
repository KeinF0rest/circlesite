<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$data = $_SESSION['regist'] ?? [];

?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>アカウント登録</title>
        <link rel="stylesheet" href="style.css">
        <script src="menu.js" defer></script>
        <style>
            body {
                font-family: sans-serif;
                margin: 0px;
            }

            .header-bar {
                display: flex;
                justify-content: center;
                align-items: center;
                position: relative;
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
                align-items: center;
                gap: 20px;
            }

            .form-row label {
                width: 160px;
                font-weight: bold;
            }

            .form-row input,
            .form-row select {
                flex: 1;
                padding: 8px;
                font-size: 16px;
                border-radius: 6px;
                border: 1px solid #ccc;
            }
            
            .form-row input[type="radio"] {
                margin-left: 10px;
                margin-right: 0;
            }
            
            .form-row:last-child {
                justify-content: flex-end;
            }
            
            .form-row button {
                background-color: #4CAF50;
                color: white;
                padding: 10px 20px;
                font-size: 16px;
                border: none;
                border-radius: 6px;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <div class="header-bar">
            <h1>アカウント新規登録</h1>
            <a href="account.php" class="back-button">戻る</a>
        </div>
        
        <form action="account-regist-confirm.php" method="post" class="form-grid">
            <div class="form-row">
                <label>名前（姓）</label>
                <input type="text" name="family_name" maxlength="10" pattern="[\u3040-\u309F\u4E00-\u9FAF]+" value="<?= htmlspecialchars($data['family_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            </div>    
            
            <div class="form-row">
                <label>名前（名）</label>
                <input type="text" name="last_name" maxlength="10" pattern="[\u3040-\u309F\u4E00-\u9FAF]+" value="<?= htmlspecialchars($data['last_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            
            <div class="form-row">
                <label>ニックネーム</label>
                <input type="text" name="nickname" maxlength="10" pattern="[\u3040-\u309F\u4E00-\u9FAF\u30A0-\u30FF0-9!-/:-@¥[-`{-~　\s]+" value="<?= htmlspecialchars($data['nickname'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
                
            <div class="form-row">
                <label>メールアドレス</label>
                <input type="email" name="mail" maxlength="100" pattern="^[a-zA-Z0-9@.\-]+$" value="<?= htmlspecialchars($data['mail'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            
            <div class="form-row">
                <label>パスワード</label>
                <input type="password" name="password" maxlength="10" pattern="[A-Za-z0-9]+" value="<?= htmlspecialchars($data['password'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            
            <div class="form-row">
                <label>性別</label>
                <label><input type="radio" name="gender" value="0" <?= (!isset($data['gender']) || $data['gender'] === '0') ? 'checked' : '' ?>>男性</label>
                <label><input type="radio" name="gender" value="1" <?= ($data['gender'] ?? '') === '1' ? 'checked' : '' ?>>女性</label>
            </div>
            
            <div class="form-row">
                <label>郵便番号</label>
                <input type="text" name="postal_code" maxlength="7" pattern="\d{7}" value="<?= htmlspecialchars($data['postal_code'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            
            <div class="form-row">
            <?php
                $prefectures = [
                    "北海道", "青森県", "岩手県", "宮城県", "秋田県", "山形県", "福島県",
                    "茨城県", "栃木県", "群馬県", "埼玉県", "千葉県", "東京都", "神奈川県",
                    "新潟県", "富山県", "石川県", "福井県", "山梨県", "長野県", "岐阜県", "静岡県", "愛知県",
                    "三重県", "滋賀県", "京都府", "大阪府", "兵庫県", "奈良県", "和歌山県",
                    "鳥取県", "島根県", "岡山県", "広島県", "山口県", "徳島県", "香川県", "愛媛県", "高知県",
                    "福岡県", "佐賀県", "長崎県", "熊本県", "大分県", "宮崎県", "鹿児島県", "沖縄県"
                ];
            ?>
                <label>都道府県</label>
                <select name="prefecture" required>
                <option value="" <?= empty($data["prefecture"]) ? "selected" : "" ?>>選択してください</option>
                    <?php
                    foreach ($prefectures as $prefecture) {
                        $selected = ($data["prefecture"] ?? '') === $prefecture ? "selected" : "";
                        echo "<option value='" . htmlspecialchars($prefecture, ENT_QUOTES, 'UTF-8') . "' $selected>" . htmlspecialchars($prefecture, ENT_QUOTES, 'UTF-8') . "</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-row">
                <label>住所（市区町村）</label>
                <input type="text" name="address1" maxlength="10" pattern="[\u3040-\u309F\u4E00-\u9FAF\u30A1-\u30FA\u3000\u30FC0-9]+" value="<?= htmlspecialchars($data['address1'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            
            <div class="form-row">
                <label>住所（番地）</label>
                <input type="text" name="address2" maxlength="100" pattern="[\u3040-\u309F\u4E00-\u9FAF\u30A1-\u30FA\u3000\u30FC0-9]+" value="<?= htmlspecialchars($data['address2'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            
            <div class="form-row">
                <label for="authority">アカウント権限</label>
                <select name="authority" id="authority" required>
                    <option value="0" <?= ($data['authority'] ?? '') === '0' ? 'selected' : ''?>>一般</option>
                    <option value="1" <?= ($data['authority'] ?? '') === '1' ? 'selected' : ''?>>管理者</option>
                </select>
            </div>
            
            <div class="form-row">
                <button type="submit" name="submit">確認する</button>
            </div>
        </form>
    </body>
</html>