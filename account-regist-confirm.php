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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: account.php");
    exit();
}

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
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                margin-bottom: 15px;
            }

            .confirm-row label {
                width: 130px;
                font-weight: bold;
                text-align: left;
                margin-right: 15px;
            }

            .confirm-row .value {
                width: 100px;
                text-align: left;
                word-break: break-all;
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
                cursor: pointer;
            }
            
            .back-button {
                background-color: #ccc;
                color: #333;
            }
            
            .submit-button {
                background-color: #4CAF50;
                color: white;
            }
            
            @media (max-width: 600px) {
                .header-bar { 
                    margin: 10px 0;
                    height: auto; 
                }
                .header-bar h1 { 
                    font-size: 20px; 
                    position: static; 
                    transform: none; 
                    text-align: center; 
                }
                .confirm-grid { 
                    padding: 10px;
                    margin: 10px; 
                    gap: 10px; 
                }
                .confirm-row {
                    margin-bottom: 10px;
                    gap: 4px; 
                }
                .confirm-row label {  
                    font-size: 14px; 
                    margin-right: 0; 
                }
                .confirm-row .value { 
                    font-size: 14px; 
                    word-break: break-all; 
                }
                .button-area {
                    gap: 10px;
                    margin: 10px;
                }
                .button-area button {
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
            <h1>アカウント新規登録確認</h1>
        </div>
        
        <div class="confirm-grid">
            <div class="confirm-row">
                <label>名前（姓）</label>
                <div class="value"><?= htmlspecialchars($_SESSION['regist']['family_name'], ENT_QUOTES, 'UTF-8') ?></div>
            </div>
            
            <div class="confirm-row">
                <label>名前（名）</label>
                <div class="value"><?= htmlspecialchars($_SESSION['regist']['last_name'], ENT_QUOTES, 'UTF-8') ?></div>
            </div>
            
            <div class="confirm-row">
                <label>ニックネーム</label>
                <div class="value"><?= htmlspecialchars($_SESSION['regist']['nickname'], ENT_QUOTES, 'UTF-8') ?></div>
            </div>
            
            <div class="confirm-row">
                <label>メールアドレス</label>
                <div class="value"><?= htmlspecialchars($_SESSION['regist']['mail'], ENT_QUOTES, 'UTF-8') ?></div>
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
                <div class="value"><?= htmlspecialchars($_SESSION['regist']['postal_code'], ENT_QUOTES, 'UTF-8') ?></div>
            </div>
            
            <div class="confirm-row">
                <label>都道府県</label>
                <div class="value"><?= htmlspecialchars($_SESSION['regist']['prefecture'], ENT_QUOTES, 'UTF-8') ?></div>
            </div>
            
            <div class="confirm-row">
                <label>住所（市区町村）</label>
                <div class="value"><?= htmlspecialchars($_SESSION['regist']['address1'], ENT_QUOTES, 'UTF-8') ?></div>
            </div>
            
            <div class="confirm-row">
                <label>住所（番地）</label>
                <div class="value"><?= htmlspecialchars($_SESSION['regist']['address2'], ENT_QUOTES, 'UTF-8') ?></div>
            </div>
            
            <div class="confirm-row">
                <label>アカウント権限</label>
                <div class="value"><?= $authority ?></div>
            </div>
        </div>
        
        <div class="button-area">
            <form action="account-regist.php" method="POST">
                <button type="submit" class="back-button">戻る</button>
            </form>
            <form action="account-regist-complete.php" method="POST">
                <button type="submit" class="submit-button">登録する</button>
            </form>
        </div>
    </body>
</html>