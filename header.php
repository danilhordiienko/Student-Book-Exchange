<?php
// Shared header for the Student Book Exchange project.
// Contains the HTML <head> section and the main navigation bar.
// Author: Danil Hordiienko, ATU student.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Book Exchange</title>

    <!-- Basic responsive layout -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Simple Bootstrap from CDN for quick layout in this project -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    >

    <!-- Custom project styles -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">Book Exchange</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNavbar" aria-controls="mainNavbar"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="books.php">Books</a></li>
                <li class="nav-item"><a class="nav-link" href="merch.php">Merch</a></li>
                <li class="nav-item"><a class="nav-link" href="reviews.php">Reviews</a></li>
            </ul>

            <ul class="navbar-nav mb-2 mb-lg-0">
                <?php if (!empty($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Sign up</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="container my-4">