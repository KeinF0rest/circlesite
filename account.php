<?php
$pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");

$sql = "SELECT id, nickname, profile_image FROM users";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$account = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>アカウント一覧</title>
        <link rel="stylesheet" href="style.css">
        <script src="menu.js" defer></script>
        <style>
            body {
                font-family: sans-serif;
                margin: 0;
            }
            
            .header-bar {
                margin: 20px;
                display: flex;
                justify-content: space-between;
            }
            
            .header-bar h1 {
                margin: 0;
                font-size: 24px;
            }
            
            .regist-button {
                font-size: 24px;
                padding: 6px 12px;
                color: #333;
                text-decoration: none;
                transition: background-color 0.3s ease;
                line-height: 1;
            }
            
            .account-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
                margin: 20px;
            }
            
            .account-card {
                margin: 10px;
                box-sizing: border-box;
                border: 1px solid #ccc;
                border-radius: 20px;
                padding: 10px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }
            
            .account-card img {
                width: 100px;
                height: 100px;
                object-fit: cover;
                border-radius: 50%;
            }
            
            .profile-image-wrapper {
                width: 100px;
                height: 100px;
                border-radius: 50%;
                overflow: hidden;
                background-color: transparent;
                display: flex;
                align-items: center;
                justify-content: center;
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
            
            .account-card-link {
                text-decoration: none;
                color: inherit;
                display: block;
                min-height: 200px;
            }
            
            .account-card-link:hover .account-card {
                box-shadow: 0 0 10px rgba(0,0,0,0.2);
                transform: scale(1.02);
                transition: 0.2s ease;
            }
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <div class="header-bar">
            <h1>アカウント一覧</h1>
            <a href="account-regist.php" class="regist-button">＋</a>
        </div>
        
        <div class="account-grid">
            <?php foreach ($account as $row): ?>
                <a href="mypage.php?id=<?= urlencode($row['id']) ?>" class="account-card-link">
                    <div class="account-card">
                        <div class="profile-image-wrapper <?= empty($row['profile_image']) ? 'no-image' : ''?>">
                            <img src="<?= htmlspecialchars($row['profile_image'] ?? 'default.png', ENT_QUOTES, 'UTF-8') ?>" alt="プロフィール画像">
                        </div>
                        <p><?= htmlspecialchars($row['nickname'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </body>

</html>