<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

$id = $_POST['id'] ?? null;

try {
    $pdo = new PDO("mysql:dbname=circlesite;host=localhost;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT * FROM users WHERE id = ? AND delete_flag = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $before = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$before) {
        $_SESSION['error'] = "指定されたアカウントは存在しません。";
        header("Location: index.php");
        exit();
    }

    $imagePath = $before['profile_image'];
    if (!empty($_FILES['profile_image']['tmp_name'])) {
        $filename = uniqid() . '_' . basename($_FILES['profile_image']['name']);
        $targetPath = 'uploads/' . $filename;
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath)) {
            $imagePath = $targetPath;
        }
    }

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    } else {
        $password = $before['password'];
    }

    $sql = "UPDATE users SET family_name = ?, last_name = ?, nickname = ?, mail = ?, password = ?, gender = ?, postal_code = ?, prefecture = ?, address1 = ?, address2 = ?, authority = ?, profile_image = ? WHERE id = ? AND delete_flag = 0";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['family_name'],
        $_POST['last_name'],
        $_POST['nickname'],
        $_POST['mail'],
        $password,
        $_POST['gender'],
        $_POST['postal_code'],
        $_POST['prefecture'],
        $_POST['address1'],
        $_POST['address2'],
        $_POST['authority'],
        $imagePath,
        $id
    ]);

    $sql = "SELECT * FROM users WHERE id = ? AND delete_flag = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $after = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$after) {
        $_SESSION['error'] = "アカウント情報が見つかりません。";
        header("Location: index.php");
        exit();
    }

    $changes = [];
    $fields = [
        'family_name' => '名前（姓）',
        'last_name' => '名前（名）',
        'nickname' => 'ニックネーム',
        'mail' => 'メールアドレス',
        'gender' => '性別',
        'postal_code' => '郵便番号',
        'prefecture' => '都道府県',
        'address1' => '住所（市区町村）',
        'address2' => '住所（番地）',
        'authority' => 'アカウント権限',
    ];

    foreach ($fields as $key => $label) {
        $beforeValue = $before[$key] ?? '';
        $afterValue = $_POST[$key] ?? '';
        if ($beforeValue != $afterValue) {
            $changes[] = $label;
        }
    }

    if (!empty($_POST['password']) && !password_verify($_POST['password'], $before['password'])) {
    $changes[] = 'パスワード';
}

    if (!empty($_FILES['profile_image']['tmp_name'])) {
        $changes[] = "プロフィール画像";
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo "<p style='color:red; font-weight:bold;'>エラーが発生したためアカウント更新ができませんでした。</p>";
    echo "<p><a href='mypage.php?id=" . $_SESSION['user']['id'] . "' style='display:inline-block; padding:10px 20px; background:#4CAF50; color:#fff; text-decoration:none; border-radius:6px;'>マイページ画面に戻る</a></p>";
    exit;
}
?>
<?php 
$login_user = $_SESSION['user'];
$is_admin = ($login_user['authority'] == 1);
$is_self = ($login_user['id'] == $id);
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>アカウント更新完了</title>
        <link rel="stylesheet" href="style.css">
        <script src="menu.js" defer></script>
        <style>
            body {
                font-family: sans-serif;
                margin: 0;
            }
            
            .header-bar {
                margin: 20px;
                text-align: center;
            }
            
            .change-item {
                margin-bottom: 20px;
                font-size: 24px;
            }
            
            .no-change {
                font-size: 18px;
                margin-bottom: 20px;
            }
            
            .back-link {
                padding: 10px 10px;
                display: inline-block;
                font-size: 16px;
                background-color: #4CAF50;
                color: white;
                text-decoration: none;
                border-radius: 6px;
            }
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <div class="header-bar">
            <?php if (!empty($changes)): ?>
                <p class="change-item"><?= implode('、', $changes) ?>が更新されました。</p>
            <?php else: ?>
                <p class="no-change">変更はありませんでした。</p>
            <?php endif; ?>
        
            <?php if ($is_admin && !$is_self): ?>
            	<a href="mypage.php?id=<?= htmlspecialchars($id) ?>" class="back-link">アカウント情報へ</a>
            <?php else: ?>
            	<a href="mypage.php?id=<?= htmlspecialchars($id) ?>" class="back-link">マイページへ</a>
            <?php endif; ?>
        </div>
    </body>
</html>