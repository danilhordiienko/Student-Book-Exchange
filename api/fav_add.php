<?php
// API endpoint for adding an item to user's favourites.
// Supports both books and merch items.
// Author: Danil Hordiienko, ATU student.

require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in.']);
    exit;
}

$type = $_POST['type'] ?? '';
$id   = (int)($_POST['id'] ?? 0);

if ($id <= 0 || ($type !== 'book' && $type !== 'merch')) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
    exit;
}

try {
    if ($type === 'book') {
        $stmt = $pdo->prepare("INSERT INTO favourites (user_id, book_id) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO favourites (user_id, merch_id) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $id]);
    }

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Already in favourites.']);
}