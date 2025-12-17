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

$data = $_SESSION['regist'];
$hashed_password = password_hash($_SESSION['regist']['password'], PASSWORD_DEFAULT);

try{
    $pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "INSERT INTO users (
    family_name, last_name, nickname, mail, password, gender, postal_code,
    prefecture, address1, address2, authority) VALUES (
    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $data['family_name'],
        $data['last_name'],
        $data['nickname'],
        $data['mail'],
        $hashed_password,
        $data['gender'],
        $data['postal_code'],
        $data['prefecture'],
        $data['address1'],
        $data['address2'],
        $data['authority'],
    ]);
} catch (Exception $e) {
    if ($e->errorInfo[1] == 1062) {
        echo "<p style='color:red; font-weight:bold;'>すでに登録されているメールアドレスです。</p>";
    } else {
         echo "<p style='color:red; font-weight:bold;'>エラーが発生したためアカウント登録できませんでした。</p>";
    }
    echo '<p><a href="account-regist.php" style="display:inline-block; padding:10px 20px; background:#4CAF50; color:#fff; text-decoration:none; border-radius:6px;">登録画面に戻る</a></p>';
    exit;
}
unset($_SESSION['regist']);
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>アカウント登録完了</title>
        <link rel="stylesheet" href="style.css">
        <script src="menu.js" defer></script>
        <style>
            body {
                font-family: sans-serif;
                margin: 0;
            }
            
            .header-bar {
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                align-items: center;
                margin: 20px;
                text-align: center;
            }

            .header-bar h1 {
                margin-bottom: 20px;
                font-size: 24px;
            }
            
            .back-link {
                padding: 10px 20px;
                background-color: #4CAF50;
                color: white;
                text-decoration: none;
                border-radius: 6px;
                font-size: 16px;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <div class="header-bar">
            <h1>アカウント登録完了しました。</h1>
            <a href="account.php" class="back-link">アカウントへ</a>
        </div>
    </body>
</html>