<?php
// API endpoint for removing an item from user's favourites.
// Very basic logic; simply deletes the matching row.
// Author: Danil Hordiienko.

require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in.']);
    exit;
}

$id = (int)($_POST['fav_id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid favourite id.']);
    exit;
}

$stmt = $pdo->prepare("DELETE FROM favourites WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);

echo json_encode(['success' => true]);