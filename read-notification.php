<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $_POST['id'] ?? $_GET['id'] ?? null;
$redirect = $_GET['redirect'] ?? 'notification.php';

if ($id) {
    $stmt = $pdo->prepare("UPDATE notification SET n_read = 1 WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION['user']['id']]);
}

header("Location: " . $redirect);
exit();
?>