<?php
// Reviews page for the Student Book Exchange project.
// Displays a list of existing reviews and allows logged-in users
// to create new reviews for books or merchandise items.
// Author: Danil Hordiienko, ATU student.

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/header.php';

// Fetch existing reviews
$reviews = [];
$errorMessage = null;

try {
    // Collect reviews with target information
    $stmt = $pdo->query("
        SELECT r.id,
               r.rating,
               r.comment,
               r.created_at,
               u.first_name,
               u.last_name,
               b.title AS book_title,
               m.name AS merch_name
        FROM reviews r
        JOIN users u ON u.id = r.user_id
        LEFT JOIN books b ON b.id = r.book_id
        LEFT JOIN merch m ON m.id = r.merch_id
        ORDER BY r.created_at DESC
    ");
    $reviews = $stmt->fetchAll();
} catch (PDOException $e) {
    $errorMessage = 'Failed to load reviews from the database.';
}

// Handle form submission (adding a review)
$successMessage = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['user_id'])) {
    $targetType = $_POST['target_type'] ?? '';
    $targetId   = (int)($_POST['target_id'] ?? 0);
    $rating     = (int)($_POST['rating'] ?? 0);
    $comment    = trim($_POST['comment'] ?? '');

    // Basic validation
    if ($rating < 1 || $rating > 5) {
        $errorMessage = 'Rating must be between 1 and 5.';
    } elseif ($comment === '') {
        $errorMessage = 'Please enter a comment.';
    } elseif ($targetType !== 'book' && $targetType !== 'merch') {
        $errorMessage = 'Invalid review target type.';
    } else {
        try {
            // Prepare an insert based on target type
            if ($targetType === 'book') {
                $stmt = $pdo->prepare("
                    INSERT INTO reviews (user_id, book_id, rating, comment)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$_SESSION['user_id'], $targetId, $rating, $comment]);
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO reviews (user_id, merch_id, rating, comment)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$_SESSION['user_id'], $targetId, $rating, $comment]);
            }

            $successMessage = 'Your review has been added.';
            // Refresh reviews list
            header('Location: reviews.php?added=1');
            exit;

        } catch (PDOException $e) {
            $errorMessage = 'Failed to submit your review.';
        }
    }
}
?>

<h1 class="h3 mb-3">Student reviews</h1>
<p class="text-muted">
    This page displays feedback written by students for both books and merchandise items.
</p>

<?php if (!empty($_GET['added'])): ?>
    <div class="alert alert-success">Your review has been added.</div>
<?php endif; ?>

<?php if ($errorMessage): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
<?php endif; ?>

<!-- Add review form (only if logged in) -->
<?php if (!empty($_SESSION['user_id'])): ?>

    <?php
    // Pre-load books and merch for the dropdowns
    $bookList = $pdo->query("SELECT id, title FROM books ORDER BY title ASC")->fetchAll();
    $merchList = $pdo->query("SELECT id, name FROM merch WHERE is_active = 1 ORDER BY name ASC")->fetchAll();
    ?>

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Write a review</h5>

            <form method="post" class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Review target</label>
                    <select name="target_type" id="target_type" class="form-select" required>
                        <option value="">Choose...</option>
                        <option value="book">Book</option>
                        <option value="merch">Merch item</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Target item</label>
                    <select name="target_id" id="target_id" class="form-select" required>
                        <option value="">Choose type first</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Rating</label>
                    <select name="rating" class="form-select" required>
                        <option value="1">1 star</option>
                        <option value="2">2 stars</option>
                        <option value="3">3 stars</option>
                        <option value="4">4 stars</option>
                        <option value="5">5 stars</option>
                    </select>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Comment</label>
                    <textarea name="comment" class="form-control" rows="3" required></textarea>
                </div>

                <div class="col-12">
                    <button class="btn btn-primary" type="submit">Submit review</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JS to dynamically load lists -->
    <script>
        const bookList = <?= json_encode($bookList) ?>;
        const merchList = <?= json_encode($merchList) ?>;

        const targetType = document.getElementById('target_type');
        const targetId   = document.getElementById('target_id');

        targetType.addEventListener('change', () => {
            const type = targetType.value;
            targetId.innerHTML = '';

            if (type === 'book') {
                bookList.forEach(b => {
                    const opt = document.createElement('option');
                    opt.value = b.id;
                    opt.textContent = b.title;
                    targetId.appendChild(opt);
                });
            } else if (type === 'merch') {
                merchList.forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m.id;
                    opt.textContent = m.name;
                    targetId.appendChild(opt);
                });
            } else {
                const opt = document.createElement('option');
                opt.textContent = 'Choose type first';
                targetId.appendChild(opt);
            }
        });
    </script>

<?php else: ?>

    <div class="alert alert-info">
        You must be logged in to write reviews.
    </div>

<?php endif; ?>

<hr class="my-4">

<h2 class="h5 mb-3">Latest reviews</h2>

<?php if (empty($reviews)): ?>
    <p class="text-muted">No reviews yet.</p>
<?php else: ?>

    <?php foreach ($reviews as $r): ?>
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <h6 class="card-title mb-1">
                    <?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?>
                    rated
                    <strong><?= $r['rating'] ?>/5</strong>
                </h6>

                <p class="text-muted small mb-1">
                    <?php if ($r['book_title']): ?>
                        Book: <?= htmlspecialchars($r['book_title']) ?>
                    <?php else: ?>
                        Merch: <?= htmlspecialchars($r['merch_name']) ?>
                    <?php endif; ?>
                </p>

                <p class="mb-2">
                    <?= nl2br(htmlspecialchars($r['comment'])) ?>
                </p>

                <p class="text-muted small mb-0">
                    Posted on <?= htmlspecialchars($r['created_at']) ?>
                </p>
            </div>
        </div>
    <?php endforeach; ?>

<?php endif; ?>

<?php
require_once __DIR__ . '/footer.php';
?>