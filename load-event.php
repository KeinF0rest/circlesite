<?php
header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SELECT e.id, e.title, e.start_date, e.end_date, (SELECT COUNT(*) FROM event_participant p WHERE p.event_id = e.id) AS participant_count FROM event e WHERE e.delete_flag = 0");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($events as &$event) {
        $event['title'] = htmlspecialchars($event['title']);
        $event['start'] = $event['start_date'];
        if (!empty($event['end_date'])) {
            $event['end'] = date('Y-m-d', strtotime($event['end_date'] . ' +1 day'));
            $event['original_end'] = $event['end_date'];
        } else {
            $event['original_end'] = null;
        }
        $event['extendedProps'] = [
            'participant_count' => $event['participant_count']
        ];
        unset($event['participant_count']);
    }
    echo json_encode($events);
} catch (Exception $e) {
    echo json_encode([]);
}
?>
