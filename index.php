<?php
// Landing page for the Student Book Exchange project.
// For now this page only introduces the idea of the site.
// Later steps will connect real data and user accounts.

require_once __DIR__ . '/header.php';
?>

<div class="p-5 mb-4 bg-white rounded-3 shadow-sm">
    <div class="container-fluid py-4">
        <h1 class="display-5 fw-bold">Welcome to Student Book Exchange</h1>
        <p class="col-md-8 fs-5">
            This small web app is a college project where students can share,
            find and review textbooks and simple campus merchandise.
        </p>
        <div class="d-flex flex-wrap gap-2 mt-3">
            <a href="books.php" class="btn btn-primary btn-lg">Browse books</a>
            <a href="register.php" class="btn btn-outline-secondary btn-lg">
                Create account
            </a>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/footer.php';