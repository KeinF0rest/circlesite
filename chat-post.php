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
    
    $eventStmt = $pdo->prepare("SELECT title FROM event WHERE id = ?");
    $eventStmt->execute([$event_id]);
    $event = $eventStmt->fetch(PDO::FETCH_ASSOC);
    $event_title = $event['title'] ?? 'イベント';
    
    $notice_message = !empty($message) 
        ? "イベント「{$event_title}」に新しいメッセージがあります。" 
        : "イベント「{$event_title}」に画像メッセージがあります。";
    
    $stmt_users = $pdo->query("SELECT id FROM users");
    $users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt_notify = $pdo->prepare(" INSERT INTO notification (type, related_id, message, user_id, n_read) VALUES ('chat', ?, ?, ?, 0) ");
    
    foreach ($users as $u) {
        if ($u['id'] == $user_id) continue;
        
        $stmt_notify->execute([ 
            $event_id, 
            $notice_message, 
            $u['id'] 
        ]); 
    }
}
if ($error) {
    $_SESSION['error'] = $error;
}

header("Location: chat.php?event_id=" . $event_id);
exit;
?>