<?php
session_start();

$data = $_SESSION['regist'];
$hashed_password = password_hash($_SESSION['regist']['password'], PASSWORD_DEFAULT);

try{
    $pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
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
} catch(PDOException $e) {
    error_log($e->getMessage());
    echo"<p style='color:red; font-weight:bold;'>エラーが発生したためアカウント登録できません。" . $e->getMessage() . "</p>";
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
            <a href="account.php" class="back-link">アカウント一覧へ</a>
        </div>
    </body>
</html>