<?php
header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SELECT id, title, start_date, end_date FROM event WHERE delete_flag = 0");
    $event = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($event)
} catch (Exception $e) {
    echo json_encode([]);
}
?>
