<?php
session_start();
$_SESSION['regist'] = [
    'family_name' => $_POST['family_name'],
    'last_name' => $_POST['last_name'],
    'nickname' => $_POST['nickname'],
    'mail' => $_POST['mail'],
    'password' => $_POST['password'],
    'gender' => $_POST['gender'],
    'postal_code' => $_POST['postal_code'],
    'prefecture' => $_POST['prefecture'],
    'address1' => $_POST['address1'],
    'address2' => $_POST['address2'],
    'authority' => $_POST['authority'],
];

$password_length = mb_strlen($_SESSION['regist']['password']);
$masked_password = str_repeat('●', $password_length);

$gender = $_SESSION['regist']['gender'] === '0' ? '男性' : '女性';
$authority = $_SESSION['regist']['authority'] === '0' ? '一般' : '管理者';

?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>アカウント登録確認</title>
        <link rel="stylesheet" href="style.css">
        <script src="menu.js" defer></script>
        <style>
            body {
                font-family: sans-serif;
                margin: 0;
            }

            .header-bar {
                position: relative;
                margin: 20px 0;
                height: 40px;
            }

            .header-bar h1 {
                position: absolute;
                left: 50%;
                transform: translateX(-50%);
                font-size: 24px;
                margin: 0;
            }

            .back-button {
                position: absolute;
                right: 0;
                top: 0;
                font-size: 16px;
                text-decoration: none;
                color: greenyellow;
            }

            .confirm-grid {
                display: grid;
                grid-template-columns: 1fr;
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
                border: 1px solid #ccc;
                border-radius: 12px;
                background-color: #f9f9f9;
            }

            .confirm-row {
                display: flex;
                justify-content: center;
                align-items: center;
                margin-bottom: 16px;
                text-align: left;
            }

            .confirm-row label {
                width: 130px;
                font-weight: bold;
                text-align: left;
                margin-right: 12px;
            }

            .confirm-row .value {
                width: 130px;
                text-align: left;
            }

            .button-area {
                display: flex;
                justify-content: center;
                gap: 100px;
                margin-top: 20px;
            }
            
            .button-area button {
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
            <h1>アカウント新規登録確認</h1>
        </div>
        
        <div class="confirm-grid">
            <div class="confirm-row">
                <label>名前（姓）</label>
                <div class="value"><?= htmlspecialchars($_SESSION['regist']['family_name']) ?></div>
            </div>
            
            <div class="confirm-row">
                <label>名前（名）</label>
                <div class="value"><?= htmlspecialchars($_SESSION['regist']['last_name']) ?></div>
            </div>
            
            <div class="confirm-row">
                <label>ニックネーム</label>
                <div class="value"><?= htmlspecialchars($_SESSION['regist']['nickname']) ?></div>
            </div>
            
            <div class="confirm-row">
                <label>メールアドレス</label>
                <div class="value"><?= htmlspecialchars($_SESSION['regist']['mail']) ?></div>
            </div>
            
            <div class="confirm-row">
                <label>性別</label>
                <div class="value"><?= $gender ?></div>
            </div>
            
            <div class="confirm-row">
                <label>パスワード</label>
                <div class="value"><?= $masked_password ?></div>
            </div>
            
            <div class="confirm-row">
                <label>郵便番号</label>
                <div class="value"><?= htmlspecialchars($_SESSION['regist']['postal_code']) ?></div>
            </div>
            
            <div class="confirm-row">
                <label>都道府県</label>
                <div class="value"><?= htmlspecialchars($_SESSION['regist']['prefecture']) ?></div>
            </div>
            
            <div class="confirm-row">
                <label>住所（市区町村）</label>
                <div class="value"><?= htmlspecialchars($_SESSION['regist']['address1']) ?></div>
            </div>
            
            <div class="confirm-row">
                <label>住所（番地）</label>
                <div class="value"><?= htmlspecialchars($_SESSION['regist']['address2']) ?></div>
            </div>
            
            <div class="confirm-row">
                <label>アカウント権限</label>
                <div class="value"><?= $authority ?></div>
            </div>
        </div>
        
        <div class="button-area">
            <form action="account-regist.php" method="POST">
                <input type="hidden" name="back" value="1">
                <button type="submit">前に戻る</button>
            </form>
            <form action="account-regist-complete.php" method="POST">
                <button type="submit">登録する</button>
            </form>
        </div>
    </body>
</html>