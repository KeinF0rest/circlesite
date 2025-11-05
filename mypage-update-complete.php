<?php
$id = $_POST['id'] ?? null;
$pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$before = $stmt->fetch(PDO::FETCH_ASSOC);

$password = !empty($_POST['password']) ? $_POST['password'] : $before['password'];

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

$sql = "UPDATE users SET family_name = ?, last_name = ?, nickname = ?, mail = ?, password = ?, gender = ?, postal_code = ?, prefecture = ?, address1 = ?, address2 = ?, authority = ?, profile_image = ? WHERE id = ?";

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

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$after = $stmt->fetch(PDO::FETCH_ASSOC);

$changes = [];
$fields = [
    'family_name' => '名前（姓）',
    'last_name' => '名前（名）',
    'nickname' => 'ニックネーム',
    'mail' => 'メールアドレス',
    'password' => 'パスワード',
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
    if ($key === 'password' && empty($_POST['password'])) {
        continue;
    }
    if ($beforeValue != $afterValue) {
        $changes[] = $label;
    }
}


if (!empty($_FILES['profile_image']['tmp_name'])) {
    $changes[] = "プロフィール画像が更新されました。";
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>アカウント更新完了</title>
        <link rel="stylesheet" href="style.css">
        <script src="menu.js" defer></script>
        <style>
            .header-bar {
                max-width: 600px;
                margin: 20px auto;
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
        
            <a href="mypage.php?id=<?= htmlspecialchars($id) ?>" class="back-link">マイページへ</a>
        </div>
    </body>
</html>