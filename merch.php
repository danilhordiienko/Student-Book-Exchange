<?php
// Merchandise listing page for the Student Book Exchange project.
// Shows a simple list of active merch items with price and stock.
// Author: Danil Hordiienko, ATU student.

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/header.php';

$items = [];
$errorMessage = null;

try {
    // Only active items are displayed to keep the demo clear.
    $stmt = $pdo->query("
        SELECT id,
               name,
               description,
               price_cents,
               stock_qty
        FROM merch
        WHERE is_active = 1
        ORDER BY created_at DESC
    ");
    $items = $stmt->fetchAll();
} catch (PDOException $e) {
    $errorMessage = 'Could not load merchandise from the database.';
}
?>

<h1 class="h3 mb-3">Student merch</h1>
<p class="text-muted">
    Simple campus-related items that can be used as a merch example for this project.
</p>

<?php if ($errorMessage): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($errorMessage) ?>
    </div>
<?php endif; ?>

<?php if (!$errorMessage && empty($items)): ?>
    <div class="alert alert-info">
        There is no merch data yet. Sample records can be added in the database later.
    </div>
<?php endif; ?>

<div class="row">
    <?php foreach ($items as $item): ?>
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card h-100 shadow-sm merch-card">
                <div class="card-body">
                    <h5 class="card-title mb-2">
                        <?= htmlspecialchars($item['name']) ?>
                    </h5>
                    <?php if (!empty($item['description'])): ?>
                        <p class="card-text small mb-2">
                            <?= nl2br(htmlspecialchars($item['description'])) ?>
                        </p>
                    <?php endif; ?>
                    <p class="card-text fw-semibold mb-1">
                        Price:
                        €<?= number_format($item['price_cents'] / 100, 2) ?>
                    </p>
                    <p class="card-text small text-muted mb-0">
                        In stock: <?= (int)$item['stock_qty'] ?>
                    </p>
                </div>
                <button
                    class="btn btn-outline-primary btn-sm mt-2"
                    onclick="addFavourite('merch', <?= $item['id'] ?>)">
                    Add to favourites
                </button>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php
require_once __DIR__ . '/footer.php';
?>