<?php
// Login page for the Student Book Exchange project.
// Performs a simple email + password check and starts a session for the user.
// Author: Danil Hordiienko, ATU student.

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/header.php';

$errorMessage = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect basic credentials from the login form.
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errorMessage = 'Please enter both email and password.';
    } else {
        try {
            // Look up the user by email address.
            $stmt = $pdo->prepare('SELECT id, role, first_name, last_name, password_hash FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($password, $user['password_hash'])) {
                $errorMessage = 'Incorrect email or password.';
            } else {
                // On successful login store minimal data in the session.
                $_SESSION['user_id']    = $user['id'];
                $_SESSION['role']       = $user['role'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name']  = $user['last_name'];

                // Basic redirect back to the homepage after login.
                header('Location: index.php');
                exit;
            }
        } catch (PDOException $e) {
            $errorMessage = 'Database error while checking credentials.';
        }
    }
}
?>

<h1 class="h3 mb-3">Login</h1>

<?php if ($errorMessage): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($errorMessage) ?>
    </div>
<?php endif; ?>

<form method="post" class="row g-3">
    <div class="col-12">
        <label class="form-label" for="email">Email</label>
        <input
            type="email"
            class="form-control"
            id="email"
            name="email"
            required
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
        >
    </div>
    <div class="col-12">
        <label class="form-label" for="password">Password</label>
        <input
            type="password"
            class="form-control"
            id="password"
            name="password"
            required
        >
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-primary">Login</button>
        <a href="register.php" class="btn btn-link">Create a new account</a>
    </div>
</form>

<?php
require_once __DIR__ . '/footer.php';
?>