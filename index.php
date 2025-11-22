<?php
// Landing page for the Student Book Exchange project.
// Shows a short description of the site and quick links to the main sections.
// The page also adapts the greeting if a user is signed in.
// Author: Danil Hordiienko, ATU student.

require_once __DIR__ . '/header.php';

$isLoggedIn = !empty($_SESSION['user_id']);
$firstName  = $_SESSION['first_name'] ?? 'student';
?>

<div class="p-5 mb-4 bg-white rounded-3 shadow-sm">
    <div class="container-fluid py-4">
        <?php if ($isLoggedIn): ?>
            <h1 class="display-5 fw-bold">Welcome back, <?= htmlspecialchars($firstName) ?></h1>
            <p class="col-md-8 fs-5">
                This project demonstrates a simple student platform
                where you can browse books, check campus merchandise,
                mark favourites and place small demo orders.
            </p>
        <?php else: ?>
            <h1 class="display-5 fw-bold">Student Book Exchange</h1>
            <p class="col-md-8 fs-5">
                This web application is a college project that allows
                students to register, browse textbooks, view simple campus
                merchandise and leave feedback.
            </p>
        <?php endif; ?>

        <div class="d-flex flex-wrap gap-2 mt-3">
            <a href="books.php" class="btn btn-primary btn-lg">Browse books</a>
            <a href="merch.php" class="btn btn-outline-primary btn-lg">View merch</a>
            <a href="reviews.php" class="btn btn-outline-secondary btn-lg">Read reviews</a>
            <a href="checkout.php" class="btn btn-outline-dark btn-lg">Go to checkout</a>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/footer.php';
?>