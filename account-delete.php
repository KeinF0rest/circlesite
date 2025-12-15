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

$id = $_GET['id'] ?? null;

try {
    $pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND delete_flag = 0");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['error'] = "指定されたアカウントは存在しません。";
        header("Location: index.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
        $delete_id = $_POST['id'] ?? null;
        if ($delete_id) {
            $stmt = $pdo->prepare("UPDATE users SET delete_flag = 1 WHERE id = ?");
            $stmt->execute([$delete_id]);
            
            $_SESSION['delete_complete'] = true;
            header('Location: account-delete-complete.php');
            exit;
        }
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    $safeId = htmlspecialchars((string)($_POST['id'] ?? ''), ENT_QUOTES, 'UTF-8');
    echo "<p style='color:red; font-weight:bold;'>エラーが発生したためアカウント削除ができませんでした。</p>";
    echo "<p><a href='mypage.php?id=" . $safeId . "' style='display:inline-block; padding:10px 20px; background:#4CAF50; color:#fff; text-decoration:none; border-radius:6px;'>アカウント情報画面に戻る</a></p>";
    exit;
}

?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>アカウント削除</title>
        <link rel="stylesheet" href="style.css">
        <script src="menu.js" defer></script>
        <style>
            body {
                font-family: sans-serif;
                margin: 0;
            }
            
            .header-bar {
                position: relative;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            
            .header-bar h1 {
                font-size: 24px;
                margin: 0;
                text-align: center;
            }
            
            h2 {
                text-align: center;
                margin-bottom: 20px;
            }
            
            .back-button {
                position: absolute;
                right: 20px;
                font-size: 16px;
                color: #4CAF50;
                text-decoration: none;
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
                border: 2px dashed #aaa; 
            }
            
            .profile-image-wrapper img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                border-radius: 50%;
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
                justify-content: center;
                gap: 10px;
                margin-bottom: 16px;

            }

            .form-row label {
                font-weight: bold;
                color: #333;    
                width: 150px;
                text-align: left;
            }
            
            .form-row div {
                flex: 1;
                max-width: 100px;
                white-space: nowrap; 
                text-align: left;
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
            <h1>アカウントを削除しますか？</h1>
            <a href="mypage.php?id=<?= htmlspecialchars($user['id']) ?>" class="back-button">戻る</a>
        </div>
        
        <h2>削除すると復元することはできません。</h2>
        
        <div class="form-grid">
            <label for="profile_image" class="profile-image-wrapper <?= empty($user['profile_image']) ? 'no-image' : '' ?>">
                <?php if (!empty($user['profile_image'])): ?>
                    <img src="<?= htmlspecialchars($user['profile_image'], ENT_QUOTES, 'UTF-8') ?>" alt="プロフィール画像">
                <?php endif; ?>
            </label>
            
            <div class="form-row">
                <label>ニックネーム</label>
                <div><?= htmlspecialchars($user['nickname']) ?></div>
            </div>
            
            <div class="form-row">
                <label>名前（姓）</label>
                <div><?= htmlspecialchars($user['family_name']) ?></div>
            </div>
            
            <div class="form-row">
                <label>名前（名）</label>
                <div><?= htmlspecialchars($user['last_name']) ?></div>
            </div>
            
            <div class="form-row">
                <label>メールアドレス</label>
                <div><?= htmlspecialchars($user['mail']) ?></div>
            </div>
            
            <div class="form-row">
                <label>パスワード</label>
                <div>安全上表示されません</div>
            </div>
            
            <div class="form-row">
                <label>性別</label>
                <div><?= $user['gender'] == 0 ? '男性' : '女性' ?></div>
            </div>
            
            <div class="form-row">
                <label>郵便番号</label>
                <div><?= htmlspecialchars($user['postal_code']) ?></div>
            </div>
            
            <div class="form-row">
                <label>都道府県</label>
                <div><?= htmlspecialchars($user['prefecture']) ?></div>
            </div>
            
            <div class="form-row">
                <label>住所（市区町村）</label>
                <div><?= htmlspecialchars($user['address1']) ?></div>
            </div>
            
            <div class="form-row">
                <label>住所（番地）</label>
                <div><?= htmlspecialchars($user['address2']) ?></div>
            </div>
            
            <div class="form-row">
                <label>アカウント権限</label>
                <div><?= $user['authority'] == 0 ? '一般' : '管理者' ?></div>
            </div>
        </div>
        
        <div class="submit-area">
            <form method="post">
                <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
                <button type="submit" name="delete" class="submit-button">削除</button>
            </form>
        </div>
    </body>
</html>