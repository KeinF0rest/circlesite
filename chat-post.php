<?php
session_start();
$pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$event_id = $_POST['event_id'];  
$user_id = $_SESSION['user']['id'];     
$message = $_POST['message']; 
$image_path = null;

$error = null;

if (!empty($_FILES['image']['name'])) {
    $targetDir = "uploads/";
    $fileName = uniqid() . '_' . basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . $fileName;

    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif'];
    
    if ($_FILES["image"]["size"] > 5 * 1024 * 1024) {
        $error = "ファイルサイズは5MB以下にしてください。";
    }
    elseif (!in_array($fileType, $allowed)) {
        $error = "許可されている拡張子は jpg, jpeg, png, gif です。";
    }
    elseif (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        $error = "ファイルのアップロードに失敗しました。";
    } else {
        $image_path = $targetFile;
    }
}
if (!$error && (!empty($message) || !empty($image_path))) {
    $stmt = $pdo->prepare("INSERT INTO message (event_id, user_id, message, image_path) VALUES (?, ?, ?, ?)");
    $stmt->execute([$event_id, $user_id, $message, $image_path]);
}
if ($error) {
    $_SESSION['error'] = $error;
}

header("Location: chat.php?event_id=" . $event_id);
exit;
?>