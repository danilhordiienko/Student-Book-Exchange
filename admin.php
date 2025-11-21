<?php
// Basic admin panel for Student Book Exchange.
// Only users with role 'admin' can access this page.
// Author: Danil Hordiienko.

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/header.php';

if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    echo '<div class="alert alert-danger">Access denied. Admins only.</div>';
    require_once __DIR__ . '/footer.php';
    exit;
}

// Fetch simple datasets for review.
$users = $pdo->query("SELECT id, first_name, last_name, email, role FROM users ORDER BY id ASC")->fetchAll();
$books = $pdo->query("SELECT id, title, author, price_cents FROM books ORDER BY id DESC")->fetchAll();
$merch = $pdo->query("SELECT id, name, price_cents, stock_qty FROM merch ORDER BY id DESC")->fetchAll();
?>

<h1 class="h3 mb-4">Admin dashboard</h1>

<h2 class="h5">Users</h2>
<table class="table table-bordered mb-4">
    <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>
    <?php foreach ($users as $u): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= $u['role'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<h2 class="h5">Books</h2>
<table class="table table-bordered mb-4">
    <tr><th>ID</th><th>Title</th><th>Author</th><th>Price</th></tr>
    <?php foreach ($books as $b): ?>
        <tr>
            <td><?= $b['id'] ?></td>
            <td><?= htmlspecialchars($b['title']) ?></td>
            <td><?= htmlspecialchars($b['author']) ?></td>
            <td>€<?= number_format($b['price_cents'] / 100, 2) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<h2 class="h5">Merchandise</h2>
<table class="table table-bordered mb-4">
    <tr><th>ID</th><th>Name</th><th>Price</th><th>Stock</th></tr>
    <?php foreach ($merch as $m): ?>
        <tr>
            <td><?= $m['id'] ?></td>
            <td><?= htmlspecialchars($m['name']) ?></td>
            <td>€<?= number_format($m['price_cents'] / 100, 2) ?></td>
            <td><?= $m['stock_qty'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php
require_once __DIR__ . '/footer.php';
?>