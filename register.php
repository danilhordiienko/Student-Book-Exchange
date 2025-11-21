<?php
// Registration page for the Student Book Exchange project.
// Handles both displaying the form and processing the POST request.
// Author: Danil Hordiienko, ATU student.

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/header.php';

$errors = [];
$successMessage = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and trim basic fields from the registration form.
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName  = trim($_POST['last_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';

    // Basic validation for required fields.
    if ($firstName === '' || $lastName === '' || $email === '' || $password === '' || $confirm === '') {
        $errors[] = 'Please fill in all fields.';
    }

    // Validate email format for minimal input quality.
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    // Ensure password confirmation matches.
    if ($password !== '' && $password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    // Very simple password length rule for this project.
    if ($password !== '' && strlen($password) < 6) {
        $errors[] = 'Password should be at least 6 characters long.';
    }

    if (empty($errors)) {
        try {
            // Check if the email is already used by another user.
            $checkStmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
            $checkStmt->execute([$email]);
            if ($checkStmt->fetch()) {
                $errors[] = 'This email address is already registered.';
            } else {
                // Hash the password using PHP password_hash.
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                // Insert a new student record into the users table.
                $insertStmt = $pdo->prepare("
                    INSERT INTO users (role, first_name, last_name, email, password_hash)
                    VALUES ('student', ?, ?, ?, ?)
                ");
                $insertStmt->execute([$firstName, $lastName, $email, $passwordHash]);

                $successMessage = 'Account created successfully. You can now log in.';
            }
        } catch (PDOException $e) {
            // For a student project it is acceptable to show a generic error.
            $errors[] = 'Database error while creating the account.';
        }
    }
}
?>

<h1 class="h3 mb-3">Create account</h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $err): ?>
            <div><?= htmlspecialchars($err) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($successMessage): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($successMessage) ?>
    </div>
<?php endif; ?>

<form method="post" class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="first_name">First name</label>
        <input
            type="text"
            class="form-control"
            id="first_name"
            name="first_name"
            required
            value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>"
        >
    </div>
    <div class="col-md-6">
        <label class="form-label" for="last_name">Last name</label>
        <input
            type="text"
            class="form-control"
            id="last_name"
            name="last_name"
            required
            value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>"
        >
    </div>
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
    <div class="col-md-6">
        <label class="form-label" for="password">Password</label>
        <input
            type="password"
            class="form-control"
            id="password"
            name="password"
            required
        >
    </div>
    <div class="col-md-6">
        <label class="form-label" for="confirm_password">Confirm password</label>
        <input
            type="password"
            class="form-control"
            id="confirm_password"
            name="confirm_password"
            required
        >
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-primary">Register</button>
        <a href="login.php" class="btn btn-link">Already have an account?</a>
    </div>
</form>

<?php
require_once __DIR__ . '/footer.php';
?>