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
                    
                    <li><a href="logout.php">ログアウト</a></li>
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
        <button onclick="location.href='notification.php'">通知</button>
        <button onclick="location.href='mypage.php?id=<?= $_SESSION['user']['id'] ?>'">マイページ</button>
    </div>
</div>