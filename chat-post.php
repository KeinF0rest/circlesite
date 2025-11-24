<?php
session_start();
$pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$event_id = $_POST['event_id'];  
$user_id = $_SESSION['user']['id'];     
$message = $_POST['message']; 
$image_path = null;

if (!empty($_FILES['image']['name'])) {
    $targetDir = "uploads/";
    $fileName = time() . "_" . basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . $fileName;

    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif'];
    if (in_array($fileType, $allowed)) {
        move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
        $image_path = $targetFile;
    }
}

$stmt = $pdo->prepare("INSERT INTO message (event_id, user_id, message, image_path) VALUES (?, ?, ?, ?)");
$stmt->execute([$event_id, $user_id, $message, $image_path]);

header("Location: chat.php?event_id=" . $event_id);
exit;
?>