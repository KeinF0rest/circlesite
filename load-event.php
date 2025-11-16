<?php
header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SELECT id, title, start_date, end_date FROM event WHERE delete_flag = 0");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($events as &$event) {
        $event['title'] = htmlspecialchars($event['title']);
        $event['start'] = $event['start_date'];
        if (!empty($event['end_date'])) {
            $event['end'] = $event['end_date'];
        }
    }
    
    echo json_encode($events);
} catch (Exception $e) {
    echo json_encode([]);
}
?>
