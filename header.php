<?php
if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    header("Location: index.php");
    exit();
}

$pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->prepare("SELECT COUNT(*) FROM notification WHERE (user_id IS NULL OR user_id=?) AND n_read=0");
$stmt->execute([$_SESSION['user']['id']]);
$unread_count = $stmt->fetchColumn();
?>
<!-- header.php -->
<div class="header-top">
    <div class="site-title">サークル名</div>
    <div id="navArea">
        <nav>
            <div class="inner">
                <ul>
                    <li><a href="index.php">カレンダー</a></li>
                    <li><a href="event.php">イベント</a></li>
                    <li><a href="blog.php">ブログ</a></li>
                    <li><a href="album.php">アルバム</a></li>
                    
                    <?php if ($_SESSION['user']['authority'] !=0): ?>
                        <li><a href="account.php">アカウント</a></li>
                    <?php endif; ?>
                    
                    <li><a href="logout.php" class="logout">ログアウト</a></li>
                </ul>
            </div>
        </nav>
                
        <div class="toggle">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <div id="mask"></div>
    </div>
</div>
<div class="header-bottom">
    <div class="header-buttons">
        <button onclick="location.href='notification.php'" class="notification-btn">
            通知
            <?php if ($unread_count > 0): ?>
                <span class="badge"><?= $unread_count ?></span>
            <?php endif; ?>
        </button>
        <button onclick="location.href='mypage.php?id=<?= $_SESSION['user']['id'] ?>'">マイページ</button>
    </div>
</div>