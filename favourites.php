<?php
// Favourites page for Student Book Exchange.
// Shows all liked books and merchandise for the logged-in user.
// Author: Danil Hordiienko.

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/header.php';

if (empty($_SESSION['user_id'])) {
    echo '<div class="alert alert-warning">Please log in to view favourites.</div>';
    require_once __DIR__ . '/footer.php';
    exit;
}

$stmt = $pdo->prepare("
    SELECT f.id AS fav_id,
           b.title AS book_title,
           m.name AS merch_name,
           f.created_at
    FROM favourites f
    LEFT JOIN books b ON b.id = f.book_id
    LEFT JOIN merch m ON m.id = f.merch_id
    WHERE f.user_id = ?
    ORDER BY f.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$favs = $stmt->fetchAll();
?>

<h1 class="h3 mb-3">Your favourites</h1>

<?php if (empty($favs)): ?>
    <div class="alert alert-info">
        Your favourites list is empty.
    </div>
<?php endif; ?>

<?php foreach ($favs as $f): ?>
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-1">
                <?php if ($f['book_title']): ?>
                    Book: <?= htmlspecialchars($f['book_title']) ?>
                <?php else: ?>
                    Merch: <?= htmlspecialchars($f['merch_name']) ?>
                <?php endif; ?>
            </h5>

            <p class="text-muted small mb-2">
                Added on <?= htmlspecialchars($f['created_at']) ?>
            </p>

            <form method="post" action="api/fav_remove.php" class="d-inline">
                <input type="hidden" name="fav_id" value="<?= $f['fav_id'] ?>">
                <button class="btn btn-outline-danger btn-sm">Remove</button>
            </form>
        </div>
    </div>
<?php endforeach; ?>

<?php
require_once __DIR__ . '/footer.php';
?>