<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = new PDO("mysql:dbname=circlesite;host=localhost;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $data = json_decode(file_get_contents('php://input'), true);
    $event_id = $data['event_id'] ?? null;
    $user_id = $data['user_id'] ?? ($_SESSION['user']['id'] ?? null);
    $status = $data['status'] ?? null;

    if (!$event_id || !$user_id) {
        echo json_encode(['error' => 'invalid request']);
        exit;
    }

    if ($status === '参加') {
        $stmt = $pdo->prepare("INSERT IGNORE INTO event_participant(event_id, user_id) VALUES (?, ?)");
        $stmt->execute([$event_id, $user_id]);
    } elseif ($status === '不参加') {
        $stmt = $pdo->prepare("DELETE FROM event_participant WHERE event_id = ? AND user_id = ?");
        $stmt->execute([$event_id, $user_id]);
    }

    $stmt_count = $pdo->prepare("SELECT COUNT(*) FROM event_participant WHERE event_id = ?");
    $stmt_count->execute([$event_id]);
    $count = $stmt_count->fetchColumn();

    echo json_encode(['count' => $count]);

} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(['error' => 'server error']);
}