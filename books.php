<?php
// Books listing page for the Student Book Exchange project.
// Displays a simple list of books loaded directly from the database.
// Author: Danil Hordiienko, ATU student.

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/header.php';

$books = [];
$errorMessage = null;

try {
    // Select a basic set of fields to present books on the page.
    $stmt = $pdo->query("
        SELECT b.id,
               b.title,
               b.author,
               b.subject,
               b.course,
               b.price_cents,
               b.`condition`,
               u.first_name,
               u.last_name
        FROM books b
        JOIN users u ON u.id = b.user_id
        ORDER BY b.created_at DESC
    ");
    $books = $stmt->fetchAll();
} catch (PDOException $e) {
    // For this project it is enough to show a generic error.
    $errorMessage = 'Could not load books from the database.';
}
?>

<h1 class="h3 mb-3">Available books</h1>
<p class="text-muted">
    This page shows a simple list of books currently stored in the system.
</p>

<?php if ($errorMessage): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($errorMessage) ?>
    </div>
<?php endif; ?>

<?php if (!$errorMessage && empty($books)): ?>
    <div class="alert alert-info">
        There are no books yet. A student will add the first listing later in the project.
    </div>
<?php endif; ?>

<div class="row">
    <?php foreach ($books as $book): ?>
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card h-100 shadow-sm book-card">
                <div class="card-body">
                    <h5 class="card-title mb-1">
                        <?= htmlspecialchars($book['title']) ?>
                    </h5>
                    <p class="card-subtitle text-muted mb-2">
                        by <?= htmlspecialchars($book['author']) ?>
                    </p>
                    <p class="card-text small mb-2">
                        <strong>Subject:</strong>
                        <?= htmlspecialchars($book['subject'] ?: 'Not specified') ?><br>
                        <strong>Course:</strong>
                        <?= htmlspecialchars($book['course'] ?: 'Not specified') ?><br>
                        <strong>Condition:</strong>
                        <?= htmlspecialchars($book['condition']) ?>
                    </p>
                    <p class="card-text fw-semibold">
                        Price:
                        €<?= number_format($book['price_cents'] / 100, 2) ?>
                    </p>
                </div>
                <div class="card-footer small text-muted">
                    Listed by
                    <?= htmlspecialchars($book['first_name'] . ' ' . $book['last_name']) ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php
require_once __DIR__ . '/footer.php';
?>