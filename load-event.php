<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = new PDO("mysql:dbname=circlesite;host=localhost;", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SELECT e.id, e.title, e.start_date, e.start_time, e.end_date, e.end_time, (SELECT COUNT(*) FROM event_participant p WHERE p.event_id = e.id) AS participant_count FROM event e WHERE e.delete_flag = 0");
    $events = [];
    
    foreach ($stmt as $row) {
        $events[] = [
            'id' => $row['id'],
            'title' => htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'),
            'start' => $row['start_date'],
            'allDay' => true,
            'extendedProps' => [
                'participant_count' => $row['participant_count'],
                'original_start' => $row['start_date'] . ' ' . $row['start_time'],
                'original_end' => !empty($row['end_date'])
                ? ($row['end_time']
                   ? $row['end_date'].' '.$row['end_time']
                   : $row['end_date'].' 23:59:59')
                : null
            ]
        ];
    }
    echo json_encode($events);
} catch (Exception $e) {
    echo json_encode([]);
}
?>
